<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Trip;
use App\Models\Destination;
use App\Models\TravelType;
use App\Models\Vendor;
use App\Models\Language;

class TripController extends Controller
{
    /**
     * Les types d'offres disponibles (alignés avec le modèle Trip)
     */
    const OFFER_TYPES = [
        'accommodation' => [
            'name' => 'Location d\'hébergement',
            'pricing_mode' => 'per_night_property',
            'fields' => ['property_capacity', 'bedrooms', 'bathrooms', 'min_nights']
        ],
        'organized_trip' => [
            'name' => 'Séjour organisé', 
            'pricing_mode' => 'per_person_per_day',
            'fields' => ['max_travelers', 'physical_level', 'meal_plan', 'meeting_point']
        ],
        'activity' => [
            'name' => 'Activité ou expérience',
            'pricing_mode' => 'per_person_activity',
            'fields' => ['duration_hours', 'max_participants', 'equipment_included', 'equipment_list']
        ],
        'custom' => [
            'name' => 'Offre sur mesure',
            'pricing_mode' => 'custom',
            'fields' => ['pricing_description', 'flexible_duration']
        ]
    ];

    /**
     * Afficher la liste des offres du vendeur
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        // Construire la requête avec filtres et inclure les disponibilités
        $query = $vendor->trips()->with(['destination', 'travelType', 'availabilities' => function($q) {
            $q->upcoming()->orderBy('start_date');
        }]);
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }
        
        if ($request->filled('destination')) {
            $query->where('destination_id', $request->destination);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $trips = $query->paginate(12)->withQueryString();
        
        // Statistiques pour les filtres
        $stats = [
            'total' => $vendor->trips()->count(),
            'active' => $vendor->trips()->where('status', 'active')->count(),
            'draft' => $vendor->trips()->where('status', 'draft')->count(),
            'inactive' => $vendor->trips()->where('status', 'inactive')->count(),
            'by_type' => [
                'accommodation' => $vendor->trips()->where('offer_type', 'accommodation')->count(),
                'organized_trip' => $vendor->trips()->where('offer_type', 'organized_trip')->count(),
                'activity' => $vendor->trips()->where('offer_type', 'activity')->count(),
                'custom' => $vendor->trips()->where('offer_type', 'custom')->count(),
            ],
            'limit' => $vendor->max_trips,
            'remaining' => $vendor->remaining_trips,
            'can_create' => $vendor->canCreateMoreTrips()
        ];
        
        // Destinations pour le filtre
        $destinations = Destination::whereHas('trips', function($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id);
        })->get();
        
        Log::info('Vendor trips index accessed', [
            'vendor_id' => $vendor->id,
            'trips_count' => $stats['total'],
            'max_trips' => $vendor->max_trips
        ]);
        
        return view('vendor.trips.index', compact('trips', 'stats', 'destinations', 'vendor'));
    }

    /**
     * Afficher la page de choix du type d'offre
     */
    public function chooseType()
    {
        $vendor = Auth::user()->vendor;
        
        // Vérifier les limites du plan
        if (!$vendor->canCreateMoreTrips()) {
            $message = 'Vous avez atteint la limite d\'offres pour votre plan.';
            
            if ($vendor->subscription_plan === 'free') {
                $message .= ' Passez à l\'abonnement Essential ou Pro pour créer plus d\'offres.';
            } elseif ($vendor->subscription_plan === 'essential') {
                $message .= ' Passez à l\'abonnement Pro pour créer des offres illimitées.';
            }
            
            Log::warning('Trip creation blocked - limit reached', [
                'vendor_id' => $vendor->id,
                'current_trips' => $vendor->trips()->count(),
                'max_trips' => $vendor->max_trips,
                'plan' => $vendor->subscription_plan
            ]);

            return redirect()->route('vendor.trips.index')
                ->with('error', $message);
        }
        
        $offerTypes = self::OFFER_TYPES;
        
        return view('vendor.trips.choose-type', compact('vendor', 'offerTypes'));
    }

