@extends('layouts.admin')

@section('title', 'Gestion des Bandeaux')

@section('header-left')
    <h1 class="text-2xl font-bold text-text-primary">Gestion des Bandeaux</h1>
    <p class="text-sm text-text-secondary mt-1">Configurez le bandeau de la page d'accueil (images des pays dans "Pays")</p>
@endsection

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.settings.hero-banner.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-border p-6 space-y-6">
            <!-- Image actuelle -->
            @if($settings['image'])
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Image actuelle</label>
                <div class="relative inline-block">
                    @if(str_starts_with($settings['image'], 'images/'))
                        <img src="{{ asset($settings['image']) }}" alt="Bandeau" class="h-64 w-auto rounded-lg object-cover">
                    @else
                        <img src="{{ Storage::url($settings['image']) }}" alt="Bandeau" class="h-64 w-auto rounded-lg object-cover">
                    @endif
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
                    {{ $settings['image'] ? 'Changer l\'image' : 'Ajouter une image' }}
                </label>
                <input type="file" name="hero_banner_image" accept="image/*" class="block w-full text-sm text-text-secondary
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-lg file:border-0
                    file:text-sm file:font-semibold
                    file:bg-primary file:text-white
                    hover:file:bg-primary-dark
                    file:cursor-pointer cursor-pointer">
                <p class="mt-2 text-xs text-text-secondary">Format: JPG, PNG. Taille max: 5MB. Recommandé: 1920x1080px</p>
                @error('hero_banner_image')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Titre -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Titre du bandeau *</label>
                <input type="text" name="hero_banner_title" value="{{ old('hero_banner_title', $settings['title']) }}" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Organisez et vivez des expériences authentiques">
                @error('hero_banner_title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sous-titre -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Sous-titre du bandeau</label>
                <textarea name="hero_banner_subtitle" rows="3" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Description...">{{ old('hero_banner_subtitle', $settings['subtitle']) }}</textarea>
                @error('hero_banner_subtitle')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Aperçu -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            À propos du bandeau d'accueil
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Le bandeau d'accueil est la première chose que vos visiteurs verront. Choisissez une image inspirante et des textes accrocheurs !</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>L'image doit être en format paysage (ratio 16:9 recommandé)</li>
                                <li>Le titre doit être court et impactant</li>
                                <li>Le sous-titre explique votre proposition de valeur</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aperçu visuel -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Aperçu</label>
                <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white rounded-lg overflow-hidden">
                    <div class="absolute inset-0">
                        <div class="absolute inset-0 bg-black opacity-40"></div>
                        @if($settings['image'])
                            @if(str_starts_with($settings['image'], 'images/'))
                                <img src="{{ asset($settings['image']) }}" alt="Preview" class="w-full h-full object-cover">
                            @else
                                <img src="{{ Storage::url($settings['image']) }}" alt="Preview" class="w-full h-full object-cover">
                            @endif
                        @endif
                    </div>
                    <div class="relative px-8 py-16">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                            {{ $settings['title'] ?? 'Titre du bandeau' }}
                        </h2>
                        <p class="text-white/90 max-w-2xl">
                            {{ $settings['subtitle'] ?? 'Sous-titre du bandeau...' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.dashboard.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-primary hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
