@extends('layouts.admin')

@section('title', 'Gestion des Abonnements')

@section('page-title', 'Gestion des Abonnements')

@section('content')
<div x-data="subscriptionsManager()" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
        <div>
            <h2 class="text-xl font-bold text-text-primary">Abonnements et Commissions</h2>
            <p class="text-sm text-text-secondary mt-1">Gérez les formules d'abonnement et taux de commission pour les vendeurs</p>
        </div>
        
        <div>
            <button @click="openHistoryModal()" class="flex items-center justify-center px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Historique des modifications
            </button>
        </div>
    </div>
    
    <!-- Abonnements -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Abonnement Gratuit -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-text-primary">Gratuit</h3>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-primary/15 text-primary">Standard</span>
                    </div>
                </div>
                <p class="text-4xl font-bold mt-4 text-text-primary">0 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                <div class="mt-4 flex items-center">
                    <div class="flex items-center space-x-1 text-text-secondary">
                        <span class="text-sm">Commission</span>
                        <div x-data="{ tooltip: false }" class="relative">
                            <svg @mouseenter="tooltip = true" @mouseleave="tooltip = false" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div x-show="tooltip" class="absolute bottom-full left-1/2 transform -translate-x-1/2 px-3 py-2 text-xs bg-text-primary text-white rounded shadow-lg mb-2 whitespace-nowrap z-10" x-cloak>
                                Commission prélevée sur chaque vente
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <span x-show="!editingFree" class="text-lg font-bold text-error">20%</span>
                        <div x-show="editingFree" class="flex items-center">
                            <input type="number" min="0" max="100" x-model="freeCommission" class="w-16 px-2 py-1 border border-border rounded-md text-center focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <span class="ml-1 text-lg font-bold">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Accès à la plateforme</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">5 destinations max</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Support par email</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-sm text-text-secondary">Profil personnalisé</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-sm text-text-secondary">Mise en avant</span>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border flex justify-between items-center">
                <div>
                    <span class="text-xs text-text-secondary">42 vendeurs actifs</span>
                </div>
                <button @click="toggleEditFree()" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn" x-text="editingFree ? 'Enregistrer' : 'Modifier'"></button>
            </div>
        </div>
        
        <!-- Abonnement Essentiel -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-text-primary">Essentiel</h3>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-accent/15 text-accent-dark">Populaire</span>
                    </div>
                </div>
                <div x-show="!editingEssential">
                    <p class="text-4xl font-bold mt-4 text-text-primary">49 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                </div>
                <div x-show="editingEssential">
                    <div class="mt-4 flex items-center">
                        <input type="number" min="0" x-model="essentialPrice" class="w-20 px-2 py-1 border border-border rounded-md text-center focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-xl font-bold">
                        <span class="ml-1 text-sm text-text-secondary">/mois</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <div class="flex items-center space-x-1 text-text-secondary">
                        <span class="text-sm">Commission</span>
                        <div x-data="{ tooltip: false }" class="relative">
                            <svg @mouseenter="tooltip = true" @mouseleave="tooltip = false" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div x-show="tooltip" class="absolute bottom-full left-1/2 transform -translate-x-1/2 px-3 py-2 text-xs bg-text-primary text-white rounded shadow-lg mb-2 whitespace-nowrap z-10" x-cloak>
                                Commission prélevée sur chaque vente
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <span x-show="!editingEssential" class="text-lg font-bold text-accent">10%</span>
                        <div x-show="editingEssential" class="flex items-center">
                            <input type="number" min="0" max="100" x-model="essentialCommission" class="w-16 px-2 py-1 border border-border rounded-md text-center focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <span class="ml-1 text-lg font-bold">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Accès à la plateforme</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">20 destinations max</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Support prioritaire</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Profil personnalisé</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-sm text-text-secondary">Mise en avant</span>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border flex justify-between items-center">
                <div>
                    <span class="text-xs text-text-secondary">18 vendeurs actifs</span>
                </div>
                <button @click="toggleEditEssential()" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn" x-text="editingEssential ? 'Enregistrer' : 'Modifier'"></button>
            </div>
        </div>
        
        <!-- Abonnement Pro -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-text-primary">Pro</h3>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-success/15 text-success">Prémium</span>
                    </div>
                </div>
                <div x-show="!editingPro">
                    <p class="text-4xl font-bold mt-4 text-text-primary">99 €<span class="text-sm font-normal text-text-secondary">/mois</span></p>
                </div>
                <div x-show="editingPro">
                    <div class="mt-4 flex items-center">
                        <input type="number" min="0" x-model="proPrice" class="w-20 px-2 py-1 border border-border rounded-md text-center focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-xl font-bold">
                        <span class="ml-1 text-sm text-text-secondary">/mois</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <div class="flex items-center space-x-1 text-text-secondary">
                        <span class="text-sm">Commission</span>
                        <div x-data="{ tooltip: false }" class="relative">
                            <svg @mouseenter="tooltip = true" @mouseleave="tooltip = false" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div x-show="tooltip" class="absolute bottom-full left-1/2 transform -translate-x-1/2 px-3 py-2 text-xs bg-text-primary text-white rounded shadow-lg mb-2 whitespace-nowrap z-10" x-cloak>
                                Commission prélevée sur chaque vente
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <span x-show="!editingPro" class="text-lg font-bold text-success">5%</span>
                        <div x-show="editingPro" class="flex items-center">
                            <input type="number" min="0" max="100" x-model="proCommission" class="w-16 px-2 py-1 border border-border rounded-md text-center focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <span class="ml-1 text-lg font-bold">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Accès à la plateforme</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Destinations illimitées</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Support 24/7</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Profil personnalisé</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-text-primary">Mise en avant</span>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border flex justify-between items-center">
                <div>
                    <span class="text-xs text-text-secondary">6 vendeurs actifs</span>
                </div>
                <button @click="toggleEditPro()" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn" x-text="editingPro ? 'Enregistrer' : 'Modifier'"></button>
            </div>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribution -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Distribution des abonnements</h3>
                <p class="text-sm text-text-secondary mt-1">Répartition des vendeurs par formule d'abonnement</p>
            </div>
            <div class="p-6">
                <div class="h-80">
                    <canvas id="subscriptionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Revenus par abonnement -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Revenus par type d'abonnement</h3>
                <p class="text-sm text-text-secondary mt-1">Abonnements + commissions</p>
            </div>
            <div class="p-6 space-y-6">
                <!-- Abonnement Gratuit -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-text-primary">Gratuit</span>
                        <span class="text-sm font-medium text-text-primary">320 € / mois</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div class="bg-error h-2.5 rounded-full" style="width: 24%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-text-secondary">42 vendeurs</span>
                        <span class="text-xs text-text-secondary">Commissions: 320 €</span>
                    </div>
                </div>
                
                <!-- Abonnement Essentiel -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-text-primary">Essentiel</span>
                        <span class="text-sm font-medium text-text-primary">1 132 € / mois</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div class="bg-accent h-2.5 rounded-full" style="width: 62%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-text-secondary">18 vendeurs</span>
                        <span class="text-xs text-text-secondary">Abonnements: 882 € | Commissions: 250 €</span>
                    </div>
                </div>
                
                <!-- Abonnement Pro -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-text-primary">Pro</span>
                        <span class="text-sm font-medium text-text-primary">774 € / mois</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div class="bg-success h-2.5 rounded-full" style="width: 42%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-text-secondary">6 vendeurs</span>
                        <span class="text-xs text-text-secondary">Abonnements: 594 € | Commissions: 180 €</span>
                    </div>
                </div>
                
                <!-- Total -->
                <div class="pt-4 border-t border-border">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-text-primary">Total</span>
                        <span class="text-sm font-bold text-text-primary">2 226 € / mois</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full" style="width: 100%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-text-secondary">66 vendeurs</span>
                        <span class="text-xs text-text-secondary">Abonnements: 1 476 € | Commissions: 750 €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal historique des modifications -->
    <div x-show="showHistoryModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showHistoryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showHistoryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-text-primary">Historique des modifications</h3>
                            <div class="mt-4 space-y-4">
                                <div class="border-l-2 border-primary pl-4 py-2">
                                    <p class="text-sm font-medium text-text-primary">Modification du tarif "Pro"</p>
                                    <p class="text-xs text-text-secondary mt-1">Commission modifiée: 8% → 5%</p>
                                    <p class="text-xs text-primary mt-1">Il y a 2 jours par Admin</p>
                                </div>
                                <div class="border-l-2 border-primary pl-4 py-2">
                                    <p class="text-sm font-medium text-text-primary">Modification du tarif "Essentiel"</p>
                                    <p class="text-xs text-text-secondary mt-1">Prix modifié: 39€ → 49€</p>
                                    <p class="text-xs text-primary mt-1">Il y a 15 jours par Admin</p>
                                </div>
