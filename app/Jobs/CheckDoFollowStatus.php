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
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($analyses->count() < 3) {
            return; // Pas assez d'articles (minimum 3 requis)
        }

        // CRITÈRES STRICTS DE RÉCIPROCITÉ (Phase 2)
        $avgScore = $analyses->avg('global_score');
        $avgReadingTime = $analyses->avg('reading_time');

        // Critère 1: Score moyen ≥ 78/100
        $hasGoodScore = $avgScore >= 78;

        // Critère 2: Temps de lecture moyen ≥ 4 minutes
        $hasGoodReadingTime = $avgReadingTime >= 4;

        // Critère 3: Minimum 2 commentaires PAR ARTICLE (pas total)
        $hasEnoughCommentsPerArticle = $this->checkCommentsPerArticle();

        // Critère 4: 80% de réponses aux commentaires
        $hasGoodResponseRate = $this->checkCommentResponseRate();

        // Critère 5: Au moins 1 partage social vérifié par article
        $hasSocialShares = $this->checkSocialShares();

        \Log::info("DoFollow criteria check", [
            'user_id' => $this->user->id,
            'avg_score' => $avgScore,
            'has_good_score' => $hasGoodScore,
            'avg_reading_time' => $avgReadingTime,
            'has_good_reading_time' => $hasGoodReadingTime,
            'has_enough_comments_per_article' => $hasEnoughCommentsPerArticle,
            'has_good_response_rate' => $hasGoodResponseRate,
            'has_social_shares' => $hasSocialShares,
        ]);

        // TOUS les critères doivent être remplis
        if ($hasGoodScore && $hasGoodReadingTime && $hasEnoughCommentsPerArticle && $hasGoodResponseRate && $hasSocialShares) {
            // Passer en dofollow
            $this->user->update(['is_dofollow' => true]);

            // Mettre à jour toutes les analyses
            SeoAnalysis::where('user_id', $this->user->id)
                ->update(['is_dofollow' => true]);

            // Envoyer notification
            $this->user->notify(new DoFollowAchieved($analyses->count()));

            // Vérifier le badge "DoFollow Débloqué"
            $doFollowBadge = \App\Models\Badge::where('code', 'dofollow_debloque')->first();
            if ($doFollowBadge) {
                \App\Jobs\CheckUserBadges::dispatch($this->user, $doFollowBadge);

                \Log::info("DoFollow achieved and badge triggered", [
                    'user_id' => $this->user->id,
                    'avg_score' => $avgScore,
                    'avg_reading_time' => $avgReadingTime,
                ]);
            }
        }
    }

    /**
     * Vérifier que l'utilisateur a au moins 2 commentaires PAR article
     * (sur minimum 3 articles)
     */
    private function checkCommentsPerArticle()
    {
        $articles = $this->user->articles()
            ->where('status', 'published')
            ->withCount(['approvedComments'])
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        if ($articles->count() < 3) {
            return false;
        }

        // Vérifier que CHAQUE article a au moins 2 commentaires
        $articlesWithEnoughComments = $articles->filter(function ($article) {
            return $article->approved_comments_count >= 2;
        })->count();

        // Au moins 3 articles doivent avoir 2+ commentaires chacun
        return $articlesWithEnoughComments >= 3;
    }

    /**
     * Vérifier que l'utilisateur répond à au moins 80% des commentaires
     */
    private function checkCommentResponseRate()
    {
        $articles = $this->user->articles()
            ->where('status', 'published')
            ->get();

        $totalComments = 0;
        $totalResponses = 0;

        foreach ($articles as $article) {
            // Compter tous les commentaires approuvés sur l'article
            $commentsOnArticle = $article->approvedComments()->count();
            $totalComments += $commentsOnArticle;

            // Compter les réponses de l'auteur (commentaires de l'auteur sur son propre article)
            $authorResponses = $article->approvedComments()
                ->where('user_id', $this->user->id)
                ->count();
            $totalResponses += $authorResponses;
        }

        if ($totalComments === 0) {
            return false; // Pas de commentaires = pas de réponses possibles
        }

        $responseRate = ($totalResponses / $totalComments) * 100;

        return $responseRate >= 80;
    }

    /**
     * Vérifier que chaque article a au moins 1 partage social vérifié
     */
    private function checkSocialShares()
    {
        $articles = $this->user->articles()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        if ($articles->count() < 3) {
            return false;
        }

        // Vérifier que CHAQUE article a au moins 1 partage vérifié
        $articlesWithShares = $articles->filter(function ($article) {
            return $article->verifiedShares()->count() >= 1;
        })->count();

        // Au moins 3 articles doivent avoir 1+ partage vérifié
        return $articlesWithShares >= 3;
    }
}