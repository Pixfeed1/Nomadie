<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    /**
     * Activer le mode maintenance
     */
    public function down()
    {
        try {
            // Mettre le site en mode maintenance
            // Autoriser l'accès au login et à l'espace admin
            Artisan::call('down', [
                '--refresh' => 15,
                '--render' => 'errors::503',
                '--except' => 'login,admin/*,logout'
            ]);

            return redirect()
                ->route('admin.dashboard.index')
                ->with('success', 'Le site est maintenant en mode maintenance. Les administrateurs peuvent toujours se connecter.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.dashboard.index')
                ->with('error', 'Erreur lors de l\'activation du mode maintenance : ' . $e->getMessage());
        }
    }

    /**
     * Désactiver le mode maintenance
     */
    public function up()
    {
        try {
            // Sortir du mode maintenance
            Artisan::call('up');

            return redirect()
                ->route('admin.dashboard.index')
                ->with('success', 'Le site est maintenant en ligne et accessible à tous.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.dashboard.index')
                ->with('error', 'Erreur lors de la désactivation du mode maintenance : ' . $e->getMessage());
        }
    }
}
