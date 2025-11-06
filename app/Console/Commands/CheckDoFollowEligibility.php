<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\CheckDoFollowStatus;

class CheckDoFollowEligibility extends Command
{
    protected $signature = 'dofollow:check';
    protected $description = 'Vérifie l\'éligibilité des utilisateurs pour le statut DoFollow';

    public function handle()
    {
        $this->info('Vérification de l\'éligibilité DoFollow...');
        
        // Récupérer tous les utilisateurs non-dofollow avec au moins 3 articles
        $users = User::where('is_dofollow', false)
            ->whereHas('articles', function ($query) {
                $query->where('status', 'published');
            }, '>=', 3)
            ->get();
        
        $this->info("Utilisateurs à vérifier : {$users->count()}");
        
        foreach ($users as $user) {
            CheckDoFollowStatus::dispatch($user);
            $this->line("- Vérification lancée pour : {$user->name}");
        }
        
        $this->info('Vérification terminée !');
    }
}