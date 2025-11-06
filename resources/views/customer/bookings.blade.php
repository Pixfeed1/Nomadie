@extends('customer.layouts.dashboard')

@section('title', 'Mes réservations')

@section('page-title', 'Mes réservations')

@section('dashboard-content')
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Mes réservations
        </h2>
        <p class="text-text-secondary mt-1">
            Gérez et suivez toutes vos réservations.
        </p>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Total"
            :value="$stats['total_bookings']"
            icon="calendar"
            color="primary"
        />

        <x-stat-card
            title="À venir"
            :value="$stats['upcoming_bookings']"
            icon="clock"
            color="accent"
        />

        <x-stat-card
            title="Terminées"
            :value="$stats['past_bookings']"
            icon="check-circle"
            color="success"
        />

        <x-stat-card
            title="Total dépensé"
            :value="number_format($stats['total_spent'], 0, ',', ' ') . ' €'"
            icon="currency"
            color="warning"
        />
    </div>

    @if($bookings->isEmpty())
        {{-- Message si aucune réservation --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore effectué de réservation.</p>
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
        {{-- Filtres --}}
        <div class="bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="{{ route('customer.bookings') }}" class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700">Statut:</label>
                    <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700">Trier par:</label>
                    <select name="sort" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Plus récent</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Plus ancien</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Liste des réservations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    {{-- Header de la carte --}}
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $booking->booking_number ?? 'RES-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $booking->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $booking->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($booking->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>

                    {{-- Image et titre --}}
                    @if($booking->trip && $booking->trip->main_image)
                        <img src="{{ Storage::url($booking->trip->main_image) }}" 
                             alt="{{ $booking->trip->title }}"
                             class="h-48 w-full object-cover">
                    @else
                        <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Contenu --}}
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            @if($booking->trip)
                                <a href="{{ route('trips.show', $booking->trip->slug) }}" class="hover:text-primary">
                                    {{ $booking->trip->title }}
                                </a>
                            @else
                                Offre non disponible
                            @endif
                        </h3>

                        <dl class="space-y-2 text-sm">
                            @if($booking->vendor)
                                <div class="flex items-center">
                                    <dt class="text-gray-500">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </dt>
                                    <dd class="text-gray-900">{{ $booking->vendor->company_name ?? 'N/A' }}</dd>
                                </div>
                            @endif

                            @if($booking->availability)
                                <div class="flex items-center">
                                    <dt class="text-gray-500">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </dt>
                                    <dd class="text-gray-900">
                                        {{ $booking->availability->start_date->format('d/m/Y') }} - 
                                        {{ $booking->availability->end_date->format('d/m/Y') }}
                                    </dd>
                                </div>
                            @endif

                            <div class="flex items-center">
                                <dt class="text-gray-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </dt>
                                <dd class="text-gray-900">
                                    {{ $booking->number_of_travelers ?? 1 }} personne(s)
                                </dd>
                            </div>

                            <div class="flex items-center">
                                <dt class="text-gray-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </dt>
                                <dd class="text-gray-900 font-semibold text-primary">
                                    {{ number_format($booking->total_price ?? 0, 2, ',', ' ') }} €
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between">
                            <div class="flex space-x-2">
                                <a href="{{ route('customer.bookings.show', $booking->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Détails
                                </a>

                                @if($booking->status == 'completed' && !$booking->hasReview())
                                    <a href="{{ route('customer.reviews.create', $booking->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-yellow-300 shadow-sm text-xs font-medium rounded text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        Donner un avis
                                    </a>
                                @endif
                            </div>

                            @if($booking->canBeCancelled())
                                <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100">
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Annuler
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $bookings->links() }}
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
@endsection