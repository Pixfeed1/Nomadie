<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ClientEmailVerification;
use App\Mail\WelcomeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ClientRegisterController extends Controller
{
    /**
     * Affiche le formulaire d'inscription client
     */
    public function showRegistrationForm()
    {
        Log::debug('ClientRegister: Affichage du formulaire d\'inscription', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return view('auth.register-client');
    }

    /**
     * Traite l'inscription d'un nouveau client
     */
    public function register(Request $request)
    {
        Log::info('ClientRegister: Début du processus d\'inscription', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'has_avatar' => $request->hasFile('avatar'),
            'newsletter' => $request->boolean('newsletter'),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        // Validation des données avec debug
        try {
            Log::debug('ClientRegister: Début de la validation des données', [
                'fields_present' => array_keys($request->all())
            ]);

            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'pseudo' => 'nullable|string|max:255|unique:users,pseudo',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'avatar' => 'nullable|image|mimes:' . implode(',', config('uploads.allowed_extensions.images')) . '|max:' . config('uploads.max_sizes.avatar'),
                'terms' => 'required|accepted',
                'newsletter' => 'nullable|in:on,off,1,0,true,false' // Accepte les valeurs checkbox HTML
            ], [
                'firstname.required' => 'Le prénom est obligatoire.',
                'lastname.required' => 'Le nom est obligatoire.',
                'pseudo.unique' => 'Ce pseudo est déjà utilisé.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'L\'adresse email n\'est pas valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.max' => 'L\'image ne doit pas dépasser 2MB.',
                'terms.accepted' => 'Vous devez accepter les conditions générales.'
            ]);

            Log::debug('ClientRegister: Validation réussie', [
                'validated_fields' => array_keys($validated)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('ClientRegister: Échec de la validation', [
                'errors' => $e->errors(),
                'email' => $request->input('email'),
                'failed_rules' => $e->validator->failed()
            ]);
            throw $e;
        }

        // Transaction pour garantir l'intégrité des données
        DB::beginTransaction();
        
        try {
            // Gérer l'upload de l'avatar avec debug détaillé
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                Log::debug('ClientRegister: Traitement de l\'avatar', [
                    'original_name' => $request->file('avatar')->getClientOriginalName(),
                    'size' => $request->file('avatar')->getSize(),
                    'mime_type' => $request->file('avatar')->getMimeType()
                ]);

                $avatar = $request->file('avatar');
                $filename = 'avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                
                try {
                    $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
                    
                    Log::info('ClientRegister: Avatar uploadé avec succès', [
                        'filename' => $filename,
                        'path' => $avatarPath,
                        'storage_path' => Storage::disk('public')->path($avatarPath)
                    ]);
                } catch (\Exception $e) {
                    Log::error('ClientRegister: Échec de l\'upload de l\'avatar', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            // Générer un token de vérification unique
            $verificationToken = Str::random(64);
            
            Log::debug('ClientRegister: Token de vérification généré', [
                'token_length' => strlen($verificationToken),
                'token_preview' => substr($verificationToken, 0, 10) . '...'
            ]);

            // Préparer les données utilisateur
            $userData = [
                'name' => $validated['firstname'] . ' ' . $validated['lastname'],
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'pseudo' => $validated['pseudo'] ?? $validated['firstname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar' => $avatarPath,
                'role' => 'customer',
                'newsletter' => $request->boolean('newsletter'),
                'email_verification_token' => $verificationToken,
                'email_verified_at' => null
            ];

            Log::debug('ClientRegister: Données utilisateur préparées', [
                'user_data' => array_diff_key($userData, array_flip(['password', 'email_verification_token']))
            ]);

            // Créer l'utilisateur
            $user = User::create($userData);

            Log::info('ClientRegister: Utilisateur créé avec succès', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString()
            ]);

            // Envoyer l'email de vérification avec retry et debug
            $maxRetries = 3;
            $retryCount = 0;
            $emailSent = false;

            while ($retryCount < $maxRetries && !$emailSent) {
                try {
                    Log::debug('ClientRegister: Tentative d\'envoi email de vérification', [
                        'attempt' => $retryCount + 1,
                        'max_attempts' => $maxRetries,
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    Mail::to($user->email)->send(new ClientEmailVerification($user));
                    $emailSent = true;

                    Log::info('ClientRegister: Email de vérification envoyé avec succès', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'attempt' => $retryCount + 1,
                        'verification_url' => route('client.verify.email', ['token' => $verificationToken])
                    ]);

                } catch (\Exception $e) {
                    $retryCount++;
                    
                    Log::warning('ClientRegister: Tentative d\'envoi email échouée', [
                        'attempt' => $retryCount,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'error_class' => get_class($e)
                    ]);

                    if ($retryCount >= $maxRetries) {
                        Log::error('ClientRegister: Échec définitif de l\'envoi de l\'email après ' . $maxRetries . ' tentatives', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        // Rollback et suppression
                        DB::rollBack();
                        
                        // Supprimer l'avatar si uploadé
                        if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                            Storage::disk('public')->delete($avatarPath);
                            Log::debug('ClientRegister: Avatar supprimé suite à l\'échec', [
                                'path' => $avatarPath
                            ]);
                        }

                        return back()
                            ->withInput($request->except(['password', 'password_confirmation']))
                            ->with('error', 'Impossible d\'envoyer l\'email de vérification. Veuillez réessayer.');
                    }

                    // Attendre avant de réessayer
                    sleep(2);
                }
            }

            // Commit de la transaction
            DB::commit();

            Log::info('ClientRegister: Inscription complète (en attente de vérification)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'duration_ms' => round((microtime(true) - LARAVEL_START) * 1000, 2)
            ]);

            // Redirection vers la page de succès
            return redirect()->route('register.success')
                ->with('success', 'Inscription réussie ! Un email de vérification a été envoyé à ' . $user->email)
                ->with('debug_info', config('app.debug') ? [
                    'user_id' => $user->id,
                    'verification_token_preview' => substr($verificationToken, 0, 10) . '...'
                ] : null);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('ClientRegister: Erreur lors de l\'inscription', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Supprimer l'avatar uploadé en cas d'erreur
            if (isset($avatarPath) && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
                Log::debug('ClientRegister: Avatar supprimé suite à l\'erreur', [
                    'path' => $avatarPath
                ]);
            }

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.')
                ->with('debug_error', config('app.debug') ? $e->getMessage() : null);
        }
    }

    /**
     * Vérification de l'email via le token
     */
    public function verifyEmail(Request $request, $token)
    {
        Log::info('ClientRegister: Tentative de vérification d\'email', [
            'token_preview' => substr($token, 0, 10) . '...',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // Rechercher l'utilisateur avec ce token
            Log::debug('ClientRegister: Recherche de l\'utilisateur avec le token');
            
            $user = User::where('email_verification_token', $token)
                       ->whereNull('email_verified_at')
                       ->first();

            if (!$user) {
                Log::warning('ClientRegister: Token de vérification invalide ou utilisateur introuvable', [
                    'token_preview' => substr($token, 0, 10) . '...'
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Lien de vérification invalide ou expiré.');
            }

            Log::debug('ClientRegister: Utilisateur trouvé', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString()
            ]);

            // Vérifier si le token n'est pas trop vieux (48h)
            $tokenAge = $user->created_at->diffInHours(now());
            
            Log::debug('ClientRegister: Vérification de l\'âge du token', [
                'token_age_hours' => $tokenAge,
                'max_age_hours' => 48,
                'is_expired' => $tokenAge > 48
            ]);

            if ($user->created_at->lt(now()->subHours(48))) {
                Log::warning('ClientRegister: Token de vérification expiré', [
                    'user_id' => $user->id,
                    'created_at' => $user->created_at->toDateTimeString(),
                    'token_age_hours' => $tokenAge
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Le lien de vérification a expiré. Veuillez vous réinscrire.');
            }

            // Valider l'email
            $verificationTime = now();
            
            $user->update([
                'email_verified_at' => $verificationTime,
                'email_verification_token' => null
            ]);

            Log::info('ClientRegister: Email vérifié avec succès', [
                'user_id' => $user->id,
                'email' => $user->email,
                'verified_at' => $verificationTime->toDateTimeString(),
                'time_to_verify_minutes' => $user->created_at->diffInMinutes($verificationTime)
            ]);

            // Envoyer l'email de bienvenue
            try {
                Log::debug('ClientRegister: Envoi de l\'email de bienvenue', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                Mail::to($user->email)->send(new WelcomeClient($user));
                
                Log::info('ClientRegister: Email de bienvenue envoyé', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
            } catch (\Exception $e) {
                Log::error('ClientRegister: Erreur lors de l\'envoi de l\'email de bienvenue (non bloquant)', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Ne pas bloquer la vérification si l'email de bienvenue échoue
            }

            // Connecter automatiquement l'utilisateur après vérification
            Auth::login($user);
            
            Log::info('ClientRegister: Utilisateur connecté automatiquement après vérification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_id' => session()->getId()
            ]);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Email vérifié avec succès ! Bienvenue sur ' . config('app.name'))
                ->with('debug_info', config('app.debug') ? [
                    'user_id' => $user->id,
                    'verification_time' => $verificationTime->toDateTimeString()
                ] : null);

        } catch (\Exception $e) {
            Log::error('ClientRegister: Erreur lors de la vérification de l\'email', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'token_preview' => substr($token, 0, 10) . '...',
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('login')
                ->with('error', 'Une erreur est survenue lors de la vérification.')
                ->with('debug_error', config('app.debug') ? $e->getMessage() : null);
        }
    }

    /**
     * Affiche la page de succès après inscription
     */
    public function showSuccessPage()
    {
        Log::debug('ClientRegister: Affichage de la page de succès', [
            'has_success_message' => session()->has('success'),
            'referrer' => request()->headers->get('referer')
        ]);

        return view('auth.register-success');
    }

    /**
     * Renvoyer l'email de vérification
     */
    public function resendVerification(Request $request)
    {
        Log::info('ClientRegister: Demande de renvoi d\'email de vérification', [
            'email' => $request->input('email'),
            'ip' => $request->ip()
        ]);

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)
                   ->whereNull('email_verified_at')
                   ->first();

        if (!$user) {
            Log::warning('ClientRegister: Tentative de renvoi pour un compte inexistant ou déjà vérifié', [
                'email' => $request->email
            ]);
            
            // Message générique pour éviter de révéler l'existence du compte
            return back()->with('info', 'Si un compte non vérifié existe avec cet email, un nouveau lien a été envoyé.');
        }

        // Vérifier le rate limiting (max 3 renvois par heure)
        $recentResends = Cache::get('resend_verification_' . $user->id, 0);
        
        if ($recentResends >= 3) {
            Log::warning('ClientRegister: Limite de renvoi atteinte', [
                'user_id' => $user->id,
                'email' => $user->email,
                'attempts' => $recentResends
            ]);
            
            return back()->with('error', 'Trop de tentatives. Veuillez réessayer dans une heure.');
        }

        // Générer un nouveau token
        $oldToken = $user->email_verification_token;
        $newToken = Str::random(64);
        
        $user->update([
            'email_verification_token' => $newToken
        ]);

        Log::debug('ClientRegister: Nouveau token de vérification généré', [
            'user_id' => $user->id,
            'old_token_preview' => substr($oldToken, 0, 10) . '...',
            'new_token_preview' => substr($newToken, 0, 10) . '...'
        ]);

        // Renvoyer l'email
        try {
            Mail::to($user->email)->send(new ClientEmailVerification($user));
            
            // Incrémenter le compteur de renvoi
            Cache::put('resend_verification_' . $user->id, $recentResends + 1, 3600);
            
            Log::info('ClientRegister: Email de vérification renvoyé avec succès', [
                'user_id' => $user->id,
                'email' => $user->email,
                'resend_count' => $recentResends + 1,
                'verification_url' => route('client.verify.email', ['token' => $newToken])
            ]);
            
            return back()->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
            
        } catch (\Exception $e) {
            Log::error('ClientRegister: Erreur lors du renvoi de l\'email de vérification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.');
        }
    }

    /**
     * Méthode de debug pour vérifier le statut d'un utilisateur (dev only)
     */
    public function debugUserStatus(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }

        $email = $request->input('email');
        
        if (!$email) {
            return response()->json(['error' => 'Email requis'], 400);
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'email_verified' => !is_null($user->email_verified_at),
            'email_verified_at' => $user->email_verified_at,
            'has_verification_token' => !is_null($user->email_verification_token),
            'created_at' => $user->created_at,
            'token_age_hours' => $user->created_at->diffInHours(now()),
            'role' => $user->role,
            'newsletter' => $user->newsletter,
            'has_avatar' => !is_null($user->avatar)
        ]);
    }
}