    /**
     * Afficher le formulaire de création d'une offre
     */
    public function create($type = null)
    {
        $vendor = Auth::user()->vendor;

        // Si pas de type fourni ou type invalide, rediriger vers le choix du type
        if (!$type || !array_key_exists($type, self::OFFER_TYPES)) {
            return redirect()->route('vendor.trips.choose-type');
        }

        // Vérifier les limites du plan
        if (!$vendor->canCreateMoreTrips()) {
            Log::warning('Trip creation blocked - limit reached', [
                'vendor_id' => $vendor->id,
                'current_trips' => $vendor->trips()->count(),
                'max_trips' => $vendor->max_trips,
                'plan' => $vendor->subscription_plan
            ]);

            return redirect()->route('vendor.trips.index')
                ->with('error', 'Vous avez atteint la limite d\'offres pour votre plan. Passez à un plan supérieur pour créer plus d\'offres.');
        }

        // Charger les pays sélectionnés par le vendor
        $countries = $vendor->countries()->orderBy('name')->get();
        $destinations = $countries;
        
        // Vérifier si le vendor peut ajouter plus de destinations
        $canAddDestinations = $vendor->canAddMoreDestinations();
        $destinationMessage = null;
        
        if (!$canAddDestinations && $destinations->isEmpty()) {
            $destinationMessage = 'Vous avez atteint la limite de destinations pour votre plan. Mettez à jour votre abonnement pour ajouter des destinations.';
        } elseif (!$canAddDestinations) {
            $destinationMessage = 'Limite de destinations atteinte. Pour ajouter d\'autres pays, mettez à jour votre abonnement.';
        } elseif ($destinations->isEmpty()) {
            return redirect()->route('vendor.settings.destinations')
                ->with('info', 'Vous devez d\'abord configurer vos destinations avant de créer une offre.');
        }
        
        // Charger les services et attributs du vendor
        $serviceCategories = $vendor->serviceCategories;
        $serviceAttributes = $vendor->serviceAttributes;
        
        // Charger tous les types de voyage
        $travelTypes = TravelType::orderBy('name')->get();
        
        // Préparer les données des langues
        $languages = Language::active()
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->get();
        
        $popularLanguages = Language::active()
                                   ->popular()
                                   ->orderBy('sort_order')
                                   ->get();
        
        // Transformation des données pour la vue
        $languagesJson = $languages->map(function($lang) {
            return [
                'id' => $lang->id,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'region' => $lang->region
            ];
        })->toJson();
        
        // Préparer les langues sélectionnées
        $selectedLanguagesJson = json_encode(old('languages', []));
        
        // Informations sur le type d'offre
        $offerTypeInfo = self::OFFER_TYPES[$type];
        
        // Statistiques
        $stats = [
            'trips_remaining' => $vendor->remaining_trips,
            'total_trips' => $vendor->trips()->count(),
            'max_trips' => $vendor->max_trips,
            'destinations_count' => $destinations->count(),
            'destinations_remaining' => $vendor->remaining_destinations,
            'services_count' => $serviceCategories->count()
        ];

        Log::info('Trip creation form accessed', [
            'vendor_id' => $vendor->id,
            'type' => $type,
            'offer_type' => $offerTypeInfo['name'],
            'pricing_mode' => $offerTypeInfo['pricing_mode'],
            'available_destinations' => $destinations->count(),
            'trips_remaining' => $vendor->remaining_trips
        ]);

        return view('vendor.trips.create', compact(
            'destinations', 
            'countries',
            'travelTypes', 
            'vendor',
            'serviceCategories',
            'serviceAttributes',
            'stats',
            'languages',
            'popularLanguages',
            'languagesJson',
            'selectedLanguagesJson',
            'type',
            'offerTypeInfo',
            'canAddDestinations',
            'destinationMessage'
        ));
    }

