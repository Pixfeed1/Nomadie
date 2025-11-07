@extends('layouts.vendor')

@section('title', 'Rapport des ventes')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Rapport des ventes</h1>
        <p class="text-gray-600 mt-2">Analysez vos réservations et revenus</p>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('vendor.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    Filtrer
                </button>
                <a href="{{ route('vendor.reports.export.sales', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                    Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques de la période -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total réservations</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Confirmées</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Annulées</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Revenus</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_revenue'], 2) }}€</p>
        </div>
    </div>

    <!-- Liste des réservations -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Réservation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voyage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voyageurs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $booking->booking_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $booking->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $booking->trip->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $booking->user->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $booking->number_of_travelers }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ number_format($booking->total_amount, 2) }}€
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($booking->status === 'confirmed' || $booking->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $booking->status }}</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $booking->status }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $booking->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Aucune réservation pour cette période
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
    @endif

    <!-- Bouton retour -->
    <div class="mt-8">
        <a href="{{ route('vendor.dashboard.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition">
            ← Retour au dashboard
        </a>
    </div>
</div>
@endsection
