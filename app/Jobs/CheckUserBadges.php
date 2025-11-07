<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Badge;
use App\Notifications\BadgeUnlocked;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckUserBadges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $specificBadge;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Badge $specificBadge = null)
    {
        $this->user = $user;
        $this->specificBadge = $specificBadge;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->specificBadge) {
            // Vérifier un badge spécifique
            $this->checkBadge($this->specificBadge);
        } else {
            // Vérifier tous les badges
            $badges = Badge::active()->ordered()->get();
            
            foreach ($badges as $badge) {
                $this->checkBadge($badge);
            }
        }
    }

    /**
     * Vérifier un badge spécifique pour l'utilisateur
     */
    protected function checkBadge(Badge $badge)
    {
        // Si l'utilisateur a déjà le badge, on passe
        if ($this->user->hasBadge($badge->code)) {
            return;
        }

        // Vérifier l'éligibilité
        if ($badge->checkEligibility($this->user)) {
            // Débloquer le badge
            // La notification est envoyée automatiquement par UserBadge::boot() → notifyUser()
            $unlocked = $this->user->unlockBadge($badge);

            if ($unlocked) {
                // Log du déblocage
                \Log::info("Badge unlocked successfully", [
                    'user_id' => $this->user->id,
                    'badge_id' => $badge->id,
                    'badge_code' => $badge->code,
                    'badge_name' => $badge->name
                ]);

                // Si c'est le premier badge, vérifier les badges en cascade
                if ($badge->code === 'premier_pas') {
                    $this->checkCascadeBadges();
                }
            }
        } else {
            // Mettre à jour la progression si le badge n'est pas encore débloqué
            $this->updateProgress($badge);
        }
    }

    /**
     * Mettre à jour la progression vers un badge
     */
    protected function updateProgress(Badge $badge)
    {
        $conditions = $badge->conditions;
        $progress = 0;
        $progressData = [];

        switch ($conditions['type']) {
            case 'articles_published':
                $currentCount = $this->user->articles()
                    ->where('status', 'published')
                    ->whereHas('latestSeoAnalysis', function($q) use ($conditions) {
                        $q->where('global_score', '>=', $conditions['min_score'] ?? 75);
                    })
                    ->count();
                
                $progress = min(100, ($currentCount / $conditions['count']) * 100);
                $progressData = [
                    'current_articles' => $currentCount,
                    'required_articles' => $conditions['count']
                ];
                break;

            case 'consecutive_high_score':
                $recentAnalyses = $this->user->seoAnalyses()
                    ->where('status', 'completed')
                    ->where('global_score', '>=', $conditions['min_score'])
                    ->orderBy('created_at', 'desc')
                    ->limit($conditions['count'])
                    ->count();
                
                $progress = min(100, ($recentAnalyses / $conditions['count']) * 100);
                $progressData = [
                    'current_streak' => $recentAnalyses,
                    'required_streak' => $conditions['count']
                ];
                break;

            case 'social_engagement':
                $totalComments = $this->user->articles()->sum('comments_count');
                $progress = min(100, ($totalComments / $conditions['min_comments']) * 100);
                $progressData = [
                    'current_comments' => $totalComments,
                    'required_comments' => $conditions['min_comments']
                ];
                break;

            case 'total_views':
                $totalViews = $this->user->articles()->sum('views_count');
                $progress = min(100, ($totalViews / $conditions['min_views']) * 100);
                $progressData = [
                    'current_views' => $totalViews,
                    'required_views' => $conditions['min_views']
                ];
                break;

            case 'featured_articles':
                $featuredCount = $this->user->articles()
                    ->where('is_featured', true)
                    ->count();
                
                $progress = min(100, ($featuredCount / $conditions['count']) * 100);
                $progressData = [
                    'current_featured' => $featuredCount,
                    'required_featured' => $conditions['count']
                ];
                break;

            case 'perfect_score':
                $perfectScores = $this->user->seoAnalyses()
                    ->where('status', 'completed')
                    ->where('global_score', '=', 100)
                    ->count();
                
                $progress = min(100, ($perfectScores / $conditions['count']) * 100);
                $progressData = [
                    'current_perfect' => $perfectScores,
                    'required_perfect' => $conditions['count']
                ];
                break;

            case 'daily_streak':
                $currentStreak = $this->user->daily_streak ?? 0;
                $requiredStreak = $conditions['days'] ?? $conditions['count'] ?? 1;
                
                $progress = min(100, ($currentStreak / $requiredStreak) * 100);
                $progressData = [
                    'current_streak' => $currentStreak,
                    'required_streak' => $requiredStreak,
                    'longest_streak' => $this->user->longest_streak ?? 0
                ];
                break;
        }

        // Créer ou mettre à jour la progression
        $userBadge = $this->user->userBadges()
            ->firstOrCreate(
                ['badge_id' => $badge->id],
                ['user_id' => $this->user->id]
            );
        
        $userBadge->updateProgress($progressData, $progress);

        // Vérifier si la progression a atteint 100% pour envoyer une notification de "badge proche"
        if ($progress >= 80 && $progress < 100) {
            $this->notifyBadgeNearCompletion($badge, $progress);
        }
    }

    /**
     * Vérifier les badges qui peuvent être débloqués en cascade
     */
    protected function checkCascadeBadges()
    {
        // Vérifier DoFollow après le premier article
        $doFollowBadge = Badge::where('code', 'dofollow_debloque')->first();
        if ($doFollowBadge) {
            $this->checkBadge($doFollowBadge);
        }
        
        // Vérifier Pionnier si dans les 100 premiers
        $pionnierBadge = Badge::where('code', 'pionnier_nomadie')->first();
        if ($pionnierBadge) {
            $this->checkBadge($pionnierBadge);
        }

        // Vérifier d'autres badges potentiels
        $cascadeBadges = [
            'explorateur_numerique',
            'contributeur_actif',
            'expert_seo'
        ];

        foreach ($cascadeBadges as $badgeCode) {
            $badge = Badge::where('code', $badgeCode)->first();
            if ($badge) {
                $this->checkBadge($badge);
            }
        }
    }

    /**
     * Notifier l'utilisateur qu'un badge est proche d'être débloqué
     */
    protected function notifyBadgeNearCompletion(Badge $badge, float $progress)
    {
        // Optionnel : Créer une notification pour informer que le badge est proche
        // Vous pouvez créer une nouvelle notification BadgeNearCompletion si souhaité
        \Log::info("Badge proche du déblocage : {$badge->name} ({$progress}%) pour l'utilisateur {$this->user->id}");
    }
}