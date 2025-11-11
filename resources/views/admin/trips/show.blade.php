@extends('layouts.admin')

@section('title', $trip->title)

@section('header-left')
    <div class="flex items-center">
        <a href="{{ route('admin.trips.index') }}" class="mr-4 text-text-secondary hover:text-primary">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">{{ $trip->title }}</h1>
            <p class="text-sm text-text-secondary mt-1">Par {{ $trip->vendor->company_name }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Réservations Totales</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ $stats['total_bookings'] }}</p>
            <p class="text-xs text-text-secondary mt-2">{{ $stats['pending_bookings'] }} en attente</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Revenus Totaux</p>
            <p class="text-3xl font-bold text-success mt-2">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} €</p>
            <p class="text-xs text-text-secondary mt-2">{{ number_format($stats['pending_revenue'], 0, ',', ' ') }} € en attente</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Voyageurs Totaux</p>
            <p class="text-3xl font-bold text-accent mt-2">{{ $stats['total_travelers'] }}</p>
            <p class="text-xs text-text-secondary mt-2">{{ $stats['total_views'] }} vues</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Note Moyenne</p>
            <p class="text-3xl font-bold text-text-primary mt-2">{{ number_format($stats['avg_rating'], 1) }} / 5</p>
            <p class="text-xs text-text-secondary mt-2">{{ $stats['total_reviews'] }} avis</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails de l'expérience -->
            <div class="bg-white rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Détails de l'expérience</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-text-secondary">Type</p>
                        <p class="text-sm font-medium text-text-primary mt-1">{{ $trip->type_text }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Statut</p>
                        <p class="text-sm font-medium text-text-primary mt-1">{{ ucfirst($trip->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Destination</p>
                        <p class="text-sm font-medium text-text-primary mt-1">{{ $trip->destination->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Prix</p>
                        <p class="text-sm font-medium text-text-primary mt-1">{{ number_format($trip->price, 0, ',', ' ') }} {{ $trip->price_unit }}</p>
                    </div>
                </div>
            </div>

            <!-- Réservations récentes -->
            <div class="bg-white rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Réservations Récentes</h3>
                <div class="space-y-3">
                    @forelse($recentBookings as $booking)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-primary/10 p-2 rounded-full mr-3">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-text-primary">{{ $booking->user->name ?? 'Client' }}</p>
                                <p class="text-xs text-text-secondary">{{ $booking->booking_number }} - {{ $booking->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-text-primary">{{ number_format($booking->total_amount, 0, ',', ' ') }} €</p>
                            <span class="text-xs px-2 py-1 rounded-full {{ $booking->status === 'confirmed' ? 'bg-success/10 text-success' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-text-secondary text-center py-4">Aucune réservation</p>
                    @endforelse
                </div>
            </div>

            <!-- Avis -->
            <div class="bg-white rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Avis ({{ $reviews->count() }})</h3>
                <div class="space-y-4">
                    @forelse($reviews as $review)
                    <div class="border-b border-border pb-4 last:border-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-sm font-medium text-text-primary">{{ $review->user->name ?? 'Anonyme' }}</p>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-text-secondary">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-text-primary">{{ $review->comment }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-text-secondary text-center py-4">Aucun avis</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Actions</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.trips.toggleStatus', $trip) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $trip->status === 'active' ? 'bg-gray-500' : 'bg-success' }} text-white rounded-lg hover:opacity-90 transition-opacity">
                            {{ $trip->status === 'active' ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.trips.toggleFeatured', $trip) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $trip->featured ? 'bg-gray-500' : 'bg-accent' }} text-white rounded-lg hover:opacity-90 transition-opacity">
                            {{ $trip->featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                        </button>
                    </form>

                    <a href="{{ route('trips.show', $trip->slug) }}" target="_blank" class="block w-full px-4 py-2 bg-primary text-white text-center rounded-lg hover:bg-primary-dark transition-colors">
                        Voir sur le site
                    </a>
                </div>
            </div>

            <!-- Statistiques avancées -->
            <div class="bg-white rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Statistiques</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Taux de conversion</span>
                        <span class="text-sm font-medium text-text-primary">{{ $stats['conversion_rate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Vues totales</span>
                        <span class="text-sm font-medium text-text-primary">{{ number_format($stats['total_views']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Réservations annulées</span>
                        <span class="text-sm font-medium text-text-primary">{{ $stats['cancelled_bookings'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
