<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Badge;
use App\Models\UserBadge;
use Carbon\Carbon;

class CheckDailyStreak
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $today = Carbon::today();
        
        // Récupérer la dernière date de connexion
        $lastLoginDate = $user->last_login_at ? Carbon::parse($user->last_login_at)->startOfDay() : null;
        
        // Si l'utilisateur s'est déjà connecté aujourd'hui, ne rien faire
        if ($lastLoginDate && $lastLoginDate->isSameDay($today)) {
            return;
        }
        
        // Récupérer le streak actuel
        $currentStreak = $user->daily_streak ?? 0;
        
        if ($lastLoginDate) {
            // Vérifier si la dernière connexion était hier
            $yesterday = $today->copy()->subDay();
            
            if ($lastLoginDate->isSameDay($yesterday)) {
                // Connexion consécutive - incrémenter le streak
                $newStreak = $currentStreak + 1;
            } else {
                // Streak cassé - recommencer à 1
                $newStreak = 1;
            }
        } else {
            // Première connexion - commencer le streak
            $newStreak = 1;
        }
        
        // Mettre à jour l'utilisateur
        $user->update([
            'daily_streak' => $newStreak,
            'longest_streak' => max($user->longest_streak ?? 0, $newStreak),
        ]);
        
        // Déclencher la vérification des badges via le Job existant
        \App\Jobs\CheckUserBadges::dispatch($user);
        
        // Log pour debug (optionnel)
        \Log::info('Daily streak updated', [
            'user_id' => $user->id,
            'previous_streak' => $currentStreak,
            'new_streak' => $newStreak,
            'last_login' => $lastLoginDate?->format('Y-m-d'),
            'today' => $today->format('Y-m-d'),
        ]);
    }
}