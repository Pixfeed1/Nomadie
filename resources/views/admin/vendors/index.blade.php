@extends('layouts.admin')

@section('title', 'Gestion des Vendeurs')

@section('page-title', 'Gestion des Vendeurs')

@section('content')
<div x-data="vendorsManager" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
        <div>
            <h2 class="text-xl font-bold text-text-primary">Liste des vendeurs</h2>
            <p class="text-sm text-text-secondary mt-1">Gérez les organisateurs de voyages de votre plateforme</p>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <div class="relative">
                <input type="text" x-model="searchTerm" placeholder="Rechercher un vendeur..." class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-text-primary transition-all pl-9">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <div>
                <select x-model="statusFilter" class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-text-primary appearance-none bg-white">
                    <option value="all">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="pending">En attente</option>
                    <option value="suspended">Suspendus</option>
                </select>
            </div>
            
            <button @click="openAddVendorModal()" class="flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajouter
            </button>
        </div>
    </div>
    
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Vendeurs actifs</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">3</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-success font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +12.5% ce mois
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">En attente</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">1</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-accent font-medium flex items-center">
                    En attente d'approbation
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Suspendus</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">1</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-error/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-3 bg-white flex items-center">
                <span class="text-xs text-error font-medium flex items-center">
                    Accès restreint
                </span>
            </div>
        </div>
    </div>
    
    <!-- Tableau des vendeurs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-bg-alt">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Nom du vendeur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    <template x-for="(vendor, index) in filteredVendors" :key="index">
                        <tr class="hover:bg-bg-alt/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full text-white flex items-center justify-center font-bold" :class="getAvatarColor(vendor.name)">
                                        <span x-text="getInitials(vendor.name)"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text-primary" x-text="vendor.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-secondary" x-text="vendor.email"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusClasses(vendor.status)">
                                    <span x-text="getStatusLabel(vendor.status)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <template x-if="vendor.status === 'active'">
                                    <button @click="confirmSuspend(vendor)" class="text-error hover:text-error-dark bg-error/5 hover:bg-error/10 px-3 py-1 rounded-md transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Suspendre
                                    </button>
                                </template>
                                <template x-if="vendor.status === 'pending'">
                                    <button @click="confirmApprove(vendor)" class="text-success hover:text-success-dark bg-success/5 hover:bg-success/10 px-3 py-1 rounded-md transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Accepter
                                    </button>
                                </template>
                                <template x-if="vendor.status === 'suspended'">
                                    <button @click="confirmActivate(vendor)" class="text-accent hover:text-accent-dark bg-accent/5 hover:bg-accent/10 px-3 py-1 rounded-md transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Activer
                                    </button>
                                </template>
                                
                                <a :href="'/admin/vendors/' + vendor.id" class="text-primary hover:text-primary-dark bg-primary/5 hover:bg-primary/10 px-3 py-1 rounded-md transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Voir profil
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
                    Affichage de <span class="font-medium">5</span> vendeurs sur <span class="font-medium">5</span>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-border bg-white text-sm font-medium text-text-secondary hover:bg-bg-alt">
                            <span class="sr-only">Précédent</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-border bg-primary text-sm font-medium text-white">
                            1
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-border bg-white text-sm font-medium text-text-secondary hover:bg-bg-alt">
                            <span class="sr-only">Suivant</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
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
    function vendorsManager() {
        return {
            vendors: [
                { id: 1, name: 'Explore World', email: 'contact@exploreworld.com', status: 'active' },
                { id: 2, name: 'Voyage by Sarah', email: 'sarah@voyagebysarah.com', status: 'pending' },
                { id: 3, name: 'Urban Adventures', email: 'info@urbanadventures.com', status: 'active' },
                { id: 4, name: 'Nature Escape Tours', email: 'contact@natureescape.com', status: 'suspended' },
                { id: 5, name: 'Travel&Fun Agency', email: 'info@travelfun.com', status: 'active' }
            ],
            searchTerm: '',
            statusFilter: 'all',
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
            selectedVendor: null,
            
            get filteredVendors() {
                return this.vendors.filter(vendor => {
                    const matchesSearch = vendor.name.toLowerCase().includes(this.searchTerm.toLowerCase()) || 
                                        vendor.email.toLowerCase().includes(this.searchTerm.toLowerCase());
                    
                    const matchesStatus = this.statusFilter === 'all' || vendor.status === this.statusFilter;
                    
                    return matchesSearch && matchesStatus;
                });
            },
            
            getInitials(name) {
                return name.split(' ').map(n => n[0]).join('').toUpperCase();
            },
            
            getAvatarColor(name) {
                const colors = {
                    'Explore World': 'bg-primary',
                    'Voyage by Sarah': 'bg-accent',
                    'Urban Adventures': 'bg-primary-dark',
                    'Nature Escape Tours': 'bg-error',
                    'Travel&Fun Agency': 'bg-primary'
                };
                
                return colors[name] || 'bg-primary';
            },
            
            getStatusClasses(status) {
                const classes = {
                    'active': 'bg-success/15 text-success',
                    'pending': 'bg-accent/15 text-accent-dark',
                    'suspended': 'bg-error/15 text-error'
                };
                
                return classes[status] || '';
            },
            
            getStatusLabel(status) {
                const labels = {
                    'active': 'Actif',
                    'pending': 'En attente',
                    'suspended': 'Suspendu'
                };
                
                return labels[status] || status;
            },
            
            confirmSuspend(vendor) {
                this.selectedVendor = vendor;
                this.modalTitle = 'Suspendre le vendeur';
                this.modalContent = `Êtes-vous sûr de vouloir suspendre ${vendor.name} ? Cela empêchera le vendeur d'accéder à son compte et de vendre des voyages.`;
                
                this.modalIcon = {
                    bgColor: 'bg-error/10',
                    color: 'text-error',
                    path: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'
                };
                
                this.modalAction = {
                    bgColor: 'bg-error hover:bg-error/90',
                    label: 'Suspendre',
                    callback: () => this.suspendVendor(vendor)
                };
                
                this.showModal = true;
            },
            
            confirmApprove(vendor) {
                this.selectedVendor = vendor;
                this.modalTitle = 'Accepter le vendeur';
                this.modalContent = `Êtes-vous sûr de vouloir accepter ${vendor.name} sur la plateforme ? Cela lui permettra de publier ses offres de voyage.`;
                
                this.modalIcon = {
                    bgColor: 'bg-success/10',
                    color: 'text-success',
                    path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                };
                
                this.modalAction = {
                    bgColor: 'bg-success hover:bg-success/90',
                    label: 'Accepter',
                    callback: () => this.approveVendor(vendor)
                };
                
                this.showModal = true;
            },
            
            confirmActivate(vendor) {
                this.selectedVendor = vendor;
                this.modalTitle = 'Activer le vendeur';
                this.modalContent = `Êtes-vous sûr de vouloir réactiver ${vendor.name} ? Cela lui redonnera l'accès à son compte et la possibilité de vendre des voyages.`;
                
                this.modalIcon = {
                    bgColor: 'bg-success/10',
                    color: 'text-success',
                    path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                };
                
                this.modalAction = {
                    bgColor: 'bg-success hover:bg-success/90',
                    label: 'Activer',
                    callback: () => this.activateVendor(vendor)
                };
                
                this.showModal = true;
            },
            
            confirmAction() {
                if (this.modalAction.callback) {
                    this.modalAction.callback();
                }
                this.showModal = false;
            },
            
            suspendVendor(vendor) {
                const index = this.vendors.findIndex(v => v.id === vendor.id);
                if (index !== -1) {
                    this.vendors[index].status = 'suspended';
                    this.showNotification('Vendeur suspendu', `${vendor.name} a été suspendu avec succès.`, 'success');
                }
            },
            
            approveVendor(vendor) {
                const index = this.vendors.findIndex(v => v.id === vendor.id);
                if (index !== -1) {
                    this.vendors[index].status = 'active';
                    this.showNotification('Vendeur accepté', `${vendor.name} a été activé avec succès.`, 'success');
                }
            },
            
            activateVendor(vendor) {
                const index = this.vendors.findIndex(v => v.id === vendor.id);
                if (index !== -1) {
                    this.vendors[index].status = 'active';
                    this.showNotification('Vendeur activé', `${vendor.name} a été réactivé avec succès.`, 'success');
                }
            },
            
            openAddVendorModal() {
                const content = `
                    <form class="space-y-4">
                        <div>
                            <label for="vendor-name" class="block text-sm font-medium text-text-primary">Nom du vendeur</label>
                            <input type="text" id="vendor-name" class="mt-1 block w-full border border-border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="vendor-email" class="block text-sm font-medium text-text-primary">Email</label>
                            <input type="email" id="vendor-email" class="mt-1 block w-full border border-border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="vendor-status" class="block text-sm font-medium text-text-primary">Statut</label>
                            <select id="vendor-status" class="mt-1 block w-full border border-border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                <option value="active">Actif</option>
                                <option value="pending">En attente</option>
                                <option value="suspended">Suspendu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-primary">Destinations</label>
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-primary">
                                    <span class="ml-2 text-sm">France</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox h-4 w-4 text-primary">
                                    <span class="ml-2 text-sm">Italie</span>
                                </label>
<label class="inline-flex items-center">
                   <input type="checkbox" class="form-checkbox h-4 w-4 text-primary">
                   <span class="ml-2 text-sm">Thaïlande</span>
               </label>
               <label class="inline-flex items-center">
                   <input type="checkbox" class="form-checkbox h-4 w-4 text-primary">
                   <span class="ml-2 text-sm">Vietnam</span>
               </label>
               <label class="inline-flex items-center">
                   <input type="checkbox" class="form-checkbox h-4 w-4 text-primary">
                   <span class="ml-2 text-sm">Japon</span>
               </label>
           </div>
       </div>
   </form>
`;

const modalTitle = document.querySelector('[x-data] [x-text="modalTitle"]');
const modalContent = document.querySelector('[x-data] [x-html="modalContent"]');

if (modalTitle && modalContent) {
   modalTitle.__x.$data.modalTitle = `Ajouter un nouveau vendeur`;
   modalContent.__x.$data.modalContent = content;
   document.querySelector('[x-data]').__x.$data.modalOpen = true;
}
},

showNotification(title, message, type) {
   // Cette fonction simulerait l'affichage d'une notification toast
   console.log(`${type.toUpperCase()}: ${title} - ${message}`);
   // Dans une version finale, on implémenterait un vrai système de notification
}
}
}
</script>
@endpush