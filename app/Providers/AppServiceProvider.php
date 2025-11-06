<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use App\Observers\CountryVendorObserver;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Configuration Stripe avec gestion d'erreurs
        $this->app->singleton('stripe', function ($app) {
            $secretKey = config('stripe.secret');
            
            if (empty($secretKey)) {
                Log::warning('Stripe secret key not configured in environment');
                throw new \Exception('Stripe secret key not configured. Please check your .env file.');
            }
            
            try {
                $stripe = new StripeClient($secretKey);
                
                Log::info('Stripe client initialized successfully', [
                    'environment' => app()->environment(),
                    'key_prefix' => substr($secretKey, 0, 7) . '...'
                ]);
                
                return $stripe;
                
            } catch (\Exception $e) {
                Log::error('Failed to initialize Stripe client', [
                    'error' => $e->getMessage(),
                    'environment' => app()->environment()
                ]);
                
                throw new \Exception('Failed to initialize Stripe client: ' . $e->getMessage());
            }
        });
        
        // Enregistrement conditionnel pour l'environnement de développement
        if ($this->app->environment('local', 'testing')) {
            // Services de développement uniquement
            $this->registerDevelopmentServices();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuration de la locale française pour Carbon (dates en français)
        Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
        
        // Configuration de base de données
        $this->configureDatabaseSettings();
        
        // Configuration Stripe globale
        $this->configureStripeGlobally();
        
        // Configuration HTTPS en production
        $this->configureHttpsInProduction();
        
        // Configuration des URLs personnalisées
        $this->configureCustomUrls();
        
        // Observer pour la synchronisation automatique des villes
        $this->registerObservers();
        
        // Logs de démarrage
        $this->logApplicationBootstrap();
    }
    
    /**
     * Enregistrer les observers
     */
    private function registerObservers(): void
    {
        // Observer pour la synchronisation automatique des villes
        // Quand un vendor ajoute un pays, synchroniser les villes
        DB::listen(function ($query) {
            if (strpos($query->sql, 'insert into `country_vendor`') !== false) {
                try {
                    // Attendre un instant pour s'assurer que la transaction est complète
                    usleep(100000); // 0.1 seconde
                    
                    // Récupérer le dernier enregistrement inséré
                    $lastInsert = DB::table('country_vendor')
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($lastInsert) {
                        $observer = new CountryVendorObserver();
                        $observer->created($lastInsert);
                        
                        Log::info('CountryVendor observer triggered', [
                            'vendor_id' => $lastInsert->vendor_id ?? null,
                            'country_id' => $lastInsert->country_id ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error in CountryVendor observer', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        });
        
        Log::info('Database observers registered successfully');
    }
    
    /**
     * Configuration des paramètres de base de données
     */
    private function configureDatabaseSettings(): void
    {
        try {
            // Définir la longueur par défaut des chaînes pour MySQL
            Schema::defaultStringLength(191);
            
            // Configuration spécifique pour MySQL
            if (config('database.default') === 'mysql') {
                // Optimisations MySQL si nécessaire
                Log::debug('MySQL database configuration applied');
            }
            
        } catch (\Exception $e) {
            Log::error('Error configuring database settings', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Configuration globale de Stripe
     */
    private function configureStripeGlobally(): void
    {
        try {
            $secretKey = config('stripe.secret');
            
            if (!empty($secretKey)) {
                // Définir la clé API Stripe globalement
                \Stripe\Stripe::setApiKey($secretKey);
                
                // Définir la version de l'API Stripe
                \Stripe\Stripe::setApiVersion('2023-10-16');
                
                // Définir l'agent utilisateur
                \Stripe\Stripe::setAppInfo(
                    config('app.name', 'Laravel Application'),
                    config('app.version', '1.0.0'),
                    config('app.url')
                );
                
                // Configuration du timeout
                \Stripe\Stripe::setMaxNetworkRetries(3);
                
                Log::info('Stripe global configuration applied successfully', [
                    'api_version' => '2023-10-16',
                    'app_name' => config('app.name'),
                    'environment' => app()->environment()
                ]);
                
            } else {
                Log::warning('Stripe configuration skipped - no secret key found', [
                    'environment' => app()->environment()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error configuring Stripe globally', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Configuration HTTPS en production
     */
    private function configureHttpsInProduction(): void
    {
        if ($this->app->environment('production')) {
            // Forcer HTTPS en production
            URL::forceScheme('https');
            
            // Configuration des en-têtes de sécurité
            $this->configureSecurity();
            
            Log::info('Production HTTPS configuration applied');
        }
    }
    
    /**
     * Configuration de sécurité pour la production
     */
    private function configureSecurity(): void
    {
        // Configuration des cookies sécurisés
        config([
            'session.secure' => true,
            'session.http_only' => true,
            'session.same_site' => 'lax'
        ]);
        
        Log::debug('Security configuration applied for production');
    }
    
    /**
     * Configuration des URLs personnalisées
     */
    private function configureCustomUrls(): void
    {
        // Configuration spécifique selon l'environnement
        if ($this->app->environment('production')) {
            // URLs de production
            $domain = config('app.url');
            if ($domain) {
                config(['app.asset_url' => $domain]);
            }
        }
        
        // Configuration des URLs de webhook Stripe
        $this->configureWebhookUrls();
    }
    
    /**
     * Configuration des URLs de webhook
     */
    private function configureWebhookUrls(): void
    {
        try {
            $baseUrl = config('app.url');
            
            if ($baseUrl) {
                $webhookUrl = rtrim($baseUrl, '/') . '/webhook/stripe';
                
                config(['stripe.webhook_url' => $webhookUrl]);
                
                Log::debug('Webhook URL configured', [
                    'webhook_url' => $webhookUrl
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error configuring webhook URLs', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Enregistrement des services de développement
     */
    private function registerDevelopmentServices(): void
    {
        // Services uniquement pour le développement
        Log::debug('Development services registered');
        
        // Désactiver certaines vérifications en développement
        if ($this->app->environment('local')) {
            // Configuration de développement spécifique
            config(['stripe.verify_webhook_signatures' => false]);
        }
    }
    
    /**
     * Logs de démarrage de l'application
     */
    private function logApplicationBootstrap(): void
    {
        Log::info('Application bootstrapped successfully', [
            'environment' => app()->environment(),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'locale' => config('app.locale'),
            'carbon_locale' => Carbon::getLocale(),
            'stripe_configured' => !empty(config('stripe.secret')),
            'stripe_webhook_configured' => !empty(config('stripe.webhook.secret')),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'mail_driver' => config('mail.default'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'observers_registered' => true
        ]);
        
        // Vérification des configurations critiques
        $this->verifyConfigurations();
    }
    
    /**
     * Vérification des configurations critiques
     */
    private function verifyConfigurations(): void
    {
        $missingConfigs = [];
        
        // Vérifications essentielles
        if (empty(config('app.key'))) {
            $missingConfigs[] = 'APP_KEY';
        }
        
        if (empty(config('app.url'))) {
            $missingConfigs[] = 'APP_URL';
        }
        
        if (empty(config('database.connections.' . config('database.default') . '.database'))) {
            $missingConfigs[] = 'Database configuration';
        }
        
        // Vérifications Stripe pour la production
        if ($this->app->environment('production')) {
            if (empty(config('stripe.secret'))) {
                $missingConfigs[] = 'STRIPE_SECRET';
            }
            
            if (empty(config('stripe.key'))) {
                $missingConfigs[] = 'STRIPE_KEY';
            }
            
            if (empty(config('stripe.webhook.secret'))) {
                $missingConfigs[] = 'STRIPE_WEBHOOK_SECRET';
            }
        }
        
        // Vérifications email
        if ($this->app->environment('production') && empty(config('mail.from.address'))) {
            $missingConfigs[] = 'MAIL_FROM_ADDRESS';
        }
        
        if (!empty($missingConfigs)) {
            Log::warning('Missing critical configurations', [
                'missing_configs' => $missingConfigs,
                'environment' => app()->environment()
            ]);
            
            if ($this->app->environment('production')) {
                // En production, c'est critique
                Log::critical('Critical configurations missing in production', [
                    'missing_configs' => $missingConfigs
                ]);
            }
        } else {
            Log::info('All critical configurations verified successfully');
        }
    }
    
    /**
     * Méthode pour vérifier la santé de Stripe
     */
    public function checkStripeHealth(): array
    {
        try {
            $stripe = app('stripe');
            
            // Test simple : récupérer la balance
            $balance = $stripe->balance->retrieve();
            
            return [
                'status' => 'healthy',
                'api_version' => \Stripe\Stripe::getApiVersion(),
                'available' => $balance->available,
                'pending' => $balance->pending,
                'timestamp' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('Stripe health check failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }
}