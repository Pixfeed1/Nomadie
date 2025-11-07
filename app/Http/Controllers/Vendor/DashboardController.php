<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Trip;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\TripAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard principal du vendeur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            Log::warning('DashboardController: User has no vendor account', [
                'user_id' => Auth::id()
            ]);

            return redirect()->route('vendor.register')
                ->with('error', 'Vous devez d\'abord vous inscrire en tant qu\'organisateur.');
        }

        // Statistiques principales
        $stats = $this->getVendorStats($vendor);

        // Derniers voyages créés avec leurs disponibilités
        $recentTrips = $vendor->trips()
            ->latest()
            ->take(5)
            ->with(['destination', 'travelType', 'availabilities' => function($q) {
                $q->upcoming()->orderBy('start_date');
            }])
            ->get();

        // Données pour les graphiques (derniers 6 mois)
        $chartData = $this->getChartData($vendor);

        // Activité récente (logs simplifiés)
        $recentActivity = $this->getRecentActivity($vendor);

        Log::info('Dashboard accessed', [
            'vendor_id' => $vendor->id,
            'company_name' => $vendor->company_name,
            'trips_count' => $stats['total_trips'],
            'trips_limit' => $vendor->max_trips
        ]);

        return view('vendor.dashboard.index', compact(
            'vendor',
            'stats',
            'recentTrips',
            'chartData',
            'recentActivity'
        ));
    }

    /**
     * Affiche la page de bienvenue après inscription/paiement
     *
     * @return \Illuminate\View\View
     */
    public function welcome()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // Marquer comme "première visite" si nécessaire
        if (!session('vendor_welcomed')) {
            session(['vendor_welcomed' => true]);

            Log::info('Vendor welcomed for first time', [
                'vendor_id' => $vendor->id,
                'company_name' => $vendor->company_name
            ]);

            return view('vendor.dashboard.welcome', compact('vendor'));
        }

        // Si déjà accueilli, rediriger vers le dashboard
        return redirect()->route('vendor.dashboard.index');
    }

    /**
     * Obtenir les statistiques du vendeur
     *
     * @param Vendor $vendor
     * @return array
     */
    private function getVendorStats(Vendor $vendor): array
    {
        // Statistiques des voyages
        $totalTrips = $vendor->trips()->count();
        
        // Voyages actifs : ceux qui ont au moins une disponibilité future
        $activeTrips = $vendor->trips()
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->upcoming();
            })
            ->count();

        // Voyages sans disponibilités futures (peuvent nécessiter attention)
        $tripsWithoutFutureAvailabilities = $vendor->trips()
            ->where('status', 'active')
            ->whereDoesntHave('availabilities', function($q) {
                $q->upcoming();
            })
            ->count();

        // Total des disponibilités à venir
        $upcomingAvailabilities = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
            ->upcoming()
            ->count();

        // Places disponibles totales
        $totalAvailableSpots = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
            ->upcoming()
            ->sum(DB::raw('total_spots - booked_spots'));

        // Note moyenne des voyages
        $avgRating = $vendor->trips()
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

        // Nombre total de réservations
        $totalBookings = $vendor->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        // Revenus totaux
        $totalRevenue = $vendor->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Informations sur l'abonnement
        $subscriptionInfo = $this->getSubscriptionInfo($vendor);

        // Nombre de destinations utilisées
        $destinationsCount = $vendor->countries()->count();

        // Limites selon le plan d'abonnement
        $maxDestinations = match($vendor->subscription_plan) {
            'free' => 1,
            'essential' => 5,
            'pro' => PHP_INT_MAX, // Illimité
            default => 1
        };

        return [
            'total_trips' => $totalTrips,
            'active_trips' => $activeTrips,
            'trips_without_availabilities' => $tripsWithoutFutureAvailabilities,
            'upcoming_availabilities' => $upcomingAvailabilities,
            'total_available_spots' => $totalAvailableSpots,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'avg_rating' => round($avgRating, 1),
            'commission_rate' => $vendor->commission_rate,
            'subscription_plan' => $vendor->subscription_plan,
            'max_trips' => $vendor->max_trips,
            'trips_remaining' => $vendor->remaining_trips,
            'can_create_trips' => $vendor->canCreateMoreTrips(),
            'subscription_info' => $subscriptionInfo,
            'destinations_count' => $destinationsCount,
            'destinations_used' => $destinationsCount,
            'max_destinations' => $maxDestinations,
            'completion_percentage' => $this->getProfileCompletion($vendor)
        ];
    }

    /**
     * Obtenir les informations d'abonnement
     *
     * @param Vendor $vendor
     * @return array
     */
    private function getSubscriptionInfo(Vendor $vendor): array
    {
        $subscription = Subscription::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'plan' => $vendor->subscription_plan,
                'status' => 'free',
                'next_billing_date' => null,
                'amount' => 0,
                'plan_name' => match($vendor->subscription_plan) {
                    'pro' => 'Pro',
                    'essential' => 'Essentiel',
                    'free' => 'Gratuit',
                    default => 'Gratuit'
                }
            ];
        }

        return [
            'has_subscription' => true,
            'plan' => $subscription->plan,
            'status' => $subscription->status,
            'next_billing_date' => $subscription->current_period_end,
            'amount' => $subscription->amount / 100, // Convertir de centimes en euros
            'stripe_id' => $subscription->stripe_id,
            'plan_name' => match($subscription->plan) {
                'pro' => 'Pro',
                'essential' => 'Essentiel',
                'free' => 'Gratuit',
                default => 'Gratuit'
            }
        ];
    }

    /**
     * Calculer le pourcentage de complétion du profil
     *
     * @param Vendor $vendor
     * @return int
     */
    private function getProfileCompletion(Vendor $vendor): int
    {
        $fields = [
            'logo' => $vendor->logo,
            'description' => $vendor->description,
            'website' => $vendor->website,
            'phone' => $vendor->phone,
        ];

        $completed = 0;
        foreach ($fields as $field => $value) {
            if (!empty($value)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    /**
     * Obtenir les données pour les graphiques
     *
     * @param Vendor $vendor
     * @return array
     */
    private function getChartData(Vendor $vendor): array
    {
        $last6Months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // Compter les voyages créés ce mois-ci
            $tripsCreated = $vendor->trips()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // Compter les disponibilités créées ce mois-ci
            $availabilitiesCreated = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // Compter les réservations ce mois-ci
            $bookingsCount = $vendor->bookings()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'completed'])
                ->count();

            // Revenus du mois
            $revenue = $vendor->bookings()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $last6Months->push([
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'trips_created' => $tripsCreated,
                'availabilities_created' => $availabilitiesCreated,
                'bookings' => $bookingsCount,
                'revenue' => $revenue
            ]);
        }

        return [
            'monthly_stats' => $last6Months,
            'total_months' => 6
        ];
    }

    /**
     * Obtenir l'activité récente
     *
     * @param Vendor $vendor
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivity(Vendor $vendor): \Illuminate\Support\Collection
    {
        $activities = collect();

        // Voyages récemment créés
        $recentTrips = $vendor->trips()
            ->latest()
            ->take(3)
            ->get(['id', 'title', 'slug', 'created_at']);

        foreach ($recentTrips as $trip) {
            $activities->push([
                'type' => 'trip_created',
                'title' => $trip->title,
                'description' => 'Nouveau voyage créé',
                'date' => $trip->created_at,
                'icon' => 'fas fa-map-marked-alt',
                'color' => 'text-primary'
            ]);
        }

        // Disponibilités récemment créées
        $recentAvailabilities = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
            ->with('trip:id,title')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentAvailabilities as $availability) {
            $activities->push([
                'type' => 'availability_created',
                'title' => 'Nouvelle disponibilité',
                'description' => 'Pour ' . $availability->trip->title . ' - Du ' . $availability->start_date->format('d/m/Y'),
                'date' => $availability->created_at,
                'icon' => 'fas fa-calendar-plus',
                'color' => 'text-success'
            ]);
        }

        // Ajouter ici d'autres types d'activités (réservations, avis, etc.)

        // Trier par date décroissante
        return $activities->sortByDesc('date')->take(5);
    }

    /**
     * Afficher les analyses avancées (page séparée)
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // Statistiques détaillées
        $detailedStats = $this->getDetailedStats($vendor);

        // Données pour graphiques avancés (12 mois)
        $yearlyData = $this->getYearlyData($vendor);

        return view('vendor.dashboard.analytics', compact(
            'vendor',
            'detailedStats',
            'yearlyData'
        ));
    }

    /**
     * Obtenir des statistiques détaillées
     *
     * @param Vendor $vendor
     * @return array
     */
    private function getDetailedStats(Vendor $vendor): array
    {
        // Répartition par destination
        $tripsByDestination = $vendor->trips()
            ->with('destination')
            ->get()
            ->groupBy('destination.name')
            ->map(function ($trips) {
                return $trips->count();
            })
            ->sortDesc()
            ->take(5);

        // Répartition par type de voyage
        $tripsByType = $vendor->trips()
            ->with('travelType')
            ->get()
            ->groupBy('travelType.name')
            ->map(function ($trips) {
                return $trips->count();
            })
            ->sortDesc();

        // Statistiques sur les disponibilités
        $availabilityStats = [
            'total_future_availabilities' => TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
                ->upcoming()
                ->count(),
            'guaranteed_departures' => TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
                ->upcoming()
                ->where('is_guaranteed', true)
                ->count(),
            'full_availabilities' => TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
                ->upcoming()
                ->where('status', 'full')
                ->count(),
            'avg_fill_rate' => $this->calculateAverageFillRate($vendor)
        ];

        // Statistiques sur les limites
        $tripLimitStats = [
            'current_trips' => $vendor->trips()->count(),
            'max_trips' => $vendor->max_trips,
            'percentage_used' => $vendor->max_trips > 0
                ? round(($vendor->trips()->count() / $vendor->max_trips) * 100, 1)
                : 0,
            'can_create' => $vendor->canCreateMoreTrips(),
            'remaining' => $vendor->remaining_trips
        ];

        return [
            'trips_by_destination' => $tripsByDestination,
            'trips_by_type' => $tripsByType,
            'avg_trip_price' => $vendor->trips()->avg('price') ?? 0,
            'most_expensive_trip' => $vendor->trips()->max('price') ?? 0,
            'cheapest_trip' => $vendor->trips()->min('price') ?? 0,
            'trip_limit_stats' => $tripLimitStats,
            'availability_stats' => $availabilityStats
        ];
    }

    /**
     * Calculer le taux de remplissage moyen
     *
     * @param Vendor $vendor
     * @return float
     */
    private function calculateAverageFillRate(Vendor $vendor): float
    {
        $availabilities = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
            ->upcoming()
            ->where('total_spots', '>', 0)
            ->get();

        if ($availabilities->isEmpty()) {
            return 0;
        }

        $totalFillRate = 0;
        foreach ($availabilities as $availability) {
            $fillRate = ($availability->booked_spots / $availability->total_spots) * 100;
            $totalFillRate += $fillRate;
        }

        return round($totalFillRate / $availabilities->count(), 1);
    }

    /**
     * Obtenir les données sur 12 mois
     *
     * @param Vendor $vendor
     * @return array
     */
    private function getYearlyData(Vendor $vendor): array
    {
        $last12Months = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $tripsCreated = $vendor->trips()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // Disponibilités qui commencent ce mois-ci
            $availabilitiesStarting = TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))
                ->whereBetween('start_date', [$monthStart, $monthEnd])
                ->count();

            // Réservations du mois
            $bookings = $vendor->bookings()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'completed'])
                ->count();

            // Revenus du mois
            $revenue = $vendor->bookings()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $last12Months->push([
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'year' => $date->format('Y'),
                'trips_created' => $tripsCreated,
                'availabilities_starting' => $availabilitiesStarting,
                'bookings' => $bookings,
                'revenue' => $revenue
            ]);
        }

        return [
            'monthly_data' => $last12Months
        ];
    }

    /**
     * Rapport des ventes
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function salesReport(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // Période par défaut : 30 derniers jours
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Réservations de la période
        $bookings = $vendor->bookings()
            ->with(['trip', 'user', 'availability'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistiques de la période
        $stats = [
            'total_bookings' => $vendor->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'confirmed_bookings' => $vendor->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['confirmed', 'completed'])
                ->count(),
            'cancelled_bookings' => $vendor->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')
                ->count(),
            'total_revenue' => $vendor->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'pending_revenue' => $vendor->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'pending')
                ->sum('total_amount'),
        ];

        return view('vendor.reports.sales', compact('vendor', 'bookings', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Rapport des voyages
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function tripsReport(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // Filtres
        $status = $request->input('status', 'all');

        // Requête de base
        $query = $vendor->trips()->with(['destination', 'travelType', 'availabilities']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $trips = $query->withCount([
            'availabilities as upcoming_availabilities' => function($q) {
                $q->upcoming();
            },
            'bookings as total_bookings' => function($q) {
                $q->whereIn('status', ['confirmed', 'completed']);
            }
        ])->paginate(15);

        // Statistiques globales
        $stats = [
            'total_trips' => $vendor->trips()->count(),
            'active_trips' => $vendor->trips()->where('status', 'active')->count(),
            'draft_trips' => $vendor->trips()->where('status', 'draft')->count(),
            'inactive_trips' => $vendor->trips()->where('status', 'inactive')->count(),
            'avg_price' => $vendor->trips()->avg('price') ?? 0,
            'total_availabilities' => TripAvailability::whereIn('trip_id', $vendor->trips()->pluck('id'))->count(),
        ];

        return view('vendor.reports.trips', compact('vendor', 'trips', 'stats', 'status'));
    }

    /**
     * Exporter le rapport des ventes en CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportSalesReport(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $bookings = $vendor->bookings()
            ->with(['trip', 'user', 'availability'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'ventes_' . $vendor->company_name . '_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Numéro réservation',
                'Date',
                'Voyage',
                'Client',
                'Voyageurs',
                'Montant',
                'Statut',
                'Paiement'
            ]);

            // Données
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->created_at->format('Y-m-d H:i'),
                    $booking->trip->title ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    $booking->number_of_travelers,
                    number_format($booking->total_amount, 2) . '€',
                    $booking->status,
                    $booking->payment_status
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporter le rapport des voyages en CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportTripsReport(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        $trips = $vendor->trips()
            ->with(['destination', 'travelType'])
            ->withCount([
                'availabilities as upcoming_availabilities' => function($q) {
                    $q->upcoming();
                },
                'bookings as total_bookings' => function($q) {
                    $q->whereIn('status', ['confirmed', 'completed']);
                }
            ])
            ->get();

        $filename = 'voyages_' . $vendor->company_name . '_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($trips) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Titre',
                'Destination',
                'Type',
                'Prix',
                'Statut',
                'Disponibilités',
                'Réservations',
                'Note',
                'Date création'
            ]);

            // Données
            foreach ($trips as $trip) {
                fputcsv($file, [
                    $trip->title,
                    $trip->destination->name ?? 'N/A',
                    $trip->travelType->name ?? 'N/A',
                    number_format($trip->price, 2) . '€',
                    $trip->status,
                    $trip->upcoming_availabilities ?? 0,
                    $trip->total_bookings ?? 0,
                    $trip->rating ?? 'N/A',
                    $trip->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}