    /**
     * Enregistrer une nouvelle offre
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier les limites du plan
        if (!$vendor->canCreateMoreTrips()) {
            return redirect()->route('vendor.trips.index')
                ->with('error', 'Limite d\'offres atteinte pour votre plan.');
        }

        $offerType = $request->input('offer_type');
        
        if (!array_key_exists($offerType, self::OFFER_TYPES)) {
            return back()->with('error', 'Type d\'offre invalide.');
        }

        // Règles de validation de base
        $rules = [
            'offer_type' => 'required|in:' . implode(',', array_keys(self::OFFER_TYPES)),
            'title' => 'required|string|max:255',
            'country_id' => [
                'required',
                'exists:countries,id',
                function ($attribute, $value, $fail) use ($vendor) {
                    if (!$vendor->countries()->where('countries.id', $value)->exists()) {
                        $fail('Vous ne pouvez créer une offre que dans vos pays autorisés.');
                    }
                }
            ],
            'travel_type_id' => 'required|exists:travel_types,id',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:EUR,USD,GBP',
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:languages,id',
            'images' => 'required|array|min:5|max:20',
            'images.*' => 'image|mimes:' . implode(',', config('uploads.allowed_extensions.images')) . '|max:' . config('uploads.max_sizes.image'),
            'image_captions' => 'nullable|array',
            'image_captions.*' => 'nullable|string|max:150',
            'status' => 'required|in:draft,active',
            'included' => 'nullable|array',
            'included.*' => 'string|max:255',
            'not_included' => 'nullable|array',
            'not_included.*' => 'string|max:255',
            'requirements' => 'nullable|string|max:2000',
            'payment_online_required' => 'boolean',
            'free_booking_allowed' => 'boolean',
        ];

        // Règles spécifiques selon le type d'offre
        switch ($offerType) {
            case 'accommodation':
                $rules += [
                    'destination_id' => [
                        'required',
                        'exists:destinations,id',
                        function ($attribute, $value, $fail) use ($request) {
                            $destination = DB::table('destinations')->find($value);
                            if (!$destination || $destination->country_id != $request->country_id) {
                                $fail('La ville sélectionnée ne correspond pas au pays choisi.');
                            }
                        }
                    ],
                    'property_capacity' => 'required|integer|min:1|max:50',
                    'bedrooms' => 'required|integer|min:0|max:20',
                    'bathrooms' => 'required|integer|min:0|max:10',
                    'min_nights' => 'required|integer|min:1|max:30',
                ];
                break;

            case 'organized_trip':
                $rules += [
                    'duration' => 'required|integer|min:1|max:365',
                    'max_travelers' => 'required|integer|min:1|max:50',
                    'min_travelers' => 'nullable|integer|min:1|max:50',
                    'physical_level' => 'required|in:easy,moderate,difficult,expert',
                    'meal_plan' => 'required|in:none,breakfast,half_board,full_board,all_inclusive',
                    'meeting_point' => 'nullable|string|max:255',
                    'meeting_time' => 'nullable|date_format:H:i',
                    'meeting_address' => 'nullable|string|max:500',
                    'meeting_instructions' => 'nullable|string|max:1000',
                ];
                
                // Pour les séjours de type circuit
                if ($request->input('is_circuit')) {
                    $rules['itinerary'] = 'required|array|min:1';
                    $rules['itinerary.*.title'] = 'required|string|max:255';
                    $rules['itinerary.*.description'] = 'required|string|max:2000';
                    $rules['itinerary.*.destination'] = 'nullable|string|max:255';
                }
                break;

            case 'activity':
                $rules += [
                    'destination_id' => [
                        'required',
                        'exists:destinations,id',
                        function ($attribute, $value, $fail) use ($request) {
                            $destination = DB::table('destinations')->find($value);
                            if (!$destination || $destination->country_id != $request->country_id) {
                                $fail('La ville sélectionnée ne correspond pas au pays choisi.');
                            }
                        }
                    ],
                    'duration_hours' => 'required|numeric|min:0.5|max:24',
                    'max_travelers' => 'required|integer|min:1|max:100',
                    'min_travelers' => 'nullable|integer|min:1|max:100',
                    'equipment_included' => 'boolean',
                    'equipment_list' => 'nullable|array',
                    'equipment_list.*' => 'string|max:100',
                ];
                break;

            case 'custom':
                $rules += [
                    'pricing_description' => 'required|string|max:1000',
                    'duration' => 'nullable|integer|min:1',
                ];
                break;
        }

        $messages = [
            'offer_type.required' => 'Le type d\'offre est requis.',
            'title.required' => 'Le titre est requis.',
            'country_id.required' => 'Veuillez sélectionner un pays.',
            'destination_id.required' => 'Veuillez sélectionner une ville.',
            'languages.required' => 'Veuillez sélectionner au moins une langue.',
            'images.required' => 'Veuillez ajouter au moins 5 photos.',
            'images.min' => 'Un minimum de 5 photos est requis.',
            'images.max' => 'Un maximum de 20 photos est autorisé.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5MB.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Préparer les données de base
            $tripData = [
                'vendor_id' => $vendor->id,
                'offer_type' => $validated['offer_type'],
                'pricing_mode' => self::OFFER_TYPES[$offerType]['pricing_mode'],
                'title' => $validated['title'],
                'country_id' => $validated['country_id'],
                'destination_id' => $validated['destination_id'] ?? null,
                'travel_type_id' => $validated['travel_type_id'],
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'currency' => $validated['currency'] ?? 'EUR',
                'included' => array_filter($validated['included'] ?? []),
                'not_included' => array_filter($validated['not_included'] ?? []),
                'requirements' => $validated['requirements'],
                'status' => $validated['status'],
                'slug' => Str::slug($validated['title']) . '-' . uniqid(),
                'payment_online_required' => $request->boolean('payment_online_required', true),
                'free_booking_allowed' => $request->boolean('free_booking_allowed', false),
            ];

            // Ajouter les champs spécifiques selon le type
            switch ($offerType) {
                case 'accommodation':
                    $tripData['property_capacity'] = $validated['property_capacity'];
                    $tripData['bedrooms'] = $validated['bedrooms'];
                    $tripData['bathrooms'] = $validated['bathrooms'];
                    $tripData['min_nights'] = $validated['min_nights'];
                    break;

                case 'organized_trip':
                    $tripData['duration'] = $validated['duration'];
                    $tripData['max_travelers'] = $validated['max_travelers'];
                    $tripData['min_travelers'] = $validated['min_travelers'] ?? 1;
                    $tripData['physical_level'] = $validated['physical_level'];
                    $tripData['meal_plan'] = $validated['meal_plan'];
                    $tripData['meeting_point'] = $validated['meeting_point'];
                    $tripData['meeting_time'] = $validated['meeting_time'];
                    $tripData['meeting_address'] = $validated['meeting_address'];
                    $tripData['meeting_instructions'] = $validated['meeting_instructions'];
                    
                    if ($request->input('is_circuit')) {
                        $tripData['type'] = 'circuit';
                        $tripData['itinerary'] = $this->processItinerary($validated['itinerary'] ?? []);
                    } else {
                        $tripData['type'] = 'fixed';
                    }
                    break;

                case 'activity':
                    $tripData['duration_hours'] = $validated['duration_hours'];
                    $tripData['max_travelers'] = $validated['max_travelers'];
                    $tripData['min_travelers'] = $validated['min_travelers'] ?? 1;
                    $tripData['equipment_included'] = $validated['equipment_included'] ?? false;
                    $tripData['equipment_list'] = $validated['equipment_list'] ?? [];
                    break;

                case 'custom':
                    $tripData['pricing_description'] = $validated['pricing_description'];
                    $tripData['duration'] = $validated['duration'] ?? null;
                    break;
            }

            // Créer l'offre
            $trip = Trip::create($tripData);

            // Attacher les langues
            $trip->languages()->attach($validated['languages']);

            // Gérer l'upload des images avec légendes
            if ($request->hasFile('images')) {
                $images = [];
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('trips/' . $trip->id, 'public');
                    $images[] = [
                        'path' => $path,
                        'caption' => $validated['image_captions'][$index] ?? null,
                        'order' => $index
                    ];
                }
                $trip->update(['images' => $images]);
            }

            DB::commit();

            Log::info('Trip created successfully', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'offer_type' => $trip->offer_type,
                'pricing_mode' => $trip->pricing_mode,
                'title' => $trip->title,
                'country' => $trip->country->name ?? 'Unknown',
                'destination' => $trip->destination->name ?? 'N/A',
                'languages_count' => count($validated['languages']),
                'status' => $trip->status,
                'payment_online_required' => $trip->payment_online_required,
                'free_booking_allowed' => $trip->free_booking_allowed
            ]);

            // Message de succès adapté selon le type
            $successMessage = match($offerType) {
                'accommodation' => 'Hébergement créé avec succès ! Configurez maintenant vos disponibilités.',
                'organized_trip' => 'Séjour créé avec succès ! Configurez maintenant vos dates et tarifs.',
                'activity' => 'Activité créée avec succès ! Configurez maintenant vos créneaux horaires.',
                'custom' => 'Offre sur mesure créée avec succès ! Configurez maintenant vos options.',
                default => 'Offre créée avec succès !'
            };

            // Rediriger vers la gestion des disponibilités
            return redirect()->route('vendor.trips.availabilities.index', $trip)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Trip creation failed', [
                'vendor_id' => $vendor->id,
                'offer_type' => $offerType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création. Veuillez réessayer.');
        }
    }

    /**
     * Afficher une offre spécifique
     */
    public function show(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette offre.');
        }

