<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTripController extends Controller
{
    /**
     * Liste de toutes les expériences avec stats et filtres
     */
    public function index(Request $request)
    {
        $query = Trip::with(['vendor', 'destination', 'country', 'travelType'])
            ->withCount(['bookings', 'reviews']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'reservations':
                $query->withCount('bookings')->orderBy('bookings_count', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'views':
                $query->orderBy('views_count', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $trips = $query->paginate(20);

        // Stats globales
        $stats = [
            'total_trips' => Trip::count(),
            'active_trips' => Trip::where('status', 'active')->count(),
            'total_bookings' => Booking::whereIn('status', ['confirmed', 'completed'])->count(),
            'total_revenue' => Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'avg_rating' => Review::avg('rating'),
            'total_reviews' => Review::count(),
        ];

        // Top 10 expériences les plus réservées
        $topTrips = Trip::withCount('bookings')
            ->where('status', 'active')
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.trips.index', compact('trips', 'stats', 'topTrips'));
    }

    /**
     * Détails complets d'une expérience
     */
    public function show(Trip $trip)
    {
        // Charger toutes les relations
        $trip->load([
            'vendor',
            'destination',
            'country',
            'travelType',
            'reviews.user',
            'bookings.user',
            'availabilities'
        ]);

        // Stats de l'expérience
        $stats = [
            // Réservations
            'total_bookings' => $trip->bookings()->whereIn('status', ['confirmed', 'completed'])->count(),
            'pending_bookings' => $trip->bookings()->where('status', 'pending')->count(),
            'cancelled_bookings' => $trip->bookings()->where('status', 'cancelled')->count(),

            // Revenus
            'total_revenue' => $trip->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'pending_revenue' => $trip->bookings()
                ->where('status', 'pending')
                ->sum('total_amount'),

            // Voyageurs
            'total_travelers' => $trip->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('number_of_travelers'),

            // Avis
            'total_reviews' => $trip->reviews_count,
            'avg_rating' => $trip->rating,

            // Vues
            'total_views' => $trip->views_count,

            // Taux de conversion
            'conversion_rate' => $trip->views_count > 0
                ? round(($trip->bookings_count / $trip->views_count) * 100, 2)
                : 0,
        ];

        // Réservations récentes
        $recentBookings = $trip->bookings()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Tous les avis
        $reviews = $trip->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Messages liés à cette expérience
        // Les messages peuvent être entre le vendeur et les clients qui ont réservé
        $messages = Message::where(function($query) use ($trip) {
            $query->where('sender_id', $trip->vendor->user_id)
                  ->orWhere('receiver_id', $trip->vendor->user_id);
        })
        ->whereHas('booking', function($q) use ($trip) {
            $q->where('trip_id', $trip->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        // Stats mensuelles (derniers 12 mois)
        $monthlyStats = Booking::where('trip_id', $trip->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(number_of_travelers) as travelers')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Répartition des notes
        $ratingDistribution = Review::where('trip_id', $trip->id)
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        return view('admin.trips.show', compact(
            'trip',
            'stats',
            'recentBookings',
            'reviews',
            'messages',
            'monthlyStats',
            'ratingDistribution'
        ));
    }

    /**
     * Activer/désactiver une expérience
     */
    public function toggleStatus(Trip $trip)
    {
        $newStatus = $trip->status === 'active' ? 'inactive' : 'active';
        $trip->update(['status' => $newStatus]);

        return back()->with('success', "L'expérience a été " . ($newStatus === 'active' ? 'activée' : 'désactivée'));
    }

    /**
     * Mettre en vedette / retirer de la vedette
     */
    public function toggleFeatured(Trip $trip)
    {
        $trip->update(['featured' => !$trip->featured]);

        return back()->with('success', $trip->featured ? "L'expérience a été mise en vedette" : "L'expérience a été retirée de la vedette");
    }

    /**
     * Supprimer une expérience
     */
    public function destroy(Trip $trip)
    {
        // Vérifier qu'il n'y a pas de réservations actives
        $activeBookings = $trip->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', "Impossible de supprimer cette expérience car elle a des réservations actives.");
        }

        $trip->delete();

        return redirect()->route('admin.trips.index')->with('success', "L'expérience a été supprimée avec succès");
    }
}
