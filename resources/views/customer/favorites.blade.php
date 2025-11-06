@extends('customer.layouts.app')

@section('title', 'Mes favoris')

@section('page-title', 'Mes favoris')

@section('content')
<div class="space-y-6">
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Mes expériences favorites
        </h2>
        <p class="text-text-secondary mt-1">
            Retrouvez ici toutes les expériences que vous avez ajoutées à vos favoris.
        </p>
    </div>

    @if($favorites->isEmpty())
        {{-- Message si aucun favori --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun favori</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore ajouté d'expérience à vos favoris.</p>
                <div class="mt-6">
                    <a href="{{ route('trips.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Découvrir nos offres
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Grille des favoris --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
                @if($favorite->trip)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden group">
                        {{-- Image --}}
                        <div class="relative aspect-w-16 aspect-h-9">
                            @if($favorite->trip->main_image)
                                <img src="{{ Storage::url($favorite->trip->main_image) }}" 
                                     alt="{{ $favorite->trip->title }}"
                                     class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            {{-- Bouton retirer des favoris --}}
                            <div class="absolute top-2 right-2">
                                <button onclick="toggleFavorite({{ $favorite->trip->id }}, this)"
                                        class="p-2 rounded-full bg-white/90 hover:bg-white transition-colors group/btn">
                                    <svg class="h-5 w-5 text-red-500 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Badge type d'offre --}}
                            @if($favorite->trip->offer_type)
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-800">
                                        {{ $favorite->trip->offer_type_label }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Contenu --}}
                        <div class="p-4">
                            {{-- Titre --}}
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                <a href="{{ route('trips.show', $favorite->trip->slug) }}" 
                                   class="hover:text-primary transition-colors">
                                    {{ $favorite->trip->title }}
                                </a>
                            </h3>

                            {{-- Destination --}}
                            @if($favorite->trip->destination)
                                <p class="text-sm text-gray-600 mb-2 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $favorite->trip->destination->name }}
                                </p>
                            @endif

                            {{-- Vendeur --}}
                            @if($favorite->trip->vendor)
                                <p class="text-xs text-gray-500 mb-3">
                                    Par {{ $favorite->trip->vendor->company_name }}
                                </p>
                            @endif

                            {{-- Prix et notation --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-bold text-primary">
                                        {{ number_format($favorite->trip->price, 0, ',', ' ') }} €
                                    </span>
                                    @if($favorite->trip->pricing_mode)
                                        <span class="text-xs text-gray-500 block">
                                            {{ $favorite->trip->price_unit }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Notation --}}
                                @if($favorite->trip->reviews_count > 0)
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="ml-1 text-sm text-gray-600">
                                            {{ number_format($favorite->trip->average_rating, 1) }}
                                            <span class="text-gray-400">({{ $favorite->trip->reviews_count }})</span>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('trips.show', $favorite->trip->slug) }}" 
                                   class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Voir détails
                                </a>
                                <button onclick="checkAvailability({{ $favorite->trip->id }})"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark">
                                    Réserver
                                </button>
                            </div>
                        </div>

                        {{-- Date d'ajout --}}
                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                Ajouté le {{ $favorite->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @endif

    {{-- Bouton retour --}}
    <div class="mt-6">
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au tableau de bord
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFavorite(tripId, button) {
    fetch(`/mon-compte/favoris/toggle/${tripId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'removed') {
            // Retirer la carte de la page avec animation
            const card = button.closest('.group');
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.remove();
                // Vérifier s'il reste des favoris
                const container = document.querySelector('.grid');
                if (container && container.children.length === 0) {
                    location.reload();
                }
            }, 300);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
}

function checkAvailability(tripId) {
    // Rediriger vers la page du voyage pour voir les disponibilités
    window.location.href = `/voyages/${tripId}#availabilities`;
}
</script>
@endpush