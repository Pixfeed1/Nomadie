@extends('layouts.admin')

@section('title', 'Analytics Briefs')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Analytics & Production</h1>
            <p class="text-gray-600 mt-1">Statistiques et performance du système de briefs</p>
        </div>
        <a href="{{ route('admin.briefs.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            ← Retour aux briefs
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Total Briefs</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-green-700 font-medium">En cours</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['active'] }}</p>
                </div>
                <div class="text-3xl">🚀</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-l-4 border-purple-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-purple-700 font-medium">Complétés</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['completed'] }}</p>
                </div>
                <div class="text-3xl">✅</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border-l-4 border-orange-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-orange-700 font-medium">Taux complétion</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $stats['completion_rate'] }}%</p>
                </div>
                <div class="text-3xl">📈</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-l-4 border-gray-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-700 font-medium">Temps moyen</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_completion_days'] }}j</p>
                </div>
                <div class="text-3xl">⏱️</div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Graphique par statut -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition par statut</h3>
            <canvas id="statusChart" height="200"></canvas>
        </div>

        <!-- Graphique par type -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition par type</h3>
            <canvas id="typeChart" height="200"></canvas>
        </div>
    </div>

    <!-- Evolution complétions -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des complétions (6 derniers mois)</h3>
        <canvas id="completionChart" height="80"></canvas>
    </div>

    <!-- Performance par rédacteur -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance par rédacteur</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rédacteur</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Complétés</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">En cours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">En retard</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taux complétion</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Temps moyen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($writerStats as $writer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($writer->avatar)
                                        <img class="h-8 w-8 rounded-full object-cover mr-3" src="{{ asset('storage/' . $writer->avatar) }}" alt="{{ $writer->name }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xs mr-3">
                                            {{ substr($writer->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $writer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $writer->total_briefs }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $writer->completed_briefs }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $writer->in_progress_briefs }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($writer->overdue_briefs > 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $writer->overdue_briefs }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $writer->completion_rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $writer->completion_rate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $writer->avg_completion_days }}j
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucun rédacteur avec des briefs assignés
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Templates populaires -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Templates les plus utilisés</h3>
        <div class="space-y-3">
            @forelse($topTemplates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $template->name }}</h4>
                        <p class="text-xs text-gray-600">{{ $template->getTypeLabel() }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ $template->usage_count }} utilisations</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($template->usage_count / max(1, $topTemplates->max('usage_count'))) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Aucun template utilisé pour le moment</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Graphique par statut
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_map(function($status) {
            return match($status) {
                'draft' => 'Brouillon',
                'assigned' => 'Assigné',
                'in_progress' => 'En cours',
                'pending_review' => 'En review',
                'revision_requested' => 'Révision',
                'completed' => 'Complété',
                'cancelled' => 'Annulé',
                default => $status
            };
        }, array_keys($briefsByStatus))) !!},
        datasets: [{
            data: {!! json_encode(array_values($briefsByStatus)) !!},
            backgroundColor: [
                '#9CA3AF', // draft - gray
                '#FCD34D', // assigned - yellow
                '#60A5FA', // in_progress - blue
                '#A78BFA', // pending_review - purple
                '#FB923C', // revision_requested - orange
                '#34D399', // completed - green
                '#EF4444', // cancelled - red
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Graphique par type
const typeCtx = document.getElementById('typeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_map(function($type) {
            return match($type) {
                'destination' => 'Destination',
                'guide_pratique' => 'Guide Pratique',
                'culture' => 'Culture',
                'gastronomie' => 'Gastronomie',
                'hebergement' => 'Hébergement',
                'transport' => 'Transport',
                'budget' => 'Budget',
                'custom' => 'Personnalisé',
                default => $type
            };
        }, array_keys($briefsByType))) !!},
        datasets: [{
            data: {!! json_encode(array_values($briefsByType)) !!},
            backgroundColor: [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                '#8B5CF6', '#EC4899', '#14B8A6', '#6366F1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Graphique évolution complétions
const completionCtx = document.getElementById('completionChart').getContext('2d');
new Chart(completionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($completedByMonth->pluck('month')->toArray()) !!},
        datasets: [{
            label: 'Briefs complétés',
            data: {!! json_encode($completedByMonth->pluck('count')->toArray()) !!},
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
