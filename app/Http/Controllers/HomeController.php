<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Continent;
use App\Models\Trip;
use App\Models\Vendor;
use App\Models\Review;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Pas de middleware auth pour permettre l'accès à tous
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Récupérer les continents pour le formulaire de recherche
        $continents = Continent::all();

        // Récupérer les destinations populaires (pays avec des trips)
        $featuredDestinations = Country::select('countries.*')
            ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.country_id = countries.id AND trips.status = "active") as trips_count')
            ->selectRaw('(SELECT AVG(rating) FROM trips WHERE trips.country_id = countries.id AND trips.rating > 0) as average_rating')
            ->having('trips_count', '>', 0)
            ->with('continent')
            ->orderByDesc('popular')
            ->orderByDesc('trips_count')
            ->take(3)
            ->get();

        // Si pas assez de destinations avec trips, prendre les pays populaires
        if ($featuredDestinations->count() < 3) {
            $featuredDestinations = Country::where('popular', true)
                ->with('continent')
                ->take(3)
                ->get()
                ->map(function($country) {
                    $country->trips_count = 0;
                    $country->average_rating = 0;
                    return $country;
                });
        }

        // NOUVEAU : Récupérer les dernières offres par type
        $latestAccommodations = Trip::with(['destination', 'vendor'])
            ->where('offer_type', 'accommodation')
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->where('start_date', '>=', now())
                  ->where('status', '!=', 'cancelled');
            })
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $latestOrganizedTrips = Trip::with(['destination', 'vendor'])
            ->where('offer_type', 'organized_trip')
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->where('start_date', '>=', now())
                  ->where('status', '!=', 'cancelled');
            })
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $latestActivities = Trip::with(['destination', 'vendor'])
            ->where('offer_type', 'activity')
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->where('start_date', '>=', now())
                  ->where('status', '!=', 'cancelled');
            })
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // NOUVEAU : Statistiques par type d'offre
        $offerTypeStats = [
            'accommodations' => [
                'count' => Trip::where('offer_type', 'accommodation')->where('status', 'active')->count(),
                'label' => 'Hébergements',
                'icon' => 'home',
                'description' => 'Gîtes, villas, appartements et maisons de charme'
            ],
            'organized_trips' => [
                'count' => Trip::where('offer_type', 'organized_trip')->where('status', 'active')->count(),
                'label' => 'Séjours organisés',
                'icon' => 'map',
                'description' => 'Voyages tout compris avec guide et activités'
            ],
            'activities' => [
                'count' => Trip::where('offer_type', 'activity')->where('status', 'active')->count(),
                'label' => 'Activités',
                'icon' => 'activity',
                'description' => 'Expériences uniques et découvertes locales'
            ],
            'custom' => [
                'count' => Trip::where('offer_type', 'custom')->where('status', 'active')->count(),
                'label' => 'Sur mesure',
                'icon' => 'star',
                'description' => 'Créez votre voyage personnalisé'
            ]
        ];

        // NOUVEAU : Offres en promotion (avec discount)
        $promotionalOffers = Trip::with(['destination', 'vendor'])
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->where('start_date', '>=', now())
                  ->where('discount_percentage', '>', 0)
                  ->where('status', '!=', 'cancelled');
            })
            ->withMax(['availabilities as best_discount' => function($query) {
                $query->where('start_date', '>=', now())
                      ->where('discount_percentage', '>', 0);
            }], 'discount_percentage')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Récupérer les meilleurs organisateurs actifs
        $featuredVendors = Vendor::where('status', 'active')
            ->withCount(['trips' => function($query) {
                $query->where('status', 'active');
            }])
            ->withAvg(['trips as trips_avg_rating' => function($query) {
                $query->where('rating', '>', 0);
            }], 'rating')
            ->orderByDesc('trips_count')
            ->take(4)
            ->get()
            ->map(function($vendor) {
                // Utiliser la note moyenne calculée par withAvg (pas de N+1 query)
                $vendor->average_rating = $vendor->trips_avg_rating
                    ? round($vendor->trips_avg_rating, 1)
                    : 4.7;

                // Ajouter une spécialité par défaut si pas définie
                $vendor->specialty = $this->getVendorSpecialty($vendor);

                return $vendor;
            });

        // Récupérer les derniers avis
        $testimonials = collect([]);
        
        // Essayer de récupérer de vrais avis
        if (class_exists('App\Models\Review')) {
            $testimonials = Review::with(['trip', 'user'])
                ->where('rating', '>=', 4)
                ->orderByDesc('created_at')
                ->take(6) // Plus d'avis pour le carrousel
                ->get();
        }

        // Si pas assez d'avis, utiliser des témoignages par défaut
        if ($testimonials->count() < 3) {
            $testimonials = $this->getDefaultTestimonials();
        }

        // Statistiques globales pour la section "Pourquoi choisir"
        $stats = [
            'total_trips' => Trip::where('status', 'active')->count(),
            'total_vendors' => Vendor::where('status', 'active')->count(),
            'total_destinations' => Country::whereRaw('EXISTS (SELECT 1 FROM trips WHERE trips.country_id = countries.id)')->count(),
            'total_bookings' => Booking::where('status', 'confirmed')->count(),
            'average_rating' => round(Trip::where('rating', '>', 0)->avg('rating'), 1) ?: 4.8
        ];

        // Paramètres du site pour le bandeau d'accueil
        $heroSettings = [
            'image' => SiteSetting::get('hero_banner_image', 'images/hero-bg.jpg'),
            'title' => SiteSetting::get('hero_banner_title', 'Organisez et vivez des expériences authentiques'),
            'subtitle' => SiteSetting::get('hero_banner_subtitle', 'Voyages, circuits, séjours, hébergements et activités uniques dans le monde entier. Réservez directement auprès d\'organisateurs locaux experts.'),
        ];

        // Paramètres pour le bandeau rédacteurs
        $writerBannerSettings = [
            'image' => SiteSetting::get('writer_banner_image', 'images/writer-bg.jpg'),
            'title' => SiteSetting::get('writer_banner_title', 'Partagez votre passion du voyage'),
            'subtitle' => SiteSetting::get('writer_banner_subtitle', 'Rejoignez notre communauté de rédacteurs et partagez vos plus belles découvertes'),
            'feature1_title' => SiteSetting::get('writer_banner_feature1_title', 'Rémunération'),
            'feature1_desc' => SiteSetting::get('writer_banner_feature1_desc', 'Valorisez vos contenus de qualité'),
            'feature2_title' => SiteSetting::get('writer_banner_feature2_title', 'Large audience'),
            'feature2_desc' => SiteSetting::get('writer_banner_feature2_desc', 'Des milliers de lecteurs passionnés'),
            'feature3_title' => SiteSetting::get('writer_banner_feature3_title', 'Liberté créative'),
            'feature3_desc' => SiteSetting::get('writer_banner_feature3_desc', 'Écrivez sur vos sujets préférés'),
        ];

        return view('home', compact(
            'continents',
            'featuredDestinations',
            'featuredVendors',
            'testimonials',
            'stats',
            'latestAccommodations',
            'latestOrganizedTrips',
            'latestActivities',
            'offerTypeStats',
            'promotionalOffers',
            'heroSettings',
            'writerBannerSettings'
        ));
    }

    /**
     * Déterminer la spécialité d'un vendor
     */
    private function getVendorSpecialty($vendor)
    {
        $tripTypes = $vendor->trips()
            ->select('offer_type', DB::raw('count(*) as count'))
            ->groupBy('offer_type')
            ->orderByDesc('count')
            ->get();

        if ($tripTypes->isEmpty()) {
            return 'Spécialiste voyages';
        }

        $mainType = $tripTypes->first()->offer_type;
        
        return match($mainType) {
            'accommodation' => 'Spécialiste Hébergements',
            'organized_trip' => 'Spécialiste Séjours Organisés',
            'activity' => 'Spécialiste Activités',
            'custom' => 'Spécialiste Sur Mesure',
            default => 'Spécialiste Voyages'
        };
    }

    /**
     * Témoignages par défaut si pas d'avis en base
     */
    private function getDefaultTestimonials()
    {
        return collect([
            (object)[
                'user' => (object)['name' => 'Marie Lefèvre'],
                'rating' => 5,
                'content' => 'Notre voyage organisé par Nomadie a été parfait de A à Z. Nous avons découvert des endroits incroyables que nous n\'aurions jamais trouvés par nous-mêmes. Un grand merci pour cette expérience inoubliable !',
                'trip' => (object)['title' => 'Découverte authentique', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(1)
            ],
            (object)[
                'user' => (object)['name' => 'Thomas Dubois'],
                'rating' => 5,
                'content' => 'L\'organisation était parfaite et l\'équipe très professionnelle. Nous avons pu découvrir la culture locale tout en respectant l\'environnement. Une expérience à renouveler !',
                'trip' => (object)['title' => 'Aventure nature', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(2)
            ],
            (object)[
                'user' => (object)['name' => 'Sophie Bernard'],
                'rating' => 5,
                'content' => 'Je cherchais une expérience différente et Nomadie m\'a offert exactement ce que je recherchais. Rencontrer les populations locales et participer aux activités traditionnelles m\'a permis de vivre un voyage authentique.',
                'trip' => (object)['title' => 'Immersion culturelle', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(3)
            ],
            (object)[
                'user' => (object)['name' => 'Pierre Martin'],
                'rating' => 5,
                'content' => 'L\'hébergement était exceptionnel, avec une vue magnifique et tout le confort nécessaire. Les propriétaires étaient adorables et nous ont fait découvrir les meilleures adresses locales.',
                'trip' => (object)['title' => 'Villa de charme', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(1)
            ],
            (object)[
                'user' => (object)['name' => 'Julie Rousseau'],
                'rating' => 5,
                'content' => 'Les activités proposées étaient variées et passionnantes. Notre guide était un vrai passionné qui nous a transmis son amour pour la région. Je recommande vivement !',
                'trip' => (object)['title' => 'Randonnée guidée', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(2)
            ],
            (object)[
                'user' => (object)['name' => 'Marc Durand'],
                'rating' => 5,
                'content' => 'Un séjour sur mesure parfaitement adapté à nos besoins. L\'équipe a été à l\'écoute et a créé un programme unique qui correspondait exactement à nos attentes.',
                'trip' => (object)['title' => 'Voyage personnalisé', 'destination' => (object)['name' => 'Destination']],
                'created_at' => now()->subMonths(1)
            ]
        ]);
    }
}