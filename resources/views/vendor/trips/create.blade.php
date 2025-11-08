@extends('layouts.vendor')

@section('title', 'Créer une offre')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Créer une offre</h1>
            <p class="text-text-secondary mt-1">{{ $offerTypeInfo['name'] }}</p>
        </div>
        <a href="{{ route('vendor.trips.choose-type') }}" class="text-text-secondary hover:text-primary">Changer de type</a>
    </div>

    @if($destinationMessage)
    <div class="bg-warning/10 border-l-4 border-warning text-warning p-4 rounded">{{ $destinationMessage }}</div>
    @endif

    <form action="{{ route('vendor.trips.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="offer_type" value="{{ $type }}">

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Informations de base</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-text-primary mb-2">Titre *</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 @error('title') border-error @enderror">
                @error('title')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Destination *</label>
                    <select name="country_id" required class="w-full px-4 py-2 border rounded-lg @error('country_id') border-error @enderror">
                        <option value="">Sélectionner</option>
                        @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Type de voyage *</label>
                    <select name="travel_type_id" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Sélectionner</option>
                        @foreach($travelTypes as $tt)
                        <option value="{{ $tt->id }}">{{ $tt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-text-primary mb-2">Description courte * (500 car. max)</label>
                <textarea name="short_description" rows="3" required maxlength="500" class="w-full px-4 py-2 border rounded-lg">{{ old('short_description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">Description complète * (5000 car. max)</label>
                <textarea name="description" rows="8" required maxlength="5000" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Tarification</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Prix de base *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Devise *</label>
                    <select name="currency" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="EUR">EUR (€)</option>
                        <option value="USD">USD ($)</option>
                        <option value="GBP">GBP (£)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Images * (min 5, max 20)</h2>
            <input type="file" name="images[]" multiple required accept="image/*" class="w-full px-4 py-2 border rounded-lg">
            <p class="text-sm text-text-secondary mt-2">JPG, PNG ou WEBP - Max 5MB par image</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Langues * (min 1)</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($popularLanguages as $lang)
                <label class="flex items-center">
                    <input type="checkbox" name="languages[]" value="{{ $lang->id }}" class="mr-2">
                    <span>{{ $lang->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        @if($type === 'accommodation')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Détails hébergement</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Capacité (pers.) *</label>
                    <input type="number" name="property_capacity" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Chambres *</label>
                    <input type="number" name="bedrooms" required min="0" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Salles de bain *</label>
                    <input type="number" name="bathrooms" required min="0" step="0.5" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Séjour min (nuits) *</label>
                    <input type="number" name="min_nights" value="1" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>
        @endif

        @if($type === 'organized_trip')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Détails séjour</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Durée (jours) *</label>
                    <input type="number" name="duration" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max voyageurs *</label>
                    <input type="number" name="max_travelers" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Niveau physique *</label>
                    <select name="physical_level" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="easy">Facile</option>
                        <option value="moderate">Modéré</option>
                        <option value="challenging">Difficile</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Formule repas *</label>
                    <select name="meal_plan" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="none">Aucun</option>
                        <option value="breakfast">Petit-déj</option>
                        <option value="half_board">Demi-pension</option>
                        <option value="full_board">Pension complète</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Point de rencontre *</label>
                <input type="text" name="meeting_point" required class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        @endif

        @if($type === 'activity')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Détails activité</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Durée (heures) *</label>
                    <input type="number" name="duration_hours" required min="0.5" step="0.5" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max participants *</label>
                    <input type="number" name="max_participants" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
            <label class="flex items-center mb-4">
                <input type="checkbox" name="equipment_included" value="1" class="mr-2">
                <span>Équipement inclus</span>
            </label>
            <div>
                <label class="block text-sm font-medium mb-2">Liste équipement</label>
                <textarea name="equipment_list" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
            </div>
        </div>
        @endif

        @if($type === 'custom')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Détails sur mesure</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Description tarification *</label>
                <textarea name="pricing_description" rows="3" required class="w-full px-4 py-2 border rounded-lg"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Durée indicative (jours)</label>
                <input type="number" name="duration" min="1" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Statut</h2>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="status" value="draft" checked class="mr-2">
                    <span>Brouillon</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="active" class="mr-2">
                    <span>Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('vendor.trips.index') }}" class="text-text-secondary hover:text-primary">Annuler</a>
            <button type="submit" class="btn bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg">Créer l'offre</button>
        </div>
    </form>
</div>
@endsection
