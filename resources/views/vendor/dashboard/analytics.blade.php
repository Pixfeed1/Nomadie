@extends('layouts.vendor')

@section('title', 'Analytics - Dashboard Organisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics détaillées</h1>
        <p class="text-gray-600 mt-2">Analyse approfondie de vos performances</p>
    </div>

    <!-- Statistiques de limites -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Limites de votre abonnement</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-2">Voyages utilisés</p>
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $detailedStats['trip_limit_stats']['percentage_used'] }}%"></div>
                        </div>
                    </div>
                    <span class="ml-4 text-lg font-bold">{{ $detailedStats['trip_limit_stats']['current_trips'] }} / {{ $detailedStats['trip_limit_stats']['max_trips'] }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $detailedStats['trip_limit_stats']['remaining'] }} restants</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 mb-2">Plan d'abonnement</p>
                <p class="text-2xl font-bold text-gray-900">{{ ucfirst($vendor->subscription_plan) }}</p>
                <p class="text-xs text-gray-500 mt-1">Commission: {{ $vendor->commission_rate }}%</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 mb-2">Taux de remplissage moyen</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($detailedStats['availability_stats']['avg_fill_rate'], 1) }}%</p>
                <p class="text-xs text-gray-500 mt-1">Sur disponibilités à venir</p>
            </div>
        </div>
    </div>

    <!-- Répartition des voyages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Par destination -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Voyages par destination</h2>
            <div class="space-y-3">
                @forelse($detailedStats['trips_by_destination'] as $destination => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $destination }}</span>
                        <span class="text-gray-900 font-medium">{{ $count }} voyages</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($count / $detailedStats['trips_by_destination']->sum()) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                @endforelse
            </div>
        </div>

        <!-- Par type -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Voyages par type</h2>
            <div class="space-y-3">
                @forelse($detailedStats['trips_by_type'] as $type => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $type }}</span>
                        <span class="text-gray-900 font-medium">{{ $count }} voyages</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($count / $detailedStats['trips_by_type']->sum()) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Statistiques de prix -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Prix moyen</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($detailedStats['avg_trip_price'], 2) }}€</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Voyage le plus cher</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($detailedStats['most_expensive_trip'], 2) }}€</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Voyage le moins cher</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($detailedStats['cheapest_trip'], 2) }}€</p>
        </div>
    </div>

    <!-- Statistiques des disponibilités -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistiques des disponibilités</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600">Disponibilités à venir</p>
                <p class="text-2xl font-bold text-gray-900">{{ $detailedStats['availability_stats']['total_future_availabilities'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Départs garantis</p>
                <p class="text-2xl font-bold text-green-600">{{ $detailedStats['availability_stats']['guaranteed_departures'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Complets</p>
                <p class="text-2xl font-bold text-orange-600">{{ $detailedStats['availability_stats']['full_availabilities'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Taux de remplissage</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($detailedStats['availability_stats']['avg_fill_rate'], 1) }}%</p>
            </div>
        </div>
    </div>

    <!-- Graphique annuel -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Évolution sur 12 mois</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mois</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voyages créés</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disponibilités</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réservations</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenus</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($yearlyData['monthly_data'] as $month)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['month'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $month['trips_created'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $month['availabilities_starting'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $month['bookings'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($month['revenue'], 2) }}€</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-8 flex gap-4">
        <a href="{{ route('vendor.dashboard.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition">
            ← Retour au dashboard
        </a>
        <a href="{{ route('vendor.reports.sales') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
            Voir les rapports détaillés
        </a>
    </div>
</div>
@endsection
