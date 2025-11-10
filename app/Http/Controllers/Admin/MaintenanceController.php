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
    public function down(Request $request)
    {
        try {
            // Mettre le site en mode maintenance
            // Autoriser l'accès au login et à l'espace admin
            Artisan::call('down', [
                '--refresh' => 15,
                '--render' => 'errors::503',
                '--except' => 'login,admin/*,logout'
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le site est maintenant en mode maintenance.'
                ]);
            }

            return redirect()
                ->route('admin.dashboard.index')
                ->with('success', 'Le site est maintenant en mode maintenance. Les administrateurs peuvent toujours se connecter.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur : ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('admin.dashboard.index')
                ->with('error', 'Erreur lors de l\'activation du mode maintenance : ' . $e->getMessage());
        }
    }

    /**
     * Désactiver le mode maintenance
     */
    public function up(Request $request)
    {
        try {
            // Sortir du mode maintenance
            Artisan::call('up');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le site est maintenant en ligne.'
                ]);
            }

            return redirect()
                ->route('admin.dashboard.index')
                ->with('success', 'Le site est maintenant en ligne et accessible à tous.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur : ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('admin.dashboard.index')
                ->with('error', 'Erreur lors de la désactivation du mode maintenance : ' . $e->getMessage());
        }
    }
}
