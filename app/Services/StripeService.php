<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = app('stripe');
    }

    /**
     * Créer un client Stripe pour un vendeur
     */
    public function createCustomer(Vendor $vendor, array $customMetadata = []): string
    {
        try {
            $metadata = [
                'vendor_id' => $vendor->id,
                'environment' => app()->environment(),
            ];
            
            if (!empty($customMetadata)) {
                $metadata = array_merge($metadata, $customMetadata);
            }
            
            Log::info('StripeService::createCustomer - Création du client Stripe', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->email,
                'metadata' => $metadata
            ]);
            
            $customerData = [
                'email' => $vendor->email,
                'name' => $vendor->company_name,
                'metadata' => $metadata,
                'phone' => $vendor->phone ?? null,
                'address' => [
                    'line1' => $vendor->address,
                    'postal_code' => $vendor->postal_code,
                    'city' => $vendor->city,
                    'country' => $vendor->country,
                ]
            ];

            $customer = $this->stripe->customers->create($customerData);
            
            $vendor->stripe_customer_id = $customer->id;
            $vendor->save();
            
            Log::info('StripeService::createCustomer - Client Stripe créé', [
                'vendor_id' => $vendor->id,
                'stripe_customer_id' => $customer->id
            ]);
            
            return $customer->id;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::createCustomer - Erreur API Stripe', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus()
            ]);
            
            report($e);
            throw new Exception("Impossible de créer le client Stripe: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::createCustomer - Erreur générale', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Erreur lors de la création du client Stripe: " . $e->getMessage());
        }
    }

    /**
     * Créer une session de paiement Stripe avec abonnement
     */
    public function createCheckoutSession($vendor, string $plan, string $priceId, string $successUrl, 
                                          string $cancelUrl, array $customMetadata = [])
    {
        // Déterminer si c'est un vendeur temporaire (lors de l'inscription)
        $isTemporaryVendor = is_string($vendor->id ?? null) && str_starts_with($vendor->id, 'temp_');
        $vendorId = $vendor->id ?? 'temp_registration';
        
        try {
            // Vérifier que le prix existe et est actif
            $price = $this->stripe->prices->retrieve($priceId);
            if (!$price->active) {
                throw new Exception("Le prix Stripe {$priceId} n'est pas actif");
            }
            
            // Métadonnées de base
            $metadata = [
                'vendor_id' => $vendorId,
                'plan' => $plan,
                'environment' => app()->environment(),
                'created_at' => now()->toISOString(),
            ];
            
            // Ajouter le token si présent dans la session
            if (session()->has('vendor_token')) {
                $metadata['token'] = session('vendor_token');
            }
            
            // Fusionner avec les métadonnées personnalisées
            if (!empty($customMetadata)) {
                $metadata = array_merge($metadata, $customMetadata);
            }
            
            Log::info('StripeService::createCheckoutSession - Création de la session de paiement', [
                'vendor_id' => $vendorId,
                'plan' => $plan,
                'price_id' => $priceId,
                'price_amount' => $price->unit_amount / 100,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'is_temporary_vendor' => $isTemporaryVendor
            ]);
            
            // Configuration de base de la session
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => $metadata,
                'allow_promotion_codes' => true,
                'billing_address_collection' => 'auto',
                'locale' => 'fr',
                'automatic_tax' => [
                    'enabled' => true,
                ],
                'subscription_data' => [
                    'metadata' => $metadata,
                    'description' => "Abonnement {$plan} pour " . ($vendor->company_name ?? 'Nouveau vendeur'),
                ]
            ];
            
            // Gestion du client selon le type de vendeur
            if (!$isTemporaryVendor && !empty($vendor->stripe_customer_id)) {
                // Vendeur existant avec un customer Stripe
                $sessionData['customer'] = $vendor->stripe_customer_id;
                Log::info('Using existing Stripe customer', ['customer_id' => $vendor->stripe_customer_id]);
            } else {
                // Vendeur temporaire ou nouveau - pré-remplir l'email si disponible
                if (!empty($vendor->email)) {
                    $sessionData['customer_email'] = $vendor->email;
                    Log::info('Pre-filling customer email', ['email' => $vendor->email]);
                }
                
                // Si c'est un vendeur existant sans customer Stripe, on peut le créer maintenant
                if (!$isTemporaryVendor && empty($vendor->stripe_customer_id)) {
                    try {
                        $customerId = $this->createCustomer($vendor, $customMetadata);
                        $sessionData['customer'] = $customerId;
                        Log::info('Created new Stripe customer for existing vendor', ['customer_id' => $customerId]);
                    } catch (Exception $e) {
                        // Si la création du customer échoue, on continue avec customer_email
                        Log::warning('Failed to create customer, using email instead', ['error' => $e->getMessage()]);
                    }
                }
            }
            
            $session = $this->stripe->checkout->sessions->create($sessionData);
            
            Log::info('StripeService::createCheckoutSession - Session créée avec succès', [
                'vendor_id' => $vendorId,
                'session_id' => $session->id,
                'session_url' => $session->url,
                'amount_total' => $session->amount_total,
                'currency' => $session->currency
            ]);
            
            return $session;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::createCheckoutSession - Erreur API Stripe', [
                'vendor_id' => $vendorId,
                'plan' => $plan,
                'price_id' => $priceId,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la création de la session: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::createCheckoutSession - Erreur générale', [
                'vendor_id' => $vendorId,
                'plan' => $plan,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de créer la session de paiement: " . $e->getMessage());
        }
    }

    /**
     * Récupérer une session de paiement avec détails complets
     */
    public function retrieveCheckoutSession($sessionId)
    {
        try {
            Log::info('StripeService::retrieveCheckoutSession - Récupération de la session', [
                'session_id' => $sessionId
            ]);
            
            $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => [
                    'subscription', 
                    'customer', 
                    'line_items',
                    'payment_intent',
                    'subscription.latest_invoice',
                    'subscription.default_payment_method'
                ],
            ]);
            
            Log::info('StripeService::retrieveCheckoutSession - Session récupérée', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
                'customer_id' => $session->customer->id ?? 'non défini',
                'subscription_id' => $session->subscription->id ?? 'non défini',
                'subscription_status' => $session->subscription->status ?? 'non défini',
                'amount_total' => $session->amount_total
            ]);
            
            return $session;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::retrieveCheckoutSession - Erreur API Stripe', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la récupération de la session: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::retrieveCheckoutSession - Erreur générale', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de récupérer la session: " . $e->getMessage());
        }
    }

    /**
     * Créer un abonnement directement (sans passer par Checkout)
     */
    public function createSubscription(Vendor $vendor, string $plan, string $priceId, array $customMetadata = [])
    {
        // Assurez-vous que le vendeur a un ID client Stripe
        if (empty($vendor->stripe_customer_id)) {
            $this->createCustomer($vendor, $customMetadata);
        }

        try {
            // Métadonnées de base
            $metadata = [
                'vendor_id' => $vendor->id,
                'plan' => $plan,
                'environment' => app()->environment(),
                'created_via' => 'direct_api',
            ];
            
            // Fusionner avec les métadonnées personnalisées
            if (!empty($customMetadata)) {
                $metadata = array_merge($metadata, $customMetadata);
            }
            
            Log::info('StripeService::createSubscription - Création de l\'abonnement', [
                'vendor_id' => $vendor->id,
                'plan' => $plan,
                'price_id' => $priceId,
                'customer_id' => $vendor->stripe_customer_id
            ]);
            
            $subscriptionData = [
                'customer' => $vendor->stripe_customer_id,
                'items' => [
                    ['price' => $priceId],
                ],
                'metadata' => $metadata,
                'expand' => ['latest_invoice.payment_intent'],
                'description' => "Abonnement {$plan} pour {$vendor->company_name}",
                'collection_method' => 'charge_automatically',
            ];
            
            $subscription = $this->stripe->subscriptions->create($subscriptionData);
            
            // Enregistrer l'abonnement dans la base de données locale
            $localSubscription = $this->createLocalSubscription($vendor, $subscription, $plan, $metadata);
            
            Log::info('StripeService::createSubscription - Abonnement créé avec succès', [
                'vendor_id' => $vendor->id,
                'stripe_subscription_id' => $subscription->id,
                'local_subscription_id' => $localSubscription->id,
                'status' => $subscription->status
            ]);
            
            return $subscription;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::createSubscription - Erreur API Stripe', [
                'vendor_id' => $vendor->id,
                'plan' => $plan,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la création de l'abonnement: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::createSubscription - Erreur générale', [
                'vendor_id' => $vendor->id,
                'plan' => $plan,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de créer l'abonnement: " . $e->getMessage());
        }
    }

    /**
     * Créer un enregistrement d'abonnement local
     */
    private function createLocalSubscription(Vendor $vendor, $stripeSubscription, string $plan, array $metadata = []): Subscription
    {
        $localSubscription = Subscription::create([
            'vendor_id' => $vendor->id,
            'stripe_id' => $stripeSubscription->id,
            'plan' => $plan,
            'status' => $stripeSubscription->status,
            'amount' => config("stripe.plans.{$plan}.amount", 0),
            'currency' => 'EUR',
            'interval' => 'month',
            'metadata' => json_encode($metadata),
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'trial_ends_at' => $stripeSubscription->trial_end ? 
                \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
        ]);
        
        Log::info('Local subscription created', [
            'subscription_id' => $localSubscription->id,
            'vendor_id' => $vendor->id,
            'stripe_id' => $stripeSubscription->id
        ]);
        
        return $localSubscription;
    }

    /**
     * Enregistrer un paiement réussi
     */
    public function recordPayment($paymentIntent, $model): Payment
    {
        Log::info('StripeService::recordPayment - Enregistrement du paiement', [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'status' => $paymentIntent->status
        ]);
        
        $payment = Payment::create([
            'payable_type' => get_class($model),
            'payable_id' => $model->id,
            'payment_id' => $paymentIntent->id,
            'payment_method' => $paymentIntent->payment_method ?? null,
            'currency' => strtoupper($paymentIntent->currency),
            'amount' => $paymentIntent->amount,
            'status' => $paymentIntent->status,
            'metadata' => json_encode($paymentIntent->metadata->toArray()),
            'paid_at' => now(),
        ]);
        
        Log::info('StripeService::recordPayment - Paiement enregistré', [
            'payment_id' => $payment->id,
            'status' => $payment->status
        ]);
        
        return $payment;
    }

    /**
     * Annuler un abonnement
     */
    public function cancelSubscription(Subscription $subscription, bool $immediateCancel = false)
    {
        try {
            Log::info('StripeService::cancelSubscription - Annulation de l\'abonnement', [
                'subscription_id' => $subscription->id,
                'stripe_id' => $subscription->stripe_id,
                'immediate_cancel' => $immediateCancel
            ]);
            
            if ($immediateCancel) {
                // Annulation immédiate
                $result = $this->stripe->subscriptions->cancel($subscription->stripe_id, [
                    'prorate' => false,
                    'invoice_now' => false,
                ]);
                
                $subscription->status = 'canceled';
                $subscription->ends_at = now();
            } else {
                // Annulation à la fin de la période
                $result = $this->stripe->subscriptions->update($subscription->stripe_id, [
                    'cancel_at_period_end' => true,
                ]);
                
                $subscription->status = 'active'; // Reste actif jusqu'à la fin
                $subscription->ends_at = \Carbon\Carbon::createFromTimestamp($result->current_period_end);
            }
            
            $subscription->save();
            
            Log::info('StripeService::cancelSubscription - Abonnement annulé', [
                'subscription_id' => $subscription->id,
                'status' => $result->status,
                'ends_at' => $subscription->ends_at
            ]);
            
            return $result;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::cancelSubscription - Erreur API Stripe', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de l'annulation: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::cancelSubscription - Erreur générale', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible d'annuler l'abonnement: " . $e->getMessage());
        }
    }
    
    /**
     * Récupérer les détails d'un abonnement
     */
    public function retrieveSubscription($subscriptionId)
    {
        try {
            Log::info('StripeService::retrieveSubscription - Récupération de l\'abonnement', [
                'subscription_id' => $subscriptionId
            ]);
            
            $subscription = $this->stripe->subscriptions->retrieve($subscriptionId, [
                'expand' => [
                    'customer', 
                    'default_payment_method', 
                    'latest_invoice',
                    'items.data.price'
                ]
            ]);
            
            Log::info('StripeService::retrieveSubscription - Abonnement récupéré', [
                'subscription_id' => $subscriptionId,
                'status' => $subscription->status,
                'customer_id' => $subscription->customer->id ?? 'non défini'
            ]);
            
            return $subscription;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::retrieveSubscription - Erreur API Stripe', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la récupération: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::retrieveSubscription - Erreur générale', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de récupérer l'abonnement: " . $e->getMessage());
        }
    }
    
    /**
     * Mettre à jour un abonnement (changer de plan)
     */
    public function updateSubscription(Subscription $subscription, string $newPriceId, string $newPlan)
    {
        try {
            Log::info('StripeService::updateSubscription - Mise à jour de l\'abonnement', [
                'subscription_id' => $subscription->id,
                'stripe_id' => $subscription->stripe_id,
                'current_plan' => $subscription->plan,
                'new_plan' => $newPlan,
                'new_price_id' => $newPriceId
            ]);
            
            // Récupérer l'abonnement Stripe actuel
            $stripeSubscription = $this->retrieveSubscription($subscription->stripe_id);
            $currentItem = $stripeSubscription->items->data[0];
            
            // Mettre à jour l'abonnement
            $updatedSubscription = $this->stripe->subscriptions->update($subscription->stripe_id, [
                'items' => [
                    [
                        'id' => $currentItem->id,
                        'price' => $newPriceId,
                    ],
                ],
                'proration_behavior' => 'create_prorations',
                'metadata' => [
                    'plan' => $newPlan,
                    'vendor_id' => $subscription->vendor_id,
                    'updated_at' => now()->toISOString(),
                ]
            ]);
            
            // Mettre à jour l'enregistrement local
            $subscription->plan = $newPlan;
            $subscription->amount = config("stripe.plans.{$newPlan}.amount", 0);
            $subscription->updated_at = now();
            $subscription->save();
            
            Log::info('StripeService::updateSubscription - Abonnement mis à jour', [
                'subscription_id' => $subscription->id,
                'status' => $updatedSubscription->status,
                'new_plan' => $newPlan
            ]);
            
            return $updatedSubscription;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::updateSubscription - Erreur API Stripe', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la mise à jour: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::updateSubscription - Erreur générale', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de mettre à jour l'abonnement: " . $e->getMessage());
        }
    }
    
    /**
     * Récupérer les factures d'un client
     */
    public function getCustomerInvoices($customerId, $limit = 10)
    {
        try {
            Log::info('StripeService::getCustomerInvoices - Récupération des factures', [
                'customer_id' => $customerId,
                'limit' => $limit
            ]);
            
            $invoices = $this->stripe->invoices->all([
                'customer' => $customerId,
                'limit' => $limit,
                'expand' => ['data.subscription', 'data.payment_intent']
            ]);
            
            Log::info('StripeService::getCustomerInvoices - Factures récupérées', [
                'customer_id' => $customerId,
                'count' => count($invoices->data)
            ]);
            
            return $invoices->data;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::getCustomerInvoices - Erreur API Stripe', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la récupération des factures: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::getCustomerInvoices - Erreur générale', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de récupérer les factures: " . $e->getMessage());
        }
    }

    /**
     * Créer une session de paiement pour un voyage (paiement unique)
     */
    public function createTripPaymentSession($trip, array $bookingData, string $successUrl, string $cancelUrl)
    {
        try {
            $participants = $bookingData['participants'] ?? 1;
            $totalAmount = $trip->price * $participants;
            
            Log::info('StripeService::createTripPaymentSession - Création session voyage', [
                'trip_id' => $trip->id,
                'trip_title' => $trip->title,
                'participants' => $participants,
                'unit_price' => $trip->price,
                'total_amount' => $totalAmount
            ]);
            
            $metadata = [
                'type' => 'trip_booking',
                'trip_id' => $trip->id,
                'vendor_id' => $trip->vendor_id,
                'participants' => $participants,
                'departure_date' => $bookingData['departure_date'] ?? null,
                'environment' => app()->environment(),
            ];
            
            if (!empty($bookingData['special_requests'])) {
                $metadata['special_requests'] = substr($bookingData['special_requests'], 0, 500);
            }
            
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $trip->title,
                            'description' => "Voyage organisé par {$trip->vendor->company_name}",
                        ],
                        'unit_amount' => $trip->price * 100, // Convertir en centimes
                    ],
                    'quantity' => $participants,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => $metadata,
                'billing_address_collection' => 'required',
                'locale' => 'fr',
                'automatic_tax' => [
                    'enabled' => true,
                ],
                'customer_creation' => 'always',
            ];
            
            // Ajouter l'email du client si connecté
            if (auth()->check()) {
                $sessionData['customer_email'] = auth()->user()->email;
            }
            
            $session = $this->stripe->checkout->sessions->create($sessionData);
            
            Log::info('StripeService::createTripPaymentSession - Session voyage créée', [
                'trip_id' => $trip->id,
                'session_id' => $session->id,
                'session_url' => $session->url,
                'amount_total' => $session->amount_total
            ]);
            
            return $session;
            
        } catch (ApiErrorException $e) {
            Log::error('StripeService::createTripPaymentSession - Erreur API Stripe', [
                'trip_id' => $trip->id,
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode()
            ]);
            
            report($e);
            throw new Exception("Erreur Stripe lors de la création du paiement voyage: " . $e->getMessage());
            
        } catch (\Exception $e) {
            Log::error('StripeService::createTripPaymentSession - Erreur générale', [
                'trip_id' => $trip->id,
                'error' => $e->getMessage()
            ]);
            
            report($e);
            throw new Exception("Impossible de créer la session de paiement voyage: " . $e->getMessage());
        }
    }

    /**
     * Vérifier la validité d'une signature webhook
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        try {
            \Stripe\Webhook::constructEvent($payload, $signature, $secret);
            return true;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Signature webhook invalide', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}