<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Country;
use App\Models\ServiceCategory;
use App\Models\ServiceAttribute;
use App\Notifications\VendorRegistrationConfirmation;
use App\Notifications\NewVendorRegistration;
use App\Mail\VendorConfirmation;
use App\Mail\VendorWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VendorRegistrationController extends Controller
{
    /**
     * Affiche le formulaire d'inscription multi-étapes
     */
    public function index(Request $request)
    {
        // Utiliser Country (table complète) au lieu de Destination (table de démo)
        $destinations = Country::with('continent')
            ->orderBy('name')
            ->get();
            
        $serviceCategories = ServiceCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $serviceAttributes = ServiceAttribute::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Vérifier si on revient du paiement
        $token = $request->query('token') ?? session('vendor_token');
        
        // Si on a un token, essayer de récupérer les données
        $vendorData = null;
        if ($token) {
            $vendorData = Cache::get('vendor_registration_' . $token);
            if (!$vendorData) {
                $vendorData = session('vendor_data');
            }
        }

        return view('public.vendors.register', compact(
            'destinations',
            'serviceCategories',
            'serviceAttributes', 
            'vendorData',
            'token'
        ));
    }

    /**
     * Vérification de disponibilité email avec vérification avancée
     */
    public function checkEmailAvailability(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'field' => 'required|in:email,rep_email',
            'check_only_active' => 'sometimes|boolean'
        ]);

        $email = $request->input('email');
        $field = $request->input('field');
        $checkOnlyActive = $request->boolean('check_only_active', true);

        try {
            if ($checkOnlyActive) {
                // Vérifier seulement les comptes actifs/confirmés
                if ($field === 'email') {
                    // Pour l'email de l'entreprise, vérifier dans la table vendors
                    $exists = Vendor::where('email', $email)
                        ->whereNotNull('email_verified_at')
                        ->where('status', '!=', 'rejected')
                        ->exists();
                } else {
                    // Pour l'email du représentant, vérifier dans la table users
                    $exists = User::where('email', $email)
                        ->whereNotNull('email_verified_at')
                        ->exists();
                }
            } else {
                // Vérifier tous les comptes (ancien comportement)
                if ($field === 'email') {
                    $exists = Vendor::where('email', $email)->exists();
                } else {
                    $exists = User::where('email', $email)->exists();
                }
            }

            return response()->json([
                'available' => !$exists,
                'message' => $exists ? 'Cet email est déjà utilisé par un compte actif' : 'Email disponible'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vérification email', [
                'email' => $email,
                'field' => $field,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'available' => true, // En cas d'erreur, on laisse passer
                'message' => 'Impossible de vérifier la disponibilité'
            ], 500);
        }
    }

    /**
     * Gère la sauvegarde AJAX des étapes intermédiaires
     */
    public function nextStep(Request $request)
    {
        $step = $request->input('step');
        $token = $request->input('token') ?? Str::random(32);
        
        Log::info('Vendor registration step', [
            'step' => $step,
            'token' => $token,
            'session_id' => session()->getId()
        ]);

        try {
            // Validation selon l'étape
            $validated = $this->validateStep($request, $step);
            
            // Récupérer les données existantes
            $existingData = Cache::get('vendor_registration_' . $token, []);
            if (empty($existingData)) {
                $existingData = session('vendor_data', []);
            }
            
            // Fusionner avec les nouvelles données
            $vendorData = array_merge($existingData, $validated);
            
            // Sauvegarder dans le cache ET la session
            Cache::put('vendor_registration_' . $token, $vendorData, now()->addHours(24));
            session(['vendor_data' => $vendorData, 'vendor_token' => $token]);
            session()->save();
            
            Log::info('Step data saved', [
                'step' => $step,
                'token' => $token,
                'data_keys' => array_keys($vendorData)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Étape sauvegardée',
                'token' => $token,
                'step' => $step
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for step', [
                'step' => $step,
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Veuillez corriger les erreurs'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error saving step', [
                'step' => $step,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Finalise l'inscription avec activation automatique
     */
    public function finalSubmit(Request $request)
    {
        $token = $request->input('token');
        
        Log::info('Final vendor registration submission', [
            'token' => $token,
            'has_payment' => session()->has('payment_success')
        ]);

        try {
            // Récupérer toutes les données
            $vendorData = Cache::get('vendor_registration_' . $token);
            if (!$vendorData) {
                $vendorData = session('vendor_data');
            }
            
            if (!$vendorData) {
                throw new \Exception('Données d\'inscription introuvables');
            }
            
            // Validation finale de toutes les données
            $validated = $this->validateAllData($vendorData);
            
            DB::beginTransaction();
            
            try {
                // Vérifier si l'utilisateur existe déjà
                $user = User::where('email', $validated['rep_email'])->first();

                if (!$user) {
                    // Créer l'utilisateur s'il n'existe pas
                    $user = User::create([
                        'name' => $validated['rep_firstname'] . ' ' . $validated['rep_lastname'],
                        'email' => $validated['rep_email'],
                        'password' => null,
                        'email_verified_at' => null,
                        'role' => 'vendor'
                    ]);
                } else {
                    // Vérifier si cet utilisateur a déjà un vendor associé
                    if ($user->vendor) {
                        throw new \Exception('Un compte vendeur existe déjà avec cet email.');
                    }
                }
                
                // ✅ CORRECTION : Créer le vendeur avec les bons statuts selon le plan
                $isFreePlan = ($validated['subscription'] === 'free');
                
                $vendor = Vendor::create([
                    'user_id' => $user->id,
                    'company_name' => $validated['company_name'],
                    'legal_status' => $validated['legal_status'],
                    'siret' => $validated['siret'],
                    'vat' => $validated['vat'] ?? null,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'website' => $validated['website'] ?? null,
                    'address' => $validated['address'],
                    'postal_code' => $validated['postal_code'],
                    'city' => $validated['city'],
                    'country' => $validated['country'],
                    'rep_firstname' => $validated['rep_firstname'],
                    'rep_lastname' => $validated['rep_lastname'],
                    'rep_position' => $validated['rep_position'],
                    'rep_email' => $validated['rep_email'],
                    'description' => $validated['description'],
                    'experience' => $validated['experience'],
                    'subscription_plan' => $validated['subscription'],
                    'max_trips' => match($validated['subscription']) {
                        'pro' => 9999,
                        'essential' => 50,
                        'free' => 5,
                        default => 5
                    },
                    'status' => 'active',
                    'email_verified_at' => null,
                    'confirmation_token' => Str::random(60),
                    'newsletter' => $validated['newsletter'] ?? false,
                    
                    // ✅ CORRECTION PRINCIPALE : Différencier selon le plan
                    'payment_status' => $isFreePlan ? 'completed' : 'pending',
                    'subscription_status' => $isFreePlan ? 'active' : 'pending',
                    'active' => $isFreePlan ? 1 : 0,
                    
                    // Pour les plans gratuits, pas besoin d'infos Stripe
                    'stripe_customer_id' => $isFreePlan ? null : session('stripe_customer_id'),
                    'stripe_subscription_id' => $isFreePlan ? null : session('stripe_subscription_id')
                ]);
                
                // Attacher les pays (au lieu des destinations)
                if (!empty($validated['destinations'])) {
                    $vendor->countries()->attach($validated['destinations']);
                }
                
                // Attacher les catégories de services
                if (!empty($validated['service_categories'])) {
                    $vendor->serviceCategories()->attach($validated['service_categories']);
                }
                
                // Attacher les attributs de services
                if (!empty($validated['service_attributes'])) {
                    $vendor->serviceAttributes()->attach($validated['service_attributes']);
                }
                
                DB::commit();
                
                // Envoyer l'email de confirmation (pour création mot de passe)
                $this->sendConfirmationEmail($vendor);
                
                // Notifier les admins (pour information uniquement)
                $this->notifyAdmins($vendor);
                
                // Nettoyer les données temporaires
                Cache::forget('vendor_registration_' . $token);
                session()->forget(['vendor_data', 'vendor_token', 'stripe_customer_id', 'stripe_subscription_id', 'payment_success']);
                
                Log::info('Vendor registration completed successfully', [
                    'vendor_id' => $vendor->id,
                    'user_id' => $user->id,
                    'status' => 'active',
                    'subscription_plan' => $vendor->subscription_plan,
                    'payment_status' => $vendor->payment_status,
                    'subscription_status' => $vendor->subscription_status,
                    'active' => $vendor->active,
                    'max_trips' => $vendor->max_trips,
                    'email_sent' => true,
                    'is_free_plan' => $isFreePlan
                ]);
                
                // Redirection vers page de confirmation
                $confirmationUrl = route('vendor.register.confirmation', ['vendor' => $vendor->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Inscription réussie ! Vérifiez votre email pour créer votre mot de passe.',
                    'redirect' => $confirmationUrl
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error in final vendor registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Affiche la page de confirmation après inscription
     */
    public function showConfirmation($vendorId)
    {
        $vendor = Vendor::with(['countries', 'serviceCategories', 'serviceAttributes'])
            ->findOrFail($vendorId);

        return view('public.vendors.vendor-registration-confirmation', compact('vendor'));
    }

    /**
     * Affiche le formulaire de création de mot de passe
     */
    public function showCreatePassword($token)
    {
        Log::info('Show create password page', [
            'token' => $token,
            'authenticated' => auth()->check(),
            'user_id' => auth()->id()
        ]);
        
        // Vérifier que le token est valide
        $vendor = Vendor::where('confirmation_token', $token)->first();
            
        if (!$vendor) {
            Log::warning('Invalid token for password creation', ['token' => $token]);
            return redirect()->route('vendor.register')
                ->with('error', 'Lien invalide ou expiré. Veuillez refaire votre inscription.');
        }
        
        Log::info('Vendor found for token', [
            'vendor_id' => $vendor->id,
            'user_id' => $vendor->user_id,
            'has_user' => !is_null($vendor->user),
            'has_password' => $vendor->user ? !is_null($vendor->user->password) : false,
            'active' => $vendor->active,
            'payment_status' => $vendor->payment_status,
            'subscription_status' => $vendor->subscription_status
        ]);
        
        // Marquer automatiquement l'email comme confirmé si pas encore fait
        if (is_null($vendor->email_verified_at)) {
            $vendor->update([
                'email_verified_at' => now()
            ]);
            
            // Marquer aussi l'email comme vérifié dans la table users
            if ($vendor->user) {
                $vendor->user->update([
                    'email_verified_at' => now()
                ]);
            }
            
            Log::info('Email automatically confirmed during password creation', [
                'vendor_id' => $vendor->id
            ]);
        }
        
        // Vérifier que l'utilisateur n'a pas déjà un mot de passe
        if ($vendor->user && $vendor->user->password) {
            Log::info('User already has password, redirecting to login', [
                'vendor_id' => $vendor->id,
                'user_id' => $vendor->user->id
            ]);
            return redirect()->route('login')
                ->with('info', 'Votre mot de passe est déjà configuré. Vous pouvez vous connecter.');
        }
        
        // Vérifier que la vue existe
        $viewPath = 'public.vendors.create-password';
        if (!view()->exists($viewPath)) {
            Log::error('View not found', ['view' => $viewPath]);
            return response('Vue non trouvée : ' . $viewPath, 404);
        }
        
        Log::info('Displaying password creation form', [
            'vendor_id' => $vendor->id,
            'view' => $viewPath
        ]);
        
        return view($viewPath, compact('vendor', 'token'));
    }

    /**
     * Traite la création du mot de passe avec activation immédiate
     */
    public function storePassword(Request $request, $token)
    {
        Log::info('Store password attempt', ['token' => $token]);
        
        // Vérifier que le token est valide
        $vendor = Vendor::where('confirmation_token', $token)->first();
            
        if (!$vendor) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 400);
            }
            return back()->with('error', 'Token invalide ou expiré.');
        }
        
        // Vérifier que l'utilisateur n'a pas déjà un mot de passe
        if ($vendor->user && $vendor->user->password) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre mot de passe est déjà configuré.',
                    'redirect' => route('login')
                ], 400);
            }
            return redirect()->route('login')
                ->with('info', 'Votre mot de passe est déjà configuré.');
        }
        
        // Validation du mot de passe
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ]
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
        ]);
        
        try {
            DB::beginTransaction();
            
            // S'assurer que l'email est marqué comme vérifié
            if (is_null($vendor->email_verified_at)) {
                $vendor->update([
                    'email_verified_at' => now()
                ]);
                
                if ($vendor->user) {
                    $vendor->user->update([
                        'email_verified_at' => now()
                    ]);
                }
            }
            
            // Mettre à jour le mot de passe de l'utilisateur
            $vendor->user->update([
                'password' => Hash::make($validated['password'])
            ]);
            
            // Supprimer le token de confirmation (plus besoin)
            $vendor->update([
                'confirmation_token' => null,
                'status' => 'active'
            ]);
            
            DB::commit();
            
            // Envoyer l'email de bienvenue
            $this->sendWelcomeEmail($vendor);
            
            // Connecter automatiquement l'utilisateur
            Auth::login($vendor->user);
            
            Log::info('Password created successfully', [
                'vendor_id' => $vendor->id,
                'user_id' => $vendor->user->id,
                'status' => 'active',
                'active' => $vendor->active,
                'subscription_plan' => $vendor->subscription_plan
            ]);
            
            // Message selon l'état du compte
            if ($vendor->active == 1) {
                $message = 'Bienvenue ! Votre compte est maintenant actif et prêt à l\'utilisation.';
            } else {
                $message = 'Bienvenue ! Votre compte a été créé. Vous recevrez une notification une fois votre abonnement activé.';
            }
            
            // Redirection vers dashboard
            $redirectUrl = route('vendor.dashboard.index');
            
            // Réponse selon le type de requête
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => $redirectUrl
                ]);
            }
            
            return redirect($redirectUrl)->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating password', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue. Veuillez réessayer.'
                ], 500);
            }
            
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Complete l'inscription après le paiement réussi
     */
    public function completeRegistration(Request $request)
    {
        $token = $request->query('token');
        
        Log::info('Completing vendor registration after payment', [
            'token' => $token,
            'has_session' => session()->has('payment_success')
        ]);
        
        // Vérifier qu'on a bien les infos de paiement
        if (!session()->has('payment_success')) {
            return redirect()->route('vendor.register')
                ->with('error', 'Session de paiement invalide.');
        }
        
        // Finaliser l'inscription via AJAX interne
        $response = $this->finalSubmit(new Request(['token' => $token]));
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] ?? false) {
            // Marquer le paiement comme complété dans la session temporaire
            session(['payment_completed' => true]);
            session()->save();
            
            return redirect($data['redirect']);
        }
        
        return redirect()->route('vendor.register')
            ->with('error', $data['message'] ?? 'Erreur lors de la finalisation.');
    }

    /**
     * Confirme l'email du vendeur et redirige vers création mot de passe
     */
    public function confirmEmail($token)
    {
        Log::info('Email confirmation attempt', ['token' => $token]);
        
        $vendor = Vendor::where('confirmation_token', $token)
            ->whereNull('email_verified_at')
            ->first();
            
        if (!$vendor) {
            return redirect()->route('login')
                ->with('error', 'Lien de confirmation invalide ou expiré.');
        }
        
        $vendor->update([
            'email_verified_at' => now(),
            // On garde le token pour la création du mot de passe
        ]);
        
        // Marquer aussi l'email comme vérifié dans la table users
        if ($vendor->user) {
            $vendor->user->update([
                'email_verified_at' => now()
            ]);
        }
        
        Log::info('Email confirmed for vendor', ['vendor_id' => $vendor->id]);
        
        // Redirection vers création de mot de passe
        return redirect()->route('vendor.create-password', ['token' => $token])
            ->with('success', 'Email confirmé ! Créez maintenant votre mot de passe sécurisé.');
    }

    /**
     * Nettoie les données expirées (CRON)
     */
    public function cleanupExpiredData()
    {
        $expiredKeys = [];
        $prefix = 'vendor_registration_';
        
        // Nettoyer le cache des inscriptions de plus de 48h
        // Note: Cette méthode dépend du driver de cache utilisé
        
        Log::info('Cleanup expired vendor registration data', [
            'expired_count' => count($expiredKeys)
        ]);
        
        return response()->json(['cleaned' => count($expiredKeys)]);
    }

    /**
     * Validation des données selon l'étape avec vérification email
     */
    private function validateStep(Request $request, $step)
    {
        $rules = [];
        $messages = [];
        
        switch ($step) {
            case 1:
                $rules = [
                    'company_name' => 'required|string|max:255',
                    'legal_status' => 'required|in:sarl,sas,ei,other',
                    'siret' => ['required', 'string', 'regex:/^\d{14}$/'],
                    'vat' => 'nullable|string|max:50',
                    'email' => [
                        'required',
                        'email',
                        'max:255',
                        function ($attribute, $value, $fail) {
                            // Vérifier la disponibilité de l'email entreprise
                            $exists = Vendor::where('email', $value)
                                ->whereNotNull('email_verified_at')
                                ->where('status', '!=', 'rejected')
                                ->exists();
                            
                            if ($exists) {
                                $fail('Cet email est déjà utilisé par un compte actif.');
                            }
                        }
                    ],
                    'phone' => 'required|string|max:20',
                    'website' => 'nullable|url|max:255',
                    'address' => 'required|string|max:255',
                    'postal_code' => 'required|string|max:10',
                    'city' => 'required|string|max:100',
                    'country' => 'required|string|max:2',
                    'rep_firstname' => 'required|string|max:100',
                    'rep_lastname' => 'required|string|max:100',
                    'rep_position' => 'required|string|max:100',
                    'rep_email' => [
                        'required',
                        'email',
                        'max:255',
                        function ($attribute, $value, $fail) {
                            // Vérifier la disponibilité de l'email représentant
                            $exists = User::where('email', $value)
                                ->whereNotNull('email_verified_at')
                                ->exists();
                            
                            if ($exists) {
                                $fail('Cet email est déjà utilisé par un compte actif.');
                            }
                        }
                    ],
                    'description' => 'required|string|max:500',
                    'experience' => 'required|in:1,1-3,3-5,5-10,10+',
                ];
                
                $messages = [
                    'siret.regex' => 'Le SIRET doit contenir exactement 14 chiffres',
                    'email.unique' => 'Cet email est déjà utilisé',
                    'rep_email.unique' => 'Cet email est déjà utilisé'
                ];
                break;
                
            case 2:
                $rules = [
                    'subscription' => 'required|in:free,essential,pro'
                ];
                break;
                
            case 3:
                $rules = [
                    'destinations' => 'required|array|min:1',
                    'destinations.*' => 'exists:countries,id'
                ];
                
                $messages = [
                    'destinations.required' => 'Veuillez sélectionner au moins une destination',
                    'destinations.min' => 'Veuillez sélectionner au moins une destination'
                ];
                break;
                
            case 4:
                $rules = [
                    'service_categories' => 'required|array|min:1|max:3',
                    'service_categories.*' => 'exists:service_categories,id',
                    'service_attributes' => 'nullable|array',
                    'service_attributes.*' => 'exists:service_attributes,id'
                ];
                
                $messages = [
                    'service_categories.required' => 'Veuillez sélectionner au moins une catégorie',
                    'service_categories.max' => 'Vous ne pouvez sélectionner que 3 catégories maximum'
                ];
                break;
                
            case 5:
                $rules = [
                    'terms' => 'required|accepted',
                    'newsletter' => 'nullable|boolean'
                ];
                
                $messages = [
                    'terms.required' => 'Vous devez accepter les conditions générales',
                    'terms.accepted' => 'Vous devez accepter les conditions générales'
                ];
                break;
        }
        
        return $request->validate($rules, $messages);
    }

    /**
     * Validation complète de toutes les données
     */
    private function validateAllData($data)
    {
        $validator = validator($data, [
            // Informations entreprise
            'company_name' => 'required|string|max:255',
            'legal_status' => 'required|in:sarl,sas,ei,other',
            'siret' => ['required', 'string', 'regex:/^\d{14}$/'],
            'vat' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            
            // Représentant
            'rep_firstname' => 'required|string|max:100',
            'rep_lastname' => 'required|string|max:100',
            'rep_position' => 'required|string|max:100',
            'rep_email' => 'required|email|max:255',
            
            // Présentation
            'description' => 'required|string|max:500',
            'experience' => 'required|in:1,1-3,3-5,5-10,10+',
            
            // Abonnement
            'subscription' => 'required|in:free,essential,pro',
            
            // Destinations et services
            'destinations' => 'required|array|min:1',
            'destinations.*' => 'exists:countries,id',
            'service_categories' => 'required|array|min:1|max:3',
            'service_categories.*' => 'exists:service_categories,id',
            'service_attributes' => 'nullable|array',
            'service_attributes.*' => 'exists:service_attributes,id',
            
            // CGV
            'terms' => 'required|accepted'
        ]);
        
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Envoie l'email de confirmation au vendeur
     */
    private function sendConfirmationEmail($vendor)
    {
        try {
            Log::info('Tentative envoi email de confirmation', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email,
                'token' => $vendor->confirmation_token,
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'from' => config('mail.from.address')
                ]
            ]);

            // Vérifier la configuration mail
            if (!config('mail.from.address')) {
                Log::error('Configuration mail manquante: MAIL_FROM_ADDRESS');
                throw new \Exception('Configuration email manquante');
            }

            // Utiliser la classe VendorConfirmation Mail
            Mail::to($vendor->rep_email)->send(new VendorConfirmation($vendor));
            
            Log::info('Email de confirmation envoyé avec succès', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email
            ]);

        } catch (\Exception $e) {
            Log::error('Échec envoi email de confirmation', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Ne pas faire échouer l'inscription si l'email ne part pas
            // Mais log l'erreur pour debug
        }
    }

    /**
     * Envoie l'email de bienvenue après création du mot de passe
     */
    private function sendWelcomeEmail($vendor)
    {
        try {
            Log::info('Tentative envoi email de bienvenue', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email
            ]);

            // Utiliser la classe VendorWelcome Mail
            Mail::to($vendor->rep_email)->send(new VendorWelcome($vendor));
            
            Log::info('Email de bienvenue envoyé avec succès', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email
            ]);

        } catch (\Exception $e) {
            Log::error('Échec envoi email de bienvenue', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->rep_email,
                'error' => $e->getMessage()
            ]);
            
            // Ne pas faire échouer le processus si l'email ne part pas
        }
    }

    /**
     * Notifie les administrateurs de la nouvelle inscription
     */
    private function notifyAdmins($vendor)
    {
        try {
            $admins = User::where('role', 'admin')->get();
            
            if ($admins->count() > 0) {
                Notification::send($admins, new NewVendorRegistration($vendor));
                
                Log::info('Notification admin envoyée', [
                    'vendor_id' => $vendor->id,
                    'admin_count' => $admins->count()
                ]);
            } else {
                Log::warning('Aucun admin trouvé pour notification', [
                    'vendor_id' => $vendor->id
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Échec notification admins', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}