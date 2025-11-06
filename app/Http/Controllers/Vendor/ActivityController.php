<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Affiche l'historique complet de l'activité du vendeur
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer toutes les activités
        $activities = collect();
        
        // 1. Activités des voyages
        $tripActivities = DB::table('trips')
            ->where('vendor_id', $vendor->id)
            ->select(
                DB::raw("'trip_created' as type"),
                DB::raw("CONCAT('Nouveau voyage créé: ', title) as title"),
                'created_at as date',
                DB::raw("'primary' as color"),
                DB::raw("'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' as icon")
            );
        
        // 2. Activités des réservations
        $bookingActivities = DB::table('bookings')
            ->join('trips', 'bookings.trip_id', '=', 'trips.id')
            ->where('trips.vendor_id', $vendor->id)
            ->select(
                DB::raw("CASE 
                    WHEN bookings.status = 'confirmed' THEN 'booking_confirmed'
                    WHEN bookings.status = 'cancelled' THEN 'booking_cancelled'
                    ELSE 'booking_created'
                END as type"),
                DB::raw("CASE 
                    WHEN bookings.status = 'confirmed' THEN CONCAT('Réservation confirmée pour: ', trips.title)
                    WHEN bookings.status = 'cancelled' THEN CONCAT('Réservation annulée pour: ', trips.title)
                    ELSE CONCAT('Nouvelle réservation pour: ', trips.title)
                END as title"),
                'bookings.created_at as date',
                DB::raw("CASE 
                    WHEN bookings.status = 'confirmed' THEN 'success'
                    WHEN bookings.status = 'cancelled' THEN 'danger'
                    ELSE 'info'
                END as color"),
                DB::raw("'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' as icon")
            );
        
        // 3. Activités des paiements - Correction pour utiliser le système polymorphique
        $paymentActivities = DB::table('payments')
            ->join('bookings', function($join) {
                $join->on('payments.payable_id', '=', 'bookings.id')
                     ->where('payments.payable_type', '=', 'App\\Models\\Booking');
            })
            ->join('trips', 'bookings.trip_id', '=', 'trips.id')
            ->where('trips.vendor_id', $vendor->id)
            ->where('payments.status', 'paid') // Optionnel : filtrer seulement les paiements complétés
            ->select(
                DB::raw("'payment_received' as type"),
                DB::raw("CONCAT('Paiement reçu: ', FORMAT(payments.vendor_amount, 2), '€ pour ', trips.title) as title"),
                'payments.created_at as date',
                DB::raw("'success' as color"),
                DB::raw("'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' as icon")
            );
        
        // 4. Activités des avis
        $reviewActivities = DB::table('reviews')
            ->join('trips', 'reviews.trip_id', '=', 'trips.id')
            ->where('trips.vendor_id', $vendor->id)
            ->select(
                DB::raw("'review_received' as type"),
                DB::raw("CONCAT('Nouvel avis (', reviews.rating, '/5) pour: ', trips.title) as title"),
                'reviews.created_at as date',
                DB::raw("CASE 
                    WHEN reviews.rating >= 4 THEN 'success'
                    WHEN reviews.rating >= 3 THEN 'warning'
                    ELSE 'danger'
                END as color"),
                DB::raw("'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' as icon")
            );
        
        // 5. Activités de connexion
        $loginActivities = DB::table('activity_logs')
            ->where('vendor_id', $vendor->id)
            ->where('type', 'login')
            ->select(
                DB::raw("'login' as type"),
                DB::raw("'Connexion au tableau de bord' as title"),
                'created_at as date',
                DB::raw("'secondary' as color"),
                DB::raw("'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1' as icon")
            );
        
        // Combiner toutes les activités
        $activities = $tripActivities
            ->union($bookingActivities)
            ->union($paymentActivities)
            ->union($reviewActivities)
            ->union($loginActivities)
            ->orderBy('date', 'desc');
        
        // Appliquer les filtres
        if ($request->has('type') && $request->type != 'all') {
            $activities->where('type', $request->type);
        }
        
        if ($request->has('date_from')) {
            $activities->where('date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $activities->where('date', '<=', $request->date_to . ' 23:59:59');
        }
        
        // Paginer les résultats
        $activities = $activities->paginate(20);
        
        // Formatter les dates
        $activities->getCollection()->transform(function ($activity) {
            $activity->date = Carbon::parse($activity->date);
            $activity->description = $this->getActivityDescription($activity->type);
            return $activity;
        });
        
        // Statistiques
        $stats = [
            'total_activities' => $activities->total(),
            'activities_today' => $this->getActivitiesCount($vendor->id, 'today'),
            'activities_week' => $this->getActivitiesCount($vendor->id, 'week'),
            'activities_month' => $this->getActivitiesCount($vendor->id, 'month'),
        ];
        
        return view('vendor.activity.index', compact('activities', 'stats'));
    }
    
    /**
     * Filtre les activités
     */
    public function filter(Request $request)
    {
        return $this->index($request);
    }
    
    /**
     * Export l'historique d'activité
     */
    public function export(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer toutes les activités sans pagination
        $activities = DB::table('trips')
            ->where('vendor_id', $vendor->id)
            ->select(
                DB::raw("'trip_created' as type"),
                DB::raw("CONCAT('Nouveau voyage créé: ', title) as title"),
                'created_at as date'
            )
            ->orderBy('date', 'desc')
            ->get();
        
        // Headers CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activite_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, ['Date', 'Type', 'Description']);
            
            // Données
            foreach ($activities as $activity) {
                fputcsv($file, [
                    Carbon::parse($activity->date)->format('d/m/Y H:i'),
                    $this->getActivityTypeLabel($activity->type),
                    $activity->title
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Retourne la description d'un type d'activité
     */
    private function getActivityDescription($type)
    {
        $descriptions = [
            'trip_created' => 'Un nouveau voyage a été créé',
            'trip_updated' => 'Un voyage a été modifié',
            'trip_deleted' => 'Un voyage a été supprimé',
            'booking_created' => 'Une nouvelle réservation a été reçue',
            'booking_confirmed' => 'Une réservation a été confirmée',
            'booking_cancelled' => 'Une réservation a été annulée',
            'payment_received' => 'Un paiement a été reçu',
            'review_received' => 'Un nouvel avis a été publié',
            'login' => 'Connexion au tableau de bord',
        ];
        
        return $descriptions[$type] ?? 'Activité inconnue';
    }
    
    /**
     * Retourne le label d'un type d'activité
     */
    private function getActivityTypeLabel($type)
    {
        $labels = [
            'trip_created' => 'Voyage créé',
            'trip_updated' => 'Voyage modifié',
            'trip_deleted' => 'Voyage supprimé',
            'booking_created' => 'Nouvelle réservation',
            'booking_confirmed' => 'Réservation confirmée',
            'booking_cancelled' => 'Réservation annulée',
            'payment_received' => 'Paiement reçu',
            'review_received' => 'Nouvel avis',
            'login' => 'Connexion',
        ];
        
        return $labels[$type] ?? 'Autre';
    }
    
    /**
     * Compte les activités pour une période donnée
     */
    private function getActivitiesCount($vendorId, $period)
    {
        $query = DB::table('activity_logs')->where('vendor_id', $vendorId);
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
        }
        
        return $query->count();
    }
}