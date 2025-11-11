<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Trip;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Message;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques globales
        $stats = $this->getAdminStats();
        
        // Derniers vendeurs inscrits
        $recentVendors = Vendor::with('user')
            ->latest()
            ->take(5)
            ->get();
            
        // Derniers voyages créés
        $recentTrips = Trip::with(['vendor', 'destination'])
            ->latest()
            ->take(5)
            ->get();
            
        // Données pour les graphiques
        $chartData = $this->getChartData();

        // Activité récente
        $recentActivity = $this->getRecentActivity();

        // Destinations populaires
        $popularDestinations = $this->getPopularDestinations();

        // Messages récents
        $recentMessages = $this->getRecentMessages();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentVendors',
            'recentTrips',
            'chartData',
            'recentActivity',
            'popularDestinations',
            'recentMessages'
        ));
    }

    /**
     * Obtenir les statistiques admin
     *
     * @return array
     */
    private function getAdminStats(): array
    {
        // Compter les utilisateurs
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Compter les vendeurs
        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('status', 'active')->count();
        $pendingVendors = Vendor::where('status', 'pending')->count();
        
        // Compter les voyages
        $totalTrips = Trip::count();
        $activeTrips = Trip::where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->where('start_date', '>', now());
            })
            ->count();
        
        // Revenus (à adapter selon votre modèle)
        $totalRevenue = Payment::where('status', 'completed')->sum('amount') / 100; // Convertir centimes en euros
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount') / 100;
        
        // Abonnements actifs
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // Répartition par plan
        $planDistribution = Vendor::groupBy('subscription_plan')
            ->selectRaw('subscription_plan, count(*) as count')
            ->get()
            ->pluck('count', 'subscription_plan')
            ->toArray();

        return [
            'total_users' => $totalUsers,
            'new_users_month' => $newUsersThisMonth,
            'total_vendors' => $totalVendors,
            'active_vendors' => $activeVendors,
            'pending_vendors' => $pendingVendors,
            'total_trips' => $totalTrips,
            'active_trips' => $activeTrips,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'active_subscriptions' => $activeSubscriptions,
            'plan_distribution' => $planDistribution,
            // Ajout des clés manquantes pour compatibilité avec la vue
            'total_bookings' => 0, // À implémenter
            'avg_rating' => 0,
            'commission_rate' => 10, // Taux moyen
            'subscription_plan' => 'admin',
            'max_trips' => 9999,
            'trips_remaining' => 9999,
            'can_create_trips' => true,
            'destinations_used' => 0,
            'max_destinations' => 9999,
            'completion_percentage' => 100,
            'subscription_info' => [
                'plan' => 'admin',
                'status' => 'active',
                'next_billing_date' => null,
                'amount' => 0,
                'plan_name' => 'Admin'
            ]
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     *
     * @return array
     */
    private function getChartData(): array
    {
        $monthlyStats = collect();
        
        // Statistiques des 6 derniers mois
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            // Nouveaux vendeurs
            $newVendors = Vendor::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            // Nouveaux voyages
            $newTrips = Trip::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            // Revenus du mois
            $revenue = Payment::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount') / 100;
            
            $monthlyStats->push([
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'new_vendors' => $newVendors,
                'new_trips' => $newTrips,
                'revenue' => $revenue,
                'bookings' => rand(10, 50) // À remplacer par vraies données
            ]);
        }
        
        return [
            'monthly_stats' => $monthlyStats,
            'total_months' => 6
        ];
    }

    /**
     * Obtenir l'activité récente
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivity()
    {
        $activities = collect();
        
        // Derniers vendeurs inscrits
        $recentVendors = Vendor::with('user')
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($recentVendors as $vendor) {
            $activities->push([
                'type' => 'vendor_registered',
                'title' => 'Nouveau vendeur inscrit',
                'description' => $vendor->company_name,
                'date' => $vendor->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => 'text-success'
            ]);
        }
        
        // Derniers voyages créés
        $recentTrips = Trip::with('vendor')
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($recentTrips as $trip) {
            $activities->push([
                'type' => 'trip_created',
                'title' => 'Nouveau voyage créé',
                'description' => $trip->title . ' par ' . ($trip->vendor->company_name ?? 'N/A'),
                'date' => $trip->created_at,
                'icon' => 'fas fa-map-marked-alt',
                'color' => 'text-primary'
            ]);
        }
        
        // Trier par date et limiter
        return $activities->sortByDesc('date')->take(5);
    }

    /**
     * Obtenir les destinations populaires
     *
     * @return \Illuminate\Support\Collection
     */
    private function getPopularDestinations()
    {
        // Récupérer les destinations les plus réservées ce mois
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $destinations = DB::table('trips')
            ->join('bookings', 'trips.id', '=', 'bookings.trip_id')
            ->join('destinations', 'trips.destination_id', '=', 'destinations.id')
            ->whereMonth('bookings.created_at', $currentMonth)
            ->whereYear('bookings.created_at', $currentYear)
            ->select(
                'destinations.name',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue')
            )
            ->groupBy('destinations.id', 'destinations.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Calculer le max pour les barres de progression
        $maxRevenue = $destinations->max('total_revenue') ?: 1;

        return $destinations->map(function ($dest) use ($maxRevenue) {
            $dest->percentage = round(($dest->total_revenue / $maxRevenue) * 100);
            $dest->revenue_formatted = number_format($dest->total_revenue, 0, ',', ' ') . ' €';
            return $dest;
        });
    }

    /**
     * Obtenir les messages récents entre clients et vendeurs
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecentMessages()
    {
        return Message::with(['sender', 'recipient', 'trip'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_name' => $message->sender->name ?? 'Utilisateur supprimé',
                    'sender_type' => $message->sender_type,
                    'recipient_name' => $message->recipient->name ?? 'Utilisateur supprimé',
                    'recipient_type' => $message->recipient_type,
                    'subject' => $message->subject,
                    'content_preview' => \Str::limit($message->content, 80),
                    'trip_title' => $message->trip->title ?? null,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'time_ago' => $message->created_at->diffForHumans(),
                ];
            });
    }
}