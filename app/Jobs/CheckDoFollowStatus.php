<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\SeoAnalysis;
use App\Notifications\DoFollowAchieved;

class CheckDoFollowStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        // Vérifier si déjà en dofollow
        if ($this->user->is_dofollow ?? false) {
            return;
        }

        // Récupérer les analyses des 5 derniers articles
        $analyses = SeoAnalysis::where('user_id', $this->user->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($analyses->count() < 3) {
            return; // Pas assez d'articles
        }

        // Critères pour passer en dofollow
        $avgScore = $analyses->avg('global_score');
        $avgReadingTime = $analyses->avg('reading_time');
        $totalComments = $this->user->articles()
            ->sum('comments_count');
        
        if ($avgScore >= 78 && $avgReadingTime >= 4 && $totalComments >= 6) {
            // Passer en dofollow
            $this->user->update(['is_dofollow' => true]);

            // Mettre à jour toutes les analyses
            SeoAnalysis::where('user_id', $this->user->id)
                ->update(['is_dofollow' => true]);

            // Envoyer notification
            $this->user->notify(new DoFollowAchieved($analyses->count()));

            // Vérifier le badge "DoFollow Débloqué"
            $doFollowBadge = \App\Models\Badge::where('code', 'dofollow_debloquer')->first();
            if ($doFollowBadge) {
                \App\Jobs\CheckUserBadges::dispatch($this->user, $doFollowBadge);

                \Log::info("DoFollow badge check triggered", [
                    'user_id' => $this->user->id,
                    'avg_score' => $avgScore,
                    'avg_reading_time' => $avgReadingTime,
                    'total_comments' => $totalComments
                ]);
            }
        }
    }
}