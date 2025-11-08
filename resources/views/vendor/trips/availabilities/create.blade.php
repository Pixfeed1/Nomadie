@extends('layouts.vendor')

@section('title', 'Créer une disponibilité')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Créer une disponibilité</h1>
        <p class="text-text-secondary mt-1">{{ $trip->title }}</p>
    </div>

    <form action="{{ route('vendor.trips.availabilities.store', $trip) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Période</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Date de début *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required 
                           min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg">
                    @error('start_date')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Date de fin *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required 
                           min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg">
                    @error('end_date')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @if($trip->duration)
            <p class="text-sm text-text-secondary mt-2">Durée du trip: {{ $trip->duration }} jours</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Places disponibles</h2>

            <div>
                <label class="block text-sm font-medium mb-2">Nombre de places *</label>
                <input type="number" name="spots_total" value="{{ old('spots_total', $trip->max_travelers ?? 1) }}" 
                       required min="1" class="w-full px-4 py-2 border rounded-lg">
                @error('spots_total')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Tarification</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Prix de base *</label>
                <input type="number" name="price" value="{{ old('price', $trip->price) }}" 
                       required min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-sm text-text-secondary mt-1">Prix par défaut: {{ number_format($trip->price, 0) }} {{ $trip->currency }}</p>
                @error('price')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Réduction (%)</label>
                    <input type="number" name="discount_percentage" value="{{ old('discount_percentage', 0) }}" 
                           min="0" max="100" class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Prix final</label>
                    <input type="number" name="final_price" value="{{ old('final_price') }}" 
                           min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-sm text-text-secondary mt-1">Calculé automatiquement si vide</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('vendor.trips.availabilities.index', $trip) }}" class="text-text-secondary hover:text-primary">Annuler</a>
            <button type="submit" class="btn bg-primary text-white px-6 py-3 rounded-lg">Créer la disponibilité</button>
        </div>
    </form>
</div>
@endsection
