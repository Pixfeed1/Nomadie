<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\User;
use App\Models\SeoAnalysis;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Observers\ArticleObserver;
use App\Observers\UserObserver;
use App\Observers\SeoAnalysisObserver;
use App\Observers\UserBadgeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Événements d'authentification
        Registered::class => [
            SendEmailVerificationNotification::class,
            \App\Listeners\CreateWelcomeBadge::class,
            \App\Listeners\SendWelcomeNotification::class,
        ],
        
        Login::class => [
            \App\Listeners\UpdateLastLoginDate::class,
            \App\Listeners\CheckDailyStreak::class,
        ],
        
        Verified::class => [
            \App\Listeners\UnlockVerifiedBadge::class,
        ],
        
        // Événements personnalisés pour les articles
        \App\Events\ArticlePublished::class => [
            \App\Listeners\NotifyArticlePublished::class,
            \App\Listeners\CheckArticleBadges::class,
            \App\Listeners\UpdateAuthorStats::class,
        ],
        
        \App\Events\ArticleAnalyzed::class => [
            \App\Listeners\CheckSeoScore::class,
            \App\Listeners\UpdateDoFollowStatus::class,
            \App\Listeners\CheckPerfectScoreBadge::class,
        ],
        
        // Événements pour les badges
        \App\Events\BadgeUnlocked::class => [
            \App\Listeners\SendBadgeNotification::class,
            \App\Listeners\UpdateUserLevel::class,
            \App\Listeners\CheckCascadeBadges::class,
        ],
        
        \App\Events\DoFollowUnlocked::class => [
            \App\Listeners\SendDoFollowNotification::class,
            \App\Listeners\UpdateArticlesDoFollow::class,
        ],
        
        // Événements pour les commentaires
        \App\Events\CommentPosted::class => [
            \App\Listeners\NotifyAuthorOfComment::class,
            \App\Listeners\CheckEngagementBadge::class,
        ],
        
        // Événements pour les statistiques
        \App\Events\MilestoneReached::class => [
            \App\Listeners\SendMilestoneNotification::class,
            \App\Listeners\UnlockMilestoneBadge::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Article::class => [ArticleObserver::class],
        // Vous pouvez ajouter d'autres observers ici si nécessaire
        // User::class => [UserObserver::class],
        // SeoAnalysis::class => [SeoAnalysisObserver::class],
        // UserBadge::class => [UserBadgeObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Enregistrer l'observer pour Article
        Article::observe(ArticleObserver::class);

        // Enregistrer d'autres observers si nécessaire
        // User::observe(UserObserver::class);
        // SeoAnalysis::observe(SeoAnalysisObserver::class);
        // UserBadge::observe(UserBadgeObserver::class);

        // Événements personnalisés supplémentaires
        $this->registerCustomEvents();

        // Événements de queue
        $this->registerQueueEvents();
    }

    /**
     * Enregistrer des événements personnalisés
     *
     * @return void
     */
    protected function registerCustomEvents()
    {
        // Événement déclenché après qu'un utilisateur atteint un certain nombre d'articles
        Event::listen('user.articles.milestone', function ($user, $count) {
            if ($count % 10 === 0) { // Tous les 10 articles
                event(new \App\Events\MilestoneReached($user, 'articles', $count));
            }
        });

        // Événement pour tracker les vues d'articles
        Event::listen('article.viewed', function ($article, $user = null) {
            $article->increment('views_count');
            
            if ($user) {
                // Enregistrer la vue authentifiée
                \App\Models\ArticleView::create([
                    'article_id' => $article->id,
                    'user_id' => $user->id,
                    'viewed_at' => now(),
                ]);
            }
        });

        // Événement pour les scores SEO exceptionnels
        Event::listen('seo.exceptional_score', function ($analysis) {
            if ($analysis->global_score >= 95) {
                $user = $analysis->article->user;
                $user->notify(new \App\Notifications\ExceptionalScore(
                    $analysis->article,
                    $analysis->global_score
                ));
            }
        });

        // Événement pour le déblocage automatique de DoFollow
        Event::listen('user.dofollow.check', function ($user) {
            if (!$user->hasDoFollowLinks()) {
                $eligibleArticles = $user->articles()
                    ->whereHas('latestSeoAnalysis', function ($q) {
                        $q->where('global_score', '>=', 75);
                    })
                    ->count();

                if ($eligibleArticles >= 3) {
                    $user->unlockDoFollow();
                    event(new \App\Events\DoFollowUnlocked($user));
                }
            }
        });
    }

    /**
     * Enregistrer les événements liés aux queues
     *
     * @return void
     */
    protected function registerQueueEvents()
    {
        // Log les jobs qui échouent
        Event::listen(\Illuminate\Queue\Events\JobFailed::class, function ($event) {
            \Log::error('Job failed', [
                'connectionName' => $event->connectionName,
                'job' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });

        // Log les jobs traités avec succès (optionnel, peut être verbeux)
        Event::listen(\Illuminate\Queue\Events\JobProcessed::class, function ($event) {
            if (config('app.debug')) {
                \Log::debug('Job processed', [
                    'connectionName' => $event->connectionName,
                    'job' => $event->job->resolveName(),
                ]);
            }
        });

        // Notification si la queue est bloquée
        Event::listen(\Illuminate\Queue\Events\Looping::class, function ($event) {
            $failedJobs = \DB::table('failed_jobs')->count();
            
            if ($failedJobs > 100) {
                // Envoyer une alerte admin
                \App\Jobs\SendAdminAlert::dispatch(
                    'Queue Alert',
                    "Plus de 100 jobs ont échoué. Vérification requise."
                )->onQueue('critical');
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }

    /**
     * Get the listeners for discoverable events.
     *
     * @return array
     */
    public function discoverEventsWithin()
    {
        return [
            $this->app->path('Listeners'),
        ];
    }
}