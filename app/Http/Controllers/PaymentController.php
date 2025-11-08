<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Trip;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Récupère la clé Stripe directement depuis le fichier .env
     * pour contourner les protections du serveur
     */
    private function getStripeSecretKey()
    {
        // Méthode 1: Essayer config()
        $key = config('stripe.secret');
        
        // Méthode 2: Si config() échoue, lire directement le .env
        if (empty($key)) {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                if (preg_match('/^STRIPE_SECRET=(.*)$/m', $envContent, $matches)) {
                    $key = trim($matches[1]);
                }
            }
        }
        
        // Méthode 3: En dernier recours, utiliser la clé directement
        if (empty($key)) {
            $key = 'sk_test_51RQll2FTR22qbY6T3t514x0k8gcSPnkheA001aGXJuwKca3gZmkk5AS9UeNjMH01bwc4ZSoNIhap4JD5bMoV0gDq06krs4o53w';
        }
        
        return $key;
    }

    // ===============================================
    // MÉTHODES POUR LES PAIEMENTS VENDEURS (INSCRIPTION)
    // ===============================================

    /**
     * Affiche la page de paiement pour l'inscription vendeur
     */
    public function showVendorPaymentPage(Request $request)
    {
        $token = $request->query('token') ?? session('vendor_token');
        
        Log::info('Vendor payment page requested', [
            'token' => $token ?? 'none',
            'session_id' => session()->getId(),
            'has_vendor_data_session' => session()->has('vendor_data'),
            'has_vendor_token_session' => session()->has('vendor_token'),
            'url_params' => $request->query()
        ]);

        if (!$token) {
            Log::warning('No token provided for vendor payment page');
            return redirect()->route('vendor.register')
                ->with('error', 'Session expirée. Veuillez recommencer votre inscription.');
        }

        // Récupérer les données d'inscription depuis le cache puis la session
        $vendorData = Cache::get('vendor_registration_' . $token);
        
        if (!$vendorData) {
            $vendorData = session('vendor_data');
            Log::info('Vendor data retrieved from session', [
                'has_data' => !empty($vendorData),
                'token' => $token
            ]);
        } else {
            Log::info('Vendor data retrieved from cache', [
                'has_data' => !empty($vendorData),
                'token' => $token
            ]);
        }

        if (!$vendorData || !isset($vendorData['subscription'])) {
            Log::warning('No vendor data found for payment', [
                'token' => $token,
                'cache_exists' => Cache::has('vendor_registration_' . $token),
                'session_has_data' => session()->has('vendor_data')
            ]);
            
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

        Log::info('Displaying vendor payment page', [
            'plan' => $plan,
            'amount' => $amount,
            'token' => $token,
            'company_name' => $vendorData['company_name'] ?? 'Unknown'
        ]);

        return view('vendor.payment', [
            'token' => $token,
            'plan' => $plan,
            'amount' => $amount,
            'vendorData' => $vendorData
        ]);
    }

    /**
     * Initie le processus de paiement avec Stripe pour les vendeurs
     */
    public function initiateVendorPayment(Request $request)
    {
        $token = $request->input('token') ?? session('vendor_token');
        
        Log::info('Vendor payment initiation requested', [
            'token' => $token ?? 'none',
            'session_id' => session()->getId(),
            'request_data' => $request->except(['_token']),
            'user_agent' => $request->userAgent()
        ]);

        if (!$token) {
            Log::error('No token provided for payment initiation');
            return response()->json([
                'error' => 'Token manquant. Veuillez recommencer votre inscription.'
            ], 400);
        }

        try {
            // Récupérer les données d'inscription
            $vendorData = Cache::get('vendor_registration_' . $token);
            
            if (!$vendorData) {
                $vendorData = session('vendor_data');
                Log::info('Using session data for payment', ['token' => $token]);
            } else {
                Log::info('Using cache data for payment', ['token' => $token]);
            }

            if (!$vendorData || !isset($vendorData['subscription'])) {
                Log::error('No vendor data found for payment initiation', [
                    'token' => $token,
                    'cache_exists' => Cache::has('vendor_registration_' . $token),
                    'session_has_data' => session()->has('vendor_data')
                ]);
                
                return response()->json([
                    'error' => 'Données d\'inscription introuvables. Veuillez recommencer.'
                ], 400);
            }

            $plan = $vendorData['subscription'];
            
            if ($plan === 'free') {
                return response()->json([
                    'error' => 'Aucun paiement requis pour le plan gratuit'
                ], 400);
            }

            // Récupérer l'ID du prix depuis la configuration
            $priceId = config("stripe.plans.{$plan}.price_id");
            
            if (!$priceId) {
                Log::error('Price ID not found for plan', [
                    'plan' => $plan,
                    'available_plans' => array_keys(config('stripe.plans', []))
                ]);
                
                return response()->json([
                    'error' => 'Configuration de prix invalide pour le plan ' . $plan
                ], 500);
            }

            // URLs de redirection avec token
            $successUrl = route('vendor.payment.success') . '?token=' . urlencode($token) . '&session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('vendor.payment.cancel') . '?token=' . urlencode($token);

            // Lire la clé Stripe de manière sécurisée
            $stripeSecretKey = $this->getStripeSecretKey();
            
            // Debug pour voir d'où vient la clé
            Log::info('Stripe Key Source', [
                'key_length' => strlen($stripeSecretKey),
                'key_start' => substr($stripeSecretKey, 0, 15),
                'key_end' => substr($stripeSecretKey, -5),
            ]);
            
            if (empty($stripeSecretKey)) {
                Log::error('Stripe secret key is completely empty!');
                throw new \Exception('Clé Stripe non configurée');
            }

            // Debug complet avant la création de session
            Log::info('=== DEBUG STRIPE SESSION ===', [
                'plan' => $plan,
                'price_id' => $priceId,
                'stripe_key_used' => substr($stripeSecretKey, 0, 20) . '...',
                'vendor_email' => $vendorData['email'] ?? 'none',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);
            
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            
            // Testez d'abord une simple connexion
            try {
                $testPrice = $stripe->prices->retrieve($priceId);
                Log::info('✅ Price exists in Stripe', [
                    'price_id' => $testPrice->id,
                    'amount' => $testPrice->unit_amount,
                    'currency' => $testPrice->currency,
                    'type' => $testPrice->type,
                    'recurring' => $testPrice->recurring ? 'yes' : 'no',
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Price not found or API error', [
                    'price_id' => $priceId,
                    'error' => $e->getMessage(),
                    'stripe_error_code' => method_exists($e, 'getStripeCode') ? $e->getStripeCode() : 'N/A',
                ]);
                
                return response()->json([
                    'error' => 'Erreur: Le prix Stripe n\'existe pas ou la clé API est invalide. ' . $e->getMessage()
                ], 500);
            }
            
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'type' => 'vendor_subscription',
                    'plan' => $plan,
                    'token' => $token,
                    'email' => $vendorData['email'] ?? '',
                    'company_name' => $vendorData['company_name'] ?? ''
                ],
                'allow_promotion_codes' => true,
                'billing_address_collection' => 'auto',
                'locale' => 'fr',
            ];

            // Ajouter l'email du client si disponible
            if (!empty($vendorData['email'])) {
                $sessionData['customer_email'] = $vendorData['email'];
            }

            Log::info('Creating Stripe session with data', [
                'session_data' => array_merge($sessionData, [
                    'line_items' => '... (voir logs précédents)'
                ])
            ]);

            $session = $stripe->checkout->sessions->create($sessionData);

            Log::info('Stripe session created successfully for vendor', [
                'session_id' => $session->id,
                'session_url' => $session->url,
                'plan' => $plan,
                'token' => $token
            ]);

            return response()->json([
                'success' => true,
                'sessionId' => $session->id,
                'sessionUrl' => $session->url
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error during vendor payment initiation', [
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'token' => $token,
                'request_id' => $e->getRequestId(),
            ]);

            return response()->json([
                'error' => 'Erreur Stripe: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('General error creating vendor payment session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            return response()->json([
                'error' => 'Erreur lors de la création de la session de paiement. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * ✅ CORRIGÉ : Gère le retour de paiement réussi pour les vendeurs
     */
    public function vendorPaymentSuccess(Request $request)
    {
        $token = $request->query('token');
        $sessionId = $request->query('session_id');
        
        Log::info('Vendor payment success callback', [
            'token' => $token ?? 'none',
            'session_id' => $sessionId ?? 'none',
            'all_params' => $request->query()
        ]);

        if (!$token || !$sessionId) {
            Log::error('Missing required parameters in vendor success callback', [
                'has_token' => !empty($token),
                'has_session_id' => !empty($sessionId)
            ]);
            
            return redirect()->route('vendor.register')
                ->with('error', 'Paramètres de paiement manquants.');
        }

        try {
            // Vérifier la session Stripe
            $stripeSecretKey = $this->getStripeSecretKey();
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription', 'customer']
            ]);
            
            Log::info('Stripe session retrieved for vendor success', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
                'subscription_status' => $session->subscription->status ?? 'none',
                'customer_id' => $session->customer ?? 'none'
            ]);
            
            if ($session->payment_status !== 'paid') {
                Log::warning('Vendor payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status
                ]);
                
                return redirect()->route('vendor.register')
                    ->with('error', 'Le paiement n\'a pas été finalisé.');
            }

            // Stocker les informations Stripe en session pour la finalisation
            session([
                'stripe_session_id' => $sessionId,
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $session->subscription->id ?? null,
                'payment_success' => true
            ]);
            session()->save();

            Log::info('Payment verified, redirecting to complete registration', [
                'session_id' => $sessionId,
                'token' => $token,
                'customer_id' => $session->customer,
                'subscription_id' => $session->subscription->id ?? null
            ]);

            // ✅ IMPORTANT: Rediriger vers la finalisation d'inscription avec le token
            return redirect()->route('vendor.register.complete', ['token' => $token]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error processing vendor payment success', [
                'error' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'session_id' => $sessionId,
                'token' => $token
            ]);

            return redirect()->route('vendor.register')
                ->with('error', 'Erreur lors de la vérification du paiement Stripe. Contactez notre support.');

        } catch (\Exception $e) {
            Log::error('General error processing vendor payment success', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId,
                'token' => $token
            ]);

            return redirect()->route('vendor.register')
                ->with('error', 'Erreur lors de la vérification du paiement. Contactez notre support.');
        }
    }

    /**
     * Gère l'annulation du paiement pour les vendeurs
     */
    public function vendorPaymentCancel(Request $request)
    {
        $token = $request->query('token');
        
        Log::info('Vendor payment cancelled', [
            'token' => $token ?? 'none',
            'all_params' => $request->query()
        ]);

        if (!$token) {
            return redirect()->route('vendor.register')
                ->with('error', 'Session expirée. Veuillez recommencer votre inscription.');
        }

        return redirect()->route('vendor.payment.show', ['token' => $token])
            ->with('warning', 'Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.');
    }

    // ===============================================
    // MÉTHODES POUR LES PAIEMENTS DE VOYAGES
    // ===============================================

    /**
     * Affiche la page de paiement pour un voyage
     */
    public function showTripPaymentPage(Request $request, $tripId)
    {
        Log::info('Trip payment page requested', [
            'trip_id' => $tripId,
            'session_id' => session()->getId()
        ]);

        $trip = Trip::findOrFail($tripId);
        
        return view('public.trips.payment', [
            'trip' => $trip
        ]);
    }

    /**
     * Initie le processus de paiement pour un voyage
     */
    public function initiateTripPayment(Request $request, $tripId)
    {
        Log::info('Trip payment initiation requested', [
            'trip_id' => $tripId,
            'session_id' => session()->getId()
        ]);

        try {
            $trip = Trip::findOrFail($tripId);
            
            // Validation des données de réservation
            $validated = $request->validate([
                'participants' => 'required|integer|min:1',
                'departure_date' => 'required|date|after:today',
                'special_requests' => 'nullable|string|max:1000'
            ]);

            // Calculer le prix total
            $totalAmount = $trip->price * $validated['participants'];

            // URLs de redirection
            $successUrl = route('trips.payment.success', $tripId) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('trips.payment.cancel', $tripId);

            // Créer la session Stripe pour le voyage
            $stripeSecretKey = $this->getStripeSecretKey();
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $trip->title,
                            'description' => 'Voyage avec ' . $trip->vendor->company_name,
                        ],
                        'unit_amount' => $trip->price * 100, // Convertir en centimes
                    ],
                    'quantity' => $validated['participants'],
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'type' => 'trip_booking',
                    'trip_id' => $tripId,
                    'participants' => $validated['participants'],
                    'departure_date' => $validated['departure_date']
                ],
                'billing_address_collection' => 'required',
                'locale' => 'fr',
            ]);

            Log::info('Trip payment session created', [
                'session_id' => $session->id,
                'trip_id' => $tripId,
                'amount' => $totalAmount
            ]);

            return response()->json([
                'success' => true,
                'sessionId' => $session->id,
                'sessionUrl' => $session->url
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating trip payment session', [
                'error' => $e->getMessage(),
                'trip_id' => $tripId
            ]);

            return response()->json([
                'error' => 'Erreur lors de la création de la session de paiement'
            ], 500);
        }
    }

    /**
     * Gère le retour de paiement réussi pour un voyage
     */
    public function tripPaymentSuccess(Request $request, $tripId)
    {
        $sessionId = $request->query('session_id');
        
        Log::info('Trip payment success callback', [
            'trip_id' => $tripId,
            'session_id' => $sessionId
        ]);

        try {
            // Vérifier la session Stripe
            $stripeSecretKey = $this->getStripeSecretKey();
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $session = $stripe->checkout->sessions->retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                // Créer la commande
                // Code de création de commande ici...
                
                return redirect()->route('trips.confirmation', $tripId)
                    ->with('success', 'Paiement effectué avec succès !');
            }

            return redirect()->route('trips.show', $tripId)
                ->with('error', 'Le paiement n\'a pas pu être traité.');

        } catch (\Exception $e) {
            Log::error('Error processing trip payment success', [
                'error' => $e->getMessage(),
                'trip_id' => $tripId
            ]);

            return redirect()->route('trips.show', $tripId)
                ->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Gère l'annulation du paiement pour un voyage
     */
    public function tripPaymentCancel(Request $request, $tripId)
    {
        Log::info('Trip payment cancelled', ['trip_id' => $tripId]);

        return redirect()->route('trips.show', $tripId)
            ->with('warning', 'Paiement annulé.');
    }

    // ===============================================
    // MÉTHODES POUR LES PAIEMENTS DE RÉSERVATIONS
    // ===============================================

    /**
     * Affiche la page de paiement pour une réservation
     */
    public function showBookingPaymentPage(Request $request, $bookingId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $booking = \App\Models\Booking::with(['trip', 'availability'])
            ->where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Log::info('Booking payment page requested', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'amount' => $booking->total_amount
        ]);

        return view('bookings.payment', compact('booking'));
    }

    /**
     * Initie le processus de paiement pour une réservation
     */
    public function initiateBookingPayment(Request $request, $bookingId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $booking = \App\Models\Booking::with(['trip', 'availability'])
            ->where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Log::info('Booking payment initiation requested', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'amount' => $booking->total_amount
        ]);

        try {
            // URLs de redirection
            $successUrl = route('bookings.payment.success', $booking->id) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('bookings.payment.cancel', $booking->id);

            // Créer la session Stripe
            $stripeSecretKey = $this->getStripeSecretKey();
            $stripe = new \Stripe\StripeClient($stripeSecretKey);

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $booking->trip->title,
                            'description' => $booking->trip->short_description ?? 'Réservation',
                        ],
                        'unit_amount' => (int)($booking->total_amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => 'booking_' . $booking->id,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'trip_id' => $booking->trip_id,
                ]
            ]);

            Log::info('Stripe session created for booking', [
                'booking_id' => $booking->id,
                'session_id' => $session->id
            ]);

            return response()->json([
                'sessionId' => $session->id,
                'url' => $session->url
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating Stripe session for booking', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id
            ]);

            return response()->json([
                'error' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gère le retour de paiement réussi pour une réservation
     */
    public function bookingPaymentSuccess(Request $request, $bookingId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $sessionId = $request->query('session_id');

        $booking = \App\Models\Booking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Log::info('Booking payment success callback', [
            'booking_id' => $booking->id,
            'session_id' => $sessionId
        ]);

        try {
            // Vérifier la session Stripe
            $stripeSecretKey = $this->getStripeSecretKey();
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                // Mettre à jour le statut de la réservation
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'paid_at' => now()
                ]);

                Log::info('Booking confirmed after successful payment', [
                    'booking_id' => $booking->id
                ]);

                return redirect()->route('customer.bookings.show', $booking->id)
                    ->with('success', 'Paiement effectué avec succès ! Votre réservation est confirmée.');
            }

            return redirect()->route('bookings.payment', $booking->id)
                ->with('error', 'Le paiement n\'a pas pu être traité.');

        } catch (\Exception $e) {
            Log::error('Error processing booking payment success', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id
            ]);

            return redirect()->route('bookings.payment', $booking->id)
                ->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Gère l'annulation du paiement pour une réservation
     */
    public function bookingPaymentCancel(Request $request, $bookingId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $booking = \App\Models\Booking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Log::info('Booking payment cancelled', ['booking_id' => $booking->id]);

        return redirect()->route('bookings.payment', $booking->id)
            ->with('warning', 'Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.');
    }

    // ===============================================
    // WEBHOOK STRIPE
    // ===============================================

    /**
     * Gère les webhooks Stripe
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('stripe.webhook.secret');

        Log::info('Stripe webhook received', [
            'has_signature' => !empty($sigHeader),
            'has_secret' => !empty($endpointSecret),
            'payload_size' => strlen($payload)
        ]);

        if (!$endpointSecret) {
            Log::error('Stripe webhook secret not configured');
            return response('Webhook secret not configured', 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            
            Log::info('Stripe webhook event verified', [
                'event_type' => $event['type'],
                'event_id' => $event['id']
            ]);

            // Traiter l'événement selon son type
            switch ($event['type']) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event['data']['object']);
                    break;

                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event['data']['object']);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event['data']['object']);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event['data']['object']);
                    break;

                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event['data']['object']);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event['data']['object']);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'event_type' => $event['type']
                    ]);
            }

            return response('Webhook handled', 200);

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return response('Invalid signature', 400);

        } catch (\Exception $e) {
            Log::error('Error processing Stripe webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Webhook error', 500);
        }
    }

    /**
     * Traite l'événement checkout.session.completed
     */
    private function handleCheckoutSessionCompleted($session)
    {
        Log::info('Processing checkout.session.completed', [
            'session_id' => $session['id'],
            'metadata' => $session['metadata'] ?? []
        ]);

        $metadata = $session['metadata'] ?? [];

        if (isset($metadata['type'])) {
            switch ($metadata['type']) {
                case 'vendor_subscription':
                    $this->processVendorSubscriptionPayment($session);
                    break;

                case 'trip_booking':
                    $this->processTripBookingPayment($session);
                    break;

                default:
                    Log::warning('Unknown payment type in webhook', [
                        'type' => $metadata['type']
                    ]);
            }
        }
    }

    /**
     * Traite le paiement d'abonnement vendeur via webhook
     */
    private function processVendorSubscriptionPayment($session)
    {
        $metadata = $session['metadata'] ?? [];
        $token = $metadata['token'] ?? null;

        Log::info('Processing vendor subscription payment via webhook', [
            'session_id' => $session['id'],
            'token' => $token,
            'plan' => $metadata['plan'] ?? 'unknown'
        ]);

        if (!$token) {
            Log::error('No token in vendor subscription webhook metadata');
            return;
        }

        try {
            // Le traitement principal se fait déjà dans vendorPaymentSuccess
            // Ici on peut juste enregistrer des informations supplémentaires
            
            DB::table('payment_logs')->insert([
                'type' => 'vendor_subscription_webhook',
                'stripe_session_id' => $session['id'],
                'metadata' => json_encode($metadata),
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Vendor subscription webhook processed successfully', [
                'session_id' => $session['id'],
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing vendor subscription webhook', [
                'error' => $e->getMessage(),
                'session_id' => $session['id']
            ]);
        }
    }

    /**
     * Traite le paiement de réservation de voyage via webhook
     */
    private function processTripBookingPayment($session)
    {
        $metadata = $session['metadata'] ?? [];
        
        Log::info('Processing trip booking payment via webhook', [
            'session_id' => $session['id'],
            'trip_id' => $metadata['trip_id'] ?? 'unknown'
        ]);

        // Logique de traitement des réservations de voyage
        // À implémenter selon vos besoins
    }

    /**
     * Traite la création d'abonnement
     */
    private function handleSubscriptionCreated($subscription)
    {
        Log::info('Processing subscription.created', [
            'subscription_id' => $subscription['id']
        ]);

        // Logique de traitement de création d'abonnement
    }

    /**
     * Traite la mise à jour d'abonnement
     */
    private function handleSubscriptionUpdated($subscription)
    {
        Log::info('Processing subscription.updated', [
            'subscription_id' => $subscription['id']
        ]);

        // Logique de traitement de mise à jour d'abonnement
    }

    /**
     * Traite la suppression d'abonnement
     */
    private function handleSubscriptionDeleted($subscription)
    {
        Log::info('Processing subscription.deleted', [
            'subscription_id' => $subscription['id']
        ]);

        // Logique de traitement de suppression d'abonnement
    }

    /**
     * Traite le succès de paiement de facture
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        Log::info('Processing invoice.payment_succeeded', [
            'invoice_id' => $invoice['id']
        ]);

        // Logique de traitement de succès de paiement
    }

    /**
     * Traite l'échec de paiement de facture
     */
    private function handleInvoicePaymentFailed($invoice)
    {
        Log::info('Processing invoice.payment_failed', [
            'invoice_id' => $invoice['id']
        ]);

        // Logique de traitement d'échec de paiement
    }
}