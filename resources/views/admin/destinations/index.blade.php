@extends('layouts.admin')

@section('title', 'Gestion des Destinations')

@section('page-title', 'Gestion des Destinations')

@section('content')
<div x-data="destinationsManager()" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
        <div>
            <h2 class="text-xl font-bold text-text-primary">Destinations par continent</h2>
            <p class="text-sm text-text-secondary mt-1">Gérez les destinations proposées sur votre plateforme</p>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <button @click="showAddDestinationModal()" class="flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajouter destination
            </button>
            
            <button @click="showImportModal()" class="flex items-center justify-center px-4 py-2 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Importer CSV
            </button>
        </div>
    </div>
    
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Continents</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['total_continents'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Pays</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['total_countries'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Villes</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['total_cities'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-xs font-medium uppercase">Destination populaire</p>
                    <p class="text-xl font-bold text-text-primary mt-1">{{ $stats['most_popular'] ? $stats['most_popular']->country : 'N/A' }}</p>
                    <p class="text-xs text-success">{{ $stats['most_popular'] ? $stats['most_popular']->trip_count . ' voyages' : '' }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-error/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Barre de recherche/filtre -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row gap-3 md:items-center">
            <div class="relative flex-1">
                <input type="text" x-model="searchTerm" placeholder="Rechercher une destination..." class="w-full pl-10 pr-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <label class="text-sm text-text-secondary whitespace-nowrap">Filtrer par continent:</label>
                <select x-model="continentFilter" class="border border-border rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="all">Tous les continents</option>
                    @foreach($continents as $continent)
                    <option value="{{ $continent['name'] }}">{{ $continent['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <!-- Continents et pays -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <template x-for="(continent, index) in filteredContinents" :key="index">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-border flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-text-primary" x-text="continent.name"></h3>
                            <p class="text-sm text-text-secondary" x-text="continent.destinations.length + ' pays disponibles'"></p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button @click="editContinent(continent.name)" class="p-2 text-primary hover:text-primary-dark rounded-full hover:bg-primary/5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <template x-if="filteredDestinationsByContinent(continent.name).length === 0">
                        <div class="py-8 text-center">
                            <p class="text-text-secondary">Aucun pays trouvé pour ce continent</p>
                        </div>
                    </template>
                    
                    <template x-for="(destination, i) in filteredDestinationsByContinent(continent.name)" :key="i">
                        <div class="p-4 border border-border rounded-lg hover:bg-bg-alt/30 transition-colors flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold">
                                    <span x-text="getCountryCode(destination.country)"></span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-text-primary" x-text="destination.country"></p>
                                    <template x-if="destination.city">
                                        <p class="text-xs text-text-secondary" x-text="destination.city"></p>
                                    </template>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-text-secondary" x-text="'Voyages: ' + destination.trip_count"></span>
                                        <span class="mx-2 text-text-secondary">•</span>
                                        <span class="text-xs text-text-secondary" x-text="'Vendeurs: ' + destination.vendor_count"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="viewTrips(destination.id)" class="flex items-center px-2 py-1 text-xs bg-primary/5 hover:bg-primary/10 text-primary rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Voyages
                                </button>
                                <button @click="editDestination(destination)" class="p-1 text-primary hover:text-primary-dark rounded hover:bg-primary/5 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button @click="confirmDeleteDestination(destination)" class="p-1 text-error hover:text-error-dark rounded hover:bg-error/5 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Modal ajout/modification destination -->
    <div x-show="showDestinationModal" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDestinationModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showDestinationModal = false"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showDestinationModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="saveDestination">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-text-primary" x-text="editMode ? 'Modifier une destination' : 'Ajouter une destination'"></h3>
                                <div class="mt-6 space-y-4">
                                    <div>
                                        <label for="continent" class="block text-sm font-medium text-text-primary mb-1">Continent</label>
                                        <div class="mt-1 relative">
                                            <input type="text" x-model="formData.continent" id="continent" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Europe, Asie, Amérique...">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-text-primary mb-1">Pays</label>
                                        <div class="mt-1 relative">
                                            <input type="text" x-model="formData.country" id="country" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="France, Japon, Australie...">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-text-primary mb-1">Ville (optionnel)</label>
                                        <div class="mt-1 relative">
                                            <input type="text" x-model="formData.city" id="city" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Paris, Tokyo, Sydney...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-text="editMode ? 'Enregistrer' : 'Ajouter'"></span>
                        </button>
                        <button type="button" @click="showDestinationModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-text-primary hover:bg-bg-alt focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal d'importation CSV -->
    <div x-show="showImportModal" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showImportModal = false"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-text-primary">Importer des destinations depuis un CSV</h3>
                            <div class="mt-2">
                                <p class="text-sm text-text-secondary">
                                    Téléchargez un fichier CSV contenant vos destinations. Le fichier doit inclure les colonnes "continent", "country" et éventuellement "city".
                                </p>
                            </div>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-text-primary">Fichier CSV</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-border border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-text-secondary" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-text-secondary">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                                <span>Téléverser un fichier</span>
                                                <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".csv" @change="handleFileUpload">
                                            </label>
                                            <p class="pl-1">ou glisser-déposer</p>
                                        </div>
                                        <p class="text-xs text-text-secondary">
                                            CSV jusqu'à 10MB
                                        </p>
                                    </div>
                                </div>
                                <p x-show="csvFile" class="mt-2 text-sm text-success" x-text="'Fichier sélectionné: ' + csvFile"></p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-text-primary mb-1">Options d'importation</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center">
                                        <input id="skip-header" type="checkbox" x-model="importOptions.skipHeader" class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                        <label for="skip-header" class="ml-2 block text-sm text-text-primary">
                                            Ignorer la première ligne (en-têtes)
                                        </label>
                                    </div>
<div class="flex items-center">
                                        <input id="update-existing" type="checkbox" x-model="importOptions.updateExisting" class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                        <label for="update-existing" class="ml-2 block text-sm text-text-primary">
                                            Mettre à jour les destinations existantes
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="delete-missing" type="checkbox" x-model="importOptions.deleteMissing" class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                        <label for="delete-missing" class="ml-2 block text-sm text-text-primary">
                                            Supprimer les destinations non présentes dans le fichier
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <details>
                                    <summary class="text-sm font-medium text-primary cursor-pointer">Format attendu du CSV</summary>
                                    <div class="mt-2 text-sm text-text-secondary">
                                        <p>Le fichier CSV doit contenir les colonnes suivantes :</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li><strong>continent</strong> (obligatoire) : nom du continent</li>
                                            <li><strong>country</strong> (obligatoire) : nom du pays</li>
                                            <li><strong>city</strong> (optionnel) : nom de la ville</li>
                                        </ul>
                                        <p class="mt-2">Exemple :</p>
                                        <pre class="mt-1 p-2 bg-bg-alt rounded-md text-xs">continent,country,city
Europe,France,Paris
Europe,Italie,Rome
Asie,Japon,Tokyo</pre>
                                    </div>
                                </details>
                            </div>
                            
                            <div x-show="importPreview.length > 0" class="mt-6">
                                <label class="block text-sm font-medium text-text-primary mb-2">Aperçu des données</label>
                                <div class="max-h-40 overflow-y-auto border border-border rounded-md">
                                    <table class="min-w-full divide-y divide-border">
                                        <thead class="bg-bg-alt">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Continent</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Pays</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Ville</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-border">
                                            <template x-for="(item, index) in importPreview.slice(0, 5)" :key="index">
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-text-primary" x-text="item.continent"></td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-text-primary" x-text="item.country"></td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-text-primary" x-text="item.city || '—'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div x-show="importPreview.length > 5" class="mt-1 text-xs text-text-secondary text-right">
                                    <span x-text="importPreview.length - 5"></span> destinations supplémentaires non affichées
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="importDestinations" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" :disabled="!csvFile">
                        Importer
                    </button>
                    <button type="button" @click="showImportModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-text-primary hover:bg-bg-alt focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal visualisation des voyages -->
    <div x-show="showTripsModal" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTripsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showTripsModal = false"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showTripsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-text-primary flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span x-text="'Voyages pour ' + selectedDestination.country + (selectedDestination.city ? ' - ' + selectedDestination.city : '')"></span>
                            </h3>
                            
                            <div class="mt-6">
                                <div class="bg-white overflow-hidden border border-border rounded-lg">
                                    <div class="flex justify-between items-center p-4 border-b border-border">
                                        <div class="text-sm text-text-secondary">
                                            <span x-text="destinationTrips.length"></span> voyage(s) trouvé(s)
                                        </div>
                                        <a :href="'/admin/destinations/' + selectedDestination.id + '/trips'" class="text-sm text-primary hover:text-primary-dark flex items-center">
                                            Voir tous les voyages
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </div>
                                    
                                    <div x-show="destinationTrips.length === 0" class="p-4 text-center text-text-secondary">
                                        Aucun voyage trouvé pour cette destination
                                    </div>
                                    
                                    <div x-show="destinationTrips.length > 0" class="divide-y divide-border">
                                        <template x-for="(trip, index) in destinationTrips" :key="index">
                                            <div class="p-4 hover:bg-bg-alt/30 transition-colors">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <h4 class="text-sm font-medium text-text-primary" x-text="trip.title"></h4>
                                                        <p class="text-xs text-text-secondary mt-1" x-text="trip.vendor_name"></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm font-medium text-text-primary" x-text="formatPrice(trip.price)"></p>
                                                        <p class="text-xs text-success mt-1" x-text="'Commission: ' + formatPrice(trip.commission)"></p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex items-center text-xs text-text-secondary space-x-4">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span x-text="formatDuration(trip.duration)"></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        <span x-text="'Max: ' + trip.max_travelers + ' voyageurs'"></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span x-text="trip.featured ? 'Mis en avant' : 'Standard'"></span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex justify-end">
                                                    <a :href="'/admin/trips/' + trip.id" class="text-xs text-primary hover:text-primary-dark flex items-center">
                                                        Voir le voyage
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showTripsModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-text-primary hover:bg-bg-alt focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:w-auto sm:text-sm">
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
    function destinationsManager() {
        return {
            continents: @json($continents),
            searchTerm: '',
            continentFilter: 'all',
            showDestinationModal: false,
            showImportModal: false,
            showTripsModal: false,
            editMode: false,
            csvFile: null,
            importPreview: [],
            importOptions: {
                skipHeader: true,
                updateExisting: true,
                deleteMissing: false
            },
            destinationTrips: [],
            selectedDestination: {},
            formData: {
                id: null,
                continent: '',
                country: '',
                city: ''
            },
            
            get filteredContinents() {
                if (this.continentFilter === 'all' && !this.searchTerm) {
                    return this.continents;
                }
                
                return this.continents.filter(continent => {
                    const matchesContinent = this.continentFilter === 'all' || continent.name === this.continentFilter;
                    
                    if (!matchesContinent) return false;
                    
                    if (!this.searchTerm) return true;
                    
                    const searchLower = this.searchTerm.toLowerCase();
                    const hasMatchingDestinations = continent.destinations.some(destination => 
                        destination.country.toLowerCase().includes(searchLower) || 
                        (destination.city && destination.city.toLowerCase().includes(searchLower))
                    );
                    
                    return hasMatchingDestinations || continent.name.toLowerCase().includes(searchLower);
                });
            },
            
            filteredDestinationsByContinent(continentName) {
                const continent = this.continents.find(c => c.name === continentName);
                
                if (!continent) return [];
                
                if (!this.searchTerm) return continent.destinations;
                
                const searchLower = this.searchTerm.toLowerCase();
                return continent.destinations.filter(destination => 
                    destination.country.toLowerCase().includes(searchLower) || 
                    (destination.city && destination.city.toLowerCase().includes(searchLower))
                );
            },
            
            getCountryCode(country) {
                return country.substring(0, 2).toUpperCase();
            },
            
            showAddDestinationModal() {
                this.editMode = false;
                this.formData = {
                    id: null,
                    continent: '',
                    country: '',
                    city: ''
                };
                this.showDestinationModal = true;
            },
            
            editDestination(destination) {
                this.editMode = true;
                this.formData = {
                    id: destination.id,
                    continent: destination.continent,
                    country: destination.country,
                    city: destination.city || ''
                };
                this.showDestinationModal = true;
            },
            
            editContinent(continentName) {
                // Logique pour éditer un continent
                // Dans un cas réel, cela pourrait ouvrir une modal pour modifier le nom du continent
                alert('Fonctionnalité à implémenter: Modifier le continent ' + continentName);
            },
            
            confirmDeleteDestination(destination) {
                if (confirm(`Êtes-vous sûr de vouloir supprimer la destination ${destination.country}${destination.city ? ' - ' + destination.city : ''} ?`)) {
                    // Simulation de suppression
                    // Dans un cas réel, on ferait un appel API ici
                    fetch(`/admin/destinations/${destination.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Actualiser la page
                            window.location.reload();
                        } else {
                            alert('Erreur lors de la suppression de la destination');
                        }
                    });
                }
            },
            
            saveDestination() {
                // Validation basique
                if (!this.formData.continent || !this.formData.country) {
                    alert('Le continent et le pays sont obligatoires');
                    return;
                }
                
                // Simulation de sauvegarde
                // Dans un cas réel, on ferait un appel API ici
                const url = this.editMode ? `/admin/destinations/${this.formData.id}` : '/admin/destinations';
                const method = this.editMode ? 'PUT' : 'POST';
                
                fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(response => {
                    if (response.ok) {
                        // Actualiser la page
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la sauvegarde de la destination');
                    }
                });
            },
            
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                
                if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                    alert('Le fichier doit être au format CSV');
                    event.target.value = '';
                    return;
                }
                
                this.csvFile = file.name;
                
                // Lire le fichier pour l'aperçu
                const reader = new FileReader();
                reader.onload = (e) => {
                    const contents = e.target.result;
                    this.parseCSV(contents);
                };
                reader.readAsText(file);
            },
            
            parseCSV(contents) {
                const lines = contents.split('\n');
                const preview = [];
                
                // Si la première ligne est un en-tête, on commence à la deuxième ligne
                const startIndex = this.importOptions.skipHeader ? 1 : 0;
                
                for (let i = startIndex; i < lines.length && preview.length < 10; i++) {
                    if (!lines[i].trim()) continue;
                    
                    const values = lines[i].split(',');
                    if (values.length >= 2) {
                        preview.push({
                            continent: values[0]?.trim() || '',
                            country: values[1]?.trim() || '',
                            city: values[2]?.trim() || ''
                        });
                    }
                }
                
                this.importPreview = preview;
            },
            
            importDestinations() {
                // Simulation d'importation
                // Dans un cas réel, on enverrait le fichier au serveur
                alert('Importation simulée : ' + this.importPreview.length + ' destinations seraient importées');
                this.showImportModal = false;
            },
            
            viewTrips(destinationId) {
                // Récupérer la destination
                let destination = null;
                for (const continent of this.continents) {
                    const found = continent.destinations.find(d => d.id === destinationId);
                    if (found) {
                        destination = found;
                        break;
                    }
                }
                
                if (!destination) return;
                
                this.selectedDestination = destination;
                
                // Simulation de récupération des voyages
                // Dans un cas réel, on ferait un appel API ici
                fetch(`/admin/destinations/${destinationId}/trips`)
                    .then(response => response.json())
                    .then(data => {
                        this.destinationTrips = data;
                        this.showTripsModal = true;
                    })
                    .catch(() => {
                        // Données simulées pour la démo
                        this.destinationTrips = [
                            {
                                id: 1,
                                title: 'Découverte de ' + destination.country,
                                vendor_name: 'Urban Adventures',
                                price: 580,
                                commission: 58,
                                duration: 3,
                                max_travelers: 10,
                                featured: true
                            },
                            {
                                id: 2,
                                title: 'Aventure en ' + destination.country,
                                vendor_name: 'Explore World',
                                price: 750,
                                commission: 75,
                                duration: 5,
                                max_travelers: 8,
                                featured: false
                            }
                        ];
                        this.showTripsModal = true;
                    });
            },
            
            formatPrice(price) {
                return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
            },
            
            formatDuration(days) {
                return days + (days > 1 ? ' jours' : ' jour');
            }
        };
    }
</script>
@endpush