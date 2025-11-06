<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripAvailability;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Destination;
use App\Models\TravelType;
use App\Models\Country;
use App\Models\Continent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * Affiche la liste des voyages avec filtres avancés
     */
    public function index(Request $request)
    {
        $query = Trip::with(['destination.country', 'travelType', 'vendor'])
            ->where('status', 'active')
            ->withCount(['availabilities' => function($q) {
                $q->upcoming()->available();
            }]);

        // Filtre par type d'offre (nouveau)
        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        // Filtre par disponibilité dans une période donnée
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->addYear();
            
            $query->whereHas('availabilities', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('start_date', [$dateFrom, $dateTo])
                  ->available();
            });
        }

        // Filtre par destination
        if ($request->filled('destination')) {
            $query->where('destination_id', $request->destination);
        }

        // Filtre par pays
        if ($request->filled('country')) {
            $query->whereHas('destination', function($q) use ($request) {
                $q->where('country_id', $request->country);
            });
        }

        // Filtre par continent
        if ($request->filled('continent')) {
            $query->whereHas('destination.country', function($q) use ($request) {
                $q->where('continent_id', $request->continent);
            });
        }

        // Filtre par type de voyage
        if ($request->filled('travel_type')) {
            $query->where('travel_type_id', $request->travel_type);
        }

        // Filtre par type (fixe ou circuit)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par prix selon le type d'offre
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtre par budget total (adapté selon le type)
        if ($request->filled('budget')) {
            $query->where(function($q) use ($request) {
                // Pour les séjours organisés : prix × durée
                $q->where(function($subQ) use ($request) {
                    $subQ->whereIn('offer_type', ['organized_trip', 'sejour'])
                         ->whereRaw('price * duration <= ?', [$request->budget]);
                })
                // Pour les hébergements : prix par nuit
                ->orWhere(function($subQ) use ($request) {
                    $subQ->where('offer_type', 'accommodation')
                         ->where('price', '<=', $request->budget);
                })
                // Pour les activités : prix par personne
                ->orWhere(function($subQ) use ($request) {
                    $subQ->where('offer_type', 'activity')
                         ->where('price', '<=', $request->budget);
                });
            });
        }

        // Filtre par durée (adapté selon le type)
        if ($request->filled('duration')) {
            if ($request->offer_type === 'activity') {
                // Pour les activités, durée en heures
                if (str_contains($request->duration, '-')) {
                    [$min, $max] = explode('-', $request->duration);
                    $query->whereBetween('duration_hours', [$min, $max]);
                } else {
                    $query->where('duration_hours', $request->duration);
                }
            } else {
                // Pour les autres, durée en jours
                if (str_contains($request->duration, '-')) {
                    [$min, $max] = explode('-', $request->duration);
                    $query->whereBetween('duration', [$min, $max]);
                } else {
                    $query->where('duration', $request->duration);
                }
            }
        }

        // Filtre par niveau physique
        if ($request->filled('physical_level')) {
            $query->where('physical_level', $request->physical_level);
        }

        // Filtre par langue
        if ($request->filled('language')) {
            $query->whereHas('languages', function($q) use ($request) {
                $q->where('languages.id', $request->language);
            });
        }

        // Filtre par nombre de places minimum disponibles
        if ($request->filled('min_spots')) {
            $query->whereHas('availabilities', function($q) use ($request) {
                $q->upcoming()
                  ->available()
                  ->withAvailableSpots($request->min_spots);
            });
        }

        // Filtre pour les départs garantis uniquement
        if ($request->boolean('guaranteed_only')) {
            $query->whereHas('availabilities', function($q) {
                $q->upcoming()
                  ->where('is_guaranteed', true);
            });
        }

        // Filtre pour les hébergements par capacité
        if ($request->filled('capacity') && $request->offer_type === 'accommodation') {
            $query->where('property_capacity', '>=', $request->capacity);
        }

        // Filtre pour les hébergements par nombre de chambres
        if ($request->filled('bedrooms') && $request->offer_type === 'accommodation') {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('destination', function($destQ) use ($search) {
                      $destQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($vendQ) use ($search) {
                      $vendQ->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Tri adapté selon le type d'offre
        $sortBy = $request->get('sort', 'created_at');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'duration_asc':
                if ($request->offer_type === 'activity') {
                    $query->orderBy('duration_hours', 'asc');
                } else {
                    $query->orderBy('duration', 'asc');
                }
                break;
            case 'duration_desc':
                if ($request->offer_type === 'activity') {
                    $query->orderBy('duration_hours', 'desc');
                } else {
                    $query->orderBy('duration', 'desc');
                }
                break;
            case 'popularity':
                $query->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'next_departure':
                $query->with(['availabilities' => function($q) {
                    $q->upcoming()->orderBy('start_date')->limit(1);
                }])
                ->orderBy(
                    TripAvailability::select('start_date')
                        ->whereColumn('trip_id', 'trips.id')
                        ->upcoming()
                        ->orderBy('start_date')
                        ->limit(1)
                );
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Pagination avec conservation des paramètres
        $trips = $query->paginate(12)->withQueryString();

        // Charger la prochaine disponibilité pour chaque voyage
        $trips->getCollection()->transform(function ($trip) {
            $trip->next_availability = $trip->availabilities()
                ->upcoming()
                ->available()
                ->orderBy('start_date')
                ->first();
            return $trip;
        });

        // Données pour les filtres (mise en cache pour performance)
        $filterData = Cache::remember('trip_filters', 3600, function () {
            return [
                'destinations' => Destination::has('trips')
                    ->withCount('trips')
                    ->orderBy('name')
                    ->get(),
                'countries' => Country::whereHas('destinations.trips')
                    ->withCount(['destinations' => function($q) {
                        $q->has('trips');
                    }])
                    ->orderBy('name')
                    ->get(),
                'continents' => Continent::whereHas('countries.destinations.trips')
                    ->orderBy('name')
                    ->get(),
                'travelTypes' => TravelType::has('trips')
                    ->withCount('trips')
                    ->orderBy('name')
                    ->get(),
                'languages' => Language::whereHas('trips')
                    ->orderBy('name')
                    ->get(),
                'offerTypes' => [
                    'accommodation' => 'Hébergements',
                    'organized_trip' => 'Séjours organisés',
                    'activity' => 'Activités',
                    'custom' => 'Sur mesure'
                ]
            ];
        });

        return view('trips.index', array_merge(compact('trips'), $filterData));
    }

    /**
     * Affiche la page détaillée d'un voyage
     */
    public function show($slug)
    {
        $trip = Trip::with([
            'destination.country.continent',
            'travelType',
            'vendor.serviceCategories',
            'languages',
            'reviews' => function($query) {
                $query->with('user')->latest()->limit(5);
            }
        ])
        ->where(function($q) use ($slug) {
            $q->where('slug', $slug)
              ->orWhere('id', $slug);
        })
        ->where('status', 'active')
        ->firstOrFail();

        // Incrémenter les vues
        $trip->increment('views_count');
        
        // Récupérer les prochaines disponibilités
        $availabilities = $trip->availabilities()
            ->upcoming()
            ->available()
            ->orderBy('start_date')
            ->take(10)
            ->get();
        
        // Statistiques des avis
        $reviewStats = Cache::remember("trip_{$trip->id}_review_stats", 3600, function () use ($trip) {
            return [
                'average' => round($trip->reviews()->avg('rating') ?? 0, 1),
                'count' => $trip->reviews()->count(),
                'distribution' => [
                    5 => $trip->reviews()->where('rating', 5)->count(),
                    4 => $trip->reviews()->where('rating', 4)->count(),
                    3 => $trip->reviews()->where('rating', 3)->count(),
                    2 => $trip->reviews()->where('rating', 2)->count(),
                    1 => $trip->reviews()->where('rating', 1)->count(),
                ]
            ];
        });
        
        // Avis paginés
        $reviews = $trip->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Voyages similaires (du même type d'offre)
        $similarTrips = Trip::with(['destination', 'vendor'])
            ->where('offer_type', $trip->offer_type) // Même type d'offre
            ->where('destination_id', $trip->destination_id)
            ->where('id', '!=', $trip->id)
            ->where('status', 'active')
            ->whereHas('availabilities', function($q) {
                $q->upcoming()->available();
            })
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        // Si pas assez d'offres similaires dans la même destination, chercher dans le même pays
        if ($similarTrips->count() < 3) {
            $additionalTrips = Trip::with(['destination', 'vendor'])
                ->where('offer_type', $trip->offer_type) // Même type d'offre
                ->whereHas('destination', function($q) use ($trip) {
                    $q->where('country_id', $trip->destination->country_id);
                })
                ->where('id', '!=', $trip->id)
                ->whereNotIn('id', $similarTrips->pluck('id'))
                ->where('status', 'active')
                ->whereHas('availabilities', function($q) {
                    $q->upcoming()->available();
                })
                ->inRandomOrder()
                ->take(3 - $similarTrips->count())
                ->get();
            
            $similarTrips = $similarTrips->concat($additionalTrips);
        }
        
        // Vérifications pour l'utilisateur connecté
        $userHasParticipated = false;
        $userBooking = null;
        $userReview = null;
        
        if (auth()->check()) {
            $userBooking = Booking::where('user_id', auth()->id())
                ->where('trip_id', $trip->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->first();
                
            $userHasParticipated = !is_null($userBooking);
            
            $userReview = Review::where('user_id', auth()->id())
                ->where('trip_id', $trip->id)
                ->first();
        }
        
        // Log de la visite
        Log::info('Trip viewed', [
            'trip_id' => $trip->id,
            'trip_slug' => $trip->slug,
            'offer_type' => $trip->offer_type,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return view('trips.show', compact(
            'trip', 
            'availabilities',
            'reviews', 
            'reviewStats',
            'similarTrips', 
            'userHasParticipated',
            'userBooking',
            'userReview'
        ));
    }

    /**
     * Vérifie la disponibilité d'un voyage (AJAX)
     */
    public function checkAvailability(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        
        $validated = $request->validate([
            'availability_id' => 'required|exists:trip_availabilities,id',
            'travelers' => 'required|integer|min:1',
            'nights' => 'nullable|integer|min:1' // Pour les hébergements
        ]);
        
        $availability = TripAvailability::find($validated['availability_id']);
        $travelers = $validated['travelers'];
        $nights = $validated['nights'] ?? null;
        
        // Vérifier que la disponibilité appartient bien au voyage
        if ($availability->trip_id !== $trip->id) {
            return response()->json([
                'available' => false,
                'message' => 'Disponibilité invalide'
            ], 400);
        }
        
        // Calcul du prix selon le type d'offre
        $basePrice = 0;
        $finalPrice = 0;
        $message = '';
        
        if ($trip->isAccommodation()) {
            // Pour les hébergements, calculer avec le nombre de nuits
            $nightsToBook = $nights ?? $trip->min_nights ?? 1;
            $basePrice = ($availability->property_price ?? $availability->adult_price) * $nightsToBook;
            $message = "Prix pour {$nightsToBook} nuit(s)";
        } elseif ($trip->isActivity()) {
            // Pour les activités, prix par personne
            $basePrice = $availability->adult_price * $travelers;
            $message = "Prix pour {$travelers} personne(s)";
        } else {
            // Pour les séjours organisés
            $basePrice = $availability->adult_price * $travelers * ($trip->duration ?? 1);
            $message = "Prix pour {$travelers} personne(s) sur {$trip->duration} jour(s)";
        }
        
        $finalPrice = $basePrice;
        
        // Vérifier la disponibilité
        $available = false;
        
        if ($availability->canBook($travelers)) {
            $available = true;
            
            if ($availability->discount_percentage > 0) {
                $discount = $basePrice * ($availability->discount_percentage / 100);
                $finalPrice = $basePrice - $discount;
                $message .= " avec {$availability->discount_percentage}% de réduction !";
            }
            
            if ($availability->is_guaranteed) {
                $message .= $trip->isActivity() ? ' - Séance garantie' : ' - Départ garanti';
            }
        } else {
            if ($availability->available_spots > 0) {
                $message = "Seulement {$availability->available_spots} places restantes";
            } else {
                $message = 'Complet';
            }
        }
        
        return response()->json([
            'available' => $available,
            'message' => $message,
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount' => $basePrice - $finalPrice,
            'formatted_price' => number_format($finalPrice, 0, ',', ' ') . ' €'
        ]);
    }

    /**
     * Affiche le formulaire de réservation
     */
    public function bookingForm(Request $request, $slug)
    {
        if (!auth()->check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('login')
                ->with('info', 'Veuillez vous connecter pour réserver.');
        }
        
        $trip = Trip::with(['destination', 'vendor'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Récupérer la disponibilité sélectionnée
        $selectedAvailability = null;
        if ($request->has('availability_id')) {
            $selectedAvailability = $trip->availabilities()
                ->upcoming()
                ->available()
                ->find($request->availability_id);
        }
        
        // Si pas de disponibilité sélectionnée, prendre la prochaine
        if (!$selectedAvailability) {
            $selectedAvailability = $trip->availabilities()
                ->upcoming()
                ->available()
                ->orderBy('start_date')
                ->first();
        }
        
        if (!$selectedAvailability) {
            return redirect()->route('trips.show', $trip->slug)
                ->with('error', 'Aucune disponibilité pour cette offre.');
        }
        
        // Nombre de voyageurs/personnes
        $travelers = $request->get('travelers', 1);
        
        // Nombre de nuits pour les hébergements
        $nights = null;
        if ($trip->isAccommodation()) {
            $nights = $request->get('nights', $trip->min_nights ?? 1);
        }
        
        return view('trips.booking', compact('trip', 'selectedAvailability', 'travelers', 'nights'));
    }

    /**
     * Traite la réservation
     */
    public function book(Request $request, $slug)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $trip = Trip::where('slug', $slug)->firstOrFail();
        
        // Validation adaptée selon le type d'offre
        $rules = [
            'availability_id' => 'required|exists:trip_availabilities,id',
            'travelers' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'accept_terms' => 'required|accepted'
        ];
        
        // Pour les hébergements
        if ($trip->isAccommodation()) {
            $rules['nights'] = 'required|integer|min:' . ($trip->min_nights ?? 1);
        } else {
            // Pour les autres, adultes et enfants
            $rules['adults'] = 'required|integer|min:1';
            $rules['children'] = 'nullable|integer|min:0';
        }
        
        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            $availability = TripAvailability::findOrFail($validated['availability_id']);
            
            // Vérifier que la disponibilité appartient au voyage
            if ($availability->trip_id !== $trip->id) {
                throw new \Exception('Disponibilité invalide pour cette offre');
            }
            
            // Calculer le nombre total de personnes
            $totalTravelers = $validated['travelers'];
            $adults = $validated['adults'] ?? $totalTravelers;
            $children = $validated['children'] ?? 0;
            
            // Pour les hébergements, vérifier la capacité
            if ($trip->isAccommodation()) {
                if ($totalTravelers > $trip->property_capacity) {
                    throw new \Exception("Ce logement ne peut accueillir que {$trip->property_capacity} personnes maximum");
                }
            } else {
                // Pour les autres, vérifier que adults + children = travelers
                if ($adults + $children !== $totalTravelers) {
                    throw new \Exception('Le nombre total de personnes ne correspond pas.');
                }
            }
            
            // Vérifier la disponibilité
            if (!$availability->canBook($totalTravelers)) {
                throw new \Exception('Pas assez de places disponibles');
            }
            
            // Calculer le prix selon le type d'offre
            if ($trip->isAccommodation()) {
                // Prix par nuit × nombre de nuits
                $nights = $validated['nights'] ?? $trip->min_nights ?? 1;
                $basePrice = ($availability->property_price ?? $availability->adult_price) * $nights;
            } elseif ($trip->isActivity()) {
                // Prix par personne pour l'activité
                $adultPrice = $availability->adult_price * $adults;
                $childPrice = $availability->child_price * $children;
                $basePrice = $adultPrice + $childPrice;
            } else {
                // Prix par personne × durée pour les séjours
                $adultPrice = $availability->adult_price * $adults * ($trip->duration ?? 1);
                $childPrice = $availability->child_price * $children * ($trip->duration ?? 1);
                $basePrice = $adultPrice + $childPrice;
            }
            
            // Appliquer la réduction si elle existe
            $discountAmount = 0;
            if ($availability->discount_percentage > 0 && (!$availability->discount_ends_at || $availability->discount_ends_at > now())) {
                $discountAmount = $basePrice * ($availability->discount_percentage / 100);
            }
            
            $finalPrice = $basePrice - $discountAmount;
            
            // Créer la réservation
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'trip_id' => $trip->id,
                'trip_availability_id' => $availability->id,
                'vendor_id' => $trip->vendor_id,
                'number_of_travelers' => $totalTravelers,
                'adults' => $adults,
                'children' => $children,
                'nights' => $validated['nights'] ?? null,
                'unit_price' => $availability->adult_price,
                'subtotal' => $basePrice,
                'discount_amount' => $discountAmount,
                'total_amount' => $finalPrice,
                'special_requests' => $validated['special_requests'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'booking_number' => 'BK' . date('Y') . str_pad(Booking::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT)
            ]);
            
            // Incrémenter les places réservées (sauf pour les hébergements)
            if (!$trip->isAccommodation()) {
                $availability->incrementBookedSpots($totalTravelers);
            }
            
            DB::commit();
            
            Log::info('Booking created', [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'trip_id' => $trip->id,
                'offer_type' => $trip->offer_type,
                'availability_id' => $availability->id,
                'amount' => $finalPrice
            ]);
            
            // Rediriger vers le paiement
            return redirect()->route('bookings.payment', $booking)
                ->with('success', 'Réservation créée avec succès. Procédez au paiement pour la confirmer.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Booking failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trip_id' => $trip->id
            ]);
            
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Calendrier de disponibilité (AJAX)
     */
    public function availabilityCalendar(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Récupérer toutes les disponibilités du mois
        $availabilities = $trip->availabilities()
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();
        
        $availableDates = [];
        
        foreach ($availabilities as $availability) {
            $currentDate = $availability->start_date->copy();
            $endAvailability = $availability->end_date->copy();
            
            while ($currentDate <= $endAvailability) {
                if ($currentDate->month == $month && $currentDate->year == $year) {
                    $availableDates[$currentDate->format('Y-m-d')] = [
                        'available' => $availability->status !== 'cancelled' && $availability->available_spots > 0,
                        'availability_id' => $availability->id,
                        'spots' => $availability->available_spots,
                        'price' => $trip->isAccommodation() 
                            ? ($availability->property_price ?? $availability->adult_price)
                            : $availability->adult_price,
                        'discount' => $availability->discount_percentage,
                        'guaranteed' => $availability->is_guaranteed,
                        'status' => $availability->status
                    ];
                }
                $currentDate->addDay();
            }
        }
        
        return response()->json([
            'dates' => $availableDates,
            'month' => $startDate->format('F Y'),
            'offer_type' => $trip->offer_type
        ]);
    }

    /**
     * Obtenir les disponibilités d'un voyage (AJAX)
     */
    public function getAvailabilities(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        
        $availabilities = $trip->availabilities()
            ->upcoming()
            ->available();
            
        if ($request->filled('min_spots')) {
            $availabilities->withAvailableSpots($request->min_spots);
        }
        
        $availabilities = $availabilities->orderBy('start_date')
            ->take(20)
            ->get()
            ->map(function($availability) use ($trip) {
                $data = [
                    'id' => $availability->id,
                    'start_date' => $availability->start_date->format('Y-m-d'),
                    'end_date' => $availability->end_date->format('Y-m-d'),
                    'available_spots' => $availability->available_spots,
                    'adult_price' => $availability->adult_price,
                    'child_price' => $availability->child_price,
                    'discount_percentage' => $availability->discount_percentage,
                    'is_guaranteed' => $availability->is_guaranteed
                ];
                
                // Format adapté selon le type
                if ($trip->isActivity()) {
                    $data['formatted_dates'] = $availability->start_date->format('d/m/Y H:i');
                    $data['formatted_price'] = number_format($availability->adult_price, 0, ',', ' ') . ' €/pers';
                } elseif ($trip->isAccommodation()) {
                    $data['formatted_dates'] = 'À partir du ' . $availability->start_date->format('d/m/Y');
                    $data['property_price'] = $availability->property_price ?? $availability->adult_price;
                    $data['formatted_price'] = number_format($data['property_price'], 0, ',', ' ') . ' €/nuit';
                } else {
                    $data['formatted_dates'] = $availability->start_date->format('d/m/Y') . ' - ' . $availability->end_date->format('d/m/Y');
                    $data['formatted_price'] = number_format($availability->adult_price, 0, ',', ' ') . ' €/pers';
                }
                
                return $data;
            });
        
        return response()->json([
            'availabilities' => $availabilities,
            'offer_type' => $trip->offer_type
        ]);
    }

    /**
     * Télécharger la brochure PDF d'un voyage
     */
    public function downloadBrochure($id)
    {
        $trip = Trip::with(['destination', 'vendor', 'travelType'])->findOrFail($id);
        
        // Générer ou récupérer le PDF
        // À implémenter selon vos besoins
        
        Log::info('Brochure downloaded', [
            'trip_id' => $trip->id,
            'offer_type' => $trip->offer_type,
            'user_id' => auth()->id()
        ]);
        
        // Pour l'instant, redirection
        return back()->with('info', 'La brochure sera bientôt disponible');
    }
}