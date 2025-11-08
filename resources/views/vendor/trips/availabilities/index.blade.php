@extends('layouts.vendor')

@section('title', 'Disponibilités - ' . $trip->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Disponibilités</h1>
            <p class="text-text-secondary mt-1">{{ $trip->title }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('vendor.trips.availabilities.create', $trip) }}" class="btn bg-primary text-white px-4 py-2 rounded-lg">+ Ajouter</a>
            <a href="{{ route('vendor.trips.show', $trip) }}" class="btn bg-gray-200 px-4 py-2 rounded-lg">Retour</a>
        </div>
    </div>

    @if($availabilities->count() === 0)
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <svg class="h-16 w-16 mx-auto text-text-secondary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="text-xl font-semibold mb-2">Aucune disponibilité</h3>
        <p class="text-text-secondary mb-6">Créez vos premières disponibilités pour que les clients puissent réserver.</p>
        <a href="{{ route('vendor.trips.availabilities.create', $trip) }}" class="btn bg-primary text-white px-6 py-3 rounded-lg inline-block">Créer une disponibilité</a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-border">
            <thead class="bg-bg-alt">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase">Places</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase">Prix</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach($availabilities as $availability)
                <tr class="hover:bg-bg-alt transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $availability->start_date->format('d/m/Y') }}</div>
                        <div class="text-sm text-text-secondary">au {{ $availability->end_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ $availability->spots_available }} / {{ $availability->spots_total }}</div>
                        <div class="text-sm text-text-secondary">disponibles</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ number_format($availability->price, 0) }} {{ $trip->currency }}</div>
                        @if($availability->discount_percentage > 0)
                        <div class="text-sm text-success">-{{ $availability->discount_percentage }}%</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($availability->spots_available === 0)
                            <span class="badge badge-error">Complet</span>
                        @elseif($availability->start_date < now())
                            <span class="badge badge-secondary">Passée</span>
                        @else
                            <span class="badge badge-success">Disponible</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('vendor.trips.availabilities.edit', [$trip, $availability]) }}" class="text-primary hover:text-primary-dark">Éditer</a>
                            @if($availability->bookings_count === 0)
                            <form action="{{ route('vendor.trips.availabilities.destroy', [$trip, $availability]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Supprimer ?')" class="text-error hover:text-error/80">Supprimer</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
