@extends('layouts.public')

@section('title', 'Inscription Organisateur de Voyages')

@section('content')
<div class="bg-bg-main min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            <img src="{{ asset('images/organizer-hero.jpg') }}" alt="Devenir Organisateur" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/1600/800?text=Devenir%20Organisateur';this.onerror=null;">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="max-w-3xl space-y-6">
                <h1 class="text-4xl md:text-5xl font-bold text-white">Devenez Organisateur de Voyages</h1>
                <p class="text-xl text-white/90">Rejoignez notre marketplace et proposez vos aventures uniques à des voyageurs passionnés.</p>
            </div>
        </div>
    </div>

    <!-- Registration Form Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 bg-primary text-white">
                <h2 class="text-2xl font-bold">Inscription Organisateur</h2>
                <p class="text-white/80">Complétez le formulaire ci-dessous pour commencer à proposer vos voyages</p>
            </div>
            
            <!-- Progress steps -->
            <div class="px-6 pt-6">
                <div class="flex justify-between mb-8">
                    <div class="step active flex flex-col items-center" data-step="1">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-2">1</div>
                        <span class="text-sm font-medium">Informations personnelles</span>
                    </div>
                    <div class="flex-1 border-t border-gray-300 self-center mx-4"></div>
                    <div class="step flex flex-col items-center" data-step="2">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mb-2">2</div>
                        <span class="text-sm font-medium text-gray-600">Informations entreprise</span>
                    </div>
                    <div class="flex-1 border-t border-gray-300 self-center mx-4"></div>
                    <div class="step flex flex-col items-center" data-step="3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mb-2">3</div>
                        <span class="text-sm font-medium text-gray-600">Spécialités et finalisation</span>
                    </div>
                </div>
            </div>
            
            <form id="organizerForm" method="POST" action="{{ route('organizer.register') }}" enctype="multipart/form-data" class="p-6 space-y-8">
                @csrf
                
                <!-- Step 1: Personal Information -->
                <div class="form-step" id="step1">
                    <h3 class="text-lg font-semibold text-text-primary border-b border-gray-200 pb-2 mb-4">Informations personnelles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-text-secondary mb-1">Nom complet</label>
                            <input id="name" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('name') border-red-500 @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-text-secondary mb-1">Adresse email</label>
                            <input id="email" type="email" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('email') border-red-500 @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-text-secondary mb-1">Mot de passe</label>
                            <input id="password" type="password" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('password') border-red-500 @enderror" 
                                   name="password" required autocomplete="new-password">
                            <p class="mt-1 text-xs text-text-secondary">8 caractères minimum, avec chiffres et lettres</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password-confirm" class="block text-sm font-medium text-text-secondary mb-1">Confirmer le mot de passe</label>
                            <input id="password-confirm" type="password" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5" 
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-text-secondary mb-1">Téléphone</label>
                            <input id="phone" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('phone') border-red-500 @enderror" 
                                   name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end">
                        <button type="button" class="next-step px-6 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-md transition-colors">
                            Continuer
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Company Information -->
                <div class="form-step hidden" id="step2">
                    <h3 class="text-lg font-semibold text-text-primary border-b border-gray-200 pb-2 mb-4">Informations sur l'entreprise</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-text-secondary mb-1">Nom de l'entreprise</label>
                            <input id="company_name" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_name') border-red-500 @enderror" 
                                   name="company_name" value="{{ old('company_name') }}" required>
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_website" class="block text-sm font-medium text-text-secondary mb-1">Site web</label>
                            <input id="company_website" type="url" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_website') border-red-500 @enderror" 
                                   name="company_website" value="{{ old('company_website') }}">
                            @error('company_website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_siret" class="block text-sm font-medium text-text-secondary mb-1">Numéro SIRET</label>
                            <input id="company_siret" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_siret') border-red-500 @enderror" 
                                   name="company_siret" value="{{ old('company_siret') }}" required>
                            @error('company_siret')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_vat" class="block text-sm font-medium text-text-secondary mb-1">Numéro de TVA <span class="text-text-secondary text-xs">(optionnel)</span></label>
                            <input id="company_vat" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_vat') border-red-500 @enderror" 
                                   name="company_vat" value="{{ old('company_vat') }}">
                            <p class="mt-1 text-xs text-text-secondary">Pour les entreprises assujetties à la TVA (format FR12345678901)</p>
                            @error('company_vat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_address" class="block text-sm font-medium text-text-secondary mb-1">Adresse de l'entreprise</label>
                            <input id="company_address" type="text" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_address') border-red-500 @enderror" 
                                   name="company_address" value="{{ old('company_address') }}" required>
                            @error('company_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_logo" class="block text-sm font-medium text-text-secondary mb-1">Logo de l'entreprise</label>
                            <input id="company_logo" type="file" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_logo') border-red-500 @enderror" 
                                   name="company_logo" accept="image/*">
                            <p class="mt-1 text-xs text-text-secondary">Format recommandé: PNG ou JPG, max 2Mo</p>
                            @error('company_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="company_description" class="block text-sm font-medium text-text-secondary mb-1">Description de l'entreprise</label>
                            <textarea id="company_description" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_description') border-red-500 @enderror" 
                                      name="company_description" rows="4">{{ old('company_description') }}</textarea>
                            @error('company_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-between">
                        <button type="button" class="prev-step px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-md transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Retour
                        </button>
                        <button type="button" class="next-step px-6 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-md transition-colors">
                            Continuer
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Specialties and Regions -->
                <div class="form-step hidden" id="step3">
                    <h3 class="text-lg font-semibold text-text-primary border-b border-gray-200 pb-2 mb-4">Spécialités et régions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="specialties" class="block text-sm font-medium text-text-secondary mb-1">Types de voyages proposés</label>
                            <select id="specialties" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('specialties') border-red-500 @enderror" 
                                    name="specialties[]" multiple required>
                                <option value="aventure" {{ in_array('aventure', old('specialties', [])) ? 'selected' : '' }}>Aventure</option>
                                <option value="culturel" {{ in_array('culturel', old('specialties', [])) ? 'selected' : '' }}>Culturel</option>
                                <option value="gastronomique" {{ in_array('gastronomique', old('specialties', [])) ? 'selected' : '' }}>Gastronomique</option>
                                <option value="detente" {{ in_array('detente', old('specialties', [])) ? 'selected' : '' }}>Détente</option>
                                <option value="sportif" {{ in_array('sportif', old('specialties', [])) ? 'selected' : '' }}>Sportif</option>
                            </select>
                            <p class="mt-1 text-xs text-text-secondary">Maintenez Ctrl pour sélectionner plusieurs options</p>
                            @error('specialties')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="regions" class="block text-sm font-medium text-text-secondary mb-1">Régions proposées</label>
                            <select id="regions" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('regions') border-red-500 @enderror" 
                                    name="regions[]" multiple required>
                                <option value="europe" {{ in_array('europe', old('regions', [])) ? 'selected' : '' }}>Europe</option>
                                <option value="asie" {{ in_array('asie', old('regions', [])) ? 'selected' : '' }}>Asie</option>
                                <option value="afriqueN" {{ in_array('afriqueN', old('regions', [])) ? 'selected' : '' }}>Afrique du Nord</option>
                                <option value="afriqueS" {{ in_array('afriqueS', old('regions', [])) ? 'selected' : '' }}>Afrique Subsaharienne</option>
                                <option value="amerique_nord" {{ in_array('amerique_nord', old('regions', [])) ? 'selected' : '' }}>Amérique du Nord</option>
                                <option value="amerique_sud" {{ in_array('amerique_sud', old('regions', [])) ? 'selected' : '' }}>Amérique du Sud</option>
                                <option value="oceanie" {{ in_array('oceanie', old('regions', [])) ? 'selected' : '' }}>Océanie</option>
                            </select>
                            <p class="mt-1 text-xs text-text-secondary">Maintenez Ctrl pour sélectionner plusieurs options</p>
                            @error('regions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex items-start mb-4">
                            <div class="flex items-center h-5">
                                <input id="terms" type="checkbox" class="w-4 h-4 border border-border rounded focus:ring-primary @error('terms') border-red-500 @enderror" name="terms" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-text-secondary">J'accepte les <a href="{{ route('terms') }}" class="text-primary hover:text-primary-dark" target="_blank">conditions d'utilisation</a> et la <a href="{{ route('privacy') }}" class="text-primary hover:text-primary-dark" target="_blank">politique de confidentialité</a></label>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" class="prev-step px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-md transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                Retour
                            </button>
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                S'inscrire comme Organisateur
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-text-secondary">Déjà inscrit ? <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">Connectez-vous ici</a></p>
        </div>
    </div>

    <!-- Why Become an Organizer Section -->
    <div class="bg-bg-alt py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-text-primary">Pourquoi devenir organisateur ?</h2>
                <p class="mt-4 text-lg text-text-secondary">Rejoignez notre marketplace et bénéficiez de nombreux avantages</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Augmentez vos revenus</h3>
                    <p class="text-text-secondary">Touchez une nouvelle clientèle et développez votre activité grâce à notre plateforme de mise en relation.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Gestion simplifiée</h3>
                    <p class="text-text-secondary">Notre plateforme s'occupe des réservations, des paiements et du service client pour vous permettre de vous concentrer sur l'essentiel.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Visibilité internationale</h3>
                    <p class="text-text-secondary">Bénéficiez d'une présence en ligne à l'échelle mondiale et faites découvrir vos offres à des voyageurs du monde entier.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form steps navigation
        const steps = document.querySelectorAll('.form-step');
        const stepIndicators = document.querySelectorAll('.step');
        const nextButtons = document.querySelectorAll('.next-step');
        const prevButtons = document.querySelectorAll('.prev-step');
        let currentStep = 0;
        
        // Function to show specific step
        function showStep(stepIndex) {
            steps.forEach((step, index) => {
                if (index === stepIndex) {
                    step.classList.remove('hidden');
                } else {
                    step.classList.add('hidden');
                }
            });
            
            // Update step indicators
            stepIndicators.forEach((indicator, index) => {
                const indicatorCircle = indicator.querySelector('div');
                const indicatorText = indicator.querySelector('span');
                
                if (index < stepIndex) {
                    // Completed step
                    indicatorCircle.classList.remove('bg-gray-200', 'text-gray-600');
                    indicatorCircle.classList.add('bg-green-500', 'text-white');
                    indicatorText.classList.remove('text-gray-600');
                    indicatorText.classList.add('text-green-500');
                    indicator.classList.add('completed');
                } else if (index === stepIndex) {
                    // Current step
                    indicatorCircle.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                    indicatorCircle.classList.add('bg-primary', 'text-white');
                    indicatorText.classList.remove('text-gray-600', 'text-green-500');
                    indicatorText.classList.add('text-primary');
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    // Future step
                    indicatorCircle.classList.remove('bg-primary', 'text-white', 'bg-green-500');
                    indicatorCircle.classList.add('bg-gray-200', 'text-gray-600');
                    indicatorText.classList.remove('text-primary', 'text-green-500');
                    indicatorText.classList.add('text-gray-600');
                    indicator.classList.remove('active', 'completed');
                }
            });
            
            currentStep = stepIndex;
        }
        
        // Validate form fields in the current step
        function validateStep(stepIndex) {
            const currentStepElement = steps[stepIndex];
            const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                    
// Show error message if not already present
const errorElement = input.nextElementSibling;
if (!errorElement || !errorElement.classList.contains('text-red-600')) {
    const errorMsg = document.createElement('p');
    errorMsg.classList.add('mt-1', 'text-sm', 'text-red-600');
    errorMsg.textContent = input.validationMessage || 'Ce champ est requis';
    input.parentNode.insertBefore(errorMsg, input.nextSibling);
}
document.addEventListener('DOMContentLoaded', function() {
    // Form steps navigation
    const steps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    let currentStep = 0;
    
    // Function to show specific step
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            if (index === stepIndex) {
                step.classList.remove('hidden');
            } else {
                step.classList.add('hidden');
            }
        });
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            const indicatorCircle = indicator.querySelector('div');
            const indicatorText = indicator.querySelector('span');
            
            if (index < stepIndex) {
                // Completed step
                indicatorCircle.classList.remove('bg-gray-200', 'text-gray-600');
                indicatorCircle.classList.add('bg-green-500', 'text-white');
                indicatorText.classList.remove('text-gray-600');
                indicatorText.classList.add('text-green-500');
                indicator.classList.add('completed');
            } else if (index === stepIndex) {
                // Current step
                indicatorCircle.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                indicatorCircle.classList.add('bg-primary', 'text-white');
                indicatorText.classList.remove('text-gray-600', 'text-green-500');
                indicatorText.classList.add('text-primary');
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                // Future step
                indicatorCircle.classList.remove('bg-primary', 'text-white', 'bg-green-500');
                indicatorCircle.classList.add('bg-gray-200', 'text-gray-600');
                indicatorText.classList.remove('text-primary', 'text-green-500');
                indicatorText.classList.add('text-gray-600');
                indicator.classList.remove('active', 'completed');
            }
        });
        
        currentStep = stepIndex;
    }
    
    // Validate form fields in the current step
    function validateStep(stepIndex) {
        const currentStepElement = steps[stepIndex];
        const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('border-red-500');
                isValid = false;
                
                // Show error message if not already present
                const errorElement = input.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('text-red-600')) {
                    const errorMsg = document.createElement('p');
                    errorMsg.classList.add('mt-1', 'text-sm', 'text-red-600');
                    errorMsg.textContent = input.validationMessage || 'Ce champ est requis';
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                }
            } else {
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
                
                // Remove error message if exists
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('text-red-600')) {
                    errorElement.remove();
                }
            }
        });
        
        return isValid;
    }
    
    // Handle next button clicks
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                showStep(currentStep + 1);
                window.scrollTo(0, 0);
            }
        });
    });
    
    // Handle previous button clicks
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            showStep(currentStep - 1);
            window.scrollTo(0, 0);
        });
    });
    
    // Initialize form
    showStep(0);
    
    // Form submission validation
    const form = document.getElementById('organizerForm');
    form.addEventListener('submit', function(event) {
        if (!validateStep(currentStep)) {
            event.preventDefault();
        }
    });
    
    // Real-time validation for inputs
    const allInputs = form.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
                
                // Remove error message if exists
                const errorElement = this.nextElementSibling;
                if (errorElement && errorElement.classList.contains('text-red-600')) {
                    errorElement.remove();
                }
            } else if (this.required || this.value !== '') {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500');
                
                // Show error message if not already present
                const errorElement = this.nextElementSi