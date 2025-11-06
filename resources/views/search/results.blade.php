@extends('layouts.public')

@section('title', 'Résultats de recherche')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-text-primary">Résultats de votre recherche</h1>
            <p class="mt-2 text-lg text-text-secondary">
                @if($trips->total() > 0)
                    {{ $trips->total() }} voyage(s) trouvé(s)
                @else
                    Aucun voyage ne correspond à votre recherche
                @endif
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Filtres avancés -->
            <div class="w-full lg:w-1/4 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">Affiner votre recherche</h2>
                
                <form action="{{ route('search') }}" method="GET" class="space-y-6">
                    <!-- Destination -->
                    <div>
                        <label for="destination" class="block text-sm font-medium text-text-secondary mb-2">Destination</label>
                        <select id="destination" name="destination" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5">
                            <option value="">Toutes les destinations</option>
                            <optgroup label="Continents">
                                @foreach($continents as $continent)
                                    <option value="{{ $continent }}" @if($destination == $continent) selected @endif>{{ $continent }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Pays">
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" @if($destination == $country->id) selected @endif>{{ $country->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    
                    <!-- Date de départ -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-text-secondary mb-2">Date de départ</label>
                        <input type="date" id="date" name="date" value="{{ $departureDate }}" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5">
                    </div>
                    
                    <!-- Nombre de voyageurs -->
                    <div>
                        <label for="travelers" class="block text-sm font-medium text-text-secondary mb-2">Voyageurs</label>
                        <select id="travelers" name="travelers" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5">
                            <option value="">Nombre de voyageurs</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @if($travelers == $i) selected @endif>{{ $i }} voyageur(s)</option>
                            @endfor
                            <option value="10+" @if($travelers == '10+') selected @endif>10+ voyageurs</option>
                        </select>
                    </div>
                    
                    <!-- Prix -->
                    <div>
                        <label for="price_range" class="block text-sm font-medium text-text-secondary mb-2">Budget (€)</label>
                        <div class="flex gap-4">
                            <input type="number" id="price_min" name="price_min" placeholder="Min" value="{{ $priceMin }}" min="0" class="w-1/2 rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5">
                            <input type="number" id="price_max" name="price_max" placeholder="Max" value="{{ $priceMax }}" min="0" class="w-1/2 rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5">
                        </div>
                    </div>
                    
                    <!-- Durée -->
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Durée (jours)</label>
                        <div class="space-y-2">
                            @foreach($durations as $durationOption)
                                <div class="flex items-center">
                                    <input type="radio" id="duration_{{ $durationOption }}" name="duration" value="{{ $durationOption }}" @if($duration == $durationOption) checked @endif class="focus:ring-primary h-4 w-4 text-primary border-border">
                                    <label for="duration_{{ $durationOption }}" class="ml-3 block text-sm font-medium text-text-secondary">
                                        {{ str_replace('-', ' à ', $durationOption) }} {{ $durationOption == '14+' ? 'ou plus' : 'jours' }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Type de voyage -->
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Type de voyage</label>
                        <div class="space-y-2">
                            @foreach($travelTypes as $type)
                                <div class="flex items-center">
                                    <input type="checkbox" id="type_{{ $type }}" name="travel_type[]" value="{{ $type }}" @if(is_array($travelType) && in_array($type, $travelType)) checked @endif class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                    <label for="type_{{ $type }}" class="ml-3 block text-sm font-medium text-text-secondary">
                                        {{ $type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="pt-2 flex flex-col space-y-2">
                        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-4 rounded-md transition-colors flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filtrer les résultats
                        </button>
                        <a href="{{ route('search') }}" class="w-full bg-white border border-border hover:bg-bg-alt text-text-primary font-medium py-2.5 px-4 rounded-md transition-colors text-center">
                            Réinitialiser les filtres
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Résultats de recherche -->
            <div class="w-full lg:w-3/4">
                @if($trips->total() > 0)
                    <!-- Options de tri -->
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-text-secondary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                            <span class="text-sm text-text-secondary">Trier par :</span>
                        </div>
                        <div class="flex space-x-2 mt-2 sm:mt-0">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="px-3 py-1 text-xs @if(request('sort') == 'price_asc' || !request('sort')) bg-primary text-white @else bg-white text-text-primary border border-border @endif rounded-full hover:bg-primary-dark hover:text-white transition-colors">
                                Prix croissant
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="px-3 py-1 text-xs @if(request('sort') == 'price_desc') bg-primary text-white @else bg-white text-text-primary border border-border @endif rounded-full hover:bg-primary-dark hover:text-white transition-colors">
                                Prix décroissant
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'popularity']) }}" class="px-3 py-1 text-xs @if(request('sort') == 'popularity') bg-primary text-white @else bg-white text-text-primary border border-border @endif rounded-full hover:bg-primary-dark hover:text-white transition-colors">
                                Popularité
                            </a>
                        </div>
                    </div>
                    
                    <!-- Grille de résultats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($trips as $trip)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ asset($trip->image_url) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" onerror="this.src='/api/placeholder/600/400?text=Voyage';this.onerror=null;">
                                @if($trip->featured)
                                <div class="absolute top-0 right-0 bg-accent/90 text-white text-xs font-bold px-3 py-1 m-3 rounded">
                                    Populaire
                                </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-text-primary">{{ $trip->title }}</h3>
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="ml-1 text-sm font-bold text-text-primary">{{ number_format($trip->rating, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-secondary mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs text-text-secondary">{{ $trip->destination->country ?? 'N/A' }}</span>
                                    <span class="mx-1 text-text-secondary">•</span>
                                    <span class="text-xs text-accent">{{ $trip->vendor->name ?? 'N/A' }}</span>
                                </div>
                                <p class="text-text-secondary text-sm mb-4 line-clamp-2">{{ $trip->description }}</p>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center text-sm text-text-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $trip->duration }} jours
                                    </div>
                                    <div class="text-primary text-lg font-bold">
                                        {{ number_format($trip->price, 0, ',', ' ') }} €
                                    </div>
                                </div>
                                <a href="{{ route('trips.show', $trip->id) }}" class="mt-4 w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-md transition-colors text-center block">
                                    Voir le détail
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        {{ $trips->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Aucun résultat -->
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <div class="mx-auto h-20 w-20 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-text-primary mb-2">Aucun voyage ne correspond à votre recherche</h2>
                        <p class="text-text-secondary mb-6">Essayez de modifier vos critères de recherche pour obtenir plus de résultats.</p>
                        <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('search') }}" class="bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-6 rounded-md transition-colors">
                                Réinitialiser les filtres
                            </a>
                            <a href="{{ route('home') }}" class="bg-white border border-border hover:bg-bg-alt text-text-primary font-medium py-2.5 px-6 rounded-md transition-colors">
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                    
                    <!-- Suggestions de destinations populaires -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-text-primary mb-6">Destinations populaires</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Ces suggestions pourraient être dynamiques, mais ici elles sont statiques pour l'exemple -->
                            <a href="{{ route('search', ['destination' => 'Japon']) }}" class="bg-white rounded-lg shadow-sm overflow-hidden card">
                                <div class="relative h-40 overflow-hidden">
                                    <img src="{{ asset('images/japan.jpg') }}" alt="Japon" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/600/400?text=Japon';this.onerror=null;">
                                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                        <h3 class="text-white text-xl font-bold">Japon</h3>
                                    </div>
                                </div>
                            </a>
                            
                            <a href="{{ route('search', ['destination' => 'Italie']) }}" class="bg-white rounded-lg shadow-sm overflow-hidden card">
                                <div class="relative h-40 overflow-hidden">
                                    <img src="{{ asset('images/italy.jpg') }}" alt="Italie" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/600/400?text=Italie';this.onerror=null;">
                                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                        <h3 class="text-white text-xl font-bold">Italie</h3>
                                    </div>
                                </div>
                            </a>
                            
                            <a href="{{ route('search', ['destination' => 'Thaïlande']) }}" class="bg-white rounded-lg shadow-sm overflow-hidden card">
                                <div class="relative h-40 overflow-hidden">
                                    <img src="{{ asset('images/thailand.jpg') }}" alt="Thaïlande" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/600/400?text=Thaïlande';this.onerror=null;">
                                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                        <h3 class="text-white text-xl font-bold">Thaïlande</h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection