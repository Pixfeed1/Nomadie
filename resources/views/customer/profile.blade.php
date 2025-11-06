@extends('customer.layouts.app')

@section('title', 'Mon profil')

@section('page-title', 'Mon profil')

@section('content')
<div class="space-y-6">
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Mon profil
        </h2>
        <p class="text-text-secondary mt-1">
            Gérez vos informations personnelles et votre photo de profil.
        </p>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <div class="alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Barre de progression de complétude du profil --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Complétude du profil</h3>
            <span class="text-2xl font-bold text-primary">{{ $profileCompletion['percentage'] }}%</span>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
            <div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: {{ $profileCompletion['percentage'] }}%"></div>
        </div>
        
        @if(count($profileCompletion['missing_fields']) > 0)
            <p class="text-sm text-gray-600">
                Champs manquants : {{ implode(', ', $profileCompletion['missing_fields']) }}
            </p>
        @else
            <p class="text-sm text-green-600">
                Félicitations ! Votre profil est complet.
            </p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Photo de profil --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Photo de profil</h3>
                    <p class="text-sm text-gray-600 mt-1">Personnalisez votre image</p>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                     alt="Avatar" 
                                     class="h-40 w-40 rounded-full object-cover ring-4 ring-white shadow-lg">
                            @else
                                <div class="h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <svg class="h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            {{-- Overlay au hover --}}
                            <div class="absolute inset-0 rounded-full bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <form action="{{ route('customer.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="w-full mt-6">
                            @csrf
                            <input type="file" 
                                   name="avatar" 
                                   id="avatar"
                                   accept="image/*"
                                   class="hidden"
                                   onchange="this.form.submit()">
                            
                            <label for="avatar" 
                                   class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg hover:from-primary-dark hover:to-primary transition-all cursor-pointer shadow-md hover:shadow-lg">
                                <svg class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Changer la photo
                            </label>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">Formats acceptés : JPG, PNG, GIF</p>
                            <p class="text-xs text-gray-500">Taille max : 2MB</p>
                        </div>
                    </div>
                    
                    {{-- Statistiques du profil --}}
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-primary">{{ $user->bookings_count ?? 0 }}</p>
                                <p class="text-xs text-gray-600">Réservations</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-primary">{{ $user->reviews_count ?? 0 }}</p>
                                <p class="text-xs text-gray-600">Avis laissés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations personnelles --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Informations personnelles</h3>
                    <p class="text-sm text-gray-600 mt-1">Vos données personnelles et de contact</p>
                </div>
                
                <form action="{{ route('customer.profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    {{-- Section Identité --}}
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center mr-2">
                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                Identité
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">
                                        Prénom <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="firstname" 
                                               id="firstname" 
                                               value="{{ old('firstname', $user->firstname) }}"
                                               required
                                               class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                    @error('firstname')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nom <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="lastname" 
                                               id="lastname" 
                                               value="{{ old('lastname', $user->lastname) }}"
                                               required
                                               class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                    @error('lastname')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="pseudo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Pseudo
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="pseudo" 
                                               id="pseudo" 
                                               value="{{ old('pseudo', $user->pseudo) }}"
                                               placeholder="Votre surnom"
                                               class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                    @error('pseudo')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Membre depuis
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               value="{{ $user->created_at->format('d/m/Y') }}"
                                               disabled
                                               class="pl-10 w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section Contact --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center mr-2">
                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                Contact
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email', $user->email) }}"
                                               required
                                               class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Téléphone
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <input type="tel" 
                                               name="phone" 
                                               id="phone" 
                                               value="{{ old('phone', $user->phone ?? '') }}"
                                               placeholder="+33 6 12 34 56 78"
                                               class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                    @error('phone')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section À propos --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center mr-2">
                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                À propos
                            </h4>
                            
                            <div class="relative">
                                <textarea name="bio" 
                                          id="bio" 
                                          rows="4"
                                          maxlength="500"
                                          placeholder="Parlez-nous un peu de vous, vos passions, vos envies de voyage..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                    <span id="bio-counter">0</span>/500
                                </div>
                            </div>
                            @error('bio')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Section Adresse --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center mr-2">
                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                Adresse
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Adresse
                                    </label>
                                    <input type="text" 
                                           name="address" 
                                           id="address" 
                                           value="{{ old('address', $user->address ?? '') }}"
                                           placeholder="123 rue de la Paix"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    @error('address')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                            Code postal
                                        </label>
                                        <input type="text" 
                                               name="postal_code" 
                                               id="postal_code" 
                                               value="{{ old('postal_code', $user->postal_code ?? '') }}"
                                               placeholder="75001"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        @error('postal_code')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                            Ville
                                        </label>
                                        <input type="text" 
                                               name="city" 
                                               id="city" 
                                               value="{{ old('city', $user->city ?? '') }}"
                                               placeholder="Paris"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        @error('city')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                                            Pays
                                        </label>
                                        <select name="country" 
                                                id="country"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                            <option value="France" {{ old('country', $user->country ?? 'France') == 'France' ? 'selected' : '' }}>France</option>
                                            <option value="Belgique" {{ old('country', $user->country) == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                            <option value="Suisse" {{ old('country', $user->country) == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                                            <option value="Luxembourg" {{ old('country', $user->country) == 'Luxembourg' ? 'selected' : '' }}>Luxembourg</option>
                                            <option value="Canada" {{ old('country', $user->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                                            <option value="Autre" {{ old('country', $user->country) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('country')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('customer.dashboard') }}" 
                           class="px-6 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg hover:from-primary-dark hover:to-primary transition-all shadow-md hover:shadow-lg">
                            <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Prévisualisation de l'avatar
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
            alert('Le fichier est trop volumineux. La taille maximum est de 2MB.');
            e.target.value = '';
        }
    });

    // Compteur de caractères pour la bio
    const bioTextarea = document.getElementById('bio');
    if (bioTextarea) {
        const updateCounter = () => {
            const counter = document.getElementById('bio-counter');
            if (counter) {
                counter.textContent = bioTextarea.value.length;
            }
        };
        
        updateCounter(); // Initialiser le compteur
        bioTextarea.addEventListener('input', updateCounter);
    }
</script>
@endpush