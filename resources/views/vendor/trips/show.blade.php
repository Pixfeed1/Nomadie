@extends('layouts.vendor')

@section('title', $trip->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">{{ $trip->title }}</h1>
            <div class="flex items-center gap-4 mt-2">
                <span class="badge badge-{{ $trip->status === 'active' ? 'success' : ($trip->status === 'draft' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($trip->status) }}
                </span>
                <span class="text-text-secondary">{{ $trip->destination->name ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('vendor.trips.edit', $trip) }}" class="btn bg-primary text-white px-4 py-2 rounded-lg">Éditer</a>
            <a href="{{ route('vendor.trips.availabilities.index', $trip) }}" class="btn bg-success text-white px-4 py-2 rounded-lg">Disponibilités</a>
            <a href="{{ route('vendor.trips.preview', $trip) }}" target="_blank" class="btn bg-gray-200 px-4 py-2 rounded-lg">Aperçu</a>
        </div>
    </div>

    @if($trip->images && count($trip->images) > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4">Images</h2>
        <div class="grid grid-cols-4 gap-4">
            @foreach($trip->images as $image)
            <img src="{{ Storage::url($image['path']) }}" class="w-full h-40 object-cover rounded-lg">
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Description</h2>
                <p class="text-text-secondary mb-4">{{ $trip->short_description }}</p>
                <div class="prose max-w-none">{{ $trip->description }}</div>
            </div>

            @if($trip->offer_type === 'accommodation')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Détails hébergement</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Capacité:</strong> {{ $trip->property_capacity }} pers.</div>
                    <div><strong>Chambres:</strong> {{ $trip->bedrooms }}</div>
                    <div><strong>Salles de bain:</strong> {{ $trip->bathrooms }}</div>
                    <div><strong>Séjour min:</strong> {{ $trip->min_nights }} nuits</div>
                </div>
            </div>
            @endif

            @if($trip->offer_type === 'organized_trip')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Détails séjour</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Durée:</strong> {{ $trip->duration }} jours</div>
                    <div><strong>Max voyageurs:</strong> {{ $trip->max_travelers }}</div>
                    <div><strong>Niveau:</strong> {{ ucfirst($trip->physical_level) }}</div>
                    <div><strong>Repas:</strong> {{ ucfirst($trip->meal_plan) }}</div>
                </div>
                <div class="mt-4"><strong>Point rencontre:</strong> {{ $trip->meeting_point }}</div>
            </div>
            @endif

            @if($trip->offer_type === 'activity')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Détails activité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Durée:</strong> {{ $trip->duration_hours }}h</div>
                    <div><strong>Max participants:</strong> {{ $trip->max_participants }}</div>
                    <div><strong>Équipement:</strong> {{ $trip->equipment_included ? 'Inclus' : 'Non inclus' }}</div>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Tarif</h2>
                <div class="text-3xl font-bold text-primary mb-2">{{ number_format($trip->price, 0) }} {{ $trip->currency }}</div>
                <p class="text-sm text-text-secondary">Prix de base</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Infos</h2>
                <div class="space-y-2 text-sm">
                    <div><strong>Type:</strong> {{ $trip->travelType->name ?? 'N/A' }}</div>
                    <div><strong>Créée:</strong> {{ $trip->created_at->format('d/m/Y') }}</div>
                    <div><strong>Modifiée:</strong> {{ $trip->updated_at->format('d/m/Y') }}</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                <div class="space-y-2">
                    <form action="{{ route('vendor.trips.toggle-status', $trip) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full btn bg-warning text-white py-2 rounded-lg">
                            {{ $trip->status === 'active' ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>

                    <form action="{{ route('vendor.trips.duplicate', $trip) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full btn bg-info text-white py-2 rounded-lg">Dupliquer</button>
                    </form>

                    <form action="{{ route('vendor.trips.destroy', $trip) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn bg-error text-white py-2 rounded-lg">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('vendor.trips.index') }}" class="text-text-secondary hover:text-primary">← Retour à mes offres</a>
    </div>
</div>
@endsection
