@extends('layouts.vendor')

@section('title', 'Éditer la disponibilité')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Éditer la disponibilité</h1>
        <p class="text-text-secondary mt-1">{{ $trip->title }}</p>
    </div>

    @if($availability->bookings_count > 0)
    <div class="bg-warning/10 border-l-4 border-warning p-4 rounded">
        <p class="text-warning font-medium">Attention : Cette disponibilité a {{ $availability->bookings_count }} réservation(s). Certaines modifications peuvent affecter les clients.</p>
    </div>
    @endif

    <form action="{{ route('vendor.trips.availabilities.update', [$trip, $availability]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Période</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Date de début *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $availability->start_date->format('Y-m-d')) }}" 
                           required min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg"
                           {{ $availability->bookings_count > 0 ? 'readonly' : '' }}>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Date de fin *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $availability->end_date->format('Y-m-d')) }}" 
                           required min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg"
                           {{ $availability->bookings_count > 0 ? 'readonly' : '' }}>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Places</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Places totales *</label>
                    <input type="number" name="spots_total" value="{{ old('spots_total', $availability->spots_total) }}" 
                           required min="{{ $availability->bookings_count }}" class="w-full px-4 py-2 border rounded-lg">
                    @if($availability->bookings_count > 0)
                    <p class="text-sm text-text-secondary mt-1">Minimum {{ $availability->bookings_count }} (réservations existantes)</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Places disponibles</label>
                    <input type="text" value="{{ $availability->spots_available }}" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                    <p class="text-sm text-text-secondary mt-1">Calculé automatiquement</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Tarification</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Prix de base *</label>
                <input type="number" name="price" value="{{ old('price', $availability->price) }}" 
                       required min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Réduction (%)</label>
                    <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $availability->discount_percentage) }}" 
                           min="0" max="100" class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Prix final</label>
                    <input type="number" name="final_price" value="{{ old('final_price', $availability->final_price) }}" 
                           min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('vendor.trips.availabilities.index', $trip) }}" class="text-text-secondary hover:text-primary">Annuler</a>
            <button type="submit" class="btn bg-primary text-white px-6 py-3 rounded-lg">Enregistrer les modifications</button>
        </div>
    </form>
</div>
@endsection
