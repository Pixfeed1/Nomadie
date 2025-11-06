<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Les commandes Artisan fournies par votre application.
     *
     * @var array
     */
    protected $commands = [
        // Liste vos commandes personnalisées ici si besoin
        // \App\Console\Commands\CheckDoFollowCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ============================================
        // VÉRIFICATION DOFOLLOW
        // ============================================
        
        // Vérification quotidienne du statut DoFollow à 2h du matin
        $schedule->command('dofollow:check')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                Log::info('Vérification DoFollow complétée avec succès');
            })
            ->onFailure(function () {
                Log::error('Erreur lors de la vérification DoFollow');
            })
            ->appendOutputTo(storage_path('logs/dofollow-check.log'));

        // ============================================
        // VÉRIFICATION DES BADGES
        // ============================================
        
        // Vérification des badges pour tous les utilisateurs actifs
        $schedule->call(function () {
            $startTime = now();
            $processedUsers = 0;
            
            try {
                \App\Models\User::where('is_active', true)
                    ->chunk(100, function ($users) use (&$processedUsers) {
                        foreach ($users as $user) {
                            \App\Jobs\CheckUserBadges::dispatch($user)
                                ->onQueue('badges');
                            $processedUsers++;
                        }
                    });
                
                Log::info("Vérification des badges lancée pour {$processedUsers} utilisateurs", [
                    'duration' => now()->diffInSeconds($startTime) . ' secondes'
                ]);
                
            } catch (\Exception $e) {
                Log::error('Erreur lors du dispatch des jobs de vérification des badges', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })
        ->name('badges:check-all')
        ->dailyAt('20:00')
        ->withoutOverlapping()
        ->onOneServer();

        // ============================================
        // NETTOYAGE ET MAINTENANCE
        // ============================================
        
        // Nettoyer les notifications anciennes (plus de 30 jours)
        $schedule->call(function () {
            $deleted = \DB::table('notifications')
                ->where('created_at', '<', now()->subDays(30))
                ->whereNotNull('read_at')
                ->delete();
                
            Log::info("Notifications anciennes supprimées : {$deleted}");
        })
        ->weekly()
        ->sundays()
        ->at('03:00');

        // Nettoyer les analyses SEO obsolètes (garder les 10 dernières par article)
        $schedule->call(function () {
            \App\Models\Article::chunk(100, function ($articles) {
                foreach ($articles as $article) {
                    $article->seoAnalyses()
                        ->orderBy('created_at', 'desc')
                        ->skip(10)
                        ->take(PHP_INT_MAX)
                        ->delete();
                }
            });
            
            Log::info('Nettoyage des analyses SEO obsolètes effectué');
        })
        ->weekly()
        ->mondays()
        ->at('04:00');

        // ============================================
        // RAPPORTS ET STATISTIQUES
        // ============================================
        
        // Générer les rapports hebdomadaires pour les utilisateurs actifs
        $schedule->call(function () {
            \App\Models\User::where('is_active', true)
                ->where('notifications_enabled', true)
                ->chunk(50, function ($users) {
                    foreach ($users as $user) {
                        \App\Jobs\GenerateWeeklyReport::dispatch($user)
                            ->onQueue('reports');
                    }
                });
                
            Log::info('Génération des rapports hebdomadaires lancée');
        })
        ->weekly()
        ->mondays()
        ->at('08:00');

        // Calculer et mettre en cache les statistiques globales
        $schedule->call(function () {
            \App\Jobs\CalculateGlobalStats::dispatch()
                ->onQueue('stats');
        })
        ->hourly()
        ->between('7:00', '23:00');

        // ============================================
        // OPTIMISATION DES PERFORMANCES
        // ============================================
        
        // Optimiser les tables de la base de données
        $schedule->command('db:optimize')
            ->weekly()
            ->sundays()
            ->at('05:00');

        // Nettoyer le cache des vues compilées
        $schedule->command('view:clear')
            ->weekly()
            ->sundays()
            ->at('05:30');

        // Nettoyer les jobs échoués de plus de 7 jours
        $schedule->command('queue:prune-failed --hours=168')
            ->daily()
            ->at('01:00');

        // ============================================
        // MONITORING ET ALERTES
        // ============================================
        
        // Vérifier la santé de l'application
        $schedule->call(function () {
            $checks = [
                'database' => $this->checkDatabase(),
                'storage' => $this->checkStorage(),
                'queue' => $this->checkQueue(),
            ];
            
            $failed = array_filter($checks, fn($check) => !$check);
            
            if (!empty($failed)) {
                Log::critical('Health check failed', $failed);
                // Envoyer une alerte email aux admins
                \App\Jobs\SendAdminAlert::dispatch('Health check failed', $failed);
            }
        })
        ->everyFifteenMinutes()
        ->name('health:check')
        ->withoutOverlapping();

        // ============================================
        // SAUVEGARDES
        // ============================================
        
        // Backup de la base de données (si le package spatie/laravel-backup est installé)
        if (class_exists(\Spatie\Backup\Commands\BackupCommand::class)) {
            $schedule->command('backup:run --only-db')
                ->daily()
                ->at('01:00');
                
            $schedule->command('backup:clean')
                ->daily()
                ->at('01:30');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Vérifier l'état de la base de données
     */
    private function checkDatabase(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Vérifier l'espace de stockage disponible
     */
    private function checkStorage(): bool
    {
        $freeSpace = disk_free_space(storage_path());
        $minSpace = 1073741824; // 1GB en bytes
        
        return $freeSpace > $minSpace;
    }

    /**
     * Vérifier que les queues fonctionnent
     */
    private function checkQueue(): bool
    {
        try {
            $failedJobs = \DB::table('failed_jobs')->count();
            return $failedJobs < 100; // Alerte si plus de 100 jobs échoués
        } catch (\Exception $e) {
            return false;
        }
    }
}