        // Charger les relations et les disponibilités à venir
        $trip->load([
            'destination', 
            'travelType', 
            'vendor', 
            'languages', 
            'country',
            'availabilities' => function($query) {
                $query->upcoming()
                      ->orderBy('start_date')
                      ->limit(5);
            }
        ]);

        // Statistiques de l'offre
        $tripStats = [
            'views' => $trip->views_count ?? 0,
            'bookings' => $trip->bookings()->count(),
            'revenue' => $trip->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'available_spots' => $trip->availabilities()
                                     ->upcoming()
                                     ->sum(DB::raw('total_spots - booked_spots')),
            'upcoming_availabilities' => $trip->availabilities()->upcoming()->count(),
            'total_availabilities' => $trip->availabilities()->count(),
            'average_rating' => $trip->reviews()->avg('rating') ?? 0,
            'reviews_count' => $trip->reviews()->count()
        ];

        // Informations sur le type d'offre
        $offerTypeInfo = self::OFFER_TYPES[$trip->offer_type] ?? null;

        return view('vendor.trips.show', compact('trip', 'vendor', 'tripStats', 'offerTypeInfo'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette offre.');
        }

        // Charger les pays du vendor
        $countries = $vendor->countries()->orderBy('name')->get();
        $destinations = $countries;
        
        // Vérifier les destinations
        $canAddDestinations = $vendor->canAddMoreDestinations();
        $destinationMessage = null;
        
        if (!$canAddDestinations) {
            $destinationMessage = 'Limite de destinations atteinte. Pour ajouter d\'autres pays, mettez à jour votre abonnement.';
        }
        
        $serviceCategories = $vendor->serviceCategories;
        $serviceAttributes = $vendor->serviceAttributes;
        $travelTypes = TravelType::orderBy('name')->get();
        
        // Charger les langues
        $languages = Language::active()
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->get();
        
        $popularLanguages = Language::active()
                                   ->popular()
                                   ->orderBy('sort_order')
                                   ->get();

        // Préparer les données JSON
        $languagesJson = $languages->map(function($lang) {
            return [
                'id' => $lang->id,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'region' => $lang->region
            ];
        })->toJson();
        
        // Charger les langues actuelles de l'offre
        $trip->load('languages');
        $selectedLanguagesJson = json_encode($trip->languages->pluck('id')->toArray());

        // Informations sur le type d'offre
        $offerTypeInfo = self::OFFER_TYPES[$trip->offer_type] ?? null;

        // Statistiques
        $stats = [
            'trips_remaining' => $vendor->remaining_trips,
            'total_trips' => $vendor->trips()->count(),
            'max_trips' => $vendor->max_trips,
            'destinations_count' => $destinations->count(),
            'destinations_remaining' => $vendor->remaining_destinations,
            'services_count' => $serviceCategories->count(),
            'availabilities_count' => $trip->availabilities()->count(),
            'upcoming_availabilities' => $trip->availabilities()->upcoming()->count()
        ];

        return view('vendor.trips.edit', compact(
            'trip', 
            'destinations', 
            'countries',
            'travelTypes', 
            'vendor',
            'serviceCategories',
            'serviceAttributes',
            'languages',
            'popularLanguages',
            'languagesJson',
            'selectedLanguagesJson',
            'offerTypeInfo',
            'stats',
            'canAddDestinations',
            'destinationMessage'
        ));
    }

