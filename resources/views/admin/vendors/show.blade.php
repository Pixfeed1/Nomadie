@extends('layouts.admin')

@section('title', 'Détails du vendeur')

@section('page-title', 'Détails du vendeur')

@section('content')
<div x-data="vendorDetails()" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
        <div class="flex items-center">
            <a href="{{ route('admin.vendors') }}" class="mr-3 text-primary hover:text-primary-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-text-primary">Explore World</h2>
                <p class="text-sm text-text-secondary mt-1">Vendeur inscrit depuis le 15 mars 2023</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button class="px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Contacter
            </button>
            <button x-show="status === 'active'" @click="confirmSuspend()" class="px-4 py-2 bg-error/10 text-error hover:bg-error/20 font-medium rounded-lg transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Suspendre
            </button>
            <button x-show="status === 'suspended'" @click="confirmActivate()" class="px-4 py-2 bg-success/10 text-success hover:bg-success/20 font-medium rounded-lg transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activer
            </button>
        </div>
    </div>
    
    <!-- Informations générales et statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations du profil -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Informations du profil</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center">
                    <div class="h-24 w-24 rounded-full bg-primary/10 flex items-center justify-center text-primary text-2xl font-bold">
                        EW
                    </div>
                    <h4 class="mt-4 text-lg font-medium text-text-primary">Explore World</h4>
                    <span x-show="status === 'active'" class="mt-1 px-3 py-1 rounded-full text-xs font-medium bg-success/15 text-success">
                        Actif
                    </span>
                    <span x-show="status === 'suspended'" class="mt-1 px-3 py-1 rounded-full text-xs font-medium bg-error/15 text-error">
                        Suspendu
                    </span>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-sm text-text-secondary">Email</p>
                        <p class="text-sm font-medium text-text-primary">contact@exploreworld.com</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Téléphone</p>
                        <p class="text-sm font-medium text-text-primary">+33 6 12 34 56 78</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Adresse</p>
                        <p class="text-sm font-medium text-text-primary">15 rue du Voyage, 75001 Paris</p>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Site web</p>
                        <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">www.exploreworld.com</a>
                    </div>
                    <div>
                        <p class="text-sm text-text-secondary">Abonnement</p>
                        <p class="text-sm font-medium text-text-primary">Pro (99€/mois)</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border">
                <button class="w-full px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifier le profil
                </button>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Cartes statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-6 card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-text-secondary">Ventes totales</p>
                            <p class="text-2xl font-bold text-text-primary mt-1">12 850 €</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +15% ce mois
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6 card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-text-secondary">Commissions</p>
                            <p class="text-2xl font-bold text-text-primary mt-1">642 €</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-accent/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +15% ce mois
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6 card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-text-secondary">Voyages</p>
                            <p class="text-2xl font-bold text-text-primary mt-1">86</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +8 nouveaux
                    </p>
                </div>
            </div>
            
            <!-- Graphique ventes -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                <div class="p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-text-primary">Ventes mensuelles</h3>
                        <div class="flex space-x-2">
                            <select class="border border-border rounded-md text-sm py-1 px-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option>Cette année</option>
                                <option>Année précédente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="vendorSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Destinations et activité récente -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Destinations -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-text-primary">Destinations</h3>
                    <button class="p-2 text-primary hover:text-primary-dark rounded-full hover:bg-primary/5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="p-3 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold">
                            FR
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">France</p>
                            <p class="text-xs text-text-secondary">28 voyages</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-text-primary">4 280 €</p>
                        <p class="text-xs text-text-secondary">33% des ventes</p>
                    </div>
                </div>
                
                <div class="p-3 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-accent/5 flex items-center justify-center text-accent font-bold">
                            JP
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">Japon</p>
                            <p class="text-xs text-text-secondary">22 voyages</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-text-primary">3 850 €</p>
                        <p class="text-xs text-text-secondary">30% des ventes</p>
                    </div>
                </div>
                
                <div class="p-3 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-success/5 flex items-center justify-center text-success font-bold">
                            IT
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">Italie</p>
                            <p class="text-xs text-text-secondary">18 voyages</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-text-primary">2 480 €</p>
                        <p class="text-xs text-text-secondary">19% des ventes</p>
                    </div>
                </div>
                
                <div class="p-3 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-error/5 flex items-center justify-center text-error font-bold">
                            TH
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">Thaïlande</p>
                            <p class="text-xs text-text-secondary">12 voyages</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-text-primary">1 520 €</p>
                        <p class="text-xs text-text-secondary">12% des ventes</p>
                    </div>
                </div>
                
                <div class="p-3 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary-dark/5 flex items-center justify-center text-primary-dark font-bold">
                            ES
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">Espagne</p>
                            <p class="text-xs text-text-secondary">6 voyages</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-text-primary">720 €</p>
                        <p class="text-xs text-text-secondary">6% des ventes</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activité récente -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Activité récente</h3>
            </div>
            <div class="divide-y divide-border">
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Nouveau voyage ajouté</p>
                        <p class="text-xs text-text-secondary mt-1">Week-end découverte à Rome - Prix: 580€</p>
                        <p class="text-xs text-primary mt-1">Il y a 2 heures</p>
                    </div>
                </div>
                
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Nouvelle vente</p>
                        <p class="text-xs text-text-secondary mt-1">Semaine découverte au Japon - 1 450€</p>
                        <p class="text-xs text-primary mt-1">Il y a 5 heures</p>
                    </div>
                </div>
                
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Changement d'abonnement</p>
                        <p class="text-xs text-text-secondary mt-1">Passage à l'abonnement Pro</p>
                        <p class="text-xs text-primary mt-1">Il y a 2 jours</p>
                    </div>
                </div>
                
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-error/10 flex items-center justify-center text-error">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Ajout de destination</p>
                        <p class="text-xs text-text-secondary mt-1">Nouvelle destination ajoutée: Espagne</p>
                        <p class="text-xs text-primary mt-1">Il y a 4 jours</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border">
                <a href="#" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center">
                    Voir toute l'activité
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Liste des voyages de ce vendeur -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-text-primary">Voyages proposés</h3>
                <button class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter un voyage
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-bg-alt">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Titre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Destination</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Durée</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Prix</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    <tr class="hover:bg-bg-alt/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-text-primary">Week-end découverte à Paris</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-text-primary">France</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-text-primary">3 jours</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-text-primary">450 €</div>
                        </td>
