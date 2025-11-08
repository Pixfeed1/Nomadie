@extends('layouts.vendor')

@section('title', 'Mes Réservations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mes Réservations</h1>
        <p class="text-gray-600 mt-1">Gérez toutes les réservations de vos voyages</p>
    </div>

    <!-- Formulaire de filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('vendor.bookings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filtre par statut -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminée</option>
                </select>
            </div>

            <!-- Filtre par voyage -->
            <div>
                <label for="trip_id" class="block text-sm font-medium text-gray-700 mb-2">Voyage</label>
                <select name="trip_id" id="trip_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les voyages</option>
                    @foreach(Auth::user()->vendor->trips as $trip)
                        <option value="{{ $trip->id }}" {{ request('trip_id') == $trip->id ? 'selected' : '' }}>
                            {{ $trip->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date début -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Du</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Date fin -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Au</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Boutons -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filtrer
                </button>
                <a href="{{ route('vendor.bookings.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Réinitialiser les filtres
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des réservations -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Référence
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Voyage
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date départ
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participants
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $booking->reference ?? '#' . $booking->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $booking->trip->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->start_date ? $booking->start_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($booking->adults ?? 0) + ($booking->children ?? 0) }} pers.
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ number_format($booking->total_price ?? 0, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'confirmed' => 'Confirmée',
                                            'cancelled' => 'Annulée',
                                            'completed' => 'Terminée',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('vendor.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900">
                                        Voir détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        @else
            <!-- Message "Aucun résultat" -->
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune réservation trouvée</h3>
                <p class="text-gray-500 mb-4">
                    @if(request()->hasAny(['status', 'trip_id', 'date_from', 'date_to']))
                        Aucune réservation ne correspond aux filtres sélectionnés.
                        <br>
                        Essayez d'ajuster vos critères de recherche.
                    @else
                        Vous n'avez pas encore de réservations pour vos voyages.
                    @endif
                </p>
                @if(request()->hasAny(['status', 'trip_id', 'date_from', 'date_to']))
                    <a href="{{ route('vendor.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Actions rapides -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('vendor.bookings.exportCsv') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
            Exporter CSV
        </a>
        <a href="{{ route('vendor.bookings.exportPdf') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
            Exporter PDF
        </a>
    </div>
</div>
@endsection
