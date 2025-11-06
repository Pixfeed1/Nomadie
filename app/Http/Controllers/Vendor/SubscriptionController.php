<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\StripeService;
use App\Models\Subscription;
use Carbon\Carbon;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    protected $stripeService;
    protected $stripe;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->stripe = new StripeClient(config('stripe.secret'));
    }

    /**
     * Affiche la page de gestion de l'abonnement
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer l'abonnement actuel
        $subscription = $vendor->subscriptions()
            ->where('status', 'active')
            ->first();
        
        // Récupérer l'historique des abonnements
        $subscriptionHistory = $vendor->subscriptions()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Informations sur le plan actuel
        $currentPlan = $this->getPlanDetails($vendor->subscription_plan);
        
        // Statistiques d'utilisation
        $usage = [
            'trips_used' => $vendor->trips()->count(),
            'trips_limit' => $currentPlan['trips'],
            'destinations_used' => $vendor->countries()->count(),
            'destinations_limit' => $currentPlan['destinations'],
            'bookings_this_month' => $vendor->bookings()
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'revenue_this_month' => $vendor->payments()
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('vendor_amount'),
        ];
        
        // Prochaine date de facturation
        $nextBillingDate = null;
        if ($subscription && $subscription->stripe_subscription_id) {
            try {
                $stripeSubscription = $this->stripe->subscriptions->retrieve(
                    $subscription->stripe_subscription_id
                );
                $nextBillingDate = Carbon::createFromTimestamp($stripeSubscription->current_period_end);
            } catch (\Exception $e) {
                Log::error('Error retrieving Stripe subscription', [
                    'error' => $e->getMessage(),
                    'subscription_id' => $subscription->stripe_subscription_id
                ]);
            }
        }
        
        return view('vendor.subscription.index', compact(
            'vendor',
            'subscription',
            'subscriptionHistory',
            'currentPlan',
            'usage',
            'nextBillingDate'
        ));
    }
    
    /**
     * Affiche la page de mise à niveau d'abonnement
     */
    public function upgrade()
    {
        $vendor = Auth::user()->vendor;
        $currentPlan = $vendor->subscription_plan;
        
        // Plans disponibles
        $plans = $this->getAllPlans();
        
        // Filtrer pour ne montrer que les plans supérieurs
        $availablePlans = array_filter($plans, function($key) use ($currentPlan) {
            $planOrder = ['essential' => 0, 'professional' => 1, 'pro' => 2];
            return $planOrder[$key] > ($planOrder[$currentPlan] ?? -1);
        }, ARRAY_FILTER_USE_KEY);
        
        // Calculer les économies pour l'abonnement annuel
        foreach ($availablePlans as $key => &$plan) {
            $plan['annual_price'] = $plan['price'] * 12 * 0.9; // 10% de réduction
            $plan['annual_savings'] = ($plan['price'] * 12) - $plan['annual_price'];
        }
        
        return view('vendor.subscription.upgrade', compact(
            'vendor',
            'currentPlan',
            'plans',
            'availablePlans'
        ));
    }
    
    /**
     * Traite la mise à niveau de l'abonnement
     */
    public function processUpgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:professional,pro',
            'billing_cycle' => 'required|in:monthly,annual'
        ]);
        
        $vendor = Auth::user()->vendor;
        $newPlan = $request->plan;
        $billingCycle = $request->billing_cycle;
        
        try {
            // Vérifier que c'est bien une mise à niveau
            $planOrder = ['essential' => 0, 'professional' => 1, 'pro' => 2];
            if ($planOrder[$newPlan] <= $planOrder[$vendor->subscription_plan]) {
                return redirect()->back()
                    ->with('error', 'Vous ne pouvez que mettre à niveau votre abonnement.');
            }
            
            // Récupérer ou créer le client Stripe
            if (!$vendor->stripe_customer_id) {
                $customer = $this->stripe->customers->create([
                    'email' => $vendor->email,
                    'name' => $vendor->company_name,
                    'metadata' => [
                        'vendor_id' => $vendor->id
                    ]
                ]);
                
                $vendor->stripe_customer_id = $customer->id;
                $vendor->save();
            }
            
            // Récupérer le price_id selon le plan et le cycle
            $priceId = config("stripe.plans.{$newPlan}.price_id_{$billingCycle}");
            
            if (!$priceId) {
                throw new \Exception('Configuration de prix invalide');
            }
            
            // Créer une session de checkout
            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('vendor.subscription.upgrade.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('vendor.subscription.upgrade'),
                'customer' => $vendor->stripe_customer_id,
                'metadata' => [
                    'vendor_id' => $vendor->id,
                    'plan' => $newPlan,
                    'billing_cycle' => $billingCycle,
                    'type' => 'upgrade'
                ],
                'subscription_data' => [
                    'metadata' => [
                        'vendor_id' => $vendor->id,
                        'plan' => $newPlan
                    ]
                ],
                'allow_promotion_codes' => true,
                'billing_address_collection' => 'auto',
                'locale' => 'fr',
            ]);
            
            // Stocker temporairement les informations de mise à niveau
            session([
                'upgrade_session_id' => $session->id,
                'upgrade_plan' => $newPlan,
                'upgrade_billing_cycle' => $billingCycle
            ]);
            
            return redirect($session->url);
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error during upgrade', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur Stripe : ' . $e->getMessage());
                
        } catch (\Exception $e) {
            Log::error('Error processing upgrade', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à niveau.');
        }
    }
    
    /**
     * Gère le retour après une mise à niveau réussie
     */
    public function upgradeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('vendor.subscription.index')
                ->with('error', 'Session invalide.');
        }
        
        try {
            // Vérifier la session Stripe
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            
            if ($session->payment_status !== 'paid') {
                return redirect()->route('vendor.subscription.index')
                    ->with('error', 'Le paiement n\'a pas été complété.');
            }
            
            $vendor = Auth::user()->vendor;
            $metadata = $session->metadata;
            
            // Mettre à jour l'abonnement du vendeur
            $vendor->subscription_plan = $metadata->plan;
            $vendor->stripe_subscription_id = $session->subscription;
            $vendor->subscription_status = 'active';
            $vendor->save();
            
            // Créer un enregistrement de l'abonnement
            Subscription::create([
                'vendor_id' => $vendor->id,
                'stripe_subscription_id' => $session->subscription,
                'stripe_customer_id' => $session->customer,
                'plan' => $metadata->plan,
                'status' => 'active',
                'current_period_end' => Carbon::now()->addMonth(),
                'amount' => $session->amount_total / 100,
                'currency' => $session->currency,
            ]);
            
            // Mettre à jour les limites selon le nouveau plan
            $planDetails = $this->getPlanDetails($metadata->plan);
            $vendor->max_trips = $planDetails['trips_numeric'];
            $vendor->save();
            
            return redirect()->route('vendor.subscription.index')
                ->with('success', 'Votre abonnement a été mis à niveau avec succès !');
                
        } catch (\Exception $e) {
            Log::error('Error processing upgrade success', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
            
            return redirect()->route('vendor.subscription.index')
                ->with('error', 'Une erreur est survenue lors de la finalisation.');
        }
    }
    
    /**
     * Annule l'abonnement
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'confirm' => 'required|accepted'
        ]);
        
        $vendor = Auth::user()->vendor;
        
        try {
            if (!$vendor->stripe_subscription_id) {
                return redirect()->back()
                    ->with('error', 'Aucun abonnement actif à annuler.');
            }
            
            // Annuler l'abonnement à la fin de la période
            $subscription = $this->stripe->subscriptions->update(
                $vendor->stripe_subscription_id,
                ['cancel_at_period_end' => true]
            );
            
            // Mettre à jour le statut local
            $vendorSubscription = $vendor->subscriptions()
                ->where('stripe_subscription_id', $vendor->stripe_subscription_id)
                ->first();
                
            if ($vendorSubscription) {
                $vendorSubscription->status = 'canceling';
                $vendorSubscription->cancel_at = Carbon::createFromTimestamp($subscription->current_period_end);
                $vendorSubscription->cancel_reason = $request->reason;
                $vendorSubscription->save();
            }
            
            // Envoyer un email de confirmation
            // Mail::to($vendor->email)->send(new SubscriptionCancelled($vendor));
            
            return redirect()->route('vendor.subscription.index')
                ->with('warning', 'Votre abonnement sera annulé à la fin de la période de facturation actuelle.');
                
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error during cancellation', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
                
        } catch (\Exception $e) {
            Log::error('Error cancelling subscription', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }
    
    /**
     * Réactive l'abonnement annulé
     */
    public function resume(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        try {
            if (!$vendor->stripe_subscription_id) {
                return redirect()->back()
                    ->with('error', 'Aucun abonnement à réactiver.');
            }
            
            // Réactiver l'abonnement
            $subscription = $this->stripe->subscriptions->update(
                $vendor->stripe_subscription_id,
                ['cancel_at_period_end' => false]
            );
            
            // Mettre à jour le statut local
            $vendorSubscription = $vendor->subscriptions()
                ->where('stripe_subscription_id', $vendor->stripe_subscription_id)
                ->first();
                
            if ($vendorSubscription) {
                $vendorSubscription->status = 'active';
                $vendorSubscription->cancel_at = null;
                $vendorSubscription->save();
            }
            
            return redirect()->route('vendor.subscription.index')
                ->with('success', 'Votre abonnement a été réactivé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Error resuming subscription', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la réactivation.');
        }
    }
    
    /**
     * Affiche l'historique des factures
     */
    public function invoices()
    {
        $vendor = Auth::user()->vendor;
        $invoices = collect();
        
        try {
            if ($vendor->stripe_customer_id) {
                // Récupérer les factures depuis Stripe
                $stripeInvoices = $this->stripe->invoices->all([
                    'customer' => $vendor->stripe_customer_id,
                    'limit' => 100
                ]);
                
                $invoices = collect($stripeInvoices->data)->map(function($invoice) {
                    return [
                        'id' => $invoice->id,
                        'number' => $invoice->number,
                        'date' => Carbon::createFromTimestamp($invoice->created),
                        'amount' => $invoice->amount_paid / 100,
                        'currency' => strtoupper($invoice->currency),
                        'status' => $invoice->status,
                        'pdf_url' => $invoice->invoice_pdf,
                        'hosted_url' => $invoice->hosted_invoice_url,
                        'description' => $invoice->lines->data[0]->description ?? 'Abonnement',
                    ];
                });
            }
        } catch (\Exception $e) {
            Log::error('Error fetching invoices', [
                'error' => $e->getMessage(),
                'vendor_id' => $vendor->id
            ]);
        }
        
        return view('vendor.subscription.invoices', compact('invoices'));
    }
    
    /**
     * Télécharge une facture
     */
    public function downloadInvoice($id)
    {
        $vendor = Auth::user()->vendor;
        
        try {
            // Vérifier que la facture appartient bien au vendeur
            $invoice = $this->stripe->invoices->retrieve($id);
            
            if ($invoice->customer !== $vendor->stripe_customer_id) {
                abort(403, 'Accès non autorisé à cette facture.');
            }
            
            // Rediriger vers le PDF Stripe
            return redirect($invoice->invoice_pdf);
            
        } catch (\Exception $e) {
            Log::error('Error downloading invoice', [
                'error' => $e->getMessage(),
                'invoice_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Impossible de télécharger la facture.');
        }
    }
    
    /**
     * Retourne les détails d'un plan
     */
    private function getPlanDetails($planKey)
    {
        $plans = $this->getAllPlans();
        return $plans[$planKey] ?? $plans['essential'];
    }
    
    /**
     * Retourne tous les plans disponibles
     */
    private function getAllPlans()
    {
        return [
            'essential' => [
                'key' => 'essential',
                'name' => 'Essentiel',
                'price' => 0,
                'currency' => '€',
                'interval' => 'mois',
                'trips' => '10 voyages',
                'trips_numeric' => 10,
                'destinations' => '5 destinations',
                'destinations_numeric' => 5,
                'commission' => 10,
                'features' => [
                    'Tableau de bord complet',
                    'Gestion des voyages',
                    'Gestion des réservations',
                    'Support par email',
                    'Export CSV des données'
                ],
                'limitations' => [
                    'Maximum 10 voyages actifs',
                    'Maximum 5 destinations',
                    'Commission de 10% sur les ventes'
                ],
                'color' => 'gray',
                'popular' => false
            ],
            'professional' => [
                'key' => 'professional',
                'name' => 'Professionnel',
                'price' => 29,
                'currency' => '€',
                'interval' => 'mois',
                'trips' => '50 voyages',
                'trips_numeric' => 50,
                'destinations' => '20 destinations',
                'destinations_numeric' => 20,
                'commission' => 8,
                'features' => [
                    'Tout du plan Essentiel',
                    'Analytics avancées',
                    'Support prioritaire',
                    'Export PDF et Excel',
                    'Personnalisation de la page vendeur',
                    'Notifications push',
                    'Rapports mensuels détaillés'
                ],
                'limitations' => [
                    'Maximum 50 voyages actifs',
                    'Maximum 20 destinations',
                    'Commission réduite à 8%'
                ],
                'color' => 'blue',
                'popular' => true
            ],
            'pro' => [
                'key' => 'pro',
                'name' => 'Pro',
                'price' => 99,
                'currency' => '€',
                'interval' => 'mois',
                'trips' => 'Illimité',
                'trips_numeric' => 9999,
                'destinations' => 'Illimité',
                'destinations_numeric' => 9999,
                'commission' => 5,
                'features' => [
                    'Tout du plan Professionnel',
                    'Voyages et destinations illimités',
                    'API Access',
                    'Support dédié 24/7',
                    'Formation personnalisée',
                    'Gestionnaire de compte dédié',
                    'Intégrations personnalisées',
                    'Marque blanche disponible'
                ],
                'limitations' => [],
                'color' => 'purple',
                'popular' => false
            ]
        ];
    }
}