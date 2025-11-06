@extends('customer.layouts.app')

@section('title', 'Mes avis')

@section('page-title', 'Mes avis')

@section('content')
<div class="space-y-6">
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Mes avis et commentaires
        </h2>
        <p class="text-text-secondary mt-1">
            Retrouvez tous les avis que vous avez laissés sur vos expériences passées.
        </p>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <div class="alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($reviews->isEmpty())
        {{-- Message si aucun avis --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun avis</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore laissé d'avis sur vos expériences.</p>
                <div class="mt-6">
                    <a href="{{ route('customer.bookings') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Voir mes réservations
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Liste des avis --}}
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            {{-- Image de l'offre --}}
                            <div class="flex-shrink-0">
                                @if($review->trip && $review->trip->main_image)
                                    <img src="{{ Storage::url($review->trip->main_image) }}" 
                                         alt="{{ $review->trip->title }}"
                                         class="h-24 w-24 rounded-lg object-cover">
                                @else
                                    <div class="h-24 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Contenu de l'avis --}}
                            <div class="flex-1 min-w-0">
                                {{-- En-tête avec titre et notation --}}
                                <div class="flex items-start justify-between">
                                    <div>
                                        @if($review->trip)
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('trips.show', $review->trip->slug) }}" class="hover:text-primary">
                                                    {{ $review->trip->title }}
                                                </a>
                                            </h3>
                                            @if($review->trip->vendor)
                                                <p class="text-sm text-gray-600">
                                                    Par {{ $review->trip->vendor->company_name }}
                                                </p>
                                            @endif
                                        @else
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                Offre non disponible
                                            </h3>
                                        @endif
                                    </div>

                                    {{-- Notation --}}
                                    <div class="flex items-center">
                                        <x-rating-stars :rating="$review->rating" size="md" />
                                        <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                    </div>
                                </div>

                                {{-- Contenu de l'avis --}}
                                <div class="mt-4">
                                    <p class="text-gray-700 leading-relaxed">
                                        {{ $review->content }}
                                    </p>
                                </div>

                                {{-- Informations supplémentaires --}}
                                <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
                                    @if($review->travel_date)
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Expérience du {{ \Carbon\Carbon::parse($review->travel_date)->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Avis laissé le {{ $review->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                {{-- Statut de l'avis --}}
                                @if($review->status)
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $review->status == 'published' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $review->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $review->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $review->status == 'published' ? 'Publié' : '' }}
                                            {{ $review->status == 'pending' ? 'En attente de validation' : '' }}
                                            {{ $review->status == 'rejected' ? 'Non publié' : '' }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Réponse du vendeur si elle existe --}}
                                @if($review->vendor_response)
                                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Réponse du prestataire :</p>
                                        <p class="text-sm text-gray-700">{{ $review->vendor_response }}</p>
                                        @if($review->vendor_response_date)
                                            <p class="text-xs text-gray-500 mt-2">
                                                Répondu le {{ \Carbon\Carbon::parse($review->vendor_response_date)->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                @if($review->trip)
                                    <a href="{{ route('trips.show', $review->trip->slug) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir l'offre
                                    </a>
                                @endif
                                
                                @if($review->booking_id)
                                    <a href="{{ route('customer.bookings.show', $review->booking_id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Voir la réservation
                                    </a>
                                @endif
                            </div>

                            {{-- Nombre de personnes ayant trouvé l'avis utile --}}
                            @if($review->helpful_count > 0)
                                <div class="text-xs text-gray-500">
                                    <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    {{ $review->helpful_count }} {{ Str::plural('personne', $review->helpful_count) }} {{ $review->helpful_count > 1 ? 'ont' : 'a' }} trouvé cet avis utile
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $reviews->links() }}
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