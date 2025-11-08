@extends('layouts.vendor')

@section('title', 'Éditer l\'offre')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Éditer l'offre</h1>
            <p class="text-text-secondary mt-1">{{ $trip->title }}</p>
        </div>
        <a href="{{ route('vendor.trips.show', $trip) }}" class="text-text-secondary hover:text-primary">Voir l'offre</a>
    </div>

    <form action="{{ route('vendor.trips.update', $trip) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Informations de base</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-text-primary mb-2">Titre *</label>
                <input type="text" name="title" value="{{ old('title', $trip->title) }}" required maxlength="255" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Destination *</label>
                    <select name="country_id" required class="w-full px-4 py-2 border rounded-lg">
                        @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ $trip->country_id == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Type de voyage *</label>
                    <select name="travel_type_id" required class="w-full px-4 py-2 border rounded-lg">
                        @foreach($travelTypes as $tt)
                        <option value="{{ $tt->id }}" {{ $trip->travel_type_id == $tt->id ? 'selected' : '' }}>{{ $tt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Description courte *</label>
                <textarea name="short_description" rows="3" required maxlength="500" class="w-full px-4 py-2 border rounded-lg">{{ old('short_description', $trip->short_description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Description complète *</label>
                <textarea name="description" rows="8" required maxlength="5000" class="w-full px-4 py-2 border rounded-lg">{{ old('description', $trip->description) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Tarification</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Prix *</label>
                    <input type="number" name="price" value="{{ old('price', $trip->price) }}" required min="0" step="0.01" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Devise *</label>
                    <select name="currency" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="EUR" {{ $trip->currency == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="USD" {{ $trip->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="GBP" {{ $trip->currency == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Images</h2>
            
            @if($trip->images && count($trip->images) > 0)
            <div class="grid grid-cols-4 gap-4 mb-4">
                @foreach($trip->images as $index => $image)
                <div class="relative">
                    <img src="{{ Storage::url($image['path']) }}" class="w-full h-24 object-cover rounded">
                    <label class="absolute top-1 right-1">
                        <input type="checkbox" name="remove_images[]" value="{{ $index }}" class="rounded">
                    </label>
                </div>
                @endforeach
            </div>
            <p class="text-sm text-text-secondary mb-4">Cochez pour supprimer</p>
            @endif

            <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border rounded-lg">
            <p class="text-sm text-text-secondary mt-2">Ajoutez de nouvelles images (min 5 total)</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-text-primary mb-4">Langues</h2>
            <div class="grid grid-cols-4 gap-3">
                @foreach($popularLanguages as $lang)
                <label class="flex items-center">
                    <input type="checkbox" name="languages[]" value="{{ $lang->id }}" 
                           {{ in_array($lang->id, $trip->languages ?? []) ? 'checked' : '' }} class="mr-2">
                    <span>{{ $lang->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        @if($trip->offer_type === 'accommodation')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Détails hébergement</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Capacité *</label>
                    <input type="number" name="property_capacity" value="{{ $trip->property_capacity }}" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Chambres *</label>
                    <input type="number" name="bedrooms" value="{{ $trip->bedrooms }}" required min="0" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">SdB *</label>
                    <input type="number" name="bathrooms" value="{{ $trip->bathrooms }}" required min="0" step="0.5" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Min nuits *</label>
                    <input type="number" name="min_nights" value="{{ $trip->min_nights }}" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>
        @endif

        @if($trip->offer_type === 'organized_trip')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Détails séjour</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Durée (jours) *</label>
                    <input type="number" name="duration" value="{{ $trip->duration }}" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max voyageurs *</label>
                    <input type="number" name="max_travelers" value="{{ $trip->max_travelers }}" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Niveau *</label>
                    <select name="physical_level" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="easy" {{ $trip->physical_level == 'easy' ? 'selected' : '' }}>Facile</option>
                        <option value="moderate" {{ $trip->physical_level == 'moderate' ? 'selected' : '' }}>Modéré</option>
                        <option value="challenging" {{ $trip->physical_level == 'challenging' ? 'selected' : '' }}>Difficile</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Repas *</label>
                    <select name="meal_plan" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="none" {{ $trip->meal_plan == 'none' ? 'selected' : '' }}>Aucun</option>
                        <option value="breakfast" {{ $trip->meal_plan == 'breakfast' ? 'selected' : '' }}>Petit-déj</option>
                        <option value="half_board" {{ $trip->meal_plan == 'half_board' ? 'selected' : '' }}>Demi-pension</option>
                        <option value="full_board" {{ $trip->meal_plan == 'full_board' ? 'selected' : '' }}>Pension complète</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Point rencontre *</label>
                <input type="text" name="meeting_point" value="{{ $trip->meeting_point }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        @endif

        @if($trip->offer_type === 'activity')
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Détails activité</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Durée (h) *</label>
                    <input type="number" name="duration_hours" value="{{ $trip->duration_hours }}" required min="0.5" step="0.5" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Max participants *</label>
                    <input type="number" name="max_participants" value="{{ $trip->max_participants }}" required min="1" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Statut</h2>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="status" value="draft" {{ $trip->status == 'draft' ? 'checked' : '' }} class="mr-2">
                    <span>Brouillon</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="active" {{ $trip->status == 'active' ? 'checked' : '' }} class="mr-2">
                    <span>Active</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="inactive" {{ $trip->status == 'inactive' ? 'checked' : '' }} class="mr-2">
                    <span>Inactive</span>
                </label>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('vendor.trips.show', $trip) }}" class="text-text-secondary hover:text-primary">Annuler</a>
            <button type="submit" class="btn bg-primary text-white px-6 py-3 rounded-lg">Enregistrer les modifications</button>
        </div>
    </form>
</div>
@endsection
