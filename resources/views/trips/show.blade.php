@extends('layouts.public')

@section('title', $trip->title . ' - ' . $trip->destination->name)

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section - Avec image de couverture du voyage -->
    <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-50 z-10"></div>
            <picture>
                @if($trip->images && count($trip->images) > 0)
                <img 
                    width="1600" 
                    height="600"
                    fetchpriority="high"
                    src="{{ Storage::url($trip->images[0]['path']) }}" 
                    alt="{{ $trip->title }}" 
                    class="w-full h-full object-cover animate-slow-zoom" 
                />
                @else
                <img 
                    width="1600" 
                    height="600"
                    fetchpriority="high"
                    src="/api/placeholder/1600/600?text={{ urlencode($trip->title) }}" 
                    alt="{{ $trip->title }}" 
                    class="w-full h-full object-cover animate-slow-zoom" 
                />
                @endif
            </picture>
        </div>
        <div class="relative z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <a href="{{ route('destinations.index') }}" class="inline-flex items-center text-white/80 hover:text-white text-sm font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Destinations
                    </a>
                    <span class="text-white/60">/</span>
                    <a href="{{ route('destinations.show', $trip->destination->slug) }}" class="inline-flex items-center text-white/80 hover:text-white text-sm font-medium transition-colors">
                        {{ $trip->destination->name }}
                    </a>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold text-white">
                    {{ $trip->title }}
                </h1>
                
                <!-- Badge et ratings -->
                <div class="flex flex-wrap items-center gap-3 mt-4">
                    @if($trip->featured)
                    <x-badge variant="accent" style="solid" size="sm">
                        En vedette
                    </x-badge>
                    @endif

                    <!-- Badge type d'offre -->
                    <x-badge variant="gray" style="solid" size="sm" class="!bg-white/20 !text-white">
                        {{ $trip->offer_type_label ?? $trip->type_text }}
                    </x-badge>
                    
                    <div class="flex items-center gap-1">
                        <x-rating-stars :rating="$trip->rating" size="md" color="text-yellow-400" emptyColor="text-white/40" />
                        <span class="text-white/90">{{ $trip->rating }} ({{ $trip->reviews_count ?? 0 }} avis)</span>
                    </div>
                    
                    @if($trip->travelType)
                    <span class="bg-primary/20 text-white text-xs px-2 py-1 rounded">{{ $trip->travelType->name }}</span>
                    @endif
                    
                    <!-- Durée adaptée selon le type -->
                    <span class="inline-flex items-center text-white/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $trip->duration_formatted }}
                    </span>
                </div>
                
                <p class="mt-6 text-xl text-white/90">
                    {{ $trip->short_description }}
                </p>
                
                <!-- CTA Buttons adaptés -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="#reservation" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none transition-colors">
                        {{ $trip->booking_button_text }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </a>
                    <a href="#{{ $trip->type === 'circuit' ? 'itineraire' : 'localisation' }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white/10 focus:outline-none transition-colors">
                        {{ $trip->type === 'circuit' ? "Voir l'itinéraire" : 'Voir la localisation' }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Vagues en bas du hero -->
        <div class="absolute bottom-0 left-0 right-0 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 140" class="w-full h-auto">
                <path 
                    fill="rgb(247, 250, 252)" 
                    fill-opacity="1" 
                    d="M0,96L48,90.7C96,85,192,75,288,80C384,85,480,107,576,106.7C672,107,768,85,864,74.7C960,64,1056,64,1152,69.3C1248,75,1344,85,1392,90.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
                />
            </svg>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Colonne principale -->
            <div class="lg:col-span-2">
                <!-- Informations du voyage/activité/hébergement -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="description">
                    <h2 class="text-2xl font-bold text-text-primary mb-6">
                        @if($trip->isActivity())
                            À propos de cette activité
                        @elseif($trip->isAccommodation())
                            À propos de cet hébergement
                        @else
                            À propos de ce {{ strtolower($trip->type_text) }}
                        @endif
                    </h2>
                    
                    <!-- Points clés adaptés selon le type -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <!-- Durée adaptée -->
                        <x-info-card
                            icon="clock"
                            :label="$trip->duration_label"
                            :value="$trip->duration_formatted"
                        />
                        
                        <!-- Capacité adaptée -->
                        <x-info-card
                            icon="users"
                            :label="$trip->isAccommodation() ? 'Capacité' : 'Taille du groupe'"
                            :value="$trip->capacity_text"
                        />
                        
                        @if($trip->physical_level)
                        <x-info-card
                            icon="activity"
                            label="Niveau physique"
                            :value="$trip->physical_level_text"
                        />
                        @endif
                        
                        <!-- Type d'offre -->
                        <x-info-card
                            icon="tag"
                            label="Type d'offre"
                            :value="$trip->offer_type_label ?? $trip->type_text"
                        />
                        
                        <!-- Informations spécifiques pour hébergements -->
                        @if($trip->isAccommodation() && $trip->bedrooms)
                        <x-info-card
                            icon="bed"
                            label="Chambres"
                            :value="$trip->bedrooms . ' ' . Str::plural('chambre', $trip->bedrooms)"
                        />
                        @endif
                        
                        @if($trip->isAccommodation() && $trip->bathrooms)
                        <x-info-card
                            icon="droplet"
                            label="Salles de bain"
                            :value="$trip->bathrooms"
                        />
                        @endif
                    </div>
                    
                    <div class="prose max-w-none text-text-secondary">
                        {!! nl2br(e($trip->description)) !!}
                    </div>
                    
                    <!-- Ce qui est inclus / non inclus -->
                    @if(($trip->included && count($trip->included) > 0) || ($trip->not_included && count($trip->not_included) > 0))
                    <div class="mt-8 space-y-6">
                        <!-- Ce qui est inclus -->
                        @if($trip->included && count($trip->included) > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-text-primary mb-4">Ce qui est inclus</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($trip->included as $item)
                                <x-checkmark-item :text="$item" />
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Ce qui n'est pas inclus -->
                        @if($trip->not_included && count($trip->not_included) > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-text-primary mb-4">Ce qui n'est pas inclus</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($trip->not_included as $item)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-text-secondary">{{ $item }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Équipement pour les activités -->
                    @if($trip->isActivity() && $trip->equipment_included)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-text-primary mb-4">Équipement fourni</h3>
                        @if($trip->equipment_list && count($trip->equipment_list) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($trip->equipment_list as $equipment)
                            <x-checkmark-item :text="$equipment" />
                            @endforeach
                        </div>
                        @else
                        <p class="text-text-secondary">Tout l'équipement nécessaire est fourni.</p>
                        @endif
                    </div>
                    @endif
                </div>
                
                <!-- Dates de départ disponibles / Créneaux horaires pour activités -->
                @if($availabilities && count($availabilities) > 0)
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="dates">
                    <h2 class="text-2xl font-bold text-text-primary mb-6">
                        @if($trip->isActivity())
                            Prochains créneaux disponibles
                        @else
                            Prochaines disponibilités
                        @endif
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($availabilities as $availability)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow 
                            {{ $availability->available_spots > 0 ? 'border-gray-200' : 'border-red-200 bg-red-50' }}">
                            
                            <!-- Dates / Horaires -->
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium">
                                    @if($trip->isActivity())
                                        {{ $availability->start_date->format('d/m/Y H:i') }}
                                    @else
                                        {{ $availability->start_date->format('d/m/Y') }} - {{ $availability->end_date->format('d/m/Y') }}
                                    @endif
                                </span>
                            </div>

                            <!-- Durée -->
                            <div class="text-sm text-gray-600 mb-2">
                                ({{ $trip->duration_formatted }})
                            </div>

                            <!-- Places disponibles -->
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 
                                    {{ $availability->available_spots > 0 ? 'text-green-600' : 'text-red-600' }}" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="{{ $availability->available_spots > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    @if($availability->available_spots > 0)
                                        {{ $availability->available_spots }} {{ $availability->available_spots > 1 ? 'places disponibles' : 'place disponible' }}
                                    @else
                                        Complet
                                    @endif
                                </span>
                            </div>

                            <!-- Prix -->
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    @if($availability->discount_percentage > 0 && (!$availability->discount_ends_at || $availability->discount_ends_at > now()))
                                        <span class="text-sm text-gray-500 line-through">{{ number_format($availability->adult_price, 0, ',', ' ') }}€</span>
                                        <span class="text-lg font-bold text-primary ml-2">
                                            {{ number_format($availability->adult_price * (1 - $availability->discount_percentage / 100), 0, ',', ' ') }}€
                                        </span>
                                        <span class="text-xs text-red-600 ml-1">-{{ $availability->discount_percentage }}%</span>
                                    @else
                                        <span class="text-lg font-bold text-primary">{{ number_format($availability->adult_price, 0, ',', ' ') }}€</span>
                                    @endif
                                    <span class="text-sm text-gray-500">{{ $trip->price_unit }}</span>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($availability->is_guaranteed)
                                <x-badge variant="success" style="soft" size="sm">
                                    @if($trip->isActivity())
                                        Séance garantie
                                    @else
                                        Départ garanti
                                    @endif
                                </x-badge>
                                @endif

                                @if($availability->available_spots > 0 && $availability->available_spots <= 5)
                                <x-badge variant="warning" style="soft" size="sm">
                                    Dernières places
                                </x-badge>
                                @endif
                            </div>

                            <!-- Bouton réserver -->
                            @if($availability->available_spots > 0)
                            <a href="{{ route('trips.booking.form', [$trip->slug, 'availability_id' => $availability->id]) }}" 
                               class="block w-full text-center bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark transition-colors">
                                Réserver
                            </a>
                            @else
                            <button disabled class="block w-full text-center bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed">
                                Complet
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @if($trip->availabilities()->upcoming()->available()->count() > 10)
                    <div class="mt-6 text-center">
                        <a href="#" class="text-primary hover:text-primary-dark" onclick="loadMoreAvailabilities()">
                            Voir plus de dates →
                        </a>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Galerie d'images -->
                @if($trip->images && count($trip->images) > 0)
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="galerie">
                    <h2 class="text-2xl font-bold text-text-primary mb-6">Galerie photos</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($trip->images as $index => $image)
                        <div class="relative group cursor-pointer" onclick="openLightbox({{ $index }})">
                            <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                                <img 
                                    src="{{ Storage::url($image['path']) }}" 
                                    alt="{{ $image['caption'] ?? 'Photo ' . ($index + 1) . ' - ' . $trip->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                />
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                            @if($image['caption'])
                            <p class="mt-2 text-sm text-text-secondary">{{ $image['caption'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Carte et itinéraire/localisation -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="{{ $trip->type === 'circuit' ? 'itineraire' : 'localisation' }}">
                    <h2 class="text-2xl font-bold text-text-primary mb-6">
                        {{ $trip->type === 'circuit' ? 'Itinéraire du voyage' : 'Localisation' }}
                    </h2>
                    
                    <!-- Carte -->
                    <div class="mb-8">
                        <div class="rounded-lg overflow-hidden h-80">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>
                    
                    @if($trip->type === 'circuit')
                        <!-- Étapes du voyage pour les circuits -->
                        <div class="space-y-6">
                            @if($trip->itinerary && count($trip->itinerary) > 0)
                                @foreach($trip->itinerary as $index => $stage)
                                <div class="border-l-4 border-primary pl-6 pb-6 relative">
                                    <div class="absolute left-0 top-0 transform -translate-x-1/2 -translate-y-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <h3 class="text-lg font-bold text-text-primary">{{ $stage['title'] ?? 'Étape ' . ($index + 1) }}</h3>
                                    @if(isset($stage['duration']))
                                    <p class="text-sm text-primary mb-2">{{ $stage['duration'] }}</p>
                                    @endif
                                    @if(isset($stage['description']))
                                    <div class="prose prose-sm text-text-secondary">
                                        {!! nl2br(e($stage['description'])) !!}
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <p class="text-text-secondary">L'itinéraire détaillé sera bientôt disponible.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Informations sur la destination -->
                        @if($trip->destination)
                        <div class="bg-bg-alt rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-text-primary mb-3">À propos de {{ $trip->destination->name }}</h3>
                            @if($trip->destination->description)
                            <p class="text-text-secondary text-sm mb-4">{{ $trip->destination->description }}</p>
                            @endif
                        </div>
                        @endif
                    @endif
                </div>
                
                <!-- Avis des voyageurs -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="avis">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-text-primary">
                            @if($trip->isActivity())
                                Avis des participants
                            @elseif($trip->isAccommodation())
                                Avis des clients
                            @else
                                Avis des voyageurs
                            @endif
                        </h2>
                        <x-rating-stars
                            :rating="$reviewStats['average']"
                            size="md"
                            :count="$reviewStats['count']"
                        />
                    </div>
                    
                    @if(!isset($reviews) || count($reviews) == 0)
                    <div class="text-center py-10 bg-bg-alt rounded-lg">
                        <p class="text-text-secondary">
                            @if($trip->isActivity())
                                Aucun avis n'a encore été déposé pour cette activité.
                            @elseif($trip->isAccommodation())
                                Aucun avis n'a encore été déposé pour cet hébergement.
                            @else
                                Aucun avis n'a encore été déposé pour ce voyage.
                            @endif
                        </p>
                        <p class="text-text-secondary mt-2">Soyez le premier à partager votre expérience !</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale - Sticky -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <!-- Réservation -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="reservation">
                        <h2 class="text-xl font-bold text-text-primary mb-4">{{ $trip->booking_button_text }}</h2>
                        
                        <div class="mb-6">
                            <div class="mb-2">
                                <div class="text-sm text-text-secondary">{{ $trip->price_label }}</div>
                                <div class="text-2xl font-bold text-primary">{{ number_format($trip->price, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-text-secondary">{{ $trip->price_unit }}</div>
                            </div>
                            
                            <div class="w-full h-px bg-border my-4"></div>
                            
                            <div class="space-y-2">
                                @if($availabilities && count($availabilities) > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary">Disponibilités</span>
                                    <span class="text-text-primary font-medium">{{ count($availabilities) }} dates</span>
                                </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary">{{ $trip->duration_label }}</span>
                                    <span class="text-text-primary font-medium">{{ $trip->duration_formatted }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary">
                                        @if($trip->isAccommodation())
                                            Capacité
                                        @else
                                            Taille du groupe
                                        @endif
                                    </span>
                                    <span class="text-text-primary font-medium">{{ $trip->capacity_text }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bouton contacter l'organisateur -->
                        <div class="mb-4">
                            @auth
                                @if(Auth::user()->role === 'customer')
                                    <button type="button" onclick="openMessageModal()" 
                                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-primary text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-primary/5 focus:outline-none transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Contacter l'organisateur
                                    </button>
                                @elseif(Auth::user()->role === 'vendor')
                                    <div class="text-center p-3 bg-gray-50 rounded-md">
                                        <p class="text-sm text-gray-600">Vous ne pouvez pas contacter un autre organisateur</p>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ url()->current() }}" 
                                   class="w-full inline-flex items-center justify-center px-6 py-3 border border-primary text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-primary/5 focus:outline-none transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Contacter l'organisateur
                                </a>
                            @endauth
                        </div>
                        
                        <!-- Formulaire de réservation -->
                        @if($availabilities && count($availabilities) > 0)
                        <form action="{{ route('trips.booking.form', $trip->slug) }}" method="GET" class="space-y-4">
                            <div>
                                <label for="availability_id" class="block text-sm font-medium text-text-primary mb-1">
                                    @if($trip->isActivity())
                                        Choisir un créneau
                                    @else
                                        Date de départ
                                    @endif
                                </label>
                                <select id="availability_id" name="availability_id" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    @foreach($availabilities as $availability)
                                    <option value="{{ $availability->id }}">
                                        @if($trip->isActivity())
                                            {{ $availability->start_date->format('d/m/Y H:i') }}
                                        @else
                                            {{ $availability->start_date->format('d/m/Y') }} - {{ $availability->end_date->format('d/m/Y') }}
                                        @endif
                                        ({{ $availability->available_spots }} places restantes)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="travelers" class="block text-sm font-medium text-text-primary mb-1">
                                    @if($trip->isAccommodation())
                                        Nombre de personnes
                                    @elseif($trip->isActivity())
                                        Nombre de participants
                                    @else
                                        Nombre de voyageurs
                                    @endif
                                </label>
                                <select id="travelers" name="travelers" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2">
                                    @for($i = $trip->min_travelers ?? 1; $i <= min($trip->max_travelers, 10); $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'personnes' : 'personne' }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none transition-colors">
                                    Réserver maintenant
                                </button>
                            </div>
                        </form>
                        @else
                        <div class="text-center py-4">
                            <p class="text-text-secondary text-sm">
                                @if($trip->isActivity())
                                    Aucun créneau disponible pour le moment.
                                @else
                                    Aucune date de départ disponible pour le moment.
                                @endif
                            </p>
                            <a href="{{ route('contact') }}" class="mt-2 text-primary hover:text-primary-dark text-sm font-medium">
                                Contactez-nous pour plus d'informations
                            </a>
                        </div>
                        @endif
                        
                        <p class="text-xs text-text-secondary text-center mt-4">Aucun paiement n'est requis à cette étape</p>
                    </div>
                    
                    <!-- Organisateur -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-text-primary mb-4">À propos de l'organisateur</h2>
                        
                        <div class="flex items-center mb-4">
                            <div class="h-14 w-14 rounded-full overflow-hidden flex-shrink-0 bg-gray-100">
                                @if($trip->vendor->logo)
                                <img 
                                    src="{{ Storage::url($trip->vendor->logo) }}" 
                                    alt="{{ $trip->vendor->company_name }}" 
                                    class="h-full w-full object-cover"
                                />
                                @else
                                <div class="h-full w-full flex items-center justify-center text-primary font-bold text-lg">
                                    {{ substr($trip->vendor->company_name, 0, 2) }}
                                </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-text-primary">{{ $trip->vendor->company_name }}</h3>
                                @if($trip->vendor->rating)
                                <x-rating-stars
                                    :rating="$trip->vendor->rating"
                                    size="sm"
                                    :count="$trip->vendor->reviews_count ?? 0"
                                    :showValue="false"
                                />
                                @endif
                            </div>
                        </div>
                        
                        @if($trip->vendor->description)
                        <p class="text-text-secondary text-sm mb-4">{{ $trip->vendor->description }}</p>
                        @endif
                        
                        <a href="{{ route('vendors.show', $trip->vendor->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-primary text-sm font-medium rounded-md text-primary hover:bg-primary/5 focus:outline-none transition-colors">
                            @if($trip->isActivity())
                                Voir toutes les activités de cet organisateur
                            @elseif($trip->isAccommodation())
                                Voir tous les hébergements de cet organisateur
                            @else
                                Voir tous les voyages de cet organisateur
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offres similaires -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-text-primary mb-8">
                @if($trip->isActivity())
                    Activités similaires
                @elseif($trip->isAccommodation())
                    Hébergements similaires
                @else
                    Voyages similaires
                @endif
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($similarTrips as $similarTrip)
                <a href="{{ route('trips.show', $similarTrip->slug) }}" class="bg-white rounded-lg shadow-sm overflow-hidden card">
                    <div class="relative h-48 overflow-hidden">
                        @if($similarTrip->images && count($similarTrip->images) > 0)
                        <img 
                            src="{{ Storage::url($similarTrip->images[0]['path']) }}" 
                            alt="{{ $similarTrip->title }}" 
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                        />
                        @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Image non disponible</span>
                        </div>
                        @endif
                        <div class="absolute top-0 right-0 bg-primary/90 text-white text-xs font-bold px-3 py-1 m-3 rounded">
                            {{ $similarTrip->duration_formatted }}
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-text-primary mb-1">{{ $similarTrip->title }}</h3>
                                <p class="text-xs text-primary">{{ $similarTrip->destination->name }}</p>
                            </div>
                            @if($similarTrip->rating > 0)
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="ml-1 text-sm font-medium text-text-primary">{{ $similarTrip->rating }}</span>
                            </div>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-text-secondary line-clamp-2">{{ $similarTrip->short_description }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="text-xs text-text-secondary">
                                {{ $similarTrip->vendor->company_name }}
                            </div>
                            <div class="text-primary font-bold">
                                À partir de {{ number_format($similarTrip->price, 0, ',', ' ') }} €{{ $similarTrip->price_unit ? '/' . $similarTrip->price_unit : '' }}
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <p class="col-span-3 text-center text-text-secondary">
                    @if($trip->isActivity())
                        Aucune activité similaire trouvée.
                    @elseif($trip->isAccommodation())
                        Aucun hébergement similaire trouvé.
                    @else
                        Aucun voyage similaire trouvé.
                    @endif
                </p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CTA Final adapté -->
    <div class="bg-gradient-to-r from-primary to-primary-dark py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative z-10 text-center text-white">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full transform translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full transform -translate-x-1/3 translate-y-1/3"></div>
                
                <h2 class="text-2xl font-bold mb-4">{{ $trip->cta_text }}</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">{{ $trip->cta_description }}</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#reservation" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-white/90 focus:outline-none transition-colors">
                        {{ $trip->booking_button_text }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white/10 focus:outline-none transition-colors">
                        Nous contacter
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal premier message -->
@auth
@if(Auth::user()->role === 'customer')
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg max-w-md w-full p-6 m-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-text-primary">Contacter {{ $trip->vendor->company_name }}</h3>
            <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('customer.messages.send') }}" method="POST">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $trip->vendor->user_id }}">
            <input type="hidden" name="trip_id" value="{{ $trip->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Concernant</label>
                <input type="text" value="{{ $trip->title }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Votre message</label>
                <textarea name="content" 
                          rows="5" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                          placeholder="Bonjour, j'aimerais avoir des informations sur..."
                          required></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeMessageModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endauth

<!-- Lightbox pour les images (en dehors du conteneur principal) -->
@if($trip->images && count($trip->images) > 0)
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 hidden flex items-center justify-center p-4" onclick="closeLightbox()">
    <button class="absolute top-4 right-4 text-white hover:text-gray-300 z-10" onclick="closeLightbox()">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    <button class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10" onclick="previousImage(event)">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10" onclick="nextImage(event)">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
    <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full rounded-lg relative z-5" onclick="event.stopPropagation()">
    <p id="lightbox-caption" class="absolute bottom-4 left-0 right-0 text-center text-white text-lg px-4 z-10"></p>
</div>
@endif
@endsection

@push('styles')
<style>
    #map {
        position: relative;
        z-index: 1;
    }
    
    #lightbox {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1050;
        isolation: isolate;
    }
    
    @keyframes slow-zoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    
    .animate-slow-zoom {
        animation: slow-zoom 20s ease-in-out infinite alternate;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonctions pour le modal de message
    function openMessageModal() {
        document.getElementById('messageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMessageModal() {
        document.getElementById('messageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Fermer le modal en cliquant en dehors
    document.getElementById('messageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMessageModal();
        }
    });

    // Lightbox pour la galerie d'images - Fonctions globales
    let currentImageIndex = 0;
    const images = [
        @foreach($trip->images ?? [] as $image)
        {
            path: "{{ Storage::url($image['path']) }}",
            caption: "{{ $image['caption'] ?? '' }}"
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ];
    
    window.openLightbox = function(index) {
        currentImageIndex = index;
        updateLightboxImage();
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    window.closeLightbox = function() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function updateLightboxImage() {
        if (images.length > 0) {
            const image = images[currentImageIndex];
            document.getElementById('lightbox-image').src = image.path;
            document.getElementById('lightbox-caption').textContent = image.caption || '';
        }
    }
    
    window.nextImage = function(event) {
        event.stopPropagation();
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateLightboxImage();
    }
    
    window.previousImage = function(event) {
        event.stopPropagation();
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        updateLightboxImage();
    }
    
    // Navigation au clavier pour la lightbox
    document.addEventListener('keydown', function(event) {
        if (!document.getElementById('lightbox').classList.contains('hidden')) {
            if (event.key === 'Escape') {
                closeLightbox();
            } else if (event.key === 'ArrowRight') {
                nextImage(event);
            } else if (event.key === 'ArrowLeft') {
                previousImage(event);
            }
        }
    });
    
    // Fonction pour sélectionner une date de départ
    window.selectDeparture = function(availabilityId) {
        const selectElement = document.getElementById('availability_id');
        if (selectElement && availabilityId) {
            selectElement.value = availabilityId;
        }
        
        // Faire défiler jusqu'au formulaire de réservation
        document.getElementById('reservation').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Code pour la carte
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de la carte de l'itinéraire (avec OpenStreetMap + Leaflet)
        // On charge Leaflet de manière différée pour optimiser les performances
        const loadMap = function() {
            // Créer les liens CSS pour Leaflet
            const leafletCSS = document.createElement('link');
            leafletCSS.rel = 'stylesheet';
            leafletCSS.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
            document.head.appendChild(leafletCSS);
            
            // Charger le script Leaflet
            const leafletScript = document.createElement('script');
            leafletScript.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
            leafletScript.onload = initMap;
            document.body.appendChild(leafletScript);
        };
        
        // Fonction d'initialisation de la carte
        const initMap = function() {
            // Données de la destination depuis PHP/Blade
            const destinationData = {
                @if($trip->destination && $trip->destination->latitude && $trip->destination->longitude)
                lat: {{ $trip->destination->latitude }},
                lng: {{ $trip->destination->longitude }},
                name: "{{ $trip->destination->name }}"
                @else
                // Coordonnées par défaut si pas de données
                lat: -1.286389,
                lng: 36.817223,
                name: "{{ $trip->destination->name ?? 'Destination' }}"
                @endif
            };
            
            // Initialiser la carte centrée sur la destination
            const map = L.map('map').setView([destinationData.lat, destinationData.lng], 8);
            
            // Utiliser OpenStreetMap France pour avoir les labels en français
            L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            @if($trip->type === 'circuit' && $trip->itinerary && count($trip->itinerary) > 0)
            // Pour un circuit, afficher l'itinéraire
            const itinerary = [
                @foreach($trip->itinerary as $index => $stage)
                {
                    name: "{{ $stage['title'] ?? 'Étape ' . ($index + 1) }}",
                    lat: {{ $stage['latitude'] ?? $trip->destination->latitude ?? -1.286389 }},
                    lng: {{ $stage['longitude'] ?? $trip->destination->longitude ?? 36.817223 }},
                    days: "{{ $stage['duration'] ?? '' }}"
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ];
            
            // Créer un tableau de points pour la ligne d'itinéraire
            const routePoints = itinerary.map(point => [point.lat, point.lng]);
            
            // Dessiner la ligne d'itinéraire avec flèches de direction
            const routeLine = L.polyline(routePoints, {
                color: '#38B2AC', // Couleur primaire
                weight: 3,
                opacity: 0.8,
                dashArray: '5, 10', // Ligne pointillée pour indiquer un trajet
            }).addTo(map);
            
            // Ajouter des marqueurs personnalisés pour chaque étape
            itinerary.forEach((point, index) => {
                // Créer une icône personnalisée
                const customIcon = L.divIcon({
                    className: 'custom-map-marker',
                    html: `<div class="marker-circle" style="width:36px;height:36px;border-radius:50%;background-color:#38B2AC;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;box-shadow:0 2px 5px rgba(0,0,0,0.3);">${index + 1}</div>`,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20],
                    popupAnchor: [0, -20]
                });
                
                // Ajouter le marqueur avec popup
                L.marker([point.lat, point.lng], {icon: customIcon}).addTo(map)
                    .bindPopup(`
                        <strong>${point.name}</strong>
                        ${point.days ? `<p>${point.days}</p>` : ''}
                    `);
            });
            
            // Ajuster la vue pour montrer tous les points
            map.fitBounds(routePoints);
            
            @else
            // Pour un séjour fixe, juste un marqueur sur la destination
            const customIcon = L.divIcon({
                className: 'custom-map-marker',
                html: `<div class="marker-circle" style="width:40px;height:40px;border-radius:50%;background-color:#38B2AC;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;box-shadow:0 2px 5px rgba(0,0,0,0.3);"><svg style="width:24px;height:24px" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2M12,11.5C10.62,11.5 9.5,10.38 9.5,9C9.5,7.62 10.62,6.5 12,6.5C13.38,6.5 14.5,7.62 14.5,9C14.5,10.38 13.38,11.5 12,11.5Z" /></svg></div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [0, -20]
            });
            
            // Ajouter le marqueur pour la destination
            L.marker([destinationData.lat, destinationData.lng], {icon: customIcon}).addTo(map)
                .bindPopup(`
                    <strong>${destinationData.name}</strong>
                    <p>{{ $trip->title }}</p>
                `).openPopup();
            @endif
        };
        
        // Observer pour le chargement différé de la carte
        const mapObserver = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                loadMap();
                mapObserver.disconnect();
            }
        }, { rootMargin: '100px' });
        
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapObserver.observe(mapElement);
        }
        
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset - 100;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush