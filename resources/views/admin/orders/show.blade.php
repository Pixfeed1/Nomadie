@extends('vendor.layouts.app')

@section('title', 'Créer un voyage')

@section('page-title', 'Créer un voyage')
@section('page-description', 'Créez une nouvelle offre de voyage pour vos clients')

@section('content')
<div x-data="createTripForm()" class="space-y-6">
    <!-- En-tête avec progression -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-text-primary">Créez votre nouveau voyage</h2>
                    <p class="text-sm text-text-secondary mt-1">Remplissez les informations pour publier votre offre</p>
                </div>

                <div class="mt-4 md:mt-0">
                    <a href="{{ route('vendor.trips.index') }}" class="flex items-center text-sm text-primary hover:text-primary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à mes voyages
                    </a>
                </div>
            </div>

            <!-- Barre de progression -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-text-secondary">Progression</span>
                    <span class="text-xs font-medium text-primary" x-text="progressPercent + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all duration-500"
                         :style="'width: ' + progressPercent + '%'">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques contextuelles -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Emplacements disponibles</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['trips_remaining'] ?? 5 }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Commission</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $vendor->commission_rate ?? 10 }}%</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <button @click="showTips = !showTips" class="bg-white rounded-lg shadow-sm overflow-hidden p-4 hover:shadow-md transition-all text-left">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Besoin d'aide ?</p>
                    <p class="text-sm font-bold text-text-primary mt-1">Conseils</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
            </div>
        </button>
    </div>

    <!-- Conseils contextuels -->
    <div x-show="showTips" x-transition class="bg-accent/10 border border-accent/20 rounded-lg p-4">
        <h4 class="font-medium text-accent-dark mb-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Conseils pour un voyage qui se vend
        </h4>
            <ul class="text-sm text-text-primary space-y-1">
                <li>• Un titre accrocheur avec la destination et la durée</li>
                <li>• Au moins 5 photos de haute qualité (10-15 recommandées)</li>
                <li>• Un itinéraire détaillé jour par jour</li>
                <li>• Des informations claires sur le point de rencontre</li>
                <li>• Un prix compétitif avec tout inclus</li>
            </ul>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="bg-error/10 border border-error/20 rounded-lg p-4 flex items-start">
            <svg class="h-5 w-5 text-error mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-error">Veuillez corriger les erreurs suivantes :</p>
                <ul class="mt-1 text-sm text-error list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.trips.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: Informations principales -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" x-data="{ activeTab: 'info' }">
            <div class="border-b border-border">
                <nav class="flex -mb-px">
                    <button type="button" @click="activeTab = 'info'"
                            :class="activeTab === 'info' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors relative">
                        Informations générales
                        <template x-if="activeTab === 'info'">
                            <span class="absolute left-0 bottom-0 w-full h-0.5 bg-primary"></span>
                        </template>
                    </button>
                    <button type="button" @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors relative">
                        Détails pratiques
                        <template x-if="activeTab === 'details'">
                            <span class="absolute left-0 bottom-0 w-full h-0.5 bg-primary"></span>
                        </template>
                    </button>
                    <button type="button" @click="activeTab = 'itinerary'"
                            :class="activeTab === 'itinerary' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors relative">
                        Itinéraire
                        <template x-if="activeTab === 'itinerary'">
                            <span class="absolute left-0 bottom-0 w-full h-0.5 bg-primary"></span>
                        </template>
                    </button>
                    <button type="button" @click="activeTab = 'services'"
                            :class="activeTab === 'services' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors relative">
                        Services & conditions
                        <template x-if="activeTab === 'services'">
                            <span class="absolute left-0 bottom-0 w-full h-0.5 bg-primary"></span>
                        </template>
                    </button>
                </nav>
            </div>

            <!-- Tab: Informations générales -->
            <div x-show="activeTab === 'info'" x-transition class="p-6">
                <div class="space-y-6">
                    <!-- Titre -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-text-primary mb-1">
                            Titre de votre voyage
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="title"
                                   id="title"
                                   x-model="title"
                                   @input="updateProgress()"
                                   value="{{ old('title') }}"
                                   class="w-full px-3 py-2 pr-16 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('title') border-error @enderror"
                                   placeholder="Ex: Trek dans l'Himalaya - 15 jours d'aventure"
                                   maxlength="100"
                                   required>
                            <span class="absolute right-3 top-2.5 text-xs text-text-secondary" x-text="title.length + '/100'"></span>
                        </div>
                        @error('title')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Destination -->
                        <div>
                            <label for="destination_id" class="block text-sm font-medium text-text-primary mb-1">
                                Destination
                            </label>
                            <select name="destination_id"
                                    id="destination_id"
                                    x-model="destinationId"
                                    @change="updateProgress()"
                                    class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary appearance-none bg-white @error('destination_id') border-error @enderror"
                                    required>
                                <option value="">Sélectionnez une destination</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_id')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type de voyage -->
                        <div>
                            <label for="travel_type_id" class="block text-sm font-medium text-text-primary mb-1">
                                Type de voyage
                            </label>
                            <select name="travel_type_id"
                                    id="travel_type_id"
                                    x-model="travelTypeId"
                                    @change="updateProgress()"
                                    class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary appearance-none bg-white @error('travel_type_id') border-error @enderror"
                                    required>
                                <option value="">Sélectionnez un type</option>
                                @foreach($travelTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('travel_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('travel_type_id')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description courte -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="short_description" class="block text-sm font-medium text-text-primary">
                                Description courte
                            </label>
                            <span class="text-xs text-text-secondary">
                                <span x-text="shortDescription.length"></span>/500
                            </span>
                        </div>
                        <textarea name="short_description"
                                  id="short_description"
                                  rows="3"
                                  x-model="shortDescription"
                                  @input="updateProgress()"
                                  maxlength="500"
                                  class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('short_description') border-error @enderror"
                                  placeholder="Décrivez votre voyage en quelques phrases captivantes..."
                                  required>{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description complète -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="description" class="block text-sm font-medium text-text-primary">
                                Description complète
                            </label>
                            <span class="text-xs text-text-secondary">
                                <span x-text="description.length"></span>/5000
                            </span>
                        </div>
                        <textarea name="description"
                                  id="description"
                                  rows="8"
                                  x-model="description"
                                  @input="updateProgress()"
                                  maxlength="5000"
                                  class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('description') border-error @enderror"
                                  placeholder="Décrivez en détail votre voyage, ce qui le rend unique, les points forts..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Langues disponibles -->
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">
                            Langues disponibles pour ce voyage
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['fr' => 'Français', 'en' => 'Anglais', 'es' => 'Espagnol', 'de' => 'Allemand', 'it' => 'Italien', 'pt' => 'Portugais', 'zh' => 'Chinois', 'ja' => 'Japonais'] as $code => $lang)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           name="languages[]"
                                           value="{{ $code }}"
                                           x-model="languages"
                                           @change="updateProgress()"
                                           class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="ml-2 text-sm text-text-primary">{{ $lang }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('languages')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Tab: Détails pratiques -->
            <div x-show="activeTab === 'details'" x-transition class="p-6">
                <div class="space-y-6">
                    <!-- Prix et durée -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-text-primary mb-1">
                                Prix par personne
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-text-secondary">€</span>
                                <input type="number"
                                       name="price"
                                       id="price"
                                       x-model="price"
                                       @input="updateProgress()"
                                       value="{{ old('price') }}"
                                       min="0"
                                       step="0.01"
                                       class="w-full pl-8 pr-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('price') border-error @enderror"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-text-primary mb-1">
                                Durée
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="duration"
                                       id="duration"
                                       x-model="duration"
                                       @input="updateProgress()"
                                       value="{{ old('duration') }}"
                                       min="1"
                                       max="365"
                                       class="w-full px-3 py-2 pr-12 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('duration') border-error @enderror"
                                       placeholder="7"
                                       required>
                                <span class="absolute right-3 top-2 text-text-secondary text-sm">jours</span>
                            </div>
                            @error('duration')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="departure_date" class="block text-sm font-medium text-text-primary mb-1">
                                Date de départ
                            </label>
                            <input type="date"
                                   name="departure_date"
                                   id="departure_date"
                                   x-model="departureDate"
                                   @change="updateProgress()"
                                   value="{{ old('departure_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('departure_date') border-error @enderror"
                                   required>
                            @error('departure_date')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="return_date" class="block text-sm font-medium text-text-primary mb-1">
                                Date de retour
                            </label>
                            <input type="date"
                                   name="return_date"
                                   id="return_date"
                                   x-model="returnDate"
                                   @change="updateProgress()"
                                   value="{{ old('return_date') }}"
                                   :min="departureDate || '{{ date('Y-m-d', strtotime('+2 days')) }}'"
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('return_date') border-error @enderror"
                                   required>
                            @error('return_date')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Nombre de voyageurs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="min_travelers" class="block text-sm font-medium text-text-primary mb-1">
                                Nombre minimum de voyageurs
                            </label>
                            <input type="number"
                                   name="min_travelers"
                                   id="min_travelers"
                                   x-model="minTravelers"
                                   @input="updateProgress()"
                                   value="{{ old('min_travelers', 1) }}"
                                   min="1"
                                   max="50"
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('min_travelers') border-error @enderror"
                                   placeholder="1">
                            @error('min_travelers')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_travelers" class="block text-sm font-medium text-text-primary mb-1">
                                Nombre maximum de voyageurs
                            </label>
                            <input type="number"
                                   name="max_travelers"
                                   id="max_travelers"
                                   x-model="maxTravelers"
                                   @input="updateProgress()"
                                   value="{{ old('max_travelers', 10) }}"
                                   min="1"
                                   max="50"
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('max_travelers') border-error @enderror"
                                   placeholder="10"
                                   required>
                            @error('max_travelers')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Niveau physique -->
                    <div>
                        <label for="physical_level" class="block text-sm font-medium text-text-primary mb-1">
                            Niveau physique requis
                        </label>
                        <select name="physical_level"
                                id="physical_level"
                                x-model="physicalLevel"
                                @change="updateProgress()"
                                class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary appearance-none bg-white @error('physical_level') border-error @enderror"
                                required>
                            <option value="">Sélectionnez le niveau requis</option>
                            <option value="easy" {{ old('physical_level') == 'easy' ? 'selected' : '' }}>Facile - Accessible à tous</option>
                            <option value="moderate" {{ old('physical_level') == 'moderate' ? 'selected' : '' }}>Modéré - Bonne condition physique</option>
                            <option value="difficult" {{ old('physical_level') == 'difficult' ? 'selected' : '' }}>Difficile - Sportifs réguliers</option>
                            <option value="expert" {{ old('physical_level') == 'expert' ? 'selected' : '' }}>Expert - Très bonne condition requise</option>
                        </select>
                        @error('physical_level')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Point de rencontre -->
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">
                            Point de rencontre / Lieu de départ
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="meeting_point" class="block text-xs text-text-secondary mb-1">
                                    Nom du lieu
                                </label>
                                <input type="text"
                                       name="meeting_point"
                                       id="meeting_point"
                                       x-model="meetingPoint"
                                       @input="updateProgress()"
                                       value="{{ old('meeting_point') }}"
                                       class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('meeting_point') border-error @enderror"
                                       placeholder="Ex: Hôtel Marriott, Aéroport Charles de Gaulle...">
                                @error('meeting_point')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="meeting_time" class="block text-xs text-text-secondary mb-1">
                                    Heure de rendez-vous
                                </label>
                                <input type="time"
                                       name="meeting_time"
                                       id="meeting_time"
                                       x-model="meetingTime"
                                       value="{{ old('meeting_time') }}"
                                       class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('meeting_time') border-error @enderror">
                                @error('meeting_time')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="meeting_address" class="block text-xs text-text-secondary mb-1">
                                Adresse complète
                            </label>
                            <input type="text"
                                   name="meeting_address"
                                   id="meeting_address"
                                   x-model="meetingAddress"
                                   value="{{ old('meeting_address') }}"
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('meeting_address') border-error @enderror"
                                   placeholder="Ex: 123 rue de la Paix, 75001 Paris, France">
                            @error('meeting_address')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label for="meeting_instructions" class="block text-xs text-text-secondary mb-1">
                                Instructions d'accès (optionnel)
                            </label>
                            <textarea name="meeting_instructions"
                                      id="meeting_instructions"
                                      rows="2"
                                      x-model="meetingInstructions"
                                      class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('meeting_instructions') border-error @enderror"
                                      placeholder="Ex: Rendez-vous dans le hall principal près de la réception...">{{ old('meeting_instructions') }}</textarea>
                            @error('meeting_instructions')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Calculateur de revenus -->
                    <div x-show="price > 0" x-transition class="p-4 bg-success/5 border border-success/20 rounded-lg">
                        <h4 class="text-sm font-medium text-text-primary mb-2">Estimation de vos revenus</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-text-secondary">Prix affiché</p>
                                <p class="font-bold text-text-primary" x-text="'€' + parseFloat(price).toFixed(2)"></p>
                            </div>
                            <div>
                                <p class="text-text-secondary">Commission ({{ $vendor->commission_rate }}%)</p>
                                <p class="font-bold text-error" x-text="'-€' + (price * {{ $vendor->commission_rate }} / 100).toFixed(2)"></p>
                            </div>
                            <div>
                                <p class="text-text-secondary">Vos revenus</p>
                                <p class="font-bold text-success" x-text="'€' + (price * (100 - {{ $vendor->commission_rate }}) / 100).toFixed(2)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Itinéraire -->
            <div x-show="activeTab === 'itinerary'" x-transition class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-text-primary">Itinéraire jour par jour</h3>
                        <button type="button" @click="addDay()" class="text-sm text-primary hover:text-primary-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter un jour
                        </button>
                    </div>

                    <div id="itinerary-days" class="space-y-4">
                        <template x-for="(day, index) in itineraryDays" :key="index">
                            <div class="border border-border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-text-primary" x-text="'Jour ' + (index + 1)"></h4>
                                    <button type="button" @click="removeDay(index)" x-show="itineraryDays.length > 1" class="text-error hover:text-error/80">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <input type="text"
                                               :name="'itinerary[' + index + '][title]'"
                                               x-model="day.title"
                                               class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                               placeholder="Titre du jour (Ex: Arrivée et découverte de la ville)">
                                    </div>
                                    <div>
                                        <textarea :name="'itinerary[' + index + '][description]'"
                                                  x-model="day.description"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                                                  placeholder="Description détaillée des activités de la journée..."></textarea>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   :name="'itinerary[' + index + '][breakfast]'"
                                                   x-model="day.breakfast"
                                                   class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-2 text-sm text-text-primary">Petit-déjeuner inclus</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   :name="'itinerary[' + index + '][lunch]'"
                                                   x-model="day.lunch"
                                                   class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-2 text-sm text-text-primary">Déjeuner inclus</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   :name="'itinerary[' + index + '][dinner]'"
                                                   x-model="day.dinner"
                                                   class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-2 text-sm text-text-primary">Dîner inclus</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Message si aucun jour -->
                    <div x-show="itineraryDays.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-text-secondary">Aucun itinéraire ajouté</p>
                        <p class="text-xs text-text-secondary mt-1">Cliquez sur "Ajouter un jour" pour commencer</p>
                    </div>
                </div>
            </div>

            <!-- Tab: Services & conditions -->
            <div x-show="activeTab === 'services'" x-transition class="p-6">
                <div class="space-y-6">
                    <!-- Services inclus -->
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">
                            Services inclus dans votre offre
                        </label>
                        <div id="included-list" class="space-y-2">
                            <!-- Champs existants seront ajoutés ici -->
                        </div>
                        
                        <!-- Bouton pour ajouter un nouveau champ -->
                        <button type="button" @click="addIncludedItem()" class="mt-3 flex items-center text-sm text-primary hover:text-primary-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter un service inclus
                        </button>

                        <!-- Suggestions -->
                        <div class="mt-3 p-3 bg-primary/5 rounded-lg">
                            <p class="text-xs font-medium text-text-primary mb-2">Suggestions populaires :</p>
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="addSuggestion('included', 'Transport local')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Transport local</button>
                                <button type="button" @click="addSuggestion('included', 'Hébergement')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Hébergement</button>
                                <button type="button" @click="addSuggestion('included', 'Guide professionnel')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Guide professionnel</button>
                                <button type="button" @click="addSuggestion('included', 'Petits déjeuners')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Petits déjeuners</button>
                                <button type="button" @click="addSuggestion('included', 'Activités prévues')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Activités prévues</button>
                                <button type="button" @click="addSuggestion('included', 'Entrées aux sites touristiques')" class="text-xs px-2 py-1 bg-white border border-border rounded hover:bg-primary/10 hover:border-primary transition-all">Entrées aux sites</button>
                            </div>
                        </div>
                    </div>

                    <!-- Exigences -->
                    <div>
                        <label for="requirements" class="block text-sm font-medium text-text-primary mb-1">
                            Exigences / Prérequis (optionnel)
                        </label>
                        <textarea name="requirements"
                                  id="requirements"
                                  rows="4"
                                  x-model="requirements"
                                  class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('requirements') border-error @enderror"
                                  placeholder="Ex: Passeport valide 6 mois après le retour, visa obligatoire, vaccins recommandés, niveau de forme physique requis...">{{ old('requirements') }}</textarea>
                        @error('requirements')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note informative -->
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-800">
                            <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <strong>Note :</strong> Les éléments non mentionnés comme inclus sont considérés à la charge du voyageur (vols internationaux, assurance voyage, repas non mentionnés, etc.).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galerie de photos -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Galerie de photos</h3>
                <p class="text-sm text-text-secondary mt-1">Ajoutez entre 5 et 15 photos de haute qualité. Les voyages avec plus de photos se vendent mieux.</p>
            </div>

            <div class="p-6">
                <!-- Zone de téléchargement multiple -->
                <div class="mb-6">
                    <input type="file"
                           name="images[]"
                           id="images"
                           @change="handleMultipleImages($event)"
                           accept="image/*"
                           multiple
                           class="sr-only">
                    <label for="images"
                           class="relative block w-full border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all border-gray-300 hover:border-gray-400"
                           @dragover.prevent
                           @drop.prevent="handleDropMultiple($event)">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-text-primary">Cliquez ou glissez des images ici</p>
                        <p class="text-xs text-text-secondary">PNG, JPG jusqu'à 5MB par image • Maximum 15 photos</p>
                    </label>
                </div>

                <!-- Aperçu des images -->
                <div x-show="uploadedImages.length > 0" class="space-y-4">
                    <h4 class="text-sm font-medium text-text-primary">Photos téléchargées</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <template x-for="(image, index) in uploadedImages" :key="index">
                            <div class="relative group">
                                <img :src="image.preview" alt="Aperçu" class="h-32 w-full object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <button type="button" @click="removeUploadedImage(index)" class="p-2 bg-error text-white rounded-full hover:bg-error/80 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <input type="text"
                                           :name="'image_captions[' + index + ']'"
                                           x-model="image.caption"
                                           class="w-full px-2 py-1 text-xs border border-border rounded focus:outline-none focus:ring-1 focus:ring-primary/20 focus:border-primary"
                                           placeholder="Légende (optionnel)">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Indicateur de nombre de photos -->
                <div x-show="uploadedImages.length > 0" class="mt-4 text-sm" :class="uploadedImages.length < 5 ? 'text-error' : 'text-success'">
                    <span x-text="uploadedImages.length"></span>/15 photos ajoutées
                    <span x-show="uploadedImages.length < 5" class="text-error"> • Minimum 5 photos requises</span>
                </div>

                @error('images')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions finales -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div>
                        <h3 class="text-lg font-semibold text-text-primary">Prêt à publier ?</h3>
                        <p class="text-sm text-text-secondary mt-1">Vérifiez vos informations avant de publier</p>
                        <div class="mt-4 space-y-2">
                            <label class="flex items-center">
                                <input type="radio"
                                       name="status"
                                       value="draft"
                                       x-model="status"
                                       checked
                                       class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-text-primary">Enregistrer comme brouillon</span>
                                    <span class="block text-xs text-text-secondary">Vous pourrez le modifier et le publier plus tard</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio"
                                       name="status"
                                       value="active"
                                       x-model="status"
                                       class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-text-primary">Publier immédiatement</span>
                                    <span class="block text-xs text-text-secondary">Votre voyage sera visible dès maintenant</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="button"
                                @click="previewTrip()"
                                class="px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Aperçu
                        </button>

                        <button type="submit"
                                class="px-6 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors"
                                :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }"
                                :disabled="!canSubmit">
                            <span x-show="status === 'draft'">Enregistrer le brouillon</span>
                            <span x-show="status === 'active'">Publier le voyage</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function createTripForm() {
    return {
        // État du formulaire
        title: '{{ old('title', '') }}',
        destinationId: '{{ old('destination_id', '') }}',
        travelTypeId: '{{ old('travel_type_id', '') }}',
        shortDescription: '{{ old('short_description', '') }}',
        description: '{{ old('description', '') }}',
        price: '{{ old('price', '') }}',
        duration: '{{ old('duration', '') }}',
        departureDate: '{{ old('departure_date', '') }}',
        returnDate: '{{ old('return_date', '') }}',
        minTravelers: {{ old('min_travelers', 1) }},
        maxTravelers: {{ old('max_travelers', 10) }},
        physicalLevel: '{{ old('physical_level', '') }}',
        requirements: '{{ old('requirements', '') }}',
        languages: [],
        meetingPoint: '{{ old('meeting_point', '') }}',
        meetingTime: '{{ old('meeting_time', '') }}',
        meetingAddress: '{{ old('meeting_address', '') }}',
        meetingInstructions: '{{ old('meeting_instructions', '') }}',
        status: 'draft',

        // Itinéraire
        itineraryDays: [],

        // UI state
        showTips: false,
        progressPercent: 0,

        // Images
        uploadedImages: [],

        // Computed
        get canSubmit() {
            return this.title && this.destinationId && this.travelTypeId &&
                   this.shortDescription && this.description && this.price && 
                   this.duration && this.departureDate && this.returnDate && 
                   this.physicalLevel && this.maxTravelers && 
                   this.uploadedImages.length >= 5 && this.languages.length > 0;
        },

        updateProgress() {
            let filled = 0;
            let total = 15;

            if (this.title) filled++;
            if (this.destinationId) filled++;
            if (this.travelTypeId) filled++;
            if (this.shortDescription) filled++;
            if (this.description) filled++;
            if (this.price) filled++;
            if (this.duration) filled++;
            if (this.departureDate) filled++;
            if (this.returnDate) filled++;
            if (this.physicalLevel) filled++;
            if (this.maxTravelers) filled++;
            if (this.uploadedImages.length >= 5) filled++;
            if (this.languages.length > 0) filled++;
            if (this.meetingPoint) filled++;
            if (this.itineraryDays.length > 0) filled++;

            this.progressPercent = Math.round((filled / total) * 100);
        },

        // Gestion de l'itinéraire
        addDay() {
            this.itineraryDays.push({
                title: '',
                description: '',
                breakfast: false,
                lunch: false,
                dinner: false
            });
            this.updateProgress();
        },

        removeDay(index) {
            this.itineraryDays.splice(index, 1);
            this.updateProgress();
        },

        // Gestion des images multiples
        handleMultipleImages(event) {
            const files = Array.from(event.target.files);
            const remainingSlots = 15 - this.uploadedImages.length;
            const filesToProcess = files.slice(0, remainingSlots);

            filesToProcess.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.uploadedImages.push({
                            file: file,
                            preview: e.target.result,
                            caption: ''
                        });
                        this.updateProgress();
                    };
                    reader.readAsDataURL(file);
                }
            });
        },

        handleDropMultiple(event) {
            const files = Array.from(event.dataTransfer.files);
            const remainingSlots = 15 - this.uploadedImages.length;
            const filesToProcess = files.slice(0, remainingSlots);

            // Créer un nouvel événement avec les fichiers
            const input = document.getElementById('images');
            const dataTransfer = new DataTransfer();
            filesToProcess.forEach(file => {
                if (file.type.startsWith('image/')) {
                    dataTransfer.items.add(file);
                }
            });
            input.files = dataTransfer.files;

            const changeEvent = new Event('change', { bubbles: true });
            input.dispatchEvent(changeEvent);
        },

        removeUploadedImage(index) {
            this.uploadedImages.splice(index, 1);
            this.updateProgress();
        },

        addIncludedItem() {
            const list = document.getElementById('included-list');
            const inputs = list.querySelectorAll('input[type="text"]');
            const lastInput = inputs[inputs.length - 1];
            
            // Ne pas ajouter de nouveau champ si le dernier est vide
            if (lastInput && lastInput.value.trim() === '') {
                lastInput.focus();
                return;
            }
            
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text"
                       name="included[]"
                       class="flex-1 px-3 py-2 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                       placeholder="Ajouter un service inclus">
                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-white border border-border text-text-primary hover:bg-error hover:text-white rounded-lg transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            list.appendChild(div);
            
            // Focus sur le nouveau champ
            const newInput = div.querySelector('input');
            newInput.focus();
        },

        addSuggestion(type, text) {
            if (type === 'included') {
                this.addIncludedItem();
                const inputs = document.querySelectorAll('#included-list input[type="text"]');
                inputs[inputs.length - 1].value = text;
            }
        },

        previewTrip() {
            // Implémenter la prévisualisation
            alert('Aperçu du voyage (à implémenter)');
        },

        init() {
            this.updateProgress();
            // Ajouter au moins un jour d'itinéraire par défaut
            if (this.itineraryDays.length === 0) {
                this.addDay();
            }
        }
    }
}
</script>
@endpush