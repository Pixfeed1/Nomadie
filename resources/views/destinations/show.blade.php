@extends('layouts.public')

@section('title', $destination->name . ' - Découvrez cette destination')

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section - Avec image de couverture du pays -->
    <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-50 z-10"></div>
            <picture>
                <source srcset="{{ $destination->cover_image ?? asset('images/destinations/' . $destination->slug . '.webp') }}" type="image/webp" />
                <img 
                    width="1600" 
                    height="600"
                    fetchpriority="high"
                    src="{{ $destination->cover_image ?? asset('images/destinations/' . $destination->slug . '.jpg') }}" 
                    alt="{{ $destination->name }}" 
                    class="w-full h-full object-cover animate-slow-zoom" 
                    onerror="this.src='/api/placeholder/1600/600?text={{ $destination->name }}';this.onerror=null;"
                />
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
                    <span class="text-white/80">{{ $destination->continent->name ?? 'Monde' }}</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold text-white">
                    {{ $destination->name }}
                </h1>
                
                <!-- Badge et statistiques -->
                <div class="flex flex-wrap items-center gap-3 mt-4">
                    @if($destination->popular)
                    <span class="bg-accent/90 text-white text-xs font-bold px-2 py-1 rounded">
                        Populaire
                    </span>
                    @endif
                    
                    @if($destination->rating > 0)
                    <div class="flex items-center gap-1">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $destination->rating)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/40" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-white/90">{{ $destination->rating }} ({{ $destination->reviews_count ?? 0 }} avis)</span>
                    </div>
                    @endif
                    
                    @php
                        $tripsCount = isset($destination->trips_count) ? $destination->trips_count : (isset($trips) ? $trips->count() : 0);
                        $offerTypes = isset($trips) ? $trips->groupBy('offer_type')->map->count() : collect();
                    @endphp
                    
                    <!-- Statistiques détaillées des offres -->
                    <div class="flex flex-wrap items-center gap-3 text-white/90 text-sm">
                        @if($offerTypes->get('accommodation', 0) > 0)
                        <span class="inline-flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            {{ $offerTypes->get('accommodation') }} hébergement(s)
                        </span>
                        @endif
                        @if($offerTypes->get('organized_trip', 0) > 0)
                        <span class="inline-flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $offerTypes->get('organized_trip') }} séjour(s)
                        </span>
                        @endif
                        @if($offerTypes->get('activity', 0) > 0)
                        <span class="inline-flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $offerTypes->get('activity') }} activité(s)
                        </span>
                        @endif
                        @if($offerTypes->get('custom', 0) > 0)
                        <span class="inline-flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ $offerTypes->get('custom') }} sur mesure
                        </span>
                        @endif
                    </div>
                </div>
                
                <p class="mt-6 text-xl text-white/90">
                    {{ $destination->short_description ?? 'Découvrez les merveilles de ' . $destination->name . ' et plongez dans une expérience unique avec nos voyages personnalisés.' }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="#offres" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none transition-colors">
                        Voir les {{ $tripsCount }} offres
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <a href="#infos" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white/10 focus:outline-none transition-colors">
                        Infos pratiques
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
                <!-- À propos de la destination -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="about">
                    <h2 class="text-2xl font-bold text-text-primary mb-6">À propos de {{ $destination->name }}</h2>
                    <div class="prose max-w-none text-text-secondary">
                        {!! $destination->description ?? '<p>Description complète de la destination. Cette magnifique destination vous offre une multitude d\'expériences uniques, de paysages à couper le souffle et une culture riche et fascinante.</p>
                        <p>Que vous soyez amateur de plages paradisiaques, de randonnées en montagnes ou d\'aventures urbaines, ' . $destination->name . ' saura vous séduire par sa diversité et son authenticité.</p>
                        <p>Avec une histoire millénaire, une cuisine réputée mondialement et des habitants chaleureux, votre voyage sera inoubliable.</p>' !!}
                    </div>
                </div>
                
                <!-- Offres disponibles avec filtres améliorés -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-10" id="offres">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-text-primary">Offres disponibles à {{ $destination->name }}</h2>
                        <span class="text-sm text-text-secondary">{{ $tripsCount }} offre(s) trouvée(s)</span>
                    </div>
                    
                    @if(isset($trips) && count($trips) > 0)
                    <!-- Barre de filtres et tri améliorée -->
                    <div class="bg-bg-alt rounded-lg p-4 mb-6">
                        <!-- Filtres par type d'offre -->
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    data-filter="all"
                                    class="filter-btn active px-4 py-2 rounded-full text-sm font-medium transition-all bg-primary text-white shadow-sm"
                                >
                                    Tout voir ({{ $trips->count() }})
                                </button>
                                @if($offerTypes->get('accommodation', 0) > 0)
                                <button 
                                    data-filter="accommodation"
                                    class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-all bg-white text-text-secondary hover:bg-primary hover:text-white shadow-sm"
                                >
                                    <span class="inline-flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        Hébergements ({{ $offerTypes->get('accommodation') }})
                                    </span>
                                </button>
                                @endif
                                @if($offerTypes->get('organized_trip', 0) > 0)
                                <button 
                                    data-filter="organized_trip"
                                    class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-all bg-white text-text-secondary hover:bg-primary hover:text-white shadow-sm"
                                >
                                    <span class="inline-flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Séjours ({{ $offerTypes->get('organized_trip') }})
                                    </span>
                                </button>
                                @endif
                                @if($offerTypes->get('activity', 0) > 0)
                                <button 
                                    data-filter="activity"
                                    class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-all bg-white text-text-secondary hover:bg-primary hover:text-white shadow-sm"
                                >
                                    <span class="inline-flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Activités ({{ $offerTypes->get('activity') }})
                                    </span>
                                </button>
                                @endif
                                @if($offerTypes->get('custom', 0) > 0)
                                <button 
                                    data-filter="custom"
                                    class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-all bg-white text-text-secondary hover:bg-primary hover:text-white shadow-sm"
                                >
                                    <span class="inline-flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Sur mesure ({{ $offerTypes->get('custom') }})
                                    </span>
                                </button>
                                @endif
                            </div>
                            
                            <!-- Options de tri -->
                            <div class="flex items-center gap-2">
                                <label for="sort-by" class="text-sm text-text-secondary">Trier par:</label>
                                <select 
                                    id="sort-by"
                                    class="bg-white border border-gray-200 text-text-secondary text-sm rounded-lg focus:ring-2 focus:ring-primary/20 focus:outline-none px-3 py-2"
                                >
                                    <option value="popular">Plus populaire</option>
                                    <option value="price-asc">Prix croissant</option>
                                    <option value="price-desc">Prix décroissant</option>
                                    <option value="duration-asc">Durée (courte)</option>
                                    <option value="duration-desc">Durée (longue)</option>
                                    <option value="rating">Meilleures notes</option>
                                    <option value="recent">Plus récent</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Compteur de résultats filtrés -->
                        <div class="mt-3 text-sm text-text-secondary">
                            <span id="filtered-count" class="font-medium">{{ $trips->count() }}</span> offre(s) affichée(s)
                        </div>
                    </div>
                    
                    <!-- Liste des offres -->
                    <div class="space-y-6" id="trips-list">
                        @foreach($trips as $trip)
                        <div 
                            class="trip-item bg-white border border-gray-200 rounded-lg p-4 md:p-6 flex flex-col md:flex-row gap-4 hover:shadow-lg transition-all"
                            data-offer-type="{{ $trip->offer_type }}"
                            data-price="{{ $trip->price }}"
                            data-duration="{{ $trip->isActivity() ? $trip->duration_hours : $trip->duration }}"
                            data-rating="{{ $trip->rating }}"
                            data-popularity="{{ $trip->views_count }}"
                            data-date="{{ $trip->created_at }}"
                        >
                            <div class="md:w-1/3 h-48 rounded-lg overflow-hidden">
                                @if($trip->main_image)
                                <img src="{{ Storage::url($trip->main_image) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="md:w-2/3 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-xl font-bold text-text-primary">{{ $trip->title }}</h3>
                                            <div class="flex gap-2 mt-2">
                                                <span class="inline-block px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                                                    {{ $trip->offer_type_label }}
                                                </span>
                                                @if($trip->featured)
                                                <span class="inline-block px-2 py-1 bg-accent/10 text-accent-dark text-xs font-medium rounded-full">
                                                    En vedette
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($trip->rating > 0)
                                        <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-1 text-sm font-bold text-text-primary">{{ number_format($trip->rating, 1) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <p class="mt-3 text-text-secondary line-clamp-2">{{ $trip->short_description }}</p>
                                    <div class="mt-4 flex flex-wrap gap-3">
                                        <!-- Badges avec icônes -->
                                        <span class="inline-flex items-center text-xs text-text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $trip->duration_formatted }}
                                        </span>
                                        
                                        @if(!$trip->isCustomOffer())
                                        <span class="inline-flex items-center text-xs text-text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ $trip->capacity_text }}
                                        </span>
                                        @endif
                                        
                                        @if($trip->has_availabilities)
                                        <span class="inline-flex items-center text-xs text-green-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Disponible
                                        </span>
                                        @endif
                                        
                                        @if($trip->meal_plan && $trip->meal_plan !== 'none')
                                        <span class="inline-flex items-center text-xs text-text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 7h18M3 11h18m-9 4h9M3 15h6" />
                                            </svg>
                                            {{ $trip->meal_plan_text }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-between items-end mt-4">
                                    <div>
                                        <div class="text-text-secondary text-xs">Proposé par</div>
                                        <div class="text-text-primary font-medium">{{ $trip->vendor->company_name ?? 'Nomadie' }}</div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <div class="text-text-secondary text-xs">à partir de</div>
                                            <div class="text-primary text-2xl font-bold">
                                                {{ number_format($trip->price, 0, ',', ' ') }} €
                                            </div>
                                            @if($trip->price_unit)
                                            <div class="text-xs text-text-secondary">{{ $trip->price_unit }}</div>
                                            @endif
                                        </div>
                                        <a href="{{ route('trips.show', $trip->slug) }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none transition-all hover:scale-105">
                                            Voir détails
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Message si aucun résultat après filtrage -->
                    <div id="no-results" class="hidden text-center py-10 bg-bg-alt rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-text-secondary mt-4">Aucune offre ne correspond à vos critères</p>
                        <button onclick="resetFilters()" class="mt-4 text-primary hover:text-primary-dark font-medium">
                            Réinitialiser les filtres
                        </button>
                    </div>
                    
                    @else
                    <!-- Message si aucune offre -->
                    <div class="text-center py-10 bg-bg-alt rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <p class="text-text-secondary mt-4">Aucune offre n'est actuellement disponible pour cette destination.</p>
                        <p class="text-text-secondary mt-2">Consultez nos autres destinations ou contactez-nous pour un voyage personnalisé.</p>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('destinations.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none transition-colors">
                                Voir toutes les destinations
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-4 py-2 border border-primary text-sm font-medium rounded-md shadow-sm text-primary bg-transparent hover:bg-primary/5 focus:outline-none transition-colors">
                                Nous contacter
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale -->
            <div>
                <!-- Informations pratiques -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="infos">
                    <h2 class="text-xl font-bold text-text-primary mb-4">Informations pratiques</h2>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-text-primary">Meilleure période</h3>
                                <p class="text-text-secondary">{{ $destination->best_time ?? 'Toute l\'année' }}</p>
                            </div>
                        </div>
                        
                        @if($destination->languages)
                        <div class="flex">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-text-primary">Langue(s)</h3>
                                <p class="text-text-secondary">{{ $destination->languages }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Autres infos pratiques... -->
                    </div>
                </div>

                <!-- Pourquoi visiter -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-text-primary mb-4">Pourquoi visiter {{ $destination->name }}</h2>
                    <div class="space-y-3">
                        @if(isset($highlights) && count($highlights) > 0)
                            @foreach($highlights as $highlight)
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p class="ml-3 text-text-secondary">{{ $highlight }}</p>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Besoin d'aide -->
                <div class="bg-primary/5 rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-text-primary mb-4">Besoin d'aide ?</h2>
                    <p class="text-text-secondary mb-4">Nos conseillers voyage sont à votre disposition pour vous aider à planifier votre voyage sur mesure.</p>
                    <a href="{{ route('contact') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none transition-colors">
                        Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Destinations similaires -->
    @if(isset($similarDestinations) && count($similarDestinations) > 0)
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-text-primary mb-8">Destinations similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($similarDestinations as $similarDestination)
                <a href="{{ route('destinations.show', $similarDestination->slug) }}" class="bg-white rounded-lg shadow-sm overflow-hidden card hover:shadow-md transition-shadow">
                    <div class="relative h-48 overflow-hidden">
                        @if($similarDestination->image)
                        <img 
                            src="{{ $similarDestination->image }}" 
                            alt="{{ $similarDestination->name }}" 
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                            onerror="this.src='{{ asset('images/placeholder-destination.jpg') }}'"
                        />
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-white">{{ $similarDestination->name }}</h3>
                                <p class="text-white/90 text-xs">
                                    @if(isset($similarDestination->trips_count))
                                        {{ $similarDestination->trips_count }} offre(s)
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($similarDestination->popular)
                        <div class="absolute top-2 right-2 bg-accent/90 text-white text-xs font-bold px-2 py-1 rounded">
                            Populaire
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Final -->
    <div class="bg-gradient-to-r from-primary to-primary-dark py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative z-10 text-center text-white">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full transform translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full transform -translate-x-1/3 translate-y-1/3"></div>
                
                <h2 class="text-2xl font-bold mb-4">Prêt à explorer {{ $destination->name }} ?</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">Découvrez notre sélection d'offres personnalisées et rencontrez des experts locaux qui vous feront découvrir les trésors de cette destination unique.</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#offres" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-white/90 focus:outline-none transition-colors">
                        Voir les offres
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white bg-transparent hover:bg-white/10 focus:outline-none transition-colors">
                        Voyage sur mesure
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtres par type d'offre
        const filterButtons = document.querySelectorAll('.filter-btn');
        const tripItems = document.querySelectorAll('.trip-item');
        const sortSelect = document.getElementById('sort-by');
        const filteredCount = document.getElementById('filtered-count');
        const noResults = document.getElementById('no-results');
        const tripsList = document.getElementById('trips-list');
        
        // Gestion des filtres
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Mise à jour visuelle des boutons
                filterButtons.forEach(btn => {
                    btn.classList.remove('bg-primary', 'text-white', 'active', 'shadow-sm');
                    btn.classList.add('bg-white', 'text-text-secondary');
                });
                this.classList.remove('bg-white', 'text-text-secondary');
                this.classList.add('bg-primary', 'text-white', 'active', 'shadow-sm');
                
                // Filtrage des offres
                const filter = this.dataset.filter;
                let visibleCount = 0;
                
                tripItems.forEach(item => {
                    if (filter === 'all' || item.dataset.offerType === filter) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Mise à jour du compteur
                if (filteredCount) {
                    filteredCount.textContent = visibleCount;
                }
                
                // Afficher/masquer le message "aucun résultat"
                if (visibleCount === 0 && noResults) {
                    tripsList.style.display = 'none';
                    noResults.classList.remove('hidden');
                } else if (noResults) {
                    tripsList.style.display = '';
                    noResults.classList.add('hidden');
                }
            });
        });
        
        // Gestion du tri amélioré
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                const trips = Array.from(tripItems).filter(item => item.style.display !== 'none');
                
                trips.sort((a, b) => {
                    switch(sortBy) {
                        case 'price-asc':
                            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                        case 'price-desc':
                            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                        case 'duration-asc':
                            return parseFloat(a.dataset.duration) - parseFloat(b.dataset.duration);
                        case 'duration-desc':
                            return parseFloat(b.dataset.duration) - parseFloat(a.dataset.duration);
                        case 'rating':
                            return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                        case 'recent':
                            return new Date(b.dataset.date) - new Date(a.dataset.date);
                        case 'popular':
                        default:
                            return parseFloat(b.dataset.popularity) - parseFloat(a.dataset.popularity);
                    }
                });
                
                // Réorganiser les éléments
                trips.forEach(trip => tripsList.appendChild(trip));
            });
        }
        
        // Fonction pour réinitialiser les filtres
        window.resetFilters = function() {
            // Cliquer sur le bouton "Tout voir"
            const allButton = document.querySelector('[data-filter="all"]');
            if (allButton) {
                allButton.click();
            }
            
            // Réinitialiser le tri
            if (sortSelect) {
                sortSelect.value = 'popular';
                sortSelect.dispatchEvent(new Event('change'));
            }
        };
        
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush