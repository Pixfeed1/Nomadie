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
                        <x-input
                            type="text"
                            name="name"
                            label="Nom complet"
                            :required="true"
                            autocomplete="name"
                            autofocus
                        />

                        <x-input
                            type="email"
                            name="email"
                            label="Adresse email"
                            :required="true"
                            autocomplete="email"
                        />

                        <x-input
                            type="password"
                            name="password"
                            label="Mot de passe"
                            hint="8 caractères minimum, avec chiffres et lettres"
                            :required="true"
                            autocomplete="new-password"
                        />

                        <x-input
                            type="password"
                            id="password-confirm"
                            name="password_confirmation"
                            label="Confirmer le mot de passe"
                            :required="true"
                            autocomplete="new-password"
                        />

                        <x-input
                            type="text"
                            name="phone"
                            label="Téléphone"
                            :required="true"
                        />
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
                        <x-input
                            type="text"
                            name="company_name"
                            label="Nom de l'entreprise"
                            :required="true"
                        />

                        <x-input
                            type="url"
                            name="company_website"
                            label="Site web"
                        />

                        <x-input
                            type="text"
                            name="company_siret"
                            label="Numéro SIRET"
                            :required="true"
                        />

                        <x-input
                            type="text"
                            name="company_vat"
                            label="Numéro de TVA (optionnel)"
                            hint="Pour les entreprises assujetties à la TVA (format FR12345678901)"
                        />

                        <x-input
                            type="text"
                            name="company_address"
                            label="Adresse de l'entreprise"
                            :required="true"
                        />

                        <div>
                            <label for="company_logo" class="block text-sm font-medium text-text-secondary mb-1">Logo de l'entreprise</label>
                            <input id="company_logo" type="file" class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2.5 @error('company_logo') border-red-500 @enderror"
                                   name="company_logo" accept="image/*">
                            <p class="mt-1 text-xs text-text-secondary">Format recommandé: PNG ou JPG, max 2Mo</p>
                            @error('company_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-textarea
                            name="company_description"
                            label="Description de l'entreprise"
                            :rows="4"
                            class="md:col-span-2"
                        />
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
                        <x-select
                            name="specialties[]"
                            label="Types de voyages proposés"
                            :options="[
                                'aventure' => 'Aventure',
                                'culturel' => 'Culturel',
                                'gastronomique' => 'Gastronomique',
                                'detente' => 'Détente',
                                'sportif' => 'Sportif'
                            ]"
                            :selected="old('specialties', [])"
                            :required="true"
                            hint="Maintenez Ctrl pour sélectionner plusieurs options"
                            multiple
                            placeholder=""
                        />

                        <x-select
                            name="regions[]"
                            label="Régions proposées"
                            :options="[
                                'europe' => 'Europe',
                                'asie' => 'Asie',
                                'afriqueN' => 'Afrique du Nord',
                                'afriqueS' => 'Afrique Subsaharienne',
                                'amerique_nord' => 'Amérique du Nord',
                                'amerique_sud' => 'Amérique du Sud',
                                'oceanie' => 'Océanie'
                            ]"
                            :selected="old('regions', [])"
                            :required="true"
                            hint="Maintenez Ctrl pour sélectionner plusieurs options"
                            multiple
                            placeholder=""
                        />
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