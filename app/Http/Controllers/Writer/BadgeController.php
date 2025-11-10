<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Jobs\CheckUserBadges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Dashboard des badges
     */
    public function index()
    {
        $user = Auth::user();

        // Les rédacteurs de l'équipe Nomadie voient uniquement leur badge "Team Nomadie"
        if ($user->writer_type === 'team') {
            $badges = Badge::active()->where('code', 'team_nomadie')->get();

            // Pour l'équipe Nomadie : pas de progression, juste leur badge
            $stats = [
                'total_badges' => 1,
                'unlocked_count' => 1,
                'completion_percentage' => 100,
                'next_badge' => null, // Pas de progression
                'featured_badge' => $user->featuredBadge()
            ];

            // Badge team simple sans progression
            $badgesWithProgress = $badges->map(function($badge) {
                return [
                    'badge' => $badge,
                    'is_unlocked' => true,
                    'unlocked_at' => now(), // Toujours débloqué
                    'progress' => 100,
                    'progress_data' => null
                ];
            });
        } else {
            // Autres rédacteurs : tous les badges sauf "Team Nomadie"
            $badges = Badge::active()->where('code', '!=', 'team_nomadie')->ordered()->get();

            // Récupérer les badges débloqués
            $unlockedBadges = $user->badges()->get()->keyBy('id');

            // Récupérer les progressions en cours
            $progressions = $user->userBadges()
                ->whereNull('unlocked_at')
                ->get()
                ->keyBy('badge_id');

            // Statistiques avec progression
            $stats = [
                'total_badges' => $badges->count(),
                'unlocked_count' => $unlockedBadges->count(),
                'completion_percentage' => round(($unlockedBadges->count() / max(1, $badges->count())) * 100),
                'next_badge' => $this->getNextBadge($badges, $unlockedBadges, $progressions),
                'featured_badge' => $user->featuredBadge()
            ];

            // Analyser les badges pour la vue avec progression
            $badgesWithProgress = $badges->map(function($badge) use ($unlockedBadges, $progressions) {
                $isUnlocked = $unlockedBadges->has($badge->id);
                $progression = $progressions->get($badge->id);

                return [
                    'badge' => $badge,
                    'is_unlocked' => $isUnlocked,
                    'unlocked_at' => $isUnlocked ? $unlockedBadges->get($badge->id)->pivot->unlocked_at : null,
                    'progress' => $progression ? $progression->progress_percentage : 0,
                    'progress_data' => $progression ? $progression->progress_data : null
                ];
            });
        }

        return view('writer.badges.index', compact('badgesWithProgress', 'stats'));
    }
    
    /**
     * Mettre en avant un badge
     */
    public function feature($badgeId)
    {
        $user = Auth::user();
        $userBadge = $user->userBadges()
            ->where('badge_id', $badgeId)
            ->whereNotNull('unlocked_at')
            ->firstOrFail();
        
        $userBadge->feature();
        
        return redirect()->back()->with('success', 'Badge mis en avant sur votre profil');
    }
    
    /**
     * Forcer la vérification des badges
     */
    public function check()
    {
        $user = Auth::user();
        CheckUserBadges::dispatch($user);
        
        return redirect()->back()->with('success', 'Vérification des badges en cours...');
    }
    
    /**
     * Déterminer le prochain badge à débloquer
     */
    private function getNextBadge($badges, $unlockedBadges, $progressions)
    {
        $nextBadge = null;
        $highestProgress = 0;
        
        foreach ($badges as $badge) {
            if (!$unlockedBadges->has($badge->id)) {
                $progress = $progressions->get($badge->id);
                $progressPercentage = $progress ? $progress->progress_percentage : 0;
                
                if ($progressPercentage > $highestProgress) {
                    $highestProgress = $progressPercentage;
                    $nextBadge = [
                        'badge' => $badge,
                        'progress' => $progressPercentage
                    ];
                }
            }
        }
        
        return $nextBadge;
    }
}