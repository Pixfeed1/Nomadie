@extends('layouts.admin')

@section('title', 'Gestion des Commandes')

@section('page-title', 'Gestion des Commandes')

@section('content')
<div x-data="ordersManager" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
        <div>
            <h2 class="text-xl font-bold text-text-primary">Liste des commandes</h2>
            <p class="text-sm text-text-secondary mt-1">Gérez toutes les réservations de votre marketplace</p>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <div class="relative">
                <input type="text" x-model="searchTerm" placeholder="Rechercher une commande..." class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-text-primary transition-all pl-9">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <div>
                <select x-model="statusFilter" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-text-primary appearance-none bg-white">
                    <option value="all">Tous les statuts</option>
                    <option value="paid">Payée</option>
                    <option value="pending">En attente</option>
                    <option value="cancelled">Annulée</option>
                    <option value="refunded">Remboursée</option>
                </select>
            </div>
            
            <div>
                <select x-model="vendorFilter" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-text-primary appearance-none bg-white">
                    <option value="all">Tous les vendeurs</option>
                    <option value="Explore World">Explore World</option>
                    <option value="Voyage by Sarah">Voyage by Sarah</option>
                    <option value="Urban Adventures">Urban Adventures</option>
                    <option value="Nature Escape Tours">Nature Escape Tours</option>
                    <option value="Travel&Fun Agency">Travel&Fun Agency</option>
                </select>
            </div>
            
            <button @click="exportOrders()" class="flex items-center justify-center px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exporter
            </button>
        </div>
    </div>
    
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Total commandes</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">152</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-success font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +8.5% ce mois
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Chiffre d'affaires</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">32 450 €</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-success font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +12.3% ce mois
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Commissions</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">2 890 €</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-success font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +9.2% ce mois
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Panier moyen</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">785 €</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary-dark/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-success font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +5.8% ce mois
                </span>
            </div>
        </div>
    </div>
    
    <!-- Tableau des commandes - CORRECTION MINIMALE AJOUTÉE ICI -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto max-w-full">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-bg-alt">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('id')">
                            <div class="flex items-center">
                                ID 
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'id'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('client')">
                            <div class="flex items-center">
                                Client
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'client'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('vendor')">
                            <div class="flex items-center">
                                Organisateur
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'vendor'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('destination')">
                            <div class="flex items-center">
                                Destination
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'destination'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('date')">
                            <div class="flex items-center">
                                Date
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'date'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('amount')">
                            <div class="flex items-center">
                                Montant
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'amount'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer" @click="sortBy('status')">
                            <div class="flex items-center">
                                Statut
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" :class="{'text-primary': sortColumn === 'status'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    <template x-for="(order, index) in sortedOrders" :key="index">
                        <tr class="hover:bg-bg-alt/30 transition-colors" :class="{'bg-primary/5': order.highlight}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-text-primary" x-text="'#' + order.id"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        <span x-text="getInitials(order.client)"></span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-text-primary" x-text="order.client"></div>
                                        <div class="text-xs text-text-secondary" x-text="order.email"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary" x-text="order.vendor"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary" x-text="order.destination"></div>
                                <div class="text-xs text-text-secondary" x-text="order.product"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary" x-text="formatDate(order.date)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-text-primary" x-text="formatCurrency(order.amount)"></div>
                                <div class="text-xs text-text-secondary" x-text="'Commission: ' + formatCurrency(order.commission)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusClasses(order.status)">
                                    <span x-text="getStatusLabel(order.status)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a :href="'/admin/orders/' + order.id" class="text-primary hover:text-primary-dark bg-primary/5 hover:bg-primary/10 px-3 py-1 rounded-md transition-colors inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Détails
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border-t border-border">
            <div class="flex items-center justify-between">
                <div class="text-sm text-text-secondary">
                    Affichage de <span class="font-medium" x-text="paginatedOrders.length"></span> commandes sur <span class="font-medium" x-text="filteredOrders.length"></span>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="prevPage" :disabled="currentPage === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-border bg-white text-sm font-medium text-text-secondary hover:bg-bg-alt disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="sr-only">Précédent</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <template x-for="page in totalPages" :key="page">
                            <button @click="goToPage(page)" class="relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium" :class="page === currentPage ? 'bg-primary text-white z-10' : 'bg-white text-text-primary hover:bg-bg-alt'">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        <button @click="nextPage" :disabled="currentPage === totalPages" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-border bg-white text-sm font-medium text-text-secondary hover:bg-bg-alt disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="sr-only">Suivant</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal d'export -->
    <div x-show="showExportModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showExportModal = false"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-text-primary" id="modal-title">
                                Exporter les commandes
                            </h3>
                            <div class="mt-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-1">Format</label>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center">
                                            <input id="format-csv" name="format" type="radio" checked class="focus:ring-primary h-4 w-4 text-primary border-border">
                                            <label for="format-csv" class="ml-3 block text-sm font-medium text-text-primary">
                                                CSV
                                            </label>
                                        </div>
                                        <div class="flex items-center">
<input id="format-excel" name="format" type="radio" class="focus:ring-primary h-4 w-4 text-primary border-border">
                                            <label for="format-excel" class="ml-3 block text-sm font-medium text-text-primary">
                                                Excel
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="format-pdf" name="format" type="radio" class="focus:ring-primary h-4 w-4 text-primary border-border">
                                            <label for="format-pdf" class="ml-3 block text-sm font-medium text-text-primary">
                                                PDF
                                            </label>
                                        </div>
                                    </div>
                                </div>
                               
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-1">Période</label>
                                    <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-border focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                        <option selected>Toutes les dates</option>
                                        <option>Ce mois-ci</option>
                                        <option>Les 3 derniers mois</option>
                                        <option>Cette année</option>
                                        <option>Période personnalisée</option>
                                    </select>
                                </div>
                               
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-1">Inclure</label>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center">
                                            <input id="include-client" type="checkbox" checked class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                            <label for="include-client" class="ml-3 block text-sm font-medium text-text-primary">
                                                Informations client
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="include-payment" type="checkbox" checked class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                            <label for="include-payment" class="ml-3 block text-sm font-medium text-text-primary">
                                                Détails du paiement
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="include-commission" type="checkbox" checked class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                            <label for="include-commission" class="ml-3 block text-sm font-medium text-text-primary">
                                                Commissions
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Exporter
                    </button>
                    <button type="button" @click="showExportModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-text-primary hover:bg-bg-alt focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
   function ordersManager() {
       return {
           orders: [
               { id: 38295, client: 'Sophie Martin', email: 'sophie.martin@example.com', vendor: 'Urban Adventures', destination: 'Italie', product: 'Week-end à Rome', date: '2025-04-25', amount: 580, commission: 58, status: 'paid', highlight: true },
               { id: 38294, client: 'Thomas Dubois', email: 'thomas.dubois@example.com', vendor: 'Explore World', destination: 'France', product: 'Paris Romantique', date: '2025-04-24', amount: 450, commission: 45, status: 'paid' },
               { id: 38293, client: 'Émilie Bernard', email: 'emilie.bernard@example.com', vendor: 'Travel&Fun Agency', destination: 'Espagne', product: 'Barcelone City Break', date: '2025-04-23', amount: 390, commission: 39, status: 'paid' },
               { id: 38292, client: 'Pierre Moreau', email: 'pierre.moreau@example.com', vendor: 'Explore World', destination: 'Japon', product: 'Tokyo Discovery', date: '2025-04-22', amount: 1250, commission: 125, status: 'pending' },
               { id: 38291, client: 'Camille Leroy', email: 'camille.leroy@example.com', vendor: 'Nature Escape Tours', destination: 'France', product: 'Randonnée Alpes', date: '2025-04-21', amount: 680, commission: 68, status: 'paid' },
               { id: 38290, client: 'Antoine Girard', email: 'antoine.girard@example.com', vendor: 'Urban Adventures', destination: 'Vietnam', product: 'Hanoi Authentique', date: '2025-04-20', amount: 890, commission: 89, status: 'cancelled' },
               { id: 38289, client: 'Julie Petit', email: 'julie.petit@example.com', vendor: 'Explore World', destination: 'Thaïlande', product: 'Bangkok & Plages', date: '2025-04-19', amount: 1450, commission: 145, status: 'paid' },
               { id: 38288, client: 'Nicolas Fournier', email: 'nicolas.fournier@example.com', vendor: 'Travel&Fun Agency', destination: 'Espagne', product: 'Madrid Culturel', date: '2025-04-18', amount: 560, commission: 56, status: 'refunded' },
               { id: 38287, client: 'Marie Robert', email: 'marie.robert@example.com', vendor: 'Nature Escape Tours', destination: 'Italie', product: 'Toscane Gourmande', date: '2025-04-17', amount: 1120, commission: 112, status: 'paid' },
               { id: 38286, client: 'Alexandre Dupont', email: 'alexandre.dupont@example.com', vendor: 'Urban Adventures', destination: 'France', product: 'Lyon Gastronomique', date: '2025-04-16', amount: 420, commission: 42, status: 'paid' },
               { id: 38285, client: 'Chloé Simon', email: 'chloe.simon@example.com', vendor: 'Explore World', destination: 'Japon', product: 'Kyoto Traditionnel', date: '2025-04-15', amount: 1680, commission: 168, status: 'pending' },
               { id: 38284, client: 'Lucas Blanc', email: 'lucas.blanc@example.com', vendor: 'Travel&Fun Agency', destination: 'Thaïlande', product: 'Chiang Mai Adventure', date: '2025-04-14', amount: 980, commission: 98, status: 'paid' },
               { id: 38283, client: 'Emma Laurent', email: 'emma.laurent@example.com', vendor: 'Nature Escape Tours', destination: 'Vietnam', product: 'Trek Sapa', date: '2025-04-13', amount: 750, commission: 75, status: 'paid' }
           ],
           searchTerm: '',
           statusFilter: 'all',
           vendorFilter: 'all',
           currentPage: 1,
           itemsPerPage: 10,
           sortColumn: 'id',
           sortDirection: 'desc',
           showExportModal: false,
           
           get filteredOrders() {
               return this.orders.filter(order => {
                   const matchesSearch = 
                       order.id.toString().includes(this.searchTerm) ||
                       order.client.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                       order.email.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                       order.vendor.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                       order.destination.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                       order.product.toLowerCase().includes(this.searchTerm.toLowerCase());
                   
                   const matchesStatus = this.statusFilter === 'all' || order.status === this.statusFilter;
                   const matchesVendor = this.vendorFilter === 'all' || order.vendor === this.vendorFilter;
                   
                   return matchesSearch && matchesStatus && matchesVendor;
               });
           },
           
           get sortedOrders() {
               const sorted = [...this.filteredOrders].sort((a, b) => {
                   let aValue = a[this.sortColumn];
                   let bValue = b[this.sortColumn];
                   
                   if (typeof aValue === 'string') {
                       aValue = aValue.toLowerCase();
                       bValue = bValue.toLowerCase();
                   }
                   
                   if (aValue < bValue) return this.sortDirection === 'asc' ? -1 : 1;
                   if (aValue > bValue) return this.sortDirection === 'asc' ? 1 : -1;
                   return 0;
               });
               
               return sorted;
           },
           
           get paginatedOrders() {
               const start = (this.currentPage - 1) * this.itemsPerPage;
               const end = start + this.itemsPerPage;
               return this.sortedOrders.slice(start, end);
           },
           
           get totalPages() {
               return Math.ceil(this.filteredOrders.length / this.itemsPerPage);
           },
           
           prevPage() {
               if (this.currentPage > 1) {
                   this.currentPage--;
               }
           },
           
           nextPage() {
               if (this.currentPage < this.totalPages) {
                   this.currentPage++;
               }
           },
           
           goToPage(page) {
               this.currentPage = page;
           },
           
           sortBy(column) {
               if (this.sortColumn === column) {
                   this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
               } else {
                   this.sortColumn = column;
                   this.sortDirection = 'asc';
               }
           },
           
           getInitials(name) {
               return name.split(' ').map(n => n[0]).join('').toUpperCase();
           },
           
           getStatusClasses(status) {
               const classes = {
                   'paid': 'bg-success/15 text-success',
                   'pending': 'bg-accent/15 text-accent-dark',
                   'cancelled': 'bg-error/15 text-error',
                   'refunded': 'bg-primary-dark/15 text-primary-dark'
               };
               
               return classes[status] || '';
           },
           
           getStatusLabel(status) {
               const labels = {
                   'paid': 'Payée',
                   'pending': 'En attente',
                   'cancelled': 'Annulée',
                   'refunded': 'Remboursée'
               };
               
               return labels[status] || status;
           },
           
           formatDate(dateString) {
               const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
               const date = new Date(dateString);
               return date.toLocaleDateString('fr-FR', options);
           },
           
           formatCurrency(value) {
               return value.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }).replace(/\s/g, ' ');
           },
           
           exportOrders() {
               this.showExportModal = true;
           }
       }
   }
</script>
@endpush