<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\SeoAnalysis;
use App\Models\Badge;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Statistiques articles
        $articlesStats = [
            'total' => $user->articles()->count(),
            'published' => $user->articles()->where('status', 'published')->count(),
            'draft' => $user->articles()->where('status', 'draft')->count(),
            'total_views' => $user->articles()->sum('views_count'),
        ];
        
        // Statistiques SEO
        $seoStats = [
            'average_score' => $user->seoAnalyses()->avg('global_score') ?? 0,
            'dofollow_status' => $user->is_dofollow,
            'best_score' => $user->seoAnalyses()->max('global_score') ?? 0,
            'articles_above_78' => $user->seoAnalyses()->where('global_score', '>=', 78)->count(),
        ];
        
        // Badges
        $badges = [
            'unlocked' => $user->badges()->wherePivotNotNull('unlocked_at')->count(),
            'total' => Badge::count(),
            'recent' => $user->badges()
                ->wherePivotNotNull('unlocked_at')
                ->orderByPivot('unlocked_at', 'desc')
                ->limit(3)
                ->get(),
            'next' => $this->getNextBadge($user),
        ];
        
        // Articles récents
        $recentArticles = $user->articles()
            ->with('seoAnalysis')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Evolution des scores (30 derniers jours)
        $scoreEvolution = $user->seoAnalyses()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->map(function ($analysis) {
                return [
                    'date' => $analysis->created_at->format('d/m'),
                    'score' => $analysis->global_score,
                ];
            });
        
        // Notifications non lues
        $unreadNotifications = $user->unreadNotifications()->count();
        
        // Progression vers dofollow
        $doFollowProgress = $this->calculateDoFollowProgress($user);
        
        return view('writer.dashboard.index', compact(
            'articlesStats',
            'seoStats',
            'badges',
            'recentArticles',
            'scoreEvolution',
            'unreadNotifications',
            'doFollowProgress'
        ));
    }
    
    private function getNextBadge($user)
    {
        // Logique pour déterminer le prochain badge le plus proche
        $unlockedBadgeIds = $user->badges()->pluck('badges.id');
        
        // Badge "Premier Pas"
        if (!$unlockedBadgeIds->contains(1) && $user->articles()->count() == 0) {
            return Badge::find(1);
        }
        
        // Badge "Contributeur Confirmé"
        if (!$unlockedBadgeIds->contains(2) && $user->articles()->count() < 5) {
            return Badge::find(2);
        }
        
        // Retourner le premier badge non débloqué
        return Badge::whereNotIn('id', $unlockedBadgeIds)->first();
    }
    
    private function calculateDoFollowProgress($user)
    {
        if ($user->is_dofollow) {
            return 100;
        }
        
        $progress = 0;
        $criteria = 4; // Nombre de critères pour dofollow
        
        // Articles publiés (min 3)
        $articles = $user->articles()->where('status', 'published')->count();
        if ($articles >= 3) $progress += 25;
        else $progress += ($articles / 3) * 25;
        
        // Score moyen (min 78)
        $avgScore = $user->seoAnalyses()->avg('global_score') ?? 0;
        if ($avgScore >= 78) $progress += 25;
        else $progress += ($avgScore / 78) * 25;
        
        // Temps de lecture moyen (min 4 minutes)
        $avgReadTime = $user->seoAnalyses()->avg('reading_time') ?? 0;
        if ($avgReadTime >= 4) $progress += 25;
        else $progress += ($avgReadTime / 4) * 25;
        
        // Engagement (commentaires)
        $comments = $user->articles()->sum('comments_count');
        if ($comments >= 6) $progress += 25;
        else $progress += ($comments / 6) * 25;
        
        return min(100, round($progress));
    }
}