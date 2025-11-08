@extends('layouts.writer')

@section('title', 'Mes Briefs')

@section('page-title', 'Mes Briefs')
@section('page-description', 'Vos missions de rédaction et leur progression')

@section('content')
<div x-data="{ showFilters: false }">
    <!-- Statistiques en haut -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-xl">📋</span>
                </div>
            </div>
        </div>

        <!-- En cours -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">En cours</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-xl">✍️</span>
                </div>
            </div>
        </div>

        <!-- En attente review -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">En review</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['pending_review'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-xl">👀</span>
                </div>
            </div>
        </div>

        <!-- Révision demandée -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Révisions</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['revision_requested'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <span class="text-xl">🔄</span>
                </div>
            </div>
        </div>

        <!-- Complétés -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Complétés</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-xl">✅</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert si briefs en retard -->
    @if($stats['overdue'] > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
            <div class="flex items-center">
                <span class="text-2xl mr-3">⚠️</span>
                <div>
                    <p class="font-semibold text-red-900">{{ $stats['overdue'] }} brief(s) en retard !</p>
                    <p class="text-sm text-red-700">Merci de finaliser vos briefs en priorité pour respecter les deadlines.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-text-primary">Filtrer par statut:</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('writer.briefs.index') }}"
                       class="px-3 py-1 rounded-lg text-sm {{ !request('status') ? 'bg-blue-100 text-blue-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Tous
                    </a>
                    <a href="{{ route('writer.briefs.index', ['status' => 'assigned']) }}"
                       class="px-3 py-1 rounded-lg text-sm {{ request('status') === 'assigned' ? 'bg-yellow-100 text-yellow-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Assignés
                    </a>
                    <a href="{{ route('writer.briefs.index', ['status' => 'in_progress']) }}"
                       class="px-3 py-1 rounded-lg text-sm {{ request('status') === 'in_progress' ? 'bg-blue-100 text-blue-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        En cours
                    </a>
                    <a href="{{ route('writer.briefs.index', ['status' => 'pending_review']) }}"
                       class="px-3 py-1 rounded-lg text-sm {{ request('status') === 'pending_review' ? 'bg-purple-100 text-purple-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        En review
                    </a>
                    <a href="{{ route('writer.briefs.index', ['status' => 'revision_requested']) }}"
                       class="px-3 py-1 rounded-lg text-sm {{ request('status') === 'revision_requested' ? 'bg-orange-100 text-orange-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Révisions
                    </a>
                    <a href="{{ route('writer.briefs.index', ['status' => 'completed']) }}"
                       class="px-3 py-1 rounded-lg text-sm {{ request('status') === 'completed' ? 'bg-green-100 text-green-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Complétés
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des briefs -->
    <div class="space-y-4">
        @forelse($briefs as $brief)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition {{ $brief->isOverdue() ? 'border-l-4 border-red-500' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <!-- Contenu principal -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-text-primary">{{ $brief->title }}</h3>

                                @if($brief->isOverdue())
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">
                                        ⚠️ En retard
                                    </span>
                                @endif

                                <!-- Priorité -->
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $brief->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $brief->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $brief->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $brief->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($brief->priority) }}
                                </span>

                                <!-- Type -->
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    {{ $brief->getTypeLabel() }}
                                </span>
                            </div>

                            @if($brief->description)
                                <p class="text-sm text-text-secondary mb-3 line-clamp-2">{{ $brief->description }}</p>
                            @endif

                            <!-- Métadonnées -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-text-secondary">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>{{ $brief->min_words ?? 'N/A' }} mots min</span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Score cible: {{ $brief->target_score ?? 'N/A' }}/100</span>
                                </div>

                                @if($brief->deadline)
                                    <div class="flex items-center gap-1 {{ $brief->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Deadline: {{ $brief->deadline->format('d/m/Y') }}</span>
                                        @if(!$brief->isOverdue())
                                            <span class="text-xs">({{ $brief->deadline->diffForHumans() }})</span>
                                        @endif
                                    </div>
                                @endif

                                @if($brief->article)
                                    <div class="flex items-center gap-1 text-green-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Article lié</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Statut et actions -->
                        <div class="flex flex-col items-end gap-3 ml-4">
                            <!-- Statut -->
                            <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                {{ $brief->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $brief->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $brief->status === 'pending_review' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $brief->status === 'revision_requested' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $brief->status === 'assigned' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                @switch($brief->status)
                                    @case('assigned') Assigné @break
                                    @case('in_progress') En cours @break
                                    @case('pending_review') En review @break
                                    @case('revision_requested') Révision @break
                                    @case('completed') Complété @break
                                @endswitch
                            </span>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                @if($brief->status === 'assigned' || $brief->status === 'revision_requested')
                                    <form method="POST" action="{{ route('writer.briefs.start', $brief) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                                            🚀 Démarrer
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('writer.briefs.show', $brief) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition">
                                    Voir détails →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Notes admin si révision demandée -->
                    @if($brief->status === 'revision_requested' && $brief->admin_notes)
                        <div class="mt-4 p-3 bg-orange-50 border-l-4 border-orange-500 rounded">
                            <p class="text-xs font-semibold text-orange-900 mb-1">📝 Modifications demandées:</p>
                            <p class="text-sm text-orange-800">{{ $brief->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-text-primary mb-2">Aucun brief assigné</h3>
                <p class="text-text-secondary">Vous n'avez pas encore de missions de rédaction. L'équipe admin vous assignera des briefs prochainement.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($briefs->hasPages())
        <div class="mt-6">
            {{ $briefs->links() }}
        </div>
    @endif
</div>
@endsection
