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
                    <x-trip-card :trip="$favorite->trip" :showVendor="true" />
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