@extends('layouts.vendor')

@section('title', 'Dashboard Organisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-gray-600 mt-2">Bienvenue {{ $vendor->company_name }}</p>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total des voyages -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Voyages totaux</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_trips'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_trips'] }} actifs</p>
                </div>
                <div class="text-blue-500">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Réservations -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Réservations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Toutes périodes</p>
                </div>
                <div class="text-green-500">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Revenus totaux</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 2) }}€</p>
                    <p class="text-xs text-gray-500 mt-1">Commission {{ $stats['commission_rate'] }}%</p>
                </div>
                <div class="text-yellow-500">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Note moyenne -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Note moyenne</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_rating'], 1) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sur 5.0</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique et voyages récents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique des 6 derniers mois -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Activité des 6 derniers mois</h2>
            <div class="space-y-4">
                @foreach($chartData['monthly_stats'] as $month)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $month['month'] }}</span>
                        <span class="text-gray-900 font-medium">{{ $month['bookings'] }} réservations</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($month['bookings'] / max(1, $chartData['monthly_stats']->max('bookings'))) * 100, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($month['revenue'], 2) }}€</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Voyages récents -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Voyages récents</h2>
            <div class="space-y-4">
                @forelse($recentTrips as $trip)
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="font-medium text-gray-900">{{ $trip->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $trip->destination->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $trip->availabilities->count() }} disponibilités</p>
                    <p class="text-xs text-gray-400">Créé le {{ $trip->created_at->format('d/m/Y') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucun voyage récent</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('vendor.trips.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center transition">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <h3 class="font-semibold">Créer un voyage</h3>
        </a>

        <a href="{{ route('vendor.dashboard.analytics') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center transition">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="font-semibold">Voir les analytics</h3>
        </a>

        <a href="{{ route('vendor.reports.sales') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center transition">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="font-semibold">Rapports</h3>
        </a>
    </div>
</div>
@endsection
