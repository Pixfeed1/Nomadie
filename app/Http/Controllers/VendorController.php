<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Affiche le profil public d'un organisateur
     */
    public function show($vendor)
    {
        // Charger le vendor avec ses relations et statistiques (évite N+1 queries)
        $vendor = Vendor::with(['user', 'countries', 'serviceCategories'])
            ->where(function($query) use ($vendor) {
                // Chercher par ID ou slug
                $query->where('id', $vendor);

                // Si on ajoute un slug plus tard
                if (is_string($vendor) && !is_numeric($vendor)) {
                    $query->orWhere('slug', $vendor);
                    // Ou chercher par company_name slugifié
                    $query->orWhereRaw('LOWER(REPLACE(company_name, " ", "-")) = ?', [strtolower($vendor)]);
                }
            })
            ->where('status', 'active') // Seulement les vendors actifs
            ->withCount(['trips as active_trips_count' => function($query) {
                $query->where('status', 'active');
            }])
            ->withAvg(['trips as trips_avg_rating' => function($query) {
                $query->where('rating', '>', 0);
            }], 'rating')
            ->withSum(['trips as trips_reviews_count'], 'reviews_count')
            ->withCount('countries as destinations_count')
            ->firstOrFail();
        
        // Récupérer toutes les offres actives avec pagination
        $trips = $vendor->trips()
            ->where('status', 'active')
            ->with(['destination', 'availabilities' => function($query) {
                $query->where('start_date', '>=', now())
                      ->orderBy('start_date')
                      ->limit(3);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Grouper les offres par type pour les filtres
        $tripsByType = $vendor->trips()
            ->where('status', 'active')
            ->get()
            ->groupBy(function($trip) {
                return $trip->offer_type ?? 'organized_trip';
            });
        
        // Compter les offres par type (utilise la collection déjà chargée)
        $offerCounts = [
            'all' => $vendor->active_trips_count, // Utilise withCount (pas de N+1)
            'accommodation' => $tripsByType->get('accommodation', collect())->count(),
            'organized_trip' => $tripsByType->get('organized_trip', collect())->count(),
            'activity' => $tripsByType->get('activity', collect())->count(),
            'custom' => $tripsByType->get('custom', collect())->count(),
        ];

        // Statistiques du vendor (utilise les valeurs pré-chargées par withCount/withAvg/withSum)
        $stats = [
            'total_trips' => $vendor->active_trips_count, // Pré-chargé avec withCount
            'total_bookings' => \App\Models\Booking::whereHas('trip', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->where('status', 'confirmed')->count(),
            'average_rating' => $vendor->trips_avg_rating ? round($vendor->trips_avg_rating, 1) : 0, // Pré-chargé avec withAvg
            'total_reviews' => $vendor->trips_reviews_count ?? 0, // Pré-chargé avec withSum
            'member_since' => $vendor->created_at ? $vendor->created_at->year : date('Y'),
            'destinations_count' => $vendor->destinations_count, // Pré-chargé avec withCount
            'response_time' => '< 24h', // À implémenter avec un vrai calcul
            'languages' => ['Français', 'Anglais'], // À récupérer depuis la BDD si disponible
        ];
        
        // Récupérer les avis récents
        $recentReviews = collect(); // À implémenter avec le modèle Review si disponible
        
        // Récupérer les destinations principales
        $topDestinations = $vendor->countries()
            ->select('countries.*')
            ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.destination_id = countries.id AND trips.vendor_id = ? AND trips.status = "active") as trips_count', [$vendor->id])
            ->orderBy('trips_count', 'desc')
            ->limit(5)
            ->get();
        
        // Informations sur l'abonnement
        $subscriptionInfo = [
            'plan' => $vendor->subscription_plan,
            'plan_label' => match($vendor->subscription_plan) {
                'pro' => 'Professionnel',
                'essential' => 'Essentiel',
                'free' => 'Gratuit',
                default => 'Gratuit'
            },
            'verified' => $vendor->isEmailVerified(),
            'commission_rate' => $vendor->commission_rate,
        ];
        
        // Générer un slug temporaire pour l'URL (à sauvegarder en BDD plus tard)
        $suggestedSlug = Str::slug($vendor->company_name);
        
        return view('vendor.show', compact(
            'vendor',
            'trips',
            'tripsByType',
            'offerCounts',
            'stats',
            'recentReviews',
            'topDestinations',
            'subscriptionInfo',
            'suggestedSlug'
        ));
    }
    
    /**
     * Liste tous les organisateurs actifs
     */
    public function index(Request $request)
    {
        $vendors = Vendor::where('status', 'active')
            ->withCount(['trips' => function($query) {
                $query->where('status', 'active');
            }])
            ->when($request->filled('type'), function($query) use ($request) {
                // Filtrer par type de service si nécessaire
                $query->whereHas('serviceCategories', function($q) use ($request) {
                    $q->where('slug', $request->type);
                });
            })
            ->when($request->filled('destination'), function($query) use ($request) {
                // Filtrer par destination
                $query->whereHas('countries', function($q) use ($request) {
                    $q->where('slug', $request->destination);
                });
            })
            ->orderBy('trips_count', 'desc')
            ->paginate(12);
        
        // CORRECTION ICI : vendor.index au lieu de vendors.index
        return view('vendor.index', compact('vendors'));
    }
}