@extends('layouts.public')

@section('title', 'Explorez nos destinations - Trouvez votre prochaine aventure')

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-40 z-10"></div>
            <img 
                src="{{ asset('images/hero-destinations.jpg') }}" 
                alt="Explorez le monde" 
                class="w-full h-full object-cover"
                onerror="this.style.display='none'"
            />
        </div>
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-28">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold text-white">
                    Trouvez votre prochaine aventure
                </h1>
                <p class="mt-4 text-xl text-white/90">
                    Découvrez nos offres d'hébergements, séjours organisés, activités et voyages sur mesure à travers le monde
                </p>
                
                <!-- Barre de recherche -->
                <div class="mt-8 bg-white/10 backdrop-blur-md p-2 rounded-lg border border-white/20">
                    <form action="{{ route('search') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <input
                            type="text"
                            name="q"
                            placeholder="Rechercher une destination, un pays..."
                            class="flex-grow bg-white/80 py-3 px-4 rounded-md text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30 focus:bg-white transition-colors"
                        />
                        <button 
                            type="submit"
                            class="sm:w-auto flex-shrink-0 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none transition-colors"
                        >
                            Explorer
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Destinations populaires (pays avec des offres) -->
        @if($featuredDestinations && $featuredDestinations->count() > 0)
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-text-primary mb-8">Destinations populaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featuredDestinations->take(6) as $destination)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="{{ route('destinations.show', $destination->slug) }}">
                        <div class="relative h-56 overflow-hidden">
                            @if($destination->image)
                            <img 
                                src="{{ $destination->image }}" 
                                alt="{{ $destination->name }}" 
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                onerror="this.onerror=null; this.src='{{ asset('images/placeholder-destination.jpg') }}'"
                            />
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <div class="p-6">
                                    <h3 class="text-2xl font-bold text-white">{{ $destination->name }}</h3>
                                    @if($destination->continent)
                                    <p class="text-white/90 text-sm">{{ $destination->continent->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($destination->popular)
                            <x-badge variant="accent" style="solid" size="sm" class="absolute top-2 right-2">
                                Populaire
                            </x-badge>
                            @endif
                        </div>
                    </a>
                    <div class="p-6">
                        <p class="text-text-secondary mb-4">
                            {{ Str::limit($destination->description, 150) ?: 'Découvrez la richesse culturelle et les paysages exceptionnels de ' . $destination->name }}
                        </p>
                        
                        @if(isset($destination->trips_count) && $destination->trips_count > 0)
                        <p class="text-sm text-primary font-medium mb-4">
                            {{ $destination->trips_count }} {{ $destination->trips_count > 1 ? 'offres disponibles' : 'offre disponible' }}
                        </p>
                        @endif
                        
                        <a 
                            href="{{ route('destinations.show', $destination->slug) }}"
                            class="inline-flex items-center text-primary hover:text-primary-dark font-medium transition-colors"
                        >
                            Découvrir
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Navigation par continents -->
        <div id="continents-section" class="mt-20">
            <h2 class="text-3xl font-bold text-text-primary mb-8">Explorer par continent</h2>
            
            <!-- Onglets des continents -->
            <div x-data="{
                activeTab: '{{ $continents->first()->slug ?? 'all' }}',
                countries: [],
                isLoading: false,
                
                loadCountries(continentSlug) {
                    this.isLoading = true;
                    
                    fetch(`/api/destinations/continent/${continentSlug}`)
                        .then(response => response.json())
                        .then(data => {
                            this.countries = data.countries || [];
                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            this.countries = [];
                            this.isLoading = false;
                        });
                },
                
                init() {
                    this.loadCountries(this.activeTab);
                }
            }">
                <div class="border-b border-border overflow-x-auto">
                    <nav class="flex -mb-px">
                        <button 
                            @click="activeTab = 'all'; loadCountries('all')" 
                            :class="activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-primary'"
                            class="whitespace-nowrap py-4 px-6 font-medium text-sm border-b-2 transition-colors"
                        >
                            Tous
                        </button>
                        @foreach($continents as $continent)
                        <button 
                            @click="activeTab = '{{ $continent->slug }}'; loadCountries('{{ $continent->slug }}')" 
                            :class="activeTab === '{{ $continent->slug }}' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-primary'"
                            class="whitespace-nowrap py-4 px-6 font-medium text-sm border-b-2 transition-colors"
                        >
                            {{ $continent->name }}
                        </button>
                        @endforeach
                    </nav>
                </div>
                
                <!-- Grille des pays -->
                <div class="mt-8">
                    <!-- Loading state -->
                    <div x-show="isLoading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        @for($i = 0; $i < 8; $i++)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden animate-pulse">
                            <div class="h-40 bg-gray-200"></div>
                            <div class="p-4">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                    <!-- Liste des pays -->
                    <div x-show="!isLoading && countries.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        <template x-for="country in countries" :key="country.id">
                            <a 
                                :href="`/destinations/${country.slug}`" 
                                class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all transform hover:-translate-y-1"
                            >
                                <div class="relative h-40 overflow-hidden">
                                    <img 
                                        :src="country.image || '{{ asset('images/placeholder-destination.jpg') }}'"
                                        :alt="country.name" 
                                        class="w-full h-full object-cover"
                                        onerror="this.src='{{ asset('images/placeholder-destination.jpg') }}'"
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                        <div class="p-4">
                                            <h3 class="text-lg font-bold text-white" x-text="country.name"></h3>
                                            <p class="text-white/80 text-xs" x-show="country.trips_count > 0">
                                                <span x-text="country.trips_count"></span> offre(s)
                                            </p>
                                        </div>
                                    </div>
                                    <x-badge x-show="country.popular" variant="accent" style="solid" size="sm" class="absolute top-2 right-2">
                                        Populaire
                                    </x-badge>
                                </div>
                            </a>
                        </template>
                    </div>
                    
                    <!-- Message si aucun pays -->
                    <div x-show="!isLoading && countries.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-text-secondary mt-4">Aucune destination trouvée dans cette région.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Types d'offres - AVEC LES 4 TYPES -->
        <div class="mt-20 bg-bg-alt rounded-lg p-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-text-primary">Trouvez l'expérience qui vous correspond</h2>
                <p class="mt-4 text-lg text-text-secondary max-w-3xl mx-auto">
                    Hébergements authentiques, séjours organisés, activités uniques ou voyages sur mesure
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Hébergements -->
                <div class="bg-white p-6 rounded-lg shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Hébergements</h3>
                    <p class="text-text-secondary mb-4">Gîtes, villas, appartements et maisons d'hôtes sélectionnés</p>
                    <a href="{{ route('search', ['offer_type' => 'accommodation']) }}" class="text-primary hover:text-primary-dark font-medium">
                        Voir les hébergements →
                    </a>
                </div>
                
                <!-- Séjours organisés -->
                <div class="bg-white p-6 rounded-lg shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Séjours organisés</h3>
                    <p class="text-text-secondary mb-4">Voyages tout compris avec guides locaux</p>
                    <a href="{{ route('search', ['offer_type' => 'organized_trip']) }}" class="text-primary hover:text-primary-dark font-medium">
                        Voir les séjours →
                    </a>
                </div>
                
                <!-- Activités -->
                <div class="bg-white p-6 rounded-lg shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Activités</h3>
                    <p class="text-text-secondary mb-4">Expériences uniques et activités locales</p>
                    <a href="{{ route('search', ['offer_type' => 'activity']) }}" class="text-primary hover:text-primary-dark font-medium">
                        Voir les activités →
                    </a>
                </div>
                
                <!-- Sur mesure -->
                <div class="bg-white p-6 rounded-lg shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Sur mesure</h3>
                    <p class="text-text-secondary mb-4">Créez votre voyage personnalisé avec nos experts</p>
                    <a href="{{ route('search', ['offer_type' => 'custom']) }}" class="text-primary hover:text-primary-dark font-medium">
                        Demander un devis →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- CTA Final -->
        <div class="mt-20 bg-gradient-to-r from-primary to-primary-dark rounded-lg p-8 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full transform translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full transform -translate-x-1/3 translate-y-1/3"></div>
            
            <div class="relative z-10 text-center text-white">
                <h2 class="text-2xl font-bold mb-4">Prêt à explorer le monde ?</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    Trouvez votre prochaine aventure parmi nos offres d'hébergements, séjours organisés, activités et voyages sur mesure.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('search') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-white/90 focus:outline-none transition-colors">
                        Rechercher une expérience
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>
                    <a href="{{ route('vendor.register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white/10 focus:outline-none transition-colors">
                        Devenir organisateur
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush