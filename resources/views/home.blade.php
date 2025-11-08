@extends('layouts.public')

@section('title', 'Accueil - Nomadie, votre plateforme d\'expériences authentiques')

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="Voyage" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/1600/800';this.onerror=null;">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="max-w-3xl space-y-6">
                <h1 class="text-4xl md:text-5xl font-bold text-white">Organisez et vivez des expériences authentiques</h1>
                <p class="text-xl text-white/90">Voyages, circuits, séjours, hébergements et activités uniques dans le monde entier. Réservez directement auprès d'organisateurs locaux experts.</p>
                <div class="pt-4 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="#experiences" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        Découvrir nos offres
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('vendor.register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors">
                        Devenir organisateur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <x-search-bar :destinations="$continents" variant="full" :showAdvanced="true" />

    <!-- Section Types d'Expériences -->
    <div id="experiences" class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-text-primary">Découvrez nos types d'expériences</h2>
                <p class="mt-4 text-lg text-text-secondary">Choisissez le type de voyage qui vous correspond</p>
            </div>

            <!-- Cartes des types d'offres - Version harmonisée -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <x-offer-type-card
                    type="accommodation"
                    :title="$offerTypeStats['accommodations']['label'] ?? 'Hébergements'"
                    :description="$offerTypeStats['accommodations']['description'] ?? 'Gîtes, villas et appartements de charme'"
                    :count="$offerTypeStats['accommodations']['count'] ?? 0"
                    :url="route('trips.index', ['offer_type' => 'accommodation'])"
                    iconColor="primary"
                />

                <x-offer-type-card
                    type="organized_trip"
                    :title="$offerTypeStats['organized_trips']['label'] ?? 'Séjours organisés'"
                    :description="$offerTypeStats['organized_trips']['description'] ?? 'Voyages tout compris avec guide'"
                    :count="$offerTypeStats['organized_trips']['count'] ?? 0"
                    :url="route('trips.index', ['offer_type' => 'organized_trip'])"
                    iconColor="primary"
                />

                <x-offer-type-card
                    type="activity"
                    :title="$offerTypeStats['activities']['label'] ?? 'Activités'"
                    :description="$offerTypeStats['activities']['description'] ?? 'Expériences et découvertes locales'"
                    :count="$offerTypeStats['activities']['count'] ?? 0"
                    :url="route('trips.index', ['offer_type' => 'activity'])"
                    iconColor="primary"
                />

                <x-offer-type-card
                    type="custom"
                    :title="$offerTypeStats['custom']['label'] ?? 'Sur mesure'"
                    :description="$offerTypeStats['custom']['description'] ?? 'Créez votre voyage personnalisé'"
                    :count="$offerTypeStats['custom']['count'] ?? 0"
                    :url="route('trips.index', ['offer_type' => 'custom'])"
                    iconColor="accent"
                />
            </div>

            <!-- Offres en promotion -->
            @if(isset($promotionalOffers) && $promotionalOffers->count() > 0)
            <div class="mb-16">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-text-primary">Offres en promotion</h3>
                    <p class="mt-2 text-text-secondary">Profitez de nos meilleures réductions</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($promotionalOffers->take(3) as $offer)
                        <x-trip-card :trip="$offer" :showDiscount="true" :featured="true" />
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tabs pour les dernières offres par type -->
            <div x-data="{ activeTab: 'accommodations' }">
                <div class="flex flex-wrap justify-center mb-8 gap-2">
                    <button @click="activeTab = 'accommodations'" :class="activeTab === 'accommodations' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-2 rounded-lg font-medium transition-colors">
                        Hébergements
                    </button>
                    <button @click="activeTab = 'organized_trips'" :class="activeTab === 'organized_trips' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-2 rounded-lg font-medium transition-colors">
                        Séjours organisés
                    </button>
                    <button @click="activeTab = 'activities'" :class="activeTab === 'activities' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-2 rounded-lg font-medium transition-colors">
                        Activités
                    </button>
                    <button @click="activeTab = 'custom'" :class="activeTab === 'custom' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-2 rounded-lg font-medium transition-colors">
                        Sur mesure
                    </button>
                </div>

                <!-- Contenu des tabs -->
                <!-- Hébergements -->
                <div x-show="activeTab === 'accommodations'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @if(isset($latestAccommodations))
                        @forelse($latestAccommodations as $accommodation)
                            <x-trip-card :trip="$accommodation" />
                        @empty
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucun hébergement disponible pour le moment
                        </div>
                        @endforelse
                    @else
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucun hébergement disponible pour le moment
                        </div>
                    @endif
                </div>

                <!-- Séjours organisés -->
                <div x-show="activeTab === 'organized_trips'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @if(isset($latestOrganizedTrips))
                        @forelse($latestOrganizedTrips as $trip)
                            <x-trip-card :trip="$trip" />
                        @empty
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucun séjour organisé disponible pour le moment
                        </div>
                        @endforelse
                    @else
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucun séjour organisé disponible pour le moment
                        </div>
                    @endif
                </div>

                <!-- Activités -->
                <div x-show="activeTab === 'activities'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @if(isset($latestActivities))
                        @forelse($latestActivities as $activity)
                            <x-trip-card :trip="$activity" />
                        @empty
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucune activité disponible pour le moment
                        </div>
                        @endforelse
                    @else
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucune activité disponible pour le moment
                        </div>
                    @endif
                </div>

                <!-- Sur mesure -->
                <div x-show="activeTab === 'custom'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @if(isset($latestCustomOffers))
                        @forelse($latestCustomOffers as $custom)
                            <x-trip-card :trip="$custom" />
                        @empty
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucune offre sur mesure disponible pour le moment
                        </div>
                        @endforelse
                    @else
                        <div class="col-span-4 text-center py-8 text-gray-500">
                            Aucune offre sur mesure disponible pour le moment
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Destinations -->
    <div id="destinations" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-text-primary">Destinations populaires</h2>
            <p class="mt-4 text-lg text-text-secondary">Découvrez nos destinations les plus appréciées par nos voyageurs</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredDestinations as $destination)
            <div class="rounded-lg overflow-hidden shadow-lg bg-white card">
                <div class="relative h-48 overflow-hidden">
                    @if($destination->image)
                        <img src="{{ asset($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" onerror="this.src='/api/placeholder/600/400?text={{ urlencode($destination->name) }}';this.onerror=null;">
                    @else
                        <img src="/api/placeholder/600/400?text={{ urlencode($destination->name) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                    @endif
                    @if($destination->popular)
                    <div class="absolute top-0 right-0 bg-accent/90 text-white text-xs font-bold px-3 py-1 m-3 rounded">
                        Populaire
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-text-primary">{{ $destination->name }}</h3>
                        @if($destination->average_rating > 0)
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-sm font-bold text-text-primary">{{ number_format($destination->average_rating, 1) }}</span>
                        </div>
                        @endif
                    </div>
                    <p class="text-text-secondary text-sm mb-4">
                        {{ Str::limit($destination->description, 120) ?: 'Découvrez cette destination unique et ses merveilles cachées.' }}
                    </p>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center text-sm text-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $destination->trips_count ?? 0 }} {{ Str::plural('offre', $destination->trips_count ?? 0) }}
                        </div>
                        @if($destination->slug)
                            <a href="{{ route('destinations.show', $destination->slug) }}" class="text-primary text-sm font-bold hover:text-primary-dark">
                                Découvrir →
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">Bientôt disponible</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <!-- Destinations par défaut si aucune en base -->
            @foreach(['République du Congo', 'France', 'Maroc'] as $index => $name)
            <div class="rounded-lg overflow-hidden shadow-lg bg-white card">
                <div class="relative h-48 overflow-hidden">
                    <img src="/api/placeholder/600/400?text={{ urlencode($name) }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                    @if($index === 0)
                    <div class="absolute top-0 right-0 bg-accent/90 text-white text-xs font-bold px-3 py-1 m-3 rounded">
                        Populaire
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-text-primary">{{ $name }}</h3>
                    </div>
                    <p class="text-text-secondary text-sm mb-4">
                        Découvrez cette destination unique et ses merveilles cachées.
                    </p>
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-text-secondary">
                            Bientôt disponible
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-6 py-3 border border-primary bg-white text-primary hover:bg-primary hover:text-white text-base font-medium rounded-md shadow-sm transition-colors">
                Voir toutes les destinations
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="bg-bg-alt py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-text-primary">Pourquoi choisir Nomadie</h2>
                <p class="mt-4 text-lg text-text-secondary">Nous révolutionnons la façon de voyager en connectant directement les voyageurs avec les experts locaux</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Expertise locale</h3>
                    <p class="text-text-secondary">Nos {{ $stats['total_vendors'] }} organisateurs sont des experts de leur région qui vous font découvrir les merveilles cachées et les expériences authentiques.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Voyages sécurisés</h3>
                    <p class="text-text-secondary">Nous vérifions tous nos organisateurs et offrons une garantie de satisfaction. Chaque partenaire est contrôlé et certifié pour votre tranquillité.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Expériences personnalisées</h3>
                    <p class="text-text-secondary">{{ $stats['total_trips'] }} offres dans {{ $stats['total_destinations'] }} destinations. Hébergements de charme, séjours organisés, activités locales ou voyages sur mesure : trouvez l'expérience qui vous ressemble.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Organizers -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-text-primary">Nos organisateurs</h2>
            <p class="mt-4 text-lg text-text-secondary">Les artisans de vos expériences authentiques</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            @php
                $vendorsToShow = $featuredVendors->take(10);
            @endphp
            @forelse($vendorsToShow as $vendor)
            <div class="bg-white rounded-lg shadow-md overflow-hidden card">
                <div class="relative h-40 overflow-hidden">
                    @if($vendor->logo)
                        <img src="{{ $vendor->logo_url }}" alt="{{ $vendor->company_name }}" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/400/300?text={{ urlencode($vendor->initials) }}';this.onerror=null;">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center">
                            <span class="text-3xl font-bold text-primary">{{ $vendor->initials }}</span>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <div class="mb-2">
                        <h3 class="text-base font-bold text-text-primary truncate">{{ $vendor->company_name }}</h3>
                        @if($vendor->average_rating > 0)
                        <div class="flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-xs font-bold text-text-primary">{{ $vendor->average_rating }}</span>
                        </div>
                        @endif
                    </div>
                    <p class="text-xs text-accent mb-2">{{ $vendor->specialty }}</p>
                    <p class="text-xs text-text-secondary mb-3 line-clamp-2">
                        {{ Str::limit($vendor->description, 60) ?: 'Expert local passionné.' }}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-text-secondary">{{ $vendor->trips_count }} offres</span>
                        @if($vendor->slug || $vendor->id)
                            <a href="{{ route('vendors.show', $vendor->slug ?: $vendor->id) }}" class="text-primary hover:text-primary-dark text-xs font-medium">Voir</a>
                        @else
                            <span class="text-gray-400 text-xs">Bientôt</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <!-- Organisateurs par défaut si aucun en base -->
            @foreach(['Aventures Locales', 'Eco Voyages', 'Culture & Traditions', 'Nature Extrême', 'Découvertes'] as $index => $name)
            @if($index < 5)
            <div class="bg-white rounded-lg shadow-md overflow-hidden card">
                <div class="relative h-40 overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center">
                        <span class="text-3xl font-bold text-primary">{{ substr($name, 0, 2) }}</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-2">
                        <h3 class="text-base font-bold text-text-primary truncate">{{ $name }}</h3>
                        <div class="flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-xs font-bold text-text-primary">4.8</span>
                        </div>
                    </div>
                    <p class="text-xs text-accent mb-2">Spécialiste</p>
                    <p class="text-xs text-text-secondary mb-3 line-clamp-2">Expert local passionné.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-text-secondary">Bientôt</span>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('vendors.index') }}" class="inline-flex items-center px-6 py-3 border border-primary bg-white text-primary hover:bg-primary hover:text-white text-base font-medium rounded-md shadow-sm transition-colors">
                Voir tous nos organisateurs
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <a href="{{ route('vendor.register') }}" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-dark text-white text-base font-medium rounded-md shadow-sm transition-colors ml-4">
                Devenir organisateur
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Section CTA Devenir Rédacteur -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 py-16 relative overflow-hidden">
        <!-- Motif de fond -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="writer-bg-pattern" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="2" fill="white"/>
                </pattern>
                <rect width="100" height="100" fill="url(#writer-bg-pattern)" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                <!-- Texte -->
                <div class="mb-8 lg:mb-0">
                    <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur rounded-full mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span class="text-white font-medium">Programme rédacteur</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Partagez vos expériences de voyage</h2>
                    <p class="text-xl text-white/90 mb-8">Rejoignez notre communauté de rédacteurs passionnés et obtenez des backlinks dofollow pour votre contenu de qualité.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="text-white font-semibold">Backlinks dofollow</p>
                                <p class="text-white/80 text-sm">Après 3-5 articles de qualité</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="text-white font-semibold">Outil SEO gratuit</p>
                                <p class="text-white/80 text-sm">NomadSEO pour optimiser vos articles</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="text-white font-semibold">Visibilité maximale</p>
                                <p class="text-white/80 text-sm">Milliers de lecteurs passionnés</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="text-white font-semibold">Badges et récompenses</p>
                                <p class="text-white/80 text-sm">Système gamifié pour progresser</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('writer.register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors">
                            Devenir rédacteur
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="{{ route('blog') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors">
                            Lire nos articles
                        </a>
                    </div>
                </div>

                <!-- Image/Illustration -->
                <div class="relative">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-8 border border-white/20">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-white/90 rounded-lg">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold mr-3">
                                        M
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Marie Dubois</p>
                                        <p class="text-xs text-gray-500">Rédactrice Communauté</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-semibold text-gray-900">Score: 92/100</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white/80 rounded-lg">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold mr-3">
                                        T
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Thomas Martin</p>
                                        <p class="text-xs text-gray-500">Client-Contributeur</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">DoFollow ✓</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white/70 rounded-lg">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold mr-3">
                                        S
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Sophie Bernard</p>
                                        <p class="text-xs text-gray-500">Partenaire-Rédactrice</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">15 articles</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section with Auto-Scrolling Carousel -->
    <div class="bg-white py-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-text-primary">Ce que disent nos voyageurs</h2>
                <p class="mt-4 text-lg text-text-secondary">Découvrez les expériences de ceux qui ont voyagé avec Nomadie</p>
            </div>
            
            <!-- Carousel Container -->
            <div class="relative">
                <!-- Gradient de fondu sur les côtés -->
                <div class="absolute left-0 top-0 h-full w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 h-full w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
                
                <!-- Carousel Wrapper -->
                <div class="testimonials-carousel-wrapper overflow-hidden">
                    <div class="testimonials-carousel flex gap-8" id="testimonialCarousel">
                        @foreach($testimonials->concat($testimonials) as $index => $testimonial)
                        <div class="flex-none w-full md:w-96 bg-bg-alt p-6 rounded-lg shadow-sm card">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                    {{ substr($testimonial->user->name ?? 'Anonyme', 0, 1) }}{{ substr(explode(' ', $testimonial->user->name ?? 'A B')[1] ?? 'B', 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-text-primary">{{ $testimonial->user->name ?? 'Voyageur' }}</h3>
                                    <div class="flex mt-1">
                                        @for($i = 0; $i < 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i < $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <p class="text-text-secondary">« {{ $testimonial->content }} »</p>
                            <p class="mt-3 text-xs text-primary">
                                @if(isset($testimonial->trip->title))
                                    {{ $testimonial->trip->title }} - 
                                @endif
                                {{ $testimonial->created_at->format('F Y') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Bouton de contrôle unique -->
                <div class="flex justify-center mt-8">
                    <button id="carouselToggle" onclick="toggleCarousel()" class="px-6 py-2 bg-primary text-white hover:bg-primary-dark rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="toggleText">Pause</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="bg-primary/5 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-text-primary mb-4">Restez informé avec Nomadie</h2>
            <p class="text-lg text-text-secondary mb-8">Inscrivez-vous à notre newsletter pour recevoir nos meilleures offres de voyages, circuits, hébergements et expériences uniques</p>
            
            <form class="flex flex-col sm:flex-row gap-4 max-w-2xl mx-auto">
                <input type="email" placeholder="Votre adresse email" class="flex-1 px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                <button type="submit" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                    S'abonner
                </button>
            </form>
            <p class="mt-4 text-xs text-text-secondary">En vous inscrivant, vous acceptez de recevoir nos emails et confirmez avoir lu notre politique de confidentialité.</p>
        </div>
    </div>
</div>

<!-- Styles CSS pour le carrousel -->
<style>
    .testimonials-carousel {
        animation: scroll-left 30s linear infinite;
        display: flex;
    }
    
    .testimonials-carousel:hover {
        animation-play-state: paused;
    }
    
    @keyframes scroll-left {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    
    /* Sur mobile, réduire la largeur des cartes */
    @media (max-width: 640px) {
        .testimonials-carousel .card {
            width: 280px;
        }
    }
    
    /* Animation plus fluide */
    .testimonials-carousel-wrapper {
        mask-image: linear-gradient(
            to right,
            transparent 0%,
            black 10%,
            black 90%,
            transparent 100%
        );
        -webkit-mask-image: linear-gradient(
            to right,
            transparent 0%,
            black 10%,
            black 90%,
            transparent 100%
        );
    }
</style>

@push('scripts')
<script>
    // Script pour améliorer l'interactivité
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des cartes au scroll
        const cards = document.querySelectorAll('.card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        entry.target.style.transition = 'all 0.5s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => observer.observe(card));
        
        // Contrôle du carrousel de témoignages
        let isPaused = false;
        const carousel = document.getElementById('testimonialCarousel');
        const toggleBtn = document.getElementById('carouselToggle');
        const toggleText = document.getElementById('toggleText');
        const pauseIcon = document.getElementById('pauseIcon');
        const playIcon = document.getElementById('playIcon');
        
        window.toggleCarousel = function() {
            if (isPaused) {
                carousel.style.animationPlayState = 'running';
                isPaused = false;
                toggleText.textContent = 'Pause';
                pauseIcon.classList.remove('hidden');
                playIcon.classList.add('hidden');
            } else {
                carousel.style.animationPlayState = 'paused';
                isPaused = true;
                toggleText.textContent = 'Reprendre';
                pauseIcon.classList.add('hidden');
                playIcon.classList.remove('hidden');
            }
        }
        
        // Pause automatique au survol
        carousel.addEventListener('mouseenter', () => {
            if (!isPaused) {
                carousel.style.animationPlayState = 'paused';
            }
        });
        
        carousel.addEventListener('mouseleave', () => {
            if (!isPaused) {
                carousel.style.animationPlayState = 'running';
            }
        });
        
        // Dupliquer les témoignages pour un défilement infini si nécessaire
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        const carouselWidth = carousel.offsetWidth;
        const containerWidth = document.querySelector('.testimonials-carousel-wrapper').offsetWidth;
        
        // Si les témoignages ne remplissent pas l'écran, on les duplique
        if (carouselWidth < containerWidth * 2) {
            testimonialCards.forEach(card => {
                const clone = card.cloneNode(true);
                carousel.appendChild(clone);
            });
        }
    });
</script>
@endpush
@endsection