    /**
     * Mettre à jour une offre
     */
    public function update(Request $request, Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette offre.');
        }

        $offerType = $trip->offer_type;

        // Règles de validation de base (similaires à store mais sans offer_type)
        $rules = [
            'title' => 'required|string|max:255',
            'country_id' => [
                'required',
                'exists:countries,id',
                function ($attribute, $value, $fail) use ($vendor) {
                    if (!$vendor->countries()->where('countries.id', $value)->exists()) {
                        $fail('Vous ne pouvez modifier une offre que dans vos pays autorisés.');
                    }
                }
            ],
            'travel_type_id' => 'required|exists:travel_types,id',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:EUR,USD,GBP',
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:languages,id',
            'images' => 'nullable|array|max:20',
            'images.*' => 'image|mimes:' . implode(',', config('uploads.allowed_extensions.images')) . '|max:' . config('uploads.max_sizes.image'),
            'image_captions' => 'nullable|array',
            'image_captions.*' => 'nullable|string|max:150',
            'existing_captions' => 'nullable|array',
            'existing_captions.*' => 'nullable|string|max:150',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
            'status' => 'required|in:draft,active,inactive',
            'included' => 'nullable|array',
            'included.*' => 'string|max:255',
            'not_included' => 'nullable|array',
            'not_included.*' => 'string|max:255',
            'requirements' => 'nullable|string|max:2000',
            'payment_online_required' => 'boolean',
            'free_booking_allowed' => 'boolean',
        ];

