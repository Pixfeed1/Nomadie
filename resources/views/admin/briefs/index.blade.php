@extends('layouts.admin')

@section('title', 'Gestion des Briefs')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mode Commande Interne</h1>
            <p class="text-gray-600 mt-1">Gérer les briefs de contenu pour la team de rédaction</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.briefs.analytics') }}"
               class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 flex items-center gap-2">
                <span>📊</span>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.briefs.templates') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center gap-2">
                <span>📋</span>
                <span>Templates</span>
            </a>
            <a href="{{ route('admin.briefs.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <span>➕</span>
                <span>Nouveau Brief</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Total Briefs</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
        </div>

        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-green-700 font-medium">En cours</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['active'] }}</p>
                </div>
                <div class="text-3xl">🚀</div>
            </div>
        </div>

        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-red-700 font-medium">En retard</p>
                    <p class="text-2xl font-bold text-red-900">{{ $stats['overdue'] }}</p>
                </div>
                <div class="text-3xl">⚠️</div>
            </div>
        </div>

        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-purple-700 font-medium">Complétés ce mois</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['completed_this_month'] }}</p>
                </div>
                <div class="text-3xl">✅</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.briefs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filter by Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigné</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>En attente de review</option>
                    <option value="revision_requested" {{ request('status') === 'revision_requested' ? 'selected' : '' }}>Révision demandée</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>

            <!-- Filter by Priority -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                <select name="priority" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Toutes les priorités</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>

            <!-- Filter by Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="destination" {{ request('type') === 'destination' ? 'selected' : '' }}>Destination</option>
                    <option value="guide_pratique" {{ request('type') === 'guide_pratique' ? 'selected' : '' }}>Guide Pratique</option>
                    <option value="culture" {{ request('type') === 'culture' ? 'selected' : '' }}>Culture</option>
                    <option value="gastronomie" {{ request('type') === 'gastronomie' ? 'selected' : '' }}>Gastronomie</option>
                    <option value="hebergement" {{ request('type') === 'hebergement' ? 'selected' : '' }}>Hébergement</option>
                    <option value="transport" {{ request('type') === 'transport' ? 'selected' : '' }}>Transport</option>
                    <option value="budget" {{ request('type') === 'budget' ? 'selected' : '' }}>Budget</option>
                    <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Personnalisé</option>
                </select>
            </div>

            <!-- Filter by Writer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rédacteur</label>
                <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Tous les rédacteurs</option>
                    @foreach($teamWriters as $writer)
                        <option value="{{ $writer->id }}" {{ request('assigned_to') == $writer->id ? 'selected' : '' }}>
                            {{ $writer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request()->hasAny(['status', 'priority', 'type', 'assigned_to']))
            <div class="mt-3">
                <a href="{{ route('admin.briefs.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    ✖ Réinitialiser les filtres
                </a>
            </div>
        @endif
    </div>

    <!-- Briefs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Brief
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rédacteur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priorité
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Deadline
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($briefs as $brief)
                    <tr class="hover:bg-gray-50 {{ $brief->isOverdue() ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $brief->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Créé le {{ $brief->created_at->format('d/m/Y') }}
                                    @if($brief->article)
                                        • <a href="{{ route('article.show', $brief->article->slug) }}" class="text-blue-600 hover:underline" target="_blank">Article lié</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $brief->getTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($brief->assignedTo)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($brief->assignedTo->avatar)
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . $brief->assignedTo->avatar) }}" alt="{{ $brief->assignedTo->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xs">
                                                {{ substr($brief->assignedTo->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $brief->assignedTo->name }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">Non assigné</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $brief->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $brief->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $brief->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $brief->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($brief->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $brief->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $brief->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $brief->status === 'pending_review' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $brief->status === 'revision_requested' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $brief->status === 'assigned' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $brief->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $brief->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                @switch($brief->status)
                                    @case('draft') Brouillon @break
                                    @case('assigned') Assigné @break
                                    @case('in_progress') En cours @break
                                    @case('pending_review') À review @break
                                    @case('revision_requested') Révision @break
                                    @case('completed') Complété @break
                                    @case('cancelled') Annulé @break
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($brief->deadline)
                                <div class="{{ $brief->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $brief->deadline->format('d/m/Y') }}
                                    @if($brief->isOverdue())
                                        <div class="text-xs text-red-500">⚠️ En retard</div>
                                    @else
                                        <div class="text-xs text-gray-400">{{ $brief->deadline->diffForHumans() }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 italic">Aucune</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.briefs.show', $brief) }}" class="text-blue-600 hover:text-blue-900">
                                Voir →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-4xl mb-2">📝</div>
                            <p class="mb-4">Aucun brief trouvé</p>
                            <a href="{{ route('admin.briefs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <span>➕</span>
                                <span>Créer votre premier brief</span>
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($briefs->hasPages())
        <div class="mt-6">
            {{ $briefs->links() }}
        </div>
    @endif
</div>
@endsection
