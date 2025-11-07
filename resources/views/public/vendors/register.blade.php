@extends('layouts.public')

@section('title', 'Devenir Organisateur - Nomadie')

@section('styles')
<style>
    /* Style pour les éléments avec erreur */
    .border-error {
        border-color: #ef4444 !important;
    }
    
    /* Styles pour les destinations cliquables */
    .destination-item {
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: between;
    }
    
    .destination-item:hover {
        border-color: var(--color-primary);
        background-color: rgba(var(--color-primary-rgb), 0.05);
    }
    
    /* Style des destinations sélectionnées */
    .destination-selected {
        border-color: var(--color-primary) !important;
        background-color: rgba(var(--color-primary-rgb), 0.1) !important;
    }
    
    /* Style des accordéons de continent */
    .continent-accordion.accordion-open .accordion-arrow {
        transform: rotate(180deg);
    }
    
    .accordion-header.has-selections {
        background-color: rgba(var(--color-primary-rgb), 0.05);
        border-left: 3px solid var(--color-primary);
    }
    
    /* Accordéons fermés par défaut */
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out;
    }
    
    .accordion-content:not(.hidden) {
        max-height: 2000px;
    }
    
    /* Style du compteur de sélection */
    .selection-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        background-color: var(--color-primary);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* Style pour les services cliquables */
    .service-category-item {
        transition: all 0.2s ease;
    }
    
    .service-category-item:hover {
        border-color: var(--color-primary);
        background-color: rgba(var(--color-primary-rgb), 0.05);
    }
    
    /* Styles pour les plans d'abonnement */
    .subscription-plan {
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .subscription-plan:hover {
        border-color: var(--color-primary);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
    
    .subscription-plan.selected {
        border-color: var(--color-primary);
        background-color: rgba(var(--color-primary-rgb), 0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .subscription-plan.featured {
        border-width: 2px;
        border-color: var(--color-primary);
    }
    
    .subscription-plan.featured::before {
        content: "Populaire";
        position: absolute;
        top: -12px;
        right: 20px;
        background-color: var(--color-accent);
        color: white;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 9999px;
    }
    
    /* Ajustements responsive */
    @media (max-width: 768px) {
        .nav-steps {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .subscription-plan {
            margin-bottom: 2rem;
        }
        
        .subscription-plan.featured::before {
            right: 10px;
            top: -10px;
            font-size: 10px;
            padding: 3px 8px;
        }
    }
    
    /* Style du logo preview */
    #logo-preview-container img {
        max-height: 100px;
        max-width: 200px;
        object-fit: contain;
    }
    
    /* Amélioration de l'accessibilité */
    .form-required::after {
        content: "*";
        color: var(--color-error);
        margin-left: 2px;
    }
    
    /* Animation de chargement pour les boutons */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }
    
    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-text-primary">Devenir Organisateur sur Nomadie</h1>
            <p class="mt-3 text-lg text-text-secondary">Rejoignez notre plateforme et proposez vos expériences uniques</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Formulaire -->
            <div x-data="vendorRegistration()">
                <!-- Étapes du formulaire - 5 étapes -->
                <div class="border-b border-border">
                    <div class="px-6 py-4 overflow-x-auto">
                        <nav class="flex justify-between nav-steps">
                            <button 
                                @click="goToStep(1)" 
                                :class="activeStep === 1 ? 'text-white bg-primary' : 'text-text-secondary bg-transparent hover:text-primary hover:bg-primary/10'"
                                type="button" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                            >
                                1. Informations
                            </button>
                            <button 
                                @click="goToStep(2)" 
                                :class="[
                                    activeStep === 2 ? 'text-white bg-primary' : 'text-text-secondary bg-transparent hover:text-primary hover:bg-primary/10',
                                    !isStepAccessible(2) ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                type="button" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                :disabled="!isStepAccessible(2)"
                            >
                                2. Abonnement
                            </button>
                            <button 
                                @click="goToStep(3)" 
                                :class="[
                                    activeStep === 3 ? 'text-white bg-primary' : 'text-text-secondary bg-transparent hover:text-primary hover:bg-primary/10',
                                    !isStepAccessible(3) ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                type="button" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                :disabled="!isStepAccessible(3)"
                            >
                                3. Destinations
                            </button>
                            <button 
                                @click="goToStep(4)" 
                                :class="[
                                    activeStep === 4 ? 'text-white bg-primary' : 'text-text-secondary bg-transparent hover:text-primary hover:bg-primary/10',
                                    !isStepAccessible(4) ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                type="button" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                :disabled="!isStepAccessible(4)"
                            >
                                4. Services
                            </button>
                            <button 
                                @click="goToStep(5)" 
                                :class="[
                                    activeStep === 5 ? 'text-white bg-primary' : 'text-text-secondary bg-transparent hover:text-primary hover:bg-primary/10',
                                    !isStepAccessible(5) ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                type="button" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                :disabled="!isStepAccessible(5)"
                            >
                                5. Finalisation
                            </button>
                        </nav>
                    </div>
                </div>
                
                <!-- FORM sans action, soumission empêchée -->
                <form class="p-6 space-y-8" @submit.prevent="" enctype="multipart/form-data">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    
                    <!-- Champ caché pour l'abonnement -->
                    <input type="hidden" name="subscription" x-model="subscription">
                    
                    <!-- Champ caché pour le token de persistance des données -->
                    <input type="hidden" name="token" value="{{ session('vendor_token') ?? request()->query('token') }}">
                    
                    <!-- Étape 1: Informations générales -->
                    <div x-show="activeStep === 1" id="step-1-content" class="space-y-8">
                        <!-- Section 1: Informations générales -->
                        <div>
                            <h2 class="text-xl font-semibold text-text-primary mb-4">Informations générales</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-input
                                    type="text"
                                    name="company_name"
                                    label="Nom de l'entreprise"
                                    placeholder="Nom de votre société ou entreprise"
                                    :required="true"
                                />

                                <x-select
                                    name="legal_status"
                                    label="Statut juridique"
                                    :options="[
                                        '' => 'Sélectionnez',
                                        'sarl' => 'SARL',
                                        'sas' => 'SAS',
                                        'ei' => 'Entreprise Individuelle',
                                        'other' => 'Autre'
                                    ]"
                                    :selected="old('legal_status')"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="siret"
                                    label="Numéro SIRET"
                                    placeholder="123 456 789 00012"
                                    hint="14 chiffres, formatage automatique"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="vat"
                                    label="Numéro de TVA"
                                    placeholder="FR 12 123456789"
                                />
                            </div>
                        </div>

                        <!-- Section 2: Coordonnées de l'entreprise -->
                        <div>
                            <h2 class="text-xl font-semibold text-text-primary mb-4">Coordonnées de l'entreprise</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-input
                                    type="email"
                                    name="email"
                                    label="Email principal de contact"
                                    placeholder="contact@votreentreprise.com"
                                    hint="Email utilisé pour les notifications de l'entreprise"
                                    :required="true"
                                />

                                <x-input
                                    type="tel"
                                    name="phone"
                                    label="Téléphone"
                                    placeholder="+33 6 12 34 56 78"
                                    :required="true"
                                />

                                <x-input
                                    type="url"
                                    name="website"
                                    label="Site internet"
                                    placeholder="www.votresite.com"
                                    hint="Le https:// sera ajouté automatiquement"
                                />

                                <x-input
                                    type="text"
                                    name="address"
                                    label="Adresse"
                                    placeholder="15 rue des voyages"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="postal_code"
                                    label="Code postal"
                                    placeholder="75001"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="city"
                                    label="Ville"
                                    placeholder="Paris"
                                    :required="true"
                                />

                                <x-select
                                    name="country"
                                    label="Pays"
                                    :options="[
                                        'FR' => 'France',
                                        'BE' => 'Belgique',
                                        'CH' => 'Suisse',
                                        'CA' => 'Canada',
                                        'other' => 'Autre'
                                    ]"
                                    :selected="old('country', 'FR')"
                                    :required="true"
                                    class="md:col-span-2"
                                />
                            </div>
                        </div>

                        <!-- Section 3: Représentant légal -->
                        <div>
                            <h2 class="text-xl font-semibold text-text-primary mb-4">Représentant légal</h2>
                            <p class="text-sm text-text-secondary mb-4">Personne physique responsable de l'entreprise</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-input
                                    type="text"
                                    name="rep_firstname"
                                    label="Prénom"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="rep_lastname"
                                    label="Nom"
                                    :required="true"
                                />

                                <x-input
                                    type="text"
                                    name="rep_position"
                                    label="Fonction"
                                    placeholder="Directeur, Gérant, etc."
                                    :required="true"
                                />

                                <x-input
                                    type="email"
                                    name="rep_email"
                                    label="Email personnel du représentant"
                                    placeholder="john.doe@email.com"
                                    hint="Peut être différent de l'email de l'entreprise"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <!-- Section 4: Présentation -->
                        <div>
                            <h2 class="text-xl font-semibold text-text-primary mb-4">Présentation de votre entreprise</h2>

                            <div class="space-y-4">
                                <x-textarea
                                    name="description"
                                    label="Description courte"
                                    :rows="4"
                                    placeholder="Présentez brièvement votre entreprise, votre spécialité dans l'organisation de voyages et votre approche..."
                                    hint="Maximum 500 caractères"
                                    :required="true"
                                />

                                <x-select
                                    name="experience"
                                    label="Années d'expérience"
                                    :options="[
                                        '' => 'Sélectionnez',
                                        '1' => 'Moins d\'1 an',
                                        '1-3' => '1 à 3 ans',
                                        '3-5' => '3 à 5 ans',
                                        '5-10' => '5 à 10 ans',
                                        '10+' => 'Plus de 10 ans'
                                    ]"
                                    :selected="old('experience')"
                                    :required="true"
                                />
                                
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-1">Logo de l'entreprise</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-border border-dashed rounded-lg">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-text-secondary" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-text-secondary">
                                                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark">
                                                    <span>Télécharger un fichier</span>
                                                    <input id="logo" name="logo" type="file" class="sr-only" accept="image/jpeg,image/png,image/gif">
                                                </label>
                                                <p class="pl-1">ou glisser-déposer</p>
                                            </div>
                                            <p class="text-xs text-text-secondary">PNG, JPG, GIF jusqu'à 2MB</p>
                                        </div>
                                    </div>
                                    <div id="logo-preview-container" class="mt-3 flex justify-center hidden"></div>
                                    @error('logo')<p class="text-xs text-error mt-1" data-field="logo">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="text-right">
                            <button
                                type="button"
                                @click="nextStep()"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'btn-loading' : ''"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>Continuer</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 2: Choix d'abonnement -->
                    <div x-show="activeStep === 2" id="step-2-content" class="space-y-8">
                        <h2 class="text-xl font-semibold text-text-primary mb-4">Choisissez votre formule</h2>
                        <p class="text-text-secondary">Sélectionnez l'abonnement qui correspond à vos besoins</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Abonnement Gratuit -->
                            <div 
                                @click="subscription = 'free'"
                                :class="{ 'selected': subscription === 'free' }"
                                class="subscription-plan border border-border rounded-lg p-6 space-y-4"
                                data-plan="free"
                            >
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-text-primary">Gratuit</h3>
                                    <div class="h-6 w-6 rounded-full border-2 border-border flex items-center justify-center">
                                        <div 
                                            :class="{ 'hidden': subscription !== 'free' }"
                                            class="h-3 w-3 rounded-full bg-primary"
                                        ></div>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold">0 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                                <p class="text-sm text-text-secondary">Commission par vente: <span class="font-bold text-error">20%</span></p>
                                <ul class="space-y-2 text-sm">
                                    <x-checkmark-item color="success">Accès à la plateforme</x-checkmark-item>
                                    <x-checkmark-item color="success"><span class="font-semibold text-error">5 offres actives maximum</span></x-checkmark-item>
                                    <x-checkmark-item color="success">Support par email</x-checkmark-item>
                                </ul>
                            </div>

                            <!-- Abonnement Essentiel -->
                            <div 
                                @click="subscription = 'essential'"
                                :class="{ 'selected': subscription === 'essential' }"
                                class="subscription-plan featured border border-border rounded-lg p-6 space-y-4"
                                data-plan="essential"
                            >
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-text-primary">Essentiel</h3>
                                    <div class="h-6 w-6 rounded-full border-2 border-border flex items-center justify-center">
                                        <div 
                                            :class="{ 'hidden': subscription !== 'essential' }"
                                            class="h-3 w-3 rounded-full bg-primary"
                                        ></div>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold">49 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                                <p class="text-sm text-text-secondary">Commission par vente: <span class="font-bold text-accent">10%</span></p>
                                <ul class="space-y-2 text-sm">
                                    <x-checkmark-item color="success">Accès à la plateforme</x-checkmark-item>
                                    <x-checkmark-item color="success"><span class="font-semibold text-accent">50 offres actives maximum</span></x-checkmark-item>
                                    <x-checkmark-item color="success">Support prioritaire</x-checkmark-item>
                                </ul>
                            </div>

                            <!-- Abonnement Pro -->
                            <div
                                @click="subscription = 'pro'"
                                :class="{ 'selected': subscription === 'pro' }"
                                class="subscription-plan border border-border rounded-lg p-6 space-y-4"
                                data-plan="pro"
                            >
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-text-primary">Pro</h3>
                                    <div class="h-6 w-6 rounded-full border-2 border-border flex items-center justify-center">
                                        <div
                                            :class="{ 'hidden': subscription !== 'pro' }"
                                            class="h-3 w-3 rounded-full bg-primary"
                                        ></div>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold">99 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                                <p class="text-sm text-text-secondary">Commission par vente: <span class="font-bold text-success">5%</span></p>
                                <ul class="space-y-2 text-sm">
                                    <x-checkmark-item color="success">Accès à la plateforme</x-checkmark-item>
                                    <x-checkmark-item color="success"><span class="font-semibold text-success">Offres illimitées</span></x-checkmark-item>
                                    <x-checkmark-item color="success">Support 24/7</x-checkmark-item>
                                    <x-checkmark-item color="success">Mise en avant prioritaire</x-checkmark-item>
                                </ul>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex flex-wrap justify-between gap-4">
                            <button 
                                type="button" 
                                @click="prevStep()"
                                class="inline-flex items-center px-6 py-3 border border-primary text-base font-medium rounded-md text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                <span>Précédent</span>
                            </button>
                            <button 
                                type="button" 
                                @click="nextStep()"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'btn-loading' : ''"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>Continuer</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 3: Destinations -->
                    <div x-show="activeStep === 3" id="step-3-content" class="space-y-8">
                        <h2 class="text-xl font-semibold text-text-primary mb-4">Vos destinations</h2>
                        
                        <!-- Information sur les destinations -->
                        <p class="text-text-secondary mb-4">
                            Sélectionnez tous les pays où vous proposez vos offres (hébergements, séjours organisés et activités). 
                            Vous pourrez ajouter ou retirer des destinations à tout moment depuis votre espace organisateur.
                        </p>

                        <!-- Note informative sur les offres -->
                        <x-alert type="info" class="mb-6">
                            <strong>Information :</strong> Les destinations que vous sélectionnez ici définissent
                            où vous pouvez créer vos offres. Le nombre d'offres actives dépend de votre abonnement :
                            <ul class="mt-2 space-y-1">
                                <li>• <strong>Gratuit :</strong> 5 offres actives maximum</li>
                                <li>• <strong>Essentiel :</strong> 50 offres actives maximum</li>
                                <li>• <strong>Pro :</strong> Offres illimitées</li>
                            </ul>
                        </x-alert>
                        
                        <!-- Barre de recherche -->
                        <div>
                            <label for="search-destination" class="block text-sm font-medium text-text-primary mb-1">Rechercher une destination:</label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="search-destination" 
                                    placeholder="Nom du pays..." 
                                    class="w-full pl-10 pr-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                    x-model="searchText"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <!-- Compteur simple sans limite -->
                            <div class="mt-2 text-sm text-text-secondary">
                                <span class="font-semibold" id="selected-destinations-counter">0</span> destinations sélectionnées
                            </div>
                        </div>

                        <!-- Boutons de sélection globaux -->
                        <div class="bg-primary/10 border border-primary/20 rounded-lg p-4 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="select-all-btn" class="px-3 py-1 bg-primary text-white text-sm rounded-md hover:bg-primary-dark transition-colors">Tout sélectionner</button>
                                <button type="button" id="deselect-all-btn" class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition-colors">Tout désélectionner</button>
                            </div>
                        </div>

                        <!-- Liste des destinations par continent -->
                        <div class="space-y-4">
                            @php
                                $continents = $destinations->groupBy('continent.id')->map(function($items, $key) {
                                    return [
                                        'id' => $key,
                                        'name' => $items->first()->continent->name ?? 'Autre',
                                        'destinations' => $items
                                    ];
                                })->sortBy('name')->values();
                            @endphp
                            
                            @foreach($continents as $continent)
                                <div 
                                    class="continent-accordion border border-border rounded-lg overflow-hidden" 
                                    data-continent-id="{{ $continent['id'] }}"
                                >
                                    <!-- En-tête de l'accordéon -->
                                    <div class="accordion-header p-4 flex items-center justify-between bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <h3 class="text-base font-medium text-text-primary">{{ $continent['name'] }}</h3>
                                            <span class="ml-3 text-xs text-text-secondary">
                                                (<span class="visible-counter">{{ count($continent['destinations']) }}</span> pays, 
                                                <span class="continent-counter">0</span> sélectionné(s))
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button 
                                                type="button" 
                                                class="select-continent-btn px-2 py-1 text-xs bg-primary text-white rounded hover:bg-primary-dark transition-colors"
                                                data-continent-id="{{ $continent['id'] }}"
                                            >
                                                Tout sélectionner
                                            </button>
                                            <button 
                                                type="button" 
                                                class="deselect-continent-btn px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors"
                                                data-continent-id="{{ $continent['id'] }}"
                                            >
                                                Tout désélectionner
                                            </button>
                                            <svg class="h-5 w-5 text-text-secondary transform transition-transform accordion-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu de l'accordéon -->
                                    <div class="accordion-content hidden p-4 border-t border-border">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            @foreach($continent['destinations'] as $destination)
                                            <div 
                                                class="destination-item" 
                                                data-destination-id="{{ $destination->id }}"
                                                data-continent-id="{{ $destination->continent_id ?? '' }}"
                                                data-destination-name="{{ htmlspecialchars(strtolower($destination->name), ENT_QUOTES, 'UTF-8') }}"
                                            >
                                                <span class="font-medium flex-1">{{ $destination->name }}</span>
                                                <input 
                                                    type="checkbox" 
                                                    name="destinations[]" 
                                                    value="{{ $destination->id }}" 
                                                    class="h-4 w-4 text-primary focus:ring-primary border-border rounded cursor-pointer ml-2"
                                                    {{ (is_array(old('destinations')) && in_array($destination->id, old('destinations'))) ? 'checked' : '' }}
                                                >
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation -->
                        <div class="flex flex-wrap justify-between mt-6 gap-4">
                            <button 
                                type="button" 
                                @click="prevStep()"
                                class="inline-flex items-center px-6 py-3 border border-primary text-base font-medium rounded-md text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                <span>Précédent</span>
                            </button>
                            <button 
                                type="button" 
                                @click="nextStep()"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'btn-loading' : ''"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>Continuer</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 4: Services -->
                    <div x-show="activeStep === 4" id="step-4-content" class="space-y-8">
                        <h2 class="text-xl font-semibold text-text-primary mb-4">Vos services</h2>
                        <p class="text-text-secondary">Indiquez les types d'offres et services que vous proposez</p>

                        <!-- Catégories principales de services (max 3) -->
                        <div class="bg-white rounded-lg border border-border p-6 space-y-4">
                            <h3 class="text-lg font-medium text-text-primary mb-2">Vos spécialités principales <span class="text-sm text-text-secondary">(max 3)</span></h3>
                            <p class="text-text-secondary mb-4">Sélectionnez jusqu'à 3 types d'offres qui représentent le mieux votre activité</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    // Filtrer les catégories adaptées aux offres
                                    $filteredCategories = $serviceCategories->filter(function($category) {
                                        // Exclure les catégories qui ne correspondent pas
                                        $excludedNames = ['Croisières & Navigation', 'Voyages thématiques'];
                                        return !in_array($category->name, $excludedNames);
                                    });
                                @endphp
                                
                                @foreach($filteredCategories as $category)
                                <div class="flex items-start p-4 border border-border rounded-lg hover:border-primary transition-colors cursor-pointer service-category-item"
                                    data-category-id="{{ $category->id }}">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="service-category-{{ $category->id }}" 
                                            name="service_categories[]" 
                                            type="checkbox" 
                                            value="{{ $category->id }}" 
                                            class="focus:ring-primary h-4 w-4 text-primary border-border rounded service-category-checkbox" 
                                            {{ (is_array(old('service_categories')) && in_array($category->id, old('service_categories'))) ? 'checked' : '' }}
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="service-category-{{ $category->id }}" class="font-medium text-text-primary">{{ $category->name }}</label>
                                        <p class="text-text-secondary">{{ $category->description }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div id="category-limit-warning" class="hidden mt-2 text-sm text-error">
                                Vous ne pouvez sélectionner que 3 spécialités principales maximum.
                            </div>
                        </div>

                        <!-- Attributs de services par type -->
                        <div class="bg-white rounded-lg border border-border p-6 space-y-6">
                            <div class="flex flex-wrap justify-between items-center gap-4">
                                <h3 class="text-lg font-medium text-text-primary">Formats et attributs complémentaires</h3>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" id="select-all-service-attributes-btn" class="px-3 py-1 bg-primary text-white text-sm rounded-md hover:bg-primary-dark transition-colors">Tout sélectionner</button>
                                    <button type="button" id="deselect-all-service-attributes-btn" class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition-colors">Tout désélectionner</button>
                                </div>
                            </div>
                            <p class="text-text-secondary">Sélectionnez les attributs qui s'appliquent à vos offres</p>
                            
                            <!-- Format d'offre -->
                            <div class="mb-6">
                                <h4 class="font-medium text-text-primary mb-3 pb-2 border-b border-border">Format d'offre</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @php
                                        $formatAttributes = $serviceAttributes->where('type', 'format')->filter(function($attr) {
                                            return $attr->name !== 'Circuit organisé';
                                        });
                                    @endphp
                                    @foreach($formatAttributes as $attribute)
                                    <div class="flex items-center">
                                        <input 
                                            id="service-attribute-{{ $attribute->id }}" 
                                            name="service_attributes[]" 
                                            type="checkbox" 
                                            value="{{ $attribute->id }}" 
                                            class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                            {{ (is_array(old('service_attributes')) && in_array($attribute->id, old('service_attributes'))) ? 'checked' : '' }}
                                        >
                                        <label for="service-attribute-{{ $attribute->id }}" class="ml-2 text-sm text-text-primary">{{ $attribute->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Style d'offre -->
                            <div class="mb-6">
                                <h4 class="font-medium text-text-primary mb-3 pb-2 border-b border-border">Style d'offre</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($serviceAttributes->where('type', 'style') as $attribute)
                                    <div class="flex items-center">
                                        <input 
                                            id="service-attribute-{{ $attribute->id }}" 
                                            name="service_attributes[]" 
                                            type="checkbox" 
                                            value="{{ $attribute->id }}" 
                                            class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                            {{ (is_array(old('service_attributes')) && in_array($attribute->id, old('service_attributes'))) ? 'checked' : '' }}
                                        >
                                        <label for="service-attribute-{{ $attribute->id }}" class="ml-2 text-sm text-text-primary">{{ $attribute->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Services spéciaux -->
                            <div class="mb-6">
                                <h4 class="font-medium text-text-primary mb-3 pb-2 border-b border-border">Services spéciaux</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($serviceAttributes->where('type', 'service') as $attribute)
                                    <div class="flex items-center">
                                        <input 
                                            id="service-attribute-{{ $attribute->id }}" 
                                            name="service_attributes[]" 
                                            type="checkbox" 
                                            value="{{ $attribute->id }}" 
                                            class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                            {{ (is_array(old('service_attributes')) && in_array($attribute->id, old('service_attributes'))) ? 'checked' : '' }}
                                        >
                                        <label for="service-attribute-{{ $attribute->id }}" class="ml-2 text-sm text-text-primary">{{ $attribute->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Durée typique -->
                            <div>
                                <h4 class="font-medium text-text-primary mb-3 pb-2 border-b border-border">Durée typique</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($serviceAttributes->where('type', 'duration') as $attribute)
                                    <div class="flex items-center">
                                        <input 
                                            id="service-attribute-{{ $attribute->id }}" 
                                            name="service_attributes[]" 
                                            type="checkbox" 
                                            value="{{ $attribute->id }}" 
                                            class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                            {{ (is_array(old('service_attributes')) && in_array($attribute->id, old('service_attributes'))) ? 'checked' : '' }}
                                        >
                                        <label for="service-attribute-{{ $attribute->id }}" class="ml-2 text-sm text-text-primary">{{ $attribute->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

<!-- Navigation -->
                        <div class="flex flex-wrap justify-between gap-4">
                            <button 
                                type="button" 
                                @click="prevStep()"
                                class="inline-flex items-center px-6 py-3 border border-primary text-base font-medium rounded-md text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                <span>Précédent</span>
                            </button>
                            <button 
                                type="button" 
                                @click="nextStep()"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'btn-loading' : ''"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>Continuer</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 5: Finalisation -->
                    <div x-show="activeStep === 5" id="step-5-content" class="space-y-8">
                        <h2 class="text-xl font-semibold text-text-primary mb-4">Finalisation de votre inscription</h2>
                        <p class="text-text-secondary">Dernière étape avant de rejoindre Nomadie</p>

                        <!-- Récapitulatif de la sélection -->
                        <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                            <h3 class="text-lg font-medium text-text-primary">Récapitulatif de votre sélection</h3>
                            
                            <!-- Abonnement choisi -->
                            <div class="border-b border-gray-200 pb-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-text-primary">Abonnement :</span>
                                    <span class="text-primary font-semibold" x-text="subscriptionLimits[subscription].name"></span>
                                </div>
                                
                                <!-- Prix sans infos de facturation -->
                                <div class="mt-3 space-y-2 text-sm">
                                    <!-- Abonnement gratuit -->
                                    <div x-show="subscription === 'free'">
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Abonnement mensuel :</span>
                                            <span class="font-semibold text-success">Gratuit</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Commission par vente :</span>
                                            <span class="font-semibold text-error">20%</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Offres actives maximum :</span>
                                            <span class="font-semibold">5</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Abonnement Essentiel -->
                                    <div x-show="subscription === 'essential'">
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Abonnement mensuel :</span>
                                            <span class="font-semibold">49,00 € TTC</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Commission par vente :</span>
                                            <span class="font-semibold text-accent">10%</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Offres actives maximum :</span>
                                            <span class="font-semibold">50</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Abonnement Pro -->
                                    <div x-show="subscription === 'pro'">
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Abonnement mensuel :</span>
                                            <span class="font-semibold">99,00 € TTC</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Commission par vente :</span>
                                            <span class="font-semibold text-success">5%</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-text-secondary">Offres actives :</span>
                                            <span class="font-semibold text-success">Illimitées</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Destinations sélectionnées -->
                            <div class="border-b border-gray-200 pb-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-text-primary">Destinations :</span>
                                    <span class="text-text-secondary">
                                        <span id="final-destinations-count">0</span> destinations sélectionnées
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Catégories de services -->
                            <div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-text-primary">Spécialités principales :</span>
                                    <span class="text-text-secondary">
                                        <span id="final-categories-count">0</span> catégories sélectionnées
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Conditions générales -->
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        id="terms" 
                                        name="terms" 
                                        type="checkbox" 
                                        class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                        required 
                                        {{ old('terms') ? 'checked' : '' }}
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-text-primary">
                                        J'accepte les 
                                        <a href="#" class="text-primary hover:text-primary-dark underline" target="_blank">conditions générales d'utilisation</a> 
                                        et la 
                                        <a href="#" class="text-primary hover:text-primary-dark underline" target="_blank">politique de confidentialité</a>.
                                        <span class="text-error">*</span>
                                    </label>
                                    @error('terms')<p class="text-xs text-error mt-1" data-field="terms">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        id="newsletter" 
                                        name="newsletter" 
                                        type="checkbox" 
                                        class="focus:ring-primary h-4 w-4 text-primary border-border rounded" 
                                        {{ old('newsletter') ? 'checked' : '' }}
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="newsletter" class="font-medium text-text-primary">
                                        Je souhaite recevoir la newsletter et les offres de Nomadie.
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation finale -->
                        <div class="flex flex-wrap justify-between gap-4">
                            <button 
                                type="button" 
                                @click="prevStep()"
                                class="inline-flex items-center px-6 py-3 border border-primary text-base font-medium rounded-md text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                <span>Précédent</span>
                            </button>
                            <button 
                                type="button"
                                @click="nextStep()"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'btn-loading' : ''"
                                id="submit-button"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-text="subscription === 'free' ? 'Finaliser l\'inscription' : 'Procéder au paiement'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Avantages -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="h-12 w-12 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Plus de revenus</h3>
                <p class="text-text-secondary text-sm">Touchez une nouvelle clientèle et augmentez significativement vos réservations grâce à Nomadie.</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="h-12 w-12 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Gestion simplifiée</h3>
                <p class="text-text-secondary text-sm">Un tableau de bord complet pour gérer vos offres, réservations et paiements en toute simplicité.</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="h-12 w-12 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Visibilité accrue</h3>
                <p class="text-text-secondary text-sm">Bénéficiez de la présence en ligne de Nomadie et attirez des clients du monde entier.</p>
            </div>
        </div>

        <!-- FAQ -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-text-primary text-center mb-8">Questions fréquentes</h2>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div x-data="{ activeTab: null }">
                    <div class="divide-y divide-border">
                        <div class="p-6">
                            <button @click="activeTab = activeTab === 'inscription' ? null : 'inscription'" class="w-full flex justify-between items-center text-left focus:outline-none">
                                <h3 class="text-lg font-medium text-text-primary">Combien coûte l'inscription ?</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': activeTab === 'inscription'}" class="h-5 w-5 text-text-secondary transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeTab === 'inscription'" x-transition class="mt-2 text-text-secondary">
                                <p>L'inscription sur Nomadie est totalement gratuite. Vous ne payez que lorsque vous choisissez un abonnement payant (Essentiel ou Pro) ou lorsque vous réalisez des ventes (commission sur les transactions).</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <button @click="activeTab = activeTab === 'commissions' ? null : 'commissions'" class="w-full flex justify-between items-center text-left focus:outline-none">
                                <h3 class="text-lg font-medium text-text-primary">Comment sont versées les commissions ?</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': activeTab === 'commissions'}" class="h-5 w-5 text-text-secondary transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeTab === 'commissions'" x-transition class="mt-2 text-text-secondary">
                                <p>Les paiements sont traités automatiquement par Nomadie. Nous prélevons notre commission au moment de la transaction et vous reversons le montant restant directement sur votre compte bancaire dans un délai de 7 jours.</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <button @click="activeTab = activeTab === 'formule' ? null : 'formule'" class="w-full flex justify-between items-center text-left focus:outline-none">
                                <h3 class="text-lg font-medium text-text-primary">Puis-je changer de formule d'abonnement ?</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': activeTab === 'formule'}" class="h-5 w-5 text-text-secondary transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeTab === 'formule'" x-transition class="mt-2 text-text-secondary">
                                <p>Absolument ! Vous pouvez passer d'une formule à une autre à tout moment depuis votre espace organisateur. Le changement prendra effet au début du mois suivant.</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <button @click="activeTab = activeTab === 'reservations' ? null : 'reservations'" class="w-full flex justify-between items-center text-left focus:outline-none">
                                <h3 class="text-lg font-medium text-text-primary">Comment sont gérées les réservations ?</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': activeTab === 'reservations'}" class="h-5 w-5 text-text-secondary transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeTab === 'reservations'" x-transition class="mt-2 text-text-secondary">
                                <p>Nomadie gère automatiquement les réservations, les paiements et les confirmations. Vous recevez une notification pour chaque nouvelle réservation et pouvez gérer tous les détails depuis votre tableau de bord.</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <button @click="activeTab = activeTab === 'offres' ? null : 'offres'" class="w-full flex justify-between items-center text-left focus:outline-none">
                                <h3 class="text-lg font-medium text-text-primary">Quels types d'offres puis-je proposer sur Nomadie ?</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': activeTab === 'offres'}" class="h-5 w-5 text-text-secondary transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeTab === 'offres'" x-transition class="mt-2 text-text-secondary">
                                <p>Sur Nomadie, vous pouvez proposer trois types d'offres : des hébergements (villas, gîtes, appartements), des séjours organisés (voyages tout compris, retraites, stages) et des activités (excursions, cours, visites guidées). Chaque type d'offre dispose d'outils adaptés pour la gestion et la réservation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="mt-12 text-center">
            <p class="text-text-secondary">Vous avez d'autres questions ?</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center text-primary hover:text-primary-dark font-medium mt-2">
                Contactez notre équipe Nomadie
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/vendor-registration.js')

<script>
document.addEventListener('DOMContentLoaded', function() {
   function updateFinalCounters() {
       const destinationsCount = document.querySelectorAll('input[name="destinations[]"]:checked').length;
       const destinationsCounter = document.getElementById('final-destinations-count');
       if (destinationsCounter) {
           destinationsCounter.textContent = destinationsCount;
       }
       
       const categoriesCount = document.querySelectorAll('input[name="service_categories[]"]:checked').length;
       const categoriesCounter = document.getElementById('final-categories-count');
       if (categoriesCounter) {
           categoriesCounter.textContent = categoriesCount;
       }
   }
   
   document.addEventListener('change', function(e) {
       if (e.target.matches('input[name="destinations[]"], input[name="service_categories[]"]')) {
           updateFinalCounters();
       }
   });
   
   updateFinalCounters();
});
</script>
@endpush