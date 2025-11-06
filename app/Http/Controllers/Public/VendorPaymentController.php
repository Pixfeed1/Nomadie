<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VendorPaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Affiche la page de paiement
     */
    public function show(Request $request)
    {
        $token = $request->query('token') ?? session('vendor_token');
        
        Log::info('Payment page requested', [
            'token' => $token ?? 'none',
            'session_id' => session()->getId()
        ]);

        if (!$token) {
            Log::warning('No token provided for payment page');
            return redirect()->route('vendor.register')
                ->with('error', 'Session expirée. Veuillez recommencer votre inscription.');
        }

        // Récupérer les données d'inscription
        $vendorData = Cache::get('vendor_registration_' . $token);
        
        if (!$vendorData) {
            $vendorData = session('vendor_data');
        }

        if (!$vendorData || !isset($vendorData['subscription'])) {
            Log::warning('No vendor data found for payment', ['token' => $token]);
            return redirect()->route('vendor.register')
                ->with('error', 'Données d\'inscription introuvables. Veuillez recommencer.');
        }

        $plan = $vendorData['subscription'];
        
        // Vérifier que c'est un plan payant
        if ($plan === 'free') {
            Log::warning('Attempted to access payment page for free plan');
            return redirect()->route('vendor.register')
                ->with('error', 'Aucun paiement requis pour le plan gratuit.');
        }

        // Récupérer le montant depuis la configuration
        $amount = config("stripe.plans.{$plan}.amount");
        
        if (!$amount) {
            Log::error('Plan amount not found', ['plan' => $plan]);
            return redirect()->route('vendor.register')
                ->with('error', 'Configuration de tarif invalide.');
        }

        return view('public.vendors.payment', [
            'token' => $token,
            'plan' => $plan,
            'amount' => $amount,
            'vendorData' => $vendorData
        ]);
    }

    /**
     * Initie le processus de paiement avec Stripe
     */
    public function initiate(Request $request)
    {
        $token = $request->input('token') ?? session('vendor_token');
        
        Log::info('Payment initiation requested', [
            'token' => $token ?? 'none',
            'session_id' => session()->getId()
        ]);

        if (!$token) {
            return response()->json([
                'error' => 'Token manquant'
            ], 400);
        }

        try {
            // Récupérer les données d'inscription
            $vendorData = Cache::get('vendor_registration_' . $token);
            
            if (!$vendorData) {
                $vendorData = session('vendor_data');
            }

            if (!$vendorData || !isset($vendorData['subscription'])) {
                return response()->json([
                    'error' => 'Données d\'inscription introuvables'
                ], 400);
            }

            $plan = $vendorData['subscription'];
            
            if ($plan === 'free') {
                return response()->json([
                    'error' => 'Aucun paiement requis pour le plan gratuit'
                ], 400);
            }

            // Récupérer l'ID du prix Stripe
            $priceId = config("stripe.plans.{$plan}.price_id");
            
            if (!$priceId) {
                Log::error('Price ID not found for plan', ['plan' => $plan]);
                return response()->json([
                    'error' => 'Configuration de prix invalide'
                ], 500);
            }

            // Créer un vendeur temporaire pour Stripe
            $tempVendor = new \App\Models\Vendor();
            $tempVendor->id = 'temp_' . $token; // ID temporaire
            $tempVendor->email = $vendorData['email'];
            $tempVendor->company_name = $vendorData['company_name'];

            // URLs de redirection
            $successUrl = route('vendor.payment.success', ['token' => $token]);
            $cancelUrl = route('vendor.payment.cancel', ['token' => $token]);

            Log::info('Creating Stripe checkout session', [
                'plan' => $plan,
                'price_id' => $priceId,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl
            ]);

            // Créer la session Stripe Checkout
            $session = $this->stripeService->createCheckoutSession(
                $tempVendor,
                $plan,
                $priceId,
                $successUrl,
                $cancelUrl,
                ['token' => $token]
            );

            Log::info('Stripe session created', [
                'session_id' => $session->id,
                'session_url' => $session->url
            ]);

            return response()->json([
                'sessionId' => $session->id,
                'sessionUrl' => $session->url
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating payment session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gère le retour de paiement réussi
     */
    public function success(Request $request)
    {
        $token = $request->query('token');
        $sessionId = $request->query('session_id');
        
        Log::info('Payment success callback', [
            'token' => $token ?? 'none',
            'session_id' => $sessionId ?? 'none'
        ]);

        if (!$token || !$sessionId) {
            Log::warning('Missing token or session_id in success callback');
            return redirect()->route('vendor.register')
                ->with('error', 'Paramètres de paiement manquants.');
        }

        try {
            // Vérifier la session Stripe
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);
            
            if ($session->payment_status !== 'paid') {
                Log::warning('Payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status
                ]);
                
                return redirect()->route('vendor.register')
                    ->with('error', 'Le paiement n\'a pas été finalisé.');
            }

            Log::info('Payment verified, completing registration', [
                'session_id' => $sessionId,
                'token' => $token
            ]);

            // Finaliser l'inscription
            $vendorRegistrationController = new VendorRegistrationController();
            return $vendorRegistrationController->completeRegistration($request);

        } catch (\Exception $e) {
            Log::error('Error processing payment success', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('vendor.register')
                ->with('error', 'Erreur lors de la vérification du paiement. Contactez notre support.');
        }
    }

    /**
     * Gère l'annulation du paiement
     */
    public function cancel(Request $request)
    {
        $token = $request->query('token');
        
        Log::info('Payment cancelled', ['token' => $token ?? 'none']);

        return redirect()->route('vendor.payment.show', ['token' => $token])
            ->with('error', 'Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.');
    }
}