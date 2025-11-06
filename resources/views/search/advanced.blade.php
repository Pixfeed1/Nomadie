@extends('layouts.public')

@section('title', 'Recherche avancée - Nomadie')

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section simplifié -->
    <div class="bg-gradient-to-r from-primary to-primary-dark py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Recherche avancée</h1>
            <p class="text-xl text-white/90">Trouvez exactement l'expérience de voyage qui vous correspond</p>
        </div>
    </div>

    <!-- Formulaire principal -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-16">
        <form action="{{ route('search') }}" method="GET" id="advanced-search-form">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Colonne principale -->
                <div class="lg:col-span-2">
                    
                    <!-- Section Type d'expérience (EN PREMIER pour conditionner le reste) -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Type d'expérience</h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="offer_type" value="organized_trip" class="peer sr-only" checked>
                                <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-primary peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <svg class="h-8 w-8 mx-auto mb-2 text-gray-400 peer-checked:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-xs text-center text-text-secondary">Séjours organisés</div>
                                </div>
                            </label>
                            
                            <label class="relative cursor-pointer">
                                <input type="radio" name="offer_type" value="accommodation" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-primary peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <svg class="h-8 w-8 mx-auto mb-2 text-gray-400 peer-checked:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <div class="text-xs text-center text-text-secondary">Hébergements</div>
                                </div>
                            </label>
                            
                            <label class="relative cursor-pointer">
                                <input type="radio" name="offer_type" value="activity" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-primary peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <svg class="h-8 w-8 mx-auto mb-2 text-gray-400 peer-checked:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-xs text-center text-text-secondary">Activités</div>
                                </div>
                            </label>
                            
                            <label class="relative cursor-pointer">
                                <input type="radio" name="offer_type" value="custom" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-primary peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <svg class="h-8 w-8 mx-auto mb-2 text-gray-400 peer-checked:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <div class="text-xs text-center text-text-secondary">Sur mesure</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Section Destination avec filtrage hiérarchique -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Destination</h2>
                        
                        <div class="space-y-4">
                            <!-- Sélection du continent -->
                            <div>
                                <label for="continent" class="block text-sm font-medium text-text-secondary mb-2">Continent</label>
                                <select id="continent" name="continent_id" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    <option value="">Tous les continents</option>
                                    @php
                                        // Approche simplifiée : récupérer tous les continents qui ont des pays
                                        // On filtrera côté JavaScript pour n'afficher que ceux avec des offres
                                        $continents = \App\Models\Continent::all();
                                    @endphp
                                    @foreach($continents as $continent)
                                        <option value="{{ $continent->id }}">{{ $continent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Sélection du pays (chargé dynamiquement) -->
                            <div id="country-select-wrapper" style="display: none;">
                                <label for="country" class="block text-sm font-medium text-text-secondary mb-2">Pays</label>
                                <select id="country" name="country_id" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    <option value="">Tous les pays</option>
                                </select>
                            </div>
                            
                            <!-- Sélection de la destination/ville (chargée dynamiquement) -->
                            <div id="destination-select-wrapper" style="display: none;">
                                <label for="destination" class="block text-sm font-medium text-text-secondary mb-2">Ville/Destination</label>
                                <select id="destination" name="destination_id" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    <option value="">Toutes les destinations</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section Dates -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Dates de voyage</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-text-secondary mb-2">
                                    <span class="date-label-start">Date de départ</span>
                                </label>
                                <input type="date" id="start_date" name="start_date" 
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-text-secondary mb-2">
                                    <span class="date-label-end">Date de retour</span>
                                </label>
                                <input type="date" id="end_date" name="end_date" 
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="flexible_dates" name="flexible_dates" value="1" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                    <label for="flexible_dates" class="ml-2 text-sm text-text-secondary">
                                        Mes dates sont flexibles (+/- 3 jours)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Voyageurs et Budget (adaptative) -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">
                            <span id="travelers-budget-title">Voyageurs et budget</span>
                        </h2>
                        
                        <div class="space-y-6">
                            <!-- Section voyageurs pour séjours et activités -->
                            <div id="travelers-section" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="adults" class="block text-sm font-medium text-text-secondary mb-2">Nombre d'adultes</label>
                                    <input type="number" id="adults" name="adults" min="1" max="20" value="2"
                                           class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                </div>
                                
                                <div>
                                    <label for="children" class="block text-sm font-medium text-text-secondary mb-2">Nombre d'enfants (2-17 ans)</label>
                                    <input type="number" id="children" name="children" min="0" max="10" value="0"
                                           class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                </div>
                            </div>
                            
                            <!-- Section capacité pour hébergements -->
                            <div id="capacity-section" style="display: none;">
                                <label for="capacity" class="block text-sm font-medium text-text-secondary mb-2">Capacité souhaitée</label>
                                <select id="capacity" name="capacity" class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    <option value="">Peu importe</option>
                                    <option value="2">2 personnes</option>
                                    <option value="4">4 personnes</option>
                                    <option value="6">6 personnes</option>
                                    <option value="8">8 personnes</option>
                                    <option value="10">10+ personnes</option>
                                </select>
                            </div>
                            
                            <!-- Section budget -->
                            <div>
                                <label class="block text-sm font-medium text-text-secondary mb-3">
                                    <span id="budget-label">Budget par personne</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="budget_min" class="block text-xs text-text-secondary mb-1">Minimum</label>
                                        <input type="number" id="budget_min" name="budget_min" min="0" step="50"
                                               placeholder="0 €" 
                                               class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    </div>
                                    <div>
                                        <label for="budget_max" class="block text-xs text-text-secondary mb-1">Maximum</label>
                                        <input type="number" id="budget_max" name="budget_max" min="0" step="50"
                                               placeholder="Illimité" 
                                               class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    </div>
                                </div>
                                <div id="budget-info" class="mt-3 p-3 bg-primary/5 rounded-lg text-sm text-text-secondary">
                                    <!-- Info budget dynamique -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Préférences -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Vos préférences</h2>
                        
                        <div class="space-y-6">
                            <!-- Durée (pour séjours et hébergements) -->
                            <div id="duration-section">
                                <label class="block text-sm font-medium text-text-secondary mb-3">
                                    <span id="duration-label">Durée du séjour</span>
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="number" id="duration_min" name="duration_min" min="1" 
                                               placeholder="Min. jours" 
                                               class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    </div>
                                    <div>
                                        <input type="number" id="duration_max" name="duration_max" min="1" 
                                               placeholder="Max. jours" 
                                               class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary p-2.5">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Niveau physique (cases à cocher) -->
                            <div id="physical-level-section">
                                <label class="block text-sm font-medium text-text-secondary mb-3">Niveau physique</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="physical_levels[]" value="easy" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-2 text-sm text-text-primary">Facile</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="physical_levels[]" value="moderate" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-2 text-sm text-text-primary">Modéré</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="physical_levels[]" value="difficult" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-2 text-sm text-text-primary">Difficile</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="physical_levels[]" value="expert" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-2 text-sm text-text-primary">Expert</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Services inclus -->
                            <div>
                                <label class="block text-sm font-medium text-text-secondary mb-3">Services souhaités</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="services[]" value="transport" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Transport inclus</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="services[]" value="meals" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Repas inclus</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="services[]" value="guide" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Guide francophone</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="services[]" value="insurance" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Assurance voyage</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Options spécifiques hébergement -->
                            <div id="accommodation-options" style="display: none;">
                                <label class="block text-sm font-medium text-text-secondary mb-3">Options hébergement</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="accommodation_features[]" value="pool" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Piscine</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="accommodation_features[]" value="wifi" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Wi-Fi</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="accommodation_features[]" value="kitchen" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Cuisine équipée</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-primary/5 cursor-pointer transition-colors">
                                        <input type="checkbox" name="accommodation_features[]" value="parking" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                                        <span class="ml-3 text-sm text-text-primary">Parking</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Récapitulatif de recherche -->
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-text-primary mb-4">Votre recherche</h3>
                        
                        <div id="search-summary" class="space-y-3 mb-6">
                            <div class="text-sm text-text-secondary">
                                Configurez vos critères pour voir le récapitulatif
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition-colors mb-3">
                            <div class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Lancer la recherche
                            </div>
                        </button>
                        
                        <button type="reset" class="w-full bg-bg-alt hover:bg-gray-200 text-text-primary font-medium py-2 px-4 rounded-lg transition-colors">
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Configuration des labels selon le type d'offre
    const offerTypeConfig = {
        'organized_trip': {
            budgetLabel: 'Budget par personne/jour',
            budgetInfo: 'Prix indicatif pour un séjour tout compris',
            durationLabel: 'Durée du séjour (jours)',
            showTravelers: true,
            showCapacity: false,
            showPhysical: true,
            showAccommodationOptions: false,
            dateStartLabel: 'Date de départ',
            dateEndLabel: 'Date de retour'
        },
        'accommodation': {
            budgetLabel: 'Budget par nuit',
            budgetInfo: 'Prix pour la location complète de l\'hébergement',
            durationLabel: 'Durée du séjour (nuits)',
            showTravelers: false,
            showCapacity: true,
            showPhysical: false,
            showAccommodationOptions: true,
            dateStartLabel: 'Date d\'arrivée',
            dateEndLabel: 'Date de départ'
        },
        'activity': {
            budgetLabel: 'Budget par personne',
            budgetInfo: 'Prix par personne pour l\'activité',
            durationLabel: null, // Pas de durée pour les activités
            showTravelers: true,
            showCapacity: false,
            showPhysical: true,
            showAccommodationOptions: false,
            dateStartLabel: 'Date souhaitée',
            dateEndLabel: null // Pas de date de fin pour activités
        },
        'custom': {
            budgetLabel: 'Budget indicatif',
            budgetInfo: 'Nous vous proposerons un devis personnalisé',
            durationLabel: 'Durée souhaitée (jours)',
            showTravelers: true,
            showCapacity: false,
            showPhysical: true,
            showAccommodationOptions: false,
            dateStartLabel: 'Date de départ',
            dateEndLabel: 'Date de retour'
        }
    };

    // Gestion du changement de type d'offre
    document.querySelectorAll('input[name="offer_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateFormForOfferType(this.value);
        });
    });

    function updateFormForOfferType(offerType) {
        const config = offerTypeConfig[offerType];
        
        // Mise à jour des labels
        document.getElementById('budget-label').textContent = config.budgetLabel;
        document.getElementById('budget-info').textContent = config.budgetInfo;
        
        // Dates
        document.querySelector('.date-label-start').textContent = config.dateStartLabel;
        if (config.dateEndLabel) {
            document.querySelector('.date-label-end').textContent = config.dateEndLabel;
            document.getElementById('end_date').closest('div').style.display = 'block';
        } else {
            document.getElementById('end_date').closest('div').style.display = 'none';
        }
        
        // Sections voyageurs/capacité
        document.getElementById('travelers-section').style.display = config.showTravelers ? 'grid' : 'none';
        document.getElementById('capacity-section').style.display = config.showCapacity ? 'block' : 'none';
        
        // Section durée
        if (config.durationLabel) {
            document.getElementById('duration-section').style.display = 'block';
            document.getElementById('duration-label').textContent = config.durationLabel;
        } else {
            document.getElementById('duration-section').style.display = 'none';
        }
        
        // Niveau physique
        document.getElementById('physical-level-section').style.display = config.showPhysical ? 'block' : 'none';
        
        // Options hébergement
        document.getElementById('accommodation-options').style.display = config.showAccommodationOptions ? 'block' : 'none';
        
        // Titre de la section
        if (offerType === 'accommodation') {
            document.getElementById('travelers-budget-title').textContent = 'Capacité et budget';
        } else {
            document.getElementById('travelers-budget-title').textContent = 'Voyageurs et budget';
        }
        
        updateSummary();
    }

    // Gestion de la hiérarchie géographique
    document.getElementById('continent').addEventListener('change', function() {
        const continentId = this.value;
        const countrySelect = document.getElementById('country');
        const countryWrapper = document.getElementById('country-select-wrapper');
        const destinationWrapper = document.getElementById('destination-select-wrapper');
        
        if (continentId) {
            // Charger les pays du continent avec des offres actives
            fetch(`/api/countries-with-trips?continent_id=${continentId}`)
                .then(response => response.json())
                .then(data => {
                    countrySelect.innerHTML = '<option value="">Tous les pays</option>';
                    data.forEach(country => {
                        countrySelect.innerHTML += `<option value="${country.id}">${country.name} (${country.trips_count} offres)</option>`;
                    });
                    countryWrapper.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Fallback si l'API n'existe pas encore
                    countryWrapper.style.display = 'block';
                });
        } else {
            countryWrapper.style.display = 'none';
            destinationWrapper.style.display = 'none';
            countrySelect.innerHTML = '<option value="">Tous les pays</option>';
        }
    });

    document.getElementById('country').addEventListener('change', function() {
        const countryId = this.value;
        const destinationSelect = document.getElementById('destination');
        const destinationWrapper = document.getElementById('destination-select-wrapper');
        
        if (countryId) {
            // Charger les destinations du pays avec des offres actives
            fetch(`/api/destinations-with-trips?country_id=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    destinationSelect.innerHTML = '<option value="">Toutes les destinations</option>';
                    data.forEach(destination => {
                        destinationSelect.innerHTML += `<option value="${destination.id}">${destination.name} (${destination.trips_count} offres)</option>`;
                    });
                    destinationWrapper.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Fallback
                    destinationWrapper.style.display = 'block';
                });
        } else {
            destinationWrapper.style.display = 'none';
            destinationSelect.innerHTML = '<option value="">Toutes les destinations</option>';
        }
    });

    // Validation des dates
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate?.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });

    // Mise à jour du récapitulatif
    function updateSummary() {
        const summary = document.getElementById('search-summary');
        if (!summary) return;
        
        let items = [];
        const offerType = document.querySelector('input[name="offer_type"]:checked')?.value;
        
        // Type d'offre
        const offerTypeText = {
            'organized_trip': 'Séjour organisé',
            'accommodation': 'Hébergement',
            'activity': 'Activité',
            'custom': 'Sur mesure'
        };
        
        if (offerType) {
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Type:</span>
                <span class="font-medium text-text-primary">${offerTypeText[offerType]}</span>
            </div>`);
        }
        
        // Destination
        const continent = document.getElementById('continent');
        const country = document.getElementById('country');
        const destination = document.getElementById('destination');
        
        if (destination?.value) {
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Destination:</span>
                <span class="font-medium text-text-primary">${destination.options[destination.selectedIndex].text}</span>
            </div>`);
        } else if (country?.value) {
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Pays:</span>
                <span class="font-medium text-text-primary">${country.options[country.selectedIndex].text}</span>
            </div>`);
        } else if (continent?.value) {
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Continent:</span>
                <span class="font-medium text-text-primary">${continent.options[continent.selectedIndex].text}</span>
            </div>`);
        }
        
        // Dates
        if (startDate?.value) {
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Départ:</span>
                <span class="font-medium text-text-primary">${new Date(startDate.value).toLocaleDateString('fr-FR')}</span>
            </div>`);
        }
        
        // Voyageurs ou capacité
        if (offerType === 'accommodation') {
            const capacity = document.getElementById('capacity');
            if (capacity?.value) {
                items.push(`<div class="flex justify-between text-sm">
                    <span class="text-text-secondary">Capacité:</span>
                    <span class="font-medium text-text-primary">${capacity.options[capacity.selectedIndex].text}</span>
                </div>`);
            }
        } else {
            const adults = parseInt(document.getElementById('adults')?.value) || 0;
            const children = parseInt(document.getElementById('children')?.value) || 0;
            const total = adults + children;
            if (total > 0) {
                items.push(`<div class="flex justify-between text-sm">
                    <span class="text-text-secondary">Voyageurs:</span>
                    <span class="font-medium text-text-primary">${total} personne${total > 1 ? 's' : ''}</span>
                </div>`);
            }
        }
        
        // Budget
        const budgetMin = document.getElementById('budget_min')?.value;
        const budgetMax = document.getElementById('budget_max')?.value;
        if (budgetMin || budgetMax) {
            let budgetText = '';
            if (budgetMin && budgetMax) {
                budgetText = `${budgetMin}€ - ${budgetMax}€`;
            } else if (budgetMin) {
                budgetText = `Min. ${budgetMin}€`;
            } else {
                budgetText = `Max. ${budgetMax}€`;
            }
            items.push(`<div class="flex justify-between text-sm">
                <span class="text-text-secondary">Budget:</span>
                <span class="font-medium text-text-primary">${budgetText}</span>
            </div>`);
        }
        
        summary.innerHTML = items.length > 0 ? items.join('') : '<div class="text-sm text-text-secondary">Configurez vos critères pour voir le récapitulatif</div>';
    }

    // Écouteurs pour mise à jour du récapitulatif
    document.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', updateSummary);
        element.addEventListener('input', updateSummary);
    });

    // Reset du formulaire
    document.querySelector('button[type="reset"]')?.addEventListener('click', function() {
        setTimeout(() => {
            // Réinitialiser l'affichage
            updateFormForOfferType('organized_trip');
            document.getElementById('country-select-wrapper').style.display = 'none';
            document.getElementById('destination-select-wrapper').style.display = 'none';
            updateSummary();
        }, 10);
    });

    // Initialisation
    updateFormForOfferType('organized_trip');
    updateSummary();
</script>
@endpush
@endsection