        // Ajouter les règles spécifiques selon le type
        switch ($offerType) {
            case 'accommodation':
                $rules += [
                    'destination_id' => [
                        'required',
                        'exists:destinations,id',
                        function ($attribute, $value, $fail) use ($request) {
                            $destination = DB::table('destinations')->find($value);
                            if (!$destination || $destination->country_id != $request->country_id) {
                                $fail('La ville sélectionnée ne correspond pas au pays choisi.');
                            }
                        }
                    ],
                    'property_capacity' => 'required|integer|min:1|max:50',
                    'bedrooms' => 'required|integer|min:0|max:20',
                    'bathrooms' => 'required|integer|min:0|max:10',
                    'min_nights' => 'required|integer|min:1|max:30',
                ];
                break;

            case 'organized_trip':
                $rules += [
                    'duration' => 'required|integer|min:1|max:365',
                    'max_travelers' => 'required|integer|min:1|max:50',
                    'min_travelers' => 'nullable|integer|min:1|max:50',
                    'physical_level' => 'required|in:easy,moderate,difficult,expert',
                    'meal_plan' => 'required|in:none,breakfast,half_board,full_board,all_inclusive',
                    'meeting_point' => 'nullable|string|max:255',
                    'meeting_time' => 'nullable|date_format:H:i',
                    'meeting_address' => 'nullable|string|max:500',
                    'meeting_instructions' => 'nullable|string|max:1000',
                ];
                
                if ($trip->type === 'circuit') {
                    $rules['itinerary'] = 'required|array|min:1';
                    $rules['itinerary.*.title'] = 'required|string|max:255';
                    $rules['itinerary.*.description'] = 'required|string|max:2000';
                    $rules['itinerary.*.destination'] = 'nullable|string|max:255';
                }
                break;

            case 'activity':
                $rules += [
                    'destination_id' => [
                        'required',
                        'exists:destinations,id',
                        function ($attribute, $value, $fail) use ($request) {
                            $destination = DB::table('destinations')->find($value);
                            if (!$destination || $destination->country_id != $request->country_id) {
                                $fail('La ville sélectionnée ne correspond pas au pays choisi.');
                            }
                        }
                    ],
                    'duration_hours' => 'required|numeric|min:0.5|max:24',
                    'max_travelers' => 'required|integer|min:1|max:100',
                    'min_travelers' => 'nullable|integer|min:1|max:100',
                    'equipment_included' => 'boolean',
                    'equipment_list' => 'nullable|array',
                    'equipment_list.*' => 'string|max:100',
                ];
                break;

            case 'custom':
                $rules += [
                    'pricing_description' => 'required|string|max:1000',
                    'duration' => 'nullable|integer|min:1',
                ];
                break;
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Préparer les données de mise à jour
            $updateData = [
                'title' => $validated['title'],
                'country_id' => $validated['country_id'],
                'destination_id' => $validated['destination_id'] ?? null,
                'travel_type_id' => $validated['travel_type_id'],
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'currency' => $validated['currency'] ?? 'EUR',
                'included' => array_filter($validated['included'] ?? []),
                'not_included' => array_filter($validated['not_included'] ?? []),
                'requirements' => $validated['requirements'],
                'status' => $validated['status'],
                'slug' => Str::slug($validated['title']) . '-' . $trip->id,
                'payment_online_required' => $request->boolean('payment_online_required', true),
                'free_booking_allowed' => $request->boolean('free_booking_allowed', false),
            ];

            // Ajouter les champs spécifiques selon le type
            switch ($offerType) {
                case 'accommodation':
                    $updateData['property_capacity'] = $validated['property_capacity'];
                    $updateData['bedrooms'] = $validated['bedrooms'];
                    $updateData['bathrooms'] = $validated['bathrooms'];
                    $updateData['min_nights'] = $validated['min_nights'];
                    break;

                case 'organized_trip':
                    $updateData['duration'] = $validated['duration'];
                    $updateData['max_travelers'] = $validated['max_travelers'];
                    $updateData['min_travelers'] = $validated['min_travelers'] ?? 1;
                    $updateData['physical_level'] = $validated['physical_level'];
                    $updateData['meal_plan'] = $validated['meal_plan'];
                    $updateData['meeting_point'] = $validated['meeting_point'];
                    $updateData['meeting_time'] = $validated['meeting_time'];
                    $updateData['meeting_address'] = $validated['meeting_address'];
                    $updateData['meeting_instructions'] = $validated['meeting_instructions'];
                    
                    if ($trip->type === 'circuit') {
                        $updateData['itinerary'] = $this->processItinerary($validated['itinerary'] ?? []);
                    }
                    break;

                case 'activity':
                    $updateData['duration_hours'] = $validated['duration_hours'];
                    $updateData['max_travelers'] = $validated['max_travelers'];
                    $updateData['min_travelers'] = $validated['min_travelers'] ?? 1;
                    $updateData['equipment_included'] = $validated['equipment_included'] ?? false;
                    $updateData['equipment_list'] = $validated['equipment_list'] ?? [];
                    break;

                case 'custom':
                    $updateData['pricing_description'] = $validated['pricing_description'];
                    $updateData['duration'] = $validated['duration'] ?? null;
                    break;
            }

            // Mettre à jour l'offre
            $trip->update($updateData);

            // Synchroniser les langues
            $trip->languages()->sync($validated['languages']);

            // Gérer les images
            $currentImages = $trip->images ?? [];
            
            // Mettre à jour les légendes des images existantes
            if (!empty($validated['existing_captions'])) {
                foreach ($currentImages as $index => &$image) {
                    if (isset($validated['existing_captions'][$image['path']])) {
                        $image['caption'] = $validated['existing_captions'][$image['path']];
                    }
                }
            }
            
            // Supprimer les images marquées pour suppression
            if (!empty($validated['remove_images'])) {
                $remainingImages = [];
                
                foreach ($currentImages as $image) {
                    if (!in_array($image['path'], $validated['remove_images'])) {
                        $remainingImages[] = $image;
                    } else {
                        Storage::disk('public')->delete($image['path']);
                    }
                }
                
                $currentImages = $remainingImages;
            }

            // Ajouter les nouvelles images
            if ($request->hasFile('images')) {
                $startIndex = count($currentImages);
                
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('trips/' . $trip->id, 'public');
                    $currentImages[] = [
                        'path' => $path,
                        'caption' => $validated['image_captions'][$index] ?? null,
                        'order' => $startIndex + $index
                    ];
                }
            }
            
