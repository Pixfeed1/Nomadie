@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboardData" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="Vendeurs inscrits"
            :value="$stats['total_vendors']"
            :subtitle="$stats['active_vendors'] . ' actifs'"
            icon="users"
            color="primary"
        />

        <x-stat-card
            title="Expériences créées"
            :value="$stats['total_trips']"
            :subtitle="$stats['active_trips'] . ' actives'"
            icon="chart"
            color="accent"
        />

        <x-stat-card
            title="Revenus du mois"
            :value="number_format($stats['monthly_revenue'], 0, ',', ' ') . ' €'"
            icon="currency"
            color="success"
        />

        <x-stat-card
            title="Total revenus"
            :value="number_format($stats['total_revenue'], 0, ',', ' ') . ' €'"
            icon="wallet"
            color="purple"
        />
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
                @if($popularDestinations->count() > 0)
                <div class="space-y-4">
                    @php
                        $colors = ['primary', 'accent', 'success', 'purple', 'blue'];
                    @endphp
                    @foreach($popularDestinations as $index => $destination)
                    @php
                        $color = $colors[$index % count($colors)];
                        $initials = strtoupper(substr($destination->name, 0, 2));
                    @endphp
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-{{ $color }}/10 flex items-center justify-center">
                            <span class="text-{{ $color }} font-medium text-xs">{{ $initials }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-text-primary">{{ $destination->name }}</p>
                                <p class="text-sm font-medium text-text-primary">{{ $destination->revenue_formatted }}</p>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $color }} h-2 rounded-full" style="width: {{ $destination->percentage }}%"></div>
                            </div>
                            <p class="text-xs text-text-secondary mt-1">{{ $destination->bookings_count }} {{ Str::plural('réservation', $destination->bookings_count) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-text-secondary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-text-secondary mt-2">Aucune réservation ce mois-ci</p>
                </div>
                @endif
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
                @if($recentActivity->count() > 0)
                    @foreach($recentActivity as $activity)
                    <div class="p-6 flex items-start">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-{{ $activity['color'] === 'text-success' ? 'success' : ($activity['color'] === 'text-primary' ? 'primary' : 'accent') }}/10 flex items-center justify-center {{ $activity['color'] }}">
                                @if($activity['type'] === 'vendor_registered')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-text-primary">{{ $activity['title'] }}</p>
                            <p class="text-xs text-text-secondary mt-1">{{ $activity['description'] }}</p>
                            <p class="text-xs text-primary mt-1">{{ $activity['date']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="p-6 text-center">
                    <p class="text-sm text-text-secondary">Aucune activité récente</p>
                </div>
                @endif
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
                @if($recentVendors->count() > 0)
                    @foreach($recentVendors as $vendor)
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'pending' ? 'accent' : 'gray') }}/10 flex items-center justify-center text-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'pending' ? 'accent' : 'gray') }} font-bold text-xs">
                                {{ strtoupper(substr($vendor->company_name, 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-text-primary">{{ $vendor->company_name }}</p>
                                <p class="text-xs text-text-secondary mt-1">{{ $vendor->user->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($vendor->status === 'active') bg-success/15 text-success
                            @elseif($vendor->status === 'pending') bg-accent/15 text-accent
                            @elseif($vendor->status === 'rejected') bg-red-100 text-red-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($vendor->status === 'active') Actif
                            @elseif($vendor->status === 'pending') En attente
                            @elseif($vendor->status === 'rejected') Rejeté
                            @elseif($vendor->status === 'suspended') Suspendu
                            @else {{ ucfirst($vendor->status) }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                @else
                <div class="p-6 text-center">
                    <p class="text-sm text-text-secondary">Aucun vendeur récent</p>
                </div>
                @endif
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

    <!-- Messages récents entre clients et vendeurs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-text-primary">Messages récents</h3>
                    <p class="text-sm text-text-secondary mt-1">Échanges entre clients et vendeurs</p>
                </div>
                <span class="px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full">
                    {{ $recentMessages->count() }} messages
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($recentMessages->count() > 0)
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-bg-alt">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Expéditeur
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Destinataire
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Sujet
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Expérience
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Statut
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @foreach($recentMessages as $message)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="text-primary font-medium text-xs">{{ strtoupper(substr($message['sender_name'], 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-text-primary">{{ $message['sender_name'] }}</p>
                                    <p class="text-xs text-text-secondary">
                                        @if($message['sender_type'] === 'vendor') Vendeur
                                        @elseif($message['sender_type'] === 'customer') Client
                                        @else Admin
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-accent/10 flex items-center justify-center">
                                    <span class="text-accent font-medium text-xs">{{ strtoupper(substr($message['recipient_name'], 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-text-primary">{{ $message['recipient_name'] }}</p>
                                    <p class="text-xs text-text-secondary">
                                        @if($message['recipient_type'] === 'vendor') Vendeur
                                        @elseif($message['recipient_type'] === 'customer') Client
                                        @else Admin
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-text-primary font-medium">{{ $message['subject'] }}</p>
                            <p class="text-xs text-text-secondary mt-1">{{ $message['content_preview'] }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-text-secondary">{{ $message['trip_title'] ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                            {{ $message['time_ago'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($message['is_read'])
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                Lu
                            </span>
                            @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-600">
                                Non lu
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-text-secondary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="text-sm text-text-secondary mt-4">Aucun message récent</p>
            </div>
            @endif
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