<div class="border-l-2 border-primary pl-4 py-2">
                                    <p class="text-sm font-medium text-text-primary">Modification du tarif "Pro"</p>
                                    <p class="text-xs text-text-secondary mt-1">Prix modifié: 89€ → 99€</p>
                                    <p class="text-xs text-primary mt-1">Il y a 15 jours par Admin</p>
                                </div>
                                <div class="border-l-2 border-primary pl-4 py-2">
                                    <p class="text-sm font-medium text-text-primary">Création des formules d'abonnement</p>
                                    <p class="text-xs text-text-secondary mt-1">3 formules créées: Gratuit, Essentiel, Pro</p>
                                    <p class="text-xs text-primary mt-1">Il y a 45 jours par Admin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" @click="showHistoryModal = false">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function subscriptionsManager() {
        return {
            showHistoryModal: false,
            editingFree: false,
            editingEssential: false,
            editingPro: false,
            freeCommission: 20,
            essentialPrice: 49,
            essentialCommission: 10,
            proPrice: 99,
            proCommission: 5,
            
            init() {
                this.initSubscriptionChart();
            },
            
            toggleEditFree() {
                if (this.editingFree) {
                    // Ici, on enregistrerait normalement les modifications
                    // avec une requête AJAX vers le serveur
                    this.showSuccessToast('Commission modifiée avec succès!');
                }
                this.editingFree = !this.editingFree;
            },
            
            toggleEditEssential() {
                if (this.editingEssential) {
                    // Ici, on enregistrerait normalement les modifications
                    // avec une requête AJAX vers le serveur
                    this.showSuccessToast('Abonnement Essentiel modifié avec succès!');
                }
                this.editingEssential = !this.editingEssential;
            },
            
            toggleEditPro() {
                if (this.editingPro) {
                    // Ici, on enregistrerait normalement les modifications
                    // avec une requête AJAX vers le serveur
                    this.showSuccessToast('Abonnement Pro modifié avec succès!');
                }
                this.editingPro = !this.editingPro;
            },
            
            openHistoryModal() {
                this.showHistoryModal = true;
            },
            
            showSuccessToast(message) {
                // Dans une version finale, on implémenterait un vrai système de notification
                console.log('SUCCESS:', message);
            },
            
            initSubscriptionChart() {
                const ctx = document.getElementById('subscriptionChart').getContext('2d');
                
                const subscriptionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Gratuit', 'Essentiel', 'Pro'],
                        datasets: [{
                            data: [42, 18, 6],
                            backgroundColor: [
                                '#FC8181', // error color
                                '#F6AD55', // accent color
                                '#68D391'  // success color
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    },
                                    color: '#718096'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#2D3748',
                                titleFont: {
                                    size: 13
                                },
                                bodyFont: {
                                    size: 12
                                },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} vendeurs (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        subscriptionsManager().init();
    });
</script>
@endpush