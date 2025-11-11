@extends('layouts.admin')

@section('title', 'Modifier ' . $country->name)

@section('header-left')
    <div class="flex items-center">
        <a href="{{ route('admin.countries.index') }}" class="mr-4 text-text-secondary hover:text-primary">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">{{ $country->name }}</h1>
            <p class="text-sm text-text-secondary mt-1">Modifier l'image d'arrière-plan</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.countries.update', $country) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-border p-6 space-y-6">
            <!-- Image actuelle -->
            @if($country->image)
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Image actuelle</label>
                <div class="relative inline-block">
                    <img src="{{ Storage::url($country->image) }}" alt="{{ $country->name }}" class="h-48 w-auto rounded-lg object-cover">
                </div>

                <div class="mt-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-text-secondary">Supprimer l'image</span>
                    </label>
                </div>
            </div>
            @endif

            <!-- Upload nouvelle image -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">
                    {{ $country->image ? 'Changer l\'image' : 'Ajouter une image' }}
                </label>
                <input type="file" name="image" accept="image/*" class="block w-full text-sm text-text-secondary
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-lg file:border-0
                    file:text-sm file:font-semibold
                    file:bg-primary file:text-white
                    hover:file:bg-primary-dark
                    file:cursor-pointer cursor-pointer">
                <p class="mt-2 text-xs text-text-secondary">Format: JPG, PNG. Taille max: 5MB. Recommandé: 1920x1080px</p>
                @error('image')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Description du pays (optionnel)">{{ old('description', $country->description) }}</textarea>
                <p class="mt-2 text-xs text-text-secondary">Cette description peut être affichée sur la page du pays.</p>
                @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            À propos de l'image d'arrière-plan
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Cette image sera utilisée comme arrière-plan quand les utilisateurs consultent les expériences de ce pays. Une belle image donnera envie de découvrir vos offres !</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-border">
                <div>
                    <p class="text-sm text-text-secondary">Nombre d'expériences</p>
                    <p class="text-2xl font-bold text-primary mt-1">{{ $country->trips()->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-text-secondary">Pays populaire</p>
                    <p class="text-2xl font-bold text-accent mt-1">{{ $country->popular ? 'Oui' : 'Non' }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.countries.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-primary hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