<td class="px-6 py-4 whitespace-nowrap">
   <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success/15 text-success">
       Actif
   </span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
   <button class="text-primary hover:text-primary-dark bg-primary/5 hover:bg-primary/10 px-3 py-1 rounded-md transition-colors flex items-center inline-flex">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
       </svg>
       Voir
   </button>
   <button class="text-error hover:text-error-dark bg-error/5 hover:bg-error/10 px-3 py-1 rounded-md transition-colors flex items-center inline-flex">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
       </svg>
       Supprimer
   </button>
</td></tr>
                    
                    <tr class="hover:bg-bg-alt/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-text-primary">Semaine découverte au Japon</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-text-primary">Japon</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-text-primary">7 jours</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-text-primary">1 450 €</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success/15 text-success">
                                Actif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button class="text-primary hover:text-primary-dark bg-primary/5 hover:bg-primary/10 px-3 py-1 rounded-md transition-colors flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Voir
                            </button>
                            <button class="text-error hover:text-error-dark bg-error/5 hover:bg-error/10 px-3 py-1 rounded-md transition-colors flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-bg-alt border-t border-border">
            <a href="#" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center">
                Voir tous les voyages
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Modal de confirmation -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIcon.bgColor">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="modalIcon.color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="modalIcon.path" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-text-primary" x-text="modalTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-text-secondary" x-text="modalContent"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" :class="modalAction.bgColor" @click="confirmAction">
                        <span x-text="modalAction.label"></span>
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-text-primary hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showModal = false">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function vendorDetails() {
        return {
            status: 'active',
            showModal: false,
            modalTitle: '',
            modalContent: '',
            modalIcon: {
                bgColor: '',
                color: '',
                path: ''
            },
            modalAction: {
                bgColor: '',
                label: '',
                callback: null
            },
            
            init() {
                this.initSalesChart();
            },
            
            confirmSuspend() {
                this.modalTitle = 'Suspendre le vendeur';
                this.modalContent = 'Êtes-vous sûr de vouloir suspendre ce vendeur ? Cela l\'empêchera d\'accéder à son compte et de vendre des voyages.';
                
                this.modalIcon = {
                    bgColor: 'bg-error/10',
                    color: 'text-error',
                    path: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'
                };
                
                this.modalAction = {
                    bgColor: 'bg-error hover:bg-error/90',
                    label: 'Suspendre',
                    callback: () => this.suspendVendor()
                };
                
                this.showModal = true;
            },
            
            confirmActivate() {
                this.modalTitle = 'Activer le vendeur';
                this.modalContent = 'Êtes-vous sûr de vouloir réactiver ce vendeur ? Cela lui redonnera l\'accès à son compte et la possibilité de vendre des voyages.';
                
                this.modalIcon = {
                    bgColor: 'bg-success/10',
                    color: 'text-success',
                    path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                };
                
                this.modalAction = {
                    bgColor: 'bg-success hover:bg-success/90',
                    label: 'Activer',
                    callback: () => this.activateVendor()
                };
                
                this.showModal = true;
            },
            
            confirmAction() {
                if (this.modalAction.callback) {
                    this.modalAction.callback();
                }
                this.showModal = false;
            },
            
            suspendVendor() {
                this.status = 'suspended';
                this.showSuccessToast('Vendeur suspendu avec succès.');
            },
            
            activateVendor() {
                this.status = 'active';
                this.showSuccessToast('Vendeur activé avec succès.');
            },
            
            showSuccessToast(message) {
                // Dans une version finale, on implémenterait un vrai système de notification
                console.log('SUCCESS:', message);
            },
            
            initSalesChart() {
                const ctx = document.getElementById('vendorSalesChart').getContext('2d');
                
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(56, 178, 172, 0.3)');
                gradient.addColorStop(1, 'rgba(56, 178, 172, 0.0)');
                
                const salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                        datasets: [{
                            label: 'Ventes mensuelles (€)',
                            data: [780, 920, 1100, 840, 920, 1250, 1450, 1320, 950, 1100, 1280, 1420],
                            borderColor: '#38B2AC',
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#38B2AC',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    color: '#E2E8F0'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + ' €';
                                    },
                                    font: {
                                        size: 11
                                    },
                                    color: '#718096'
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#718096'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#2D3748',
                                titleFont: {
                                    size: 13
                                },
                                bodyFont: {
                                    size: 12
                                },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' €';
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        vendorDetails().init();
    });
</script>
@endpush