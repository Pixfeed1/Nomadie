@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboardData" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Vendeurs inscrits -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Vendeurs inscrits</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">254</p>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +12% ce mois
                    </p>
                </div>
                <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-4 bg-gradient-to-r from-primary/5 to-primary/10 border-t border-primary/10">
                <a href="{{ route('admin.vendors.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center">
                    Voir tous les vendeurs
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Ventes générées -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Ventes générées</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">7 520 €</p>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +8.5% ce mois
                    </p>
                </div>
                <div class="h-16 w-16 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-4 bg-gradient-to-r from-accent/5 to-accent/10 border-t border-accent/10">
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-accent-dark hover:text-accent font-medium flex items-center">
                    Voir les détails
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Commissions collectées -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 flex items-center justify-between">
                <div>
                    <p class="text-text-secondary text-sm">Commissions collectées</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">752 €</p>
                    <p class="text-xs text-success font-medium flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        +10% ce mois
                    </p>
                </div>
                <div class="h-16 w-16 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 py-4 bg-gradient-to-r from-success/5 to-success/10 border-t border-success/10">
                <a href="{{ route('admin.subscriptions.index') }}" class="text-sm text-success hover:text-success/80 font-medium flex items-center">
                    Voir les abonnements
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Graphique des ventes -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden lg:col-span-2 card">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-text-primary">Ventes par mois</h3>
                    <div class="flex space-x-2">
                        <select class="border border-border rounded-md text-sm py-1 px-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option>Cette année</option>
                            <option>Année précédente</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="h-72">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Destinations populaires -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Destinations populaires</h3>
                <p class="text-sm text-text-secondary mt-1">Top destinations ce mois</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <span class="text-primary font-medium">FR</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">France</p>
                                <p class="text-sm font-medium text-text-primary">2 350 €</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                            <span class="text-accent font-medium">JP</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">Japon</p>
                                <p class="text-sm font-medium text-text-primary">1 870 €</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-accent h-2 rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                            <span class="text-success font-medium">IT</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">Italie</p>
                                <p class="text-sm font-medium text-text-primary">1 560 €</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-success h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-error/10 flex items-center justify-center">
                            <span class="text-error font-medium">TH</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">Thaïlande</p>
                                <p class="text-sm font-medium text-text-primary">960 €</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-error h-2 rounded-full" style="width: 40%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-dark/10 flex items-center justify-center">
                            <span class="text-primary-dark font-medium">ES</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">Espagne</p>
                                <p class="text-sm font-medium text-text-primary">780 €</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary-dark h-2 rounded-full" style="width: 35%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Nouveau graphique des tendances d'inscription -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-text-primary">Tendances des inscriptions</h3>
                <div class="flex space-x-2">
                    <select class="border border-border rounded-md text-sm py-1 px-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option>Cette semaine</option>
                        <option>Semaine précédente</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="registrationTrendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Activité récente et Vendeurs récents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activité récente -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Activité récente</h3>
                <p class="text-sm text-text-secondary mt-1">Dernières transactions et actions</p>
            </div>
            <div class="divide-y divide-border">
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Nouvelle commande #38295</p>
                        <p class="text-xs text-text-secondary mt-1">Séjour à Rome (Italie) via Urban Adventures</p>
                        <p class="text-xs text-primary mt-1">Il y a 30 minutes</p>
                    </div>
                </div>
                
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Nouveau vendeur</p>
                        <p class="text-xs text-text-secondary mt-1">Voyage by Sarah a rejoint la plateforme</p>
                        <p class="text-xs text-primary mt-1">Il y a 2 heures</p>
                    </div>
                </div>
                
                <div class="p-6 flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Mise à jour tarifaire</p>
                        <p class="text-xs text-text-secondary mt-1">Explore World a mis à jour ses tarifs pour la France</p>
                        <p class="text-xs text-primary mt-1">Il y a 5 heures</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border">
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center">
                    Voir toute l'activité
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Derniers vendeurs -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary">Derniers vendeurs inscrits</h3>
                <p class="text-sm text-text-secondary mt-1">Nouvelles inscriptions</p>
            </div>
            <div class="divide-y divide-border">
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold">
                            VS
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-text-primary">Voyage by Sarah</p>
                            <p class="text-xs text-text-secondary mt-1">sarah@voyagebysarah.com</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-accent/15 text-accent-dark">
                        En attente
                    </span>
                </div>
                
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            UA
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-text-primary">Urban Adventures</p>
                            <p class="text-xs text-text-secondary mt-1">info@urbanadventures.com</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success/15 text-success">
                        Actif
                    </span>
                </div>
                
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            TF
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-text-primary">Travel&Fun Agency</p>
                            <p class="text-xs text-text-secondary mt-1">info@travelfun.com</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success/15 text-success">
                        Actif
                    </span>
                </div>
            </div>
            <div class="px-6 py-4 bg-bg-alt border-t border-border">
                <a href="{{ route('admin.vendors.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center">
                    Voir tous les vendeurs
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function dashboardData() {
        return {
            init() {
                this.initSalesChart();
                this.initRegistrationTrendChart();
            },
            
            initSalesChart() {
                const ctx = document.getElementById('salesChart').getContext('2d');
                
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(56, 178, 172, 0.3)');
                gradient.addColorStop(1, 'rgba(56, 178, 172, 0.0)');
                
                const salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                        datasets: [{
                            label: 'Ventes mensuelles (€)',
                            data: [450, 520, 780, 850, 690, 920, 1100, 1250, 940, 850, 980, 1220],
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
            },
            
            initRegistrationTrendChart() {
                const ctx = document.getElementById('registrationTrendChart').getContext('2d');
                
                const gradientPending = ctx.createLinearGradient(0, 0, 0, 200);
                gradientPending.addColorStop(0, 'rgba(246, 173, 85, 0.3)');
                gradientPending.addColorStop(1, 'rgba(246, 173, 85, 0.0)');
                
                const gradientConfirmed = ctx.createLinearGradient(0, 0, 0, 200);
                gradientConfirmed.addColorStop(0, 'rgba(56, 178, 172, 0.3)');
                gradientConfirmed.addColorStop(1, 'rgba(56, 178, 172, 0.0)');
                
                const registrationChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                        datasets: [
                            {
                                label: 'Inscriptions',
                                data: [5, 8, 6, 9, 12, 7, 4],
                                borderColor: '#F6AD55',
                                backgroundColor: gradientPending,
                                borderWidth: 2,
                                pointBackgroundColor: '#F6AD55',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                tension: 0.3,
                                fill: true
                            },
{
                                label: 'Confirmations',
                                data: [3, 5, 4, 7, 9, 6, 2],
                                borderColor: '#38B2AC',
                                backgroundColor: gradientConfirmed,
                                borderWidth: 2,
                                pointBackgroundColor: '#38B2AC',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                tension: 0.3,
                                fill: true
                            }
                        ]
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
                                    font: {
                                        size: 10
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
                                        size: 10
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
                                    size: 12
                                },
                                bodyFont: {
                                    size: 11
                                },
                                displayColors: true
                            }
                        }
                    }
                });
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        dashboardData().init();
    });
</script>
@endpush