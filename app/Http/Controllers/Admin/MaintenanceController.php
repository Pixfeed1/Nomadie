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
            // Générer un secret unique pour bypasser la maintenance
            $secret = config('app.key') ? md5(config('app.key')) : 'admin-secret';

            // Mettre le site en mode maintenance avec secret pour admins
            Artisan::call('down', [
                '--refresh' => 15,
                '--render' => 'errors::503',
                '--secret' => $secret
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le site est maintenant en mode maintenance.',
                    'bypass_url' => url('/' . $secret)
                ]);
            }

            return redirect('/' . $secret)
                ->with('success', 'Le site est maintenant en mode maintenance. Vous pouvez toujours y accéder.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur : ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('admin.dashboard.index')
                ->with('error' , 'Erreur lors de l\'activation du mode maintenance : ' . $e->getMessage());
        }
    }

    /**
     * Désactiver le mode maintenance
     */
    public function up(Request $request)
    {
        try {
            $maintenanceFile = storage_path('framework/down');

            // Vérifier si le fichier de maintenance existe
            if (file_exists($maintenanceFile)) {
                unlink($maintenanceFile);
            }

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
