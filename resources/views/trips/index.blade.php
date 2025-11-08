@extends('layouts.public')

@section('title', 'Toutes les offres - Nomadie')

@section('content')
<div class="bg-bg-primary min-h-screen">
    <!-- Hero Section -->
    <div class="bg-primary text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Découvrez nos offres</h1>
            <p class="text-xl opacity-90">{{ $trips->total() }} expérience(s) disponible(s)</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filtres Sidebar -->
            <aside class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Filtres</h2>

                    <form method="GET" action="{{ route('trips.index') }}" class="space-y-6">
                        <!-- Recherche -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Rechercher</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Destination, activité..." 
                                   class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>

                        <!-- Type d'offre -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Type d'offre</label>
                            <select name="offer_type" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Tous les types</option>
                                @foreach($offerTypes as $key => $label)
                                    <option value="{{ $key }}" {{ request('offer_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pays -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Pays</label>
                            <select name="country" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Tous les pays</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type de voyage -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Type de voyage</label>
                            <select name="travel_type" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Tous</option>
                                @foreach($travelTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('travel_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Budget</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="Min" class="px-3 py-2 border rounded-lg text-sm">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="Max" class="px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>

                        <!-- Durée -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Durée (jours)</label>
                            <select name="duration" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Toutes durées</option>
                                <option value="1-3" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 jours</option>
                                <option value="4-7" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 jours</option>
                                <option value="8-14" {{ request('duration') == '8-14' ? 'selected' : '' }}>8-14 jours</option>
                                <option value="15" {{ request('duration') == '15' ? 'selected' : '' }}>15+ jours</option>
                            </select>
                        </div>

                        <!-- Boutons -->
                        <div class="space-y-2">
                            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark transition">
                                Appliquer les filtres
                            </button>
                            <a href="{{ route('trips.index') }}" class="block text-center text-sm text-text-secondary hover:text-primary">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Contenu principal -->
            <main class="lg:w-3/4">
                <!-- Barre de tri -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-text-secondary">
                        {{ $trips->total() }} résultat(s) trouvé(s)
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-text-secondary">Trier par:</label>
                        <select name="sort" onchange="window.location.href='{{ route('trips.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value})" 
                                class="px-3 py-1 border rounded-lg text-sm">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récent</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularité</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Meilleure note</option>
                        </select>
                    </div>
                </div>

                <!-- Grid des offres -->
                @if($trips->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($trips as $trip)
                        <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Image -->
                            <a href="{{ route('trips.show', $trip->slug) }}" class="block relative h-48 bg-gray-200">
                                @if($trip->images && count($trip->images) > 0)
                                    <img src="{{ Storage::url($trip->images[0]['path']) }}" 
                                         alt="{{ $trip->title }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-text-secondary">
                                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Badge type -->
                                <div class="absolute top-2 left-2">
                                    @if($trip->offer_type === 'accommodation')
                                        <span class="badge badge-primary text-xs">Hébergement</span>
                                    @elseif($trip->offer_type === 'organized_trip')
                                        <span class="badge badge-info text-xs">Séjour</span>
                                    @elseif($trip->offer_type === 'activity')
                                        <span class="badge badge-success text-xs">Activité</span>
                                    @else
                                        <span class="badge badge-secondary text-xs">Sur mesure</span>
                                    @endif
                                </div>
                            </a>

                            <!-- Contenu -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 line-clamp-2">
                                    <a href="{{ route('trips.show', $trip->slug) }}" class="hover:text-primary">
                                        {{ $trip->title }}
                                    </a>
                                </h3>

                                <div class="flex items-center text-sm text-text-secondary mb-3">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $trip->destination->name ?? 'N/A' }}, {{ $trip->destination->country->name ?? '' }}
                                </div>

                                <p class="text-sm text-text-secondary mb-4 line-clamp-2">{{ $trip->short_description }}</p>

                                <div class="flex items-center justify-between pt-3 border-t">
                                    <div class="text-lg font-bold text-primary">
                                        {{ number_format($trip->price, 0, ',', ' ') }} {{ $trip->currency }}
                                        <span class="text-xs text-text-secondary font-normal">
                                            @if($trip->offer_type === 'accommodation')
                                                /nuit
                                            @elseif($trip->offer_type === 'activity')
                                                /pers.
                                            @endif
                                        </span>
                                    </div>

                                    @if($trip->next_availability)
                                        <div class="text-xs text-success">
                                            Dispo. {{ $trip->next_availability->start_date->format('d/m') }}
                                        </div>
                                    @else
                                        <div class="text-xs text-text-secondary">
                                            Voir dates
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $trips->links() }}
                    </div>
                @else
                    <!-- État vide -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="h-16 w-16 mx-auto text-text-secondary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-2">Aucune offre trouvée</h3>
                        <p class="text-text-secondary mb-6">Essayez de modifier vos critères de recherche</p>
                        <a href="{{ route('trips.index') }}" class="btn bg-primary text-white px-6 py-2 rounded-lg inline-block">
                            Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
