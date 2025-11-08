@extends('layouts.vendor')

@section('title', 'Mes Offres')

@section('content')
<div class="space-y-6">
    <!-- Header avec stats -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-text-primary">Mes Offres</h1>
            <p class="text-text-secondary mt-1">
                {{ $stats['total'] }} / {{ $stats['limit'] }} offre{{ $stats['total'] > 1 ? 's' : '' }} créée{{ $stats['total'] > 1 ? 's' : '' }}
            </p>
        </div>

        @if($stats['can_create'])
            <a href="{{ route('vendor.trips.choose-type') }}" class="btn bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg inline-flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Créer une offre
            </a>
        @else
            <div class="bg-warning/10 border border-warning text-warning px-4 py-3 rounded-lg">
                <p class="font-medium">Limite atteinte</p>
                <p class="text-sm mt-1">Vous avez atteint la limite de votre plan. Améliorez votre abonnement pour créer plus d'offres.</p>
            </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Total</p>
                    <p class="text-2xl font-bold text-text-primary">{{ $stats['total'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Actives</p>
                    <p class="text-2xl font-bold text-success">{{ $stats['active'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Brouillons</p>
                    <p class="text-2xl font-bold text-warning">{{ $stats['draft'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-warning/10 flex items-center justify-center text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Inactives</p>
                    <p class="text-2xl font-bold text-text-secondary">{{ $stats['inactive'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center text-text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" placeholder="Rechercher une offre..."
                   value="{{ request('search') }}"
                   class="px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">

            <select name="status" class="px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillons</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactives</option>
            </select>

            <select name="offer_type" class="px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Tous les types</option>
                <option value="accommodation" {{ request('offer_type') == 'accommodation' ? 'selected' : '' }}>Hébergement</option>
                <option value="organized_trip" {{ request('offer_type') == 'organized_trip' ? 'selected' : '' }}>Séjour organisé</option>
                <option value="activity" {{ request('offer_type') == 'activity' ? 'selected' : '' }}>Activité</option>
                <option value="custom" {{ request('offer_type') == 'custom' ? 'selected' : '' }}>Sur mesure</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Liste des offres -->
    @if($trips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($trips as $trip)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow card">
                <!-- Image -->
                <div class="relative h-48 bg-gray-200">
                    @if($trip->images && count($trip->images) > 0)
                        <img src="{{ Storage::url($trip->images[0]['path']) }}" alt="{{ $trip->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Badge statut -->
                    <div class="absolute top-2 right-2">
                        @if($trip->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($trip->status === 'draft')
                            <span class="badge badge-warning">Brouillon</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>

                    <!-- Badge type -->
                    <div class="absolute top-2 left-2">
                        @if($trip->offer_type === 'accommodation')
                            <span class="badge badge-primary">Hébergement</span>
                        @elseif($trip->offer_type === 'organized_trip')
                            <span class="badge badge-info">Séjour</span>
                        @elseif($trip->offer_type === 'activity')
                            <span class="badge badge-success">Activité</span>
                        @else
                            <span class="badge badge-secondary">Sur mesure</span>
                        @endif
                    </div>
                </div>

                <!-- Contenu -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-text-primary mb-2">{{ Str::limit($trip->title, 50) }}</h3>
                    <p class="text-sm text-text-secondary mb-4">{{ Str::limit($trip->short_description, 80) }}</p>

                    <div class="flex items-center text-sm text-text-secondary mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $trip->destination->name ?? 'N/A' }}
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-bold text-primary">
                            {{ number_format($trip->price, 0, ',', ' ') }} {{ $trip->currency }}
                        </div>
                        <div class="text-sm text-text-secondary">
                            @if($trip->availabilities->count() > 0)
                                <span class="text-success">{{ $trip->availabilities->count() }} dispo(s)</span>
                            @else
                                <span class="text-error">Aucune dispo</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('vendor.trips.show', $trip) }}" class="flex-1 btn bg-gray-100 hover:bg-gray-200 text-text-primary px-3 py-2 rounded-lg text-sm text-center transition-colors">
                            Voir
                        </a>
                        <a href="{{ route('vendor.trips.edit', $trip) }}" class="flex-1 btn bg-primary text-white hover:bg-primary-dark px-3 py-2 rounded-lg text-sm text-center transition-colors">
                            Éditer
                        </a>
                        <a href="{{ route('vendor.trips.availabilities.index', $trip) }}" class="flex-1 btn bg-success text-white hover:bg-success/80 px-3 py-2 rounded-lg text-sm text-center transition-colors">
                            Dispos
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $trips->links() }}
        </div>
    @else
        <!-- État vide -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-text-secondary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 class="text-xl font-semibold text-text-primary mb-2">Aucune offre trouvée</h3>
            <p class="text-text-secondary mb-6">Commencez par créer votre première offre pour attirer des voyageurs.</p>
            @if($stats['can_create'])
                <a href="{{ route('vendor.trips.choose-type') }}" class="btn bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg inline-flex items-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Créer ma première offre
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