            // Limiter à 20 images maximum et réordonner
            $currentImages = array_slice($currentImages, 0, 20);
            foreach ($currentImages as $index => &$image) {
                $image['order'] = $index;
            }
            
            $trip->update(['images' => $currentImages]);

            DB::commit();

            Log::info('Trip updated successfully', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'offer_type' => $trip->offer_type,
                'languages_count' => count($validated['languages']),
                'status' => $trip->status,
                'payment_online_required' => $trip->payment_online_required,
                'free_booking_allowed' => $trip->free_booking_allowed
            ]);

            return redirect()->route('vendor.trips.show', $trip)
                ->with('success', 'Offre mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Trip update failed', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Supprimer une offre
     */
    public function destroy(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette offre.');
        }

        // Vérifier s'il y a des réservations actives
        if ($trip->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return back()->with('error', 'Impossible de supprimer une offre avec des réservations actives.');
        }

        try {
            // Supprimer les images associées
            if ($trip->images) {
                foreach ($trip->images as $image) {
                    Storage::disk('public')->delete($image['path']);
                }
            }

            // Les langues et disponibilités seront automatiquement détachées grâce à onDelete('cascade')
            $trip->delete();

            Log::info('Trip deleted successfully', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'offer_type' => $trip->offer_type
            ]);

            return redirect()->route('vendor.trips.index')
                ->with('success', 'Offre supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Trip deletion failed', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Impossible de supprimer l\'offre.');
        }
    }

    /**
     * Changer le statut d'une offre (actif/inactif)
     */
    public function toggleStatus(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette offre.');
        }

        // Vérifier qu'il y a des disponibilités avant d'activer
        if ($trip->status !== 'active' && $trip->availabilities()->upcoming()->count() === 0) {
            return back()->with('error', 'Vous devez configurer au moins une disponibilité avant d\'activer cette offre.');
        }

        $newStatus = $trip->status === 'active' ? 'inactive' : 'active';
        $trip->update(['status' => $newStatus]);

        Log::info('Trip status toggled', [
            'trip_id' => $trip->id,
            'vendor_id' => $vendor->id,
            'old_status' => $trip->status,
            'new_status' => $newStatus
        ]);

        $message = $newStatus === 'active' 
            ? 'Offre activée avec succès.' 
            : 'Offre désactivée avec succès.';

        return back()->with('success', $message);
    }

    /**
     * Dupliquer une offre
     */
    public function duplicate(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à dupliquer cette offre.');
        }

        // Vérifier les limites du plan
        if (!$vendor->canCreateMoreTrips()) {
            return back()->with('error', 'Vous avez atteint la limite d\'offres pour votre plan.');
        }

        try {
            DB::beginTransaction();

            // Dupliquer l'offre
            $duplicatedTrip = $trip->replicate();
            $duplicatedTrip->title = $trip->title . ' (Copie)';
            $duplicatedTrip->slug = Str::slug($duplicatedTrip->title) . '-' . uniqid();
            $duplicatedTrip->status = 'draft';
            $duplicatedTrip->created_at = now();
            $duplicatedTrip->updated_at = now();
            $duplicatedTrip->save();

            // Dupliquer les langues
            $languages = $trip->languages->pluck('id')->toArray();
            if (!empty($languages)) {
                $duplicatedTrip->languages()->attach($languages);
            }

            // Dupliquer les images (copier les fichiers)
            if ($trip->images) {
                $newImages = [];
                foreach ($trip->images as $image) {
                    $oldPath = $image['path'];
                    $newPath = 'trips/' . $duplicatedTrip->id . '/' . basename($oldPath);
                    
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->copy($oldPath, $newPath);
                        $newImages[] = [
                            'path' => $newPath,
                            'caption' => $image['caption'] ?? null,
                            'order' => $image['order'] ?? 0
                        ];
                    }
                }
                $duplicatedTrip->update(['images' => $newImages]);
            }

            // NE PAS dupliquer les disponibilités - le vendor devra les créer manuellement

            DB::commit();

            Log::info('Trip duplicated successfully', [
                'original_trip_id' => $trip->id,
                'duplicated_trip_id' => $duplicatedTrip->id,
                'vendor_id' => $vendor->id,
                'offer_type' => $trip->offer_type
            ]);

            return redirect()->route('vendor.trips.edit', $duplicatedTrip)
                ->with('success', 'Offre dupliquée avec succès ! Vous pouvez maintenant la modifier et configurer ses disponibilités.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Trip duplication failed', [
                'trip_id' => $trip->id,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Impossible de dupliquer l\'offre.');
        }
    }

    /**
     * Traiter les données de l'itinéraire
     */
    private function processItinerary(array $itinerary): array
    {
        return array_values(array_filter(array_map(function($day) {
            // Nettoyer et valider chaque jour
            if (empty($day['title']) && empty($day['description'])) {
                return null;
            }
            
            return [
                'title' => $day['title'] ?? '',
                'description' => $day['description'] ?? '',
                'destination' => $day['destination'] ?? ''
            ];
        }, $itinerary)));
    }

    /**
     * Aperçu d'une offre (preview)
     */
    public function preview(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403);
        }

        $trip->load(['destination', 'travelType', 'vendor', 'languages', 'country', 'availabilities' => function($q) {
            $q->upcoming()->orderBy('start_date');
        }]);

        // Charger les statistiques et données nécessaires
        $availabilities = $trip->availabilities()->upcoming()->available()->limit(10)->get();
        $reviewStats = [
            'average' => $trip->rating ?? 0,
            'count' => $trip->reviews_count ?? 0
        ];
        $similarTrips = Trip::where('offer_type', $trip->offer_type)
            ->where('destination_id', $trip->destination_id)
            ->where('id', '!=', $trip->id)
            ->active()
            ->limit(3)
            ->get();

        return view('trips.show', [
            'trip' => $trip,
            'availabilities' => $availabilities,
            'reviewStats' => $reviewStats,
            'similarTrips' => $similarTrips,
            'preview' => true
        ]);
    }

    /**
     * Promouvoir une offre (page de promotion)
     */
    public function promote(Trip $trip)
    {
        $vendor = Auth::user()->vendor;

        // Vérifier que l'offre appartient au vendor
        if ($trip->vendor_id !== $vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à promouvoir cette offre.');
        }

        // Options de promotion disponibles
        $promotionOptions = [
            'featured' => [
                'name' => 'Mise en avant',
                'description' => 'Votre offre apparaîtra en premier dans les résultats de recherche',
                'duration' => '7 jours',
                'price' => 29.99
            ],
            'homepage' => [
                'name' => 'Page d\'accueil',
                'description' => 'Votre offre sera affichée sur la page d\'accueil',
                'duration' => '3 jours',
                'price' => 49.99
            ],
            'newsletter' => [
                'name' => 'Newsletter',
                'description' => 'Votre offre sera incluse dans notre newsletter hebdomadaire',
                'duration' => '1 envoi',
                'price' => 19.99
            ]
        ];

        return view('vendor.trips.promote', compact('trip', 'promotionOptions'));
    }

    /**
     * Récupérer les villes d'un pays pour le select dynamique
     */
    public function getCities($countryId)
    {
        try {
            // Récupérer les villes du pays depuis la table destinations
            $cities = DB::table('destinations')
                ->where('country_id', $countryId)
                ->where('active', true)
                ->where('type', 'city')
                ->orderBy('name')
                ->select('id', 'name')
                ->get();

            return response()->json([
                'success' => true,
                'cities' => $cities
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching cities', [
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des villes',
                'cities' => []
            ], 500);
        }
    }
}