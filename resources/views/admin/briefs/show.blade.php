@extends('layouts.admin')

@section('title', 'Brief - ' . $brief->title)

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.briefs.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Retour à la liste
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $brief->title }}</h1>
                    @if($brief->isOverdue())
                        <span class="text-red-600 text-sm font-semibold">⚠️ En retard</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $brief->getTypeLabel() }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
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
                            @case('pending_review') En attente de review @break
                            @case('revision_requested') Révision demandée @break
                            @case('completed') Complété @break
                            @case('cancelled') Annulé @break
                        @endswitch
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $brief->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $brief->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $brief->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $brief->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                        Priorité {{ ucfirst($brief->priority) }}
                    </span>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    Créé le {{ $brief->created_at->format('d/m/Y à H:i') }} par {{ $brief->createdBy->name }}
                    @if($brief->deadline)
                        • Deadline: <span class="{{ $brief->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">{{ $brief->deadline->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-col gap-2">
                @if(!$brief->isCompleted())
                    <a href="{{ route('admin.briefs.edit', $brief) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-center">
                        ✏️ Modifier
                    </a>
                @endif

                @if($brief->status === 'pending_review' && $brief->article)
                    <form method="POST" action="{{ route('admin.briefs.approve', $brief) }}" onsubmit="return confirm('Approuver et marquer ce brief comme complété ?');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            ✅ Approuver
                        </button>
                    </form>

                    <button onclick="document.getElementById('revisionModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                        🔄 Demander révision
                    </button>
                @endif

                @if($brief->status === 'draft' || $brief->status === 'assigned')
                    <button onclick="document.getElementById('assignModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        👤 {{ $brief->assignedTo ? 'Réassigner' : 'Assigner' }}
                    </button>
                @endif

                @if(!$brief->isCompleted())
                    <form method="POST" action="{{ route('admin.briefs.cancel', $brief) }}" onsubmit="return confirm('Annuler ce brief ?');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                            ❌ Annuler
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            @if($brief->description)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Description / Contexte</h3>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap">{{ $brief->description }}</div>
                </div>
            @endif

            <!-- Content Requirements -->
            @if($brief->content_requirements)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">✍️ Exigences de contenu</h3>

                    @if(isset($brief->content_requirements['structure']))
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Structure attendue:</h4>
                            <div class="bg-gray-50 p-4 rounded-lg text-sm">
                                <pre class="whitespace-pre-wrap text-gray-700">{{ is_array($brief->content_requirements['structure']) ? json_encode($brief->content_requirements['structure'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $brief->content_requirements['structure'] }}</pre>
                            </div>
                        </div>
                    @endif

                    @if(isset($brief->content_requirements['sections']))
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Sections:</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @foreach($brief->content_requirements['sections'] as $section => $desc)
                                    <div class="mb-3 last:mb-0">
                                        <div class="font-semibold text-gray-900">{{ $section }}</div>
                                        <div class="text-sm text-gray-600">{{ $desc }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($brief->content_requirements['tone']) || isset($brief->content_requirements['ton']))
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Ton & Style:</h4>
                            <div class="bg-blue-50 p-3 rounded-lg text-gray-700">
                                {{ $brief->content_requirements['tone'] ?? $brief->content_requirements['ton'] }}
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm text-green-700 font-medium">Mots minimum</div>
                            <div class="text-2xl font-bold text-green-900">{{ $brief->min_words ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm text-purple-700 font-medium">Score NomadSEO cible</div>
                            <div class="text-2xl font-bold text-purple-900">{{ $brief->target_score ?? 'N/A' }}/100</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Keywords -->
            @if($brief->keywords && count($brief->keywords) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Mots-clés</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($brief->keywords as $keyword)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- References -->
            @if($brief->references && count($brief->references) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔗 Références</h3>
                    <ul class="space-y-2">
                        @foreach($brief->references as $reference)
                            <li>
                                <a href="{{ $reference }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline text-sm break-all">
                                    {{ $reference }} ↗
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Admin Notes -->
            @if($brief->admin_notes)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4">📝 Notes internes</h3>
                    <div class="text-gray-700 whitespace-pre-wrap">{{ $brief->admin_notes }}</div>
                </div>
            @endif

            <!-- Writer Notes -->
            @if($brief->writer_notes)
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">💬 Notes du rédacteur</h3>
                    <div class="text-gray-700 whitespace-pre-wrap">{{ $brief->writer_notes }}</div>
                </div>
            @endif

            <!-- Article Link -->
            @if($brief->article)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📰 Article associé</h3>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $brief->article->title }}</h4>
                                <div class="text-sm text-gray-600 mb-3">
                                    {{ $brief->article->word_count ?? 0 }} mots
                                    @if($brief->article->latestSeoAnalysis)
                                        • Score SEO: {{ $brief->article->latestSeoAnalysis->overall_score }}/100
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('article.show', $brief->article->slug) }}" target="_blank"
                                       class="text-sm text-blue-600 hover:text-blue-800">
                                        Voir l'article ↗
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $brief->article->id) }}" target="_blank"
                                       class="text-sm text-gray-600 hover:text-gray-800">
                                        Éditer
                                    </a>
                                </div>
                            </div>
                            @if($brief->article->featured_image)
                                <img src="{{ asset('storage/' . $brief->article->featured_image) }}"
                                     alt="{{ $brief->article->title }}"
                                     class="w-24 h-24 object-cover rounded-lg ml-4">
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Assigned Writer -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Rédacteur</h3>
                @if($brief->assignedTo)
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0">
                            @if($brief->assignedTo->avatar)
                                <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $brief->assignedTo->avatar) }}" alt="{{ $brief->assignedTo->name }}">
                            @else
                                <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr($brief->assignedTo->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $brief->assignedTo->name }}</div>
                            <div class="text-sm text-gray-500">{{ $brief->assignedTo->email }}</div>
                        </div>
                    </div>
                    @if($brief->assigned_at)
                        <p class="text-xs text-gray-500">Assigné le {{ $brief->assigned_at->format('d/m/Y à H:i') }}</p>
                    @endif
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-3">Non assigné</p>
                        <button onclick="document.getElementById('assignModal').classList.remove('hidden')"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Assigner maintenant
                        </button>
                    </div>
                @endif
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⏱️ Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-2"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Créé</div>
                            <div class="text-xs text-gray-500">{{ $brief->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    @if($brief->assigned_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Assigné</div>
                                <div class="text-xs text-gray-500">{{ $brief->assigned_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->started_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Commencé</div>
                                <div class="text-xs text-gray-500">{{ $brief->started_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->submitted_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-purple-400 rounded-full mt-2"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Soumis</div>
                                <div class="text-xs text-gray-500">{{ $brief->submitted_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->completed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Complété</div>
                                <div class="text-xs text-gray-500">{{ $brief->completed_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->deadline)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 {{ $brief->isOverdue() ? 'bg-red-400' : 'bg-gray-300' }} rounded-full mt-2"></div>
                            <div>
                                <div class="text-sm font-medium {{ $brief->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                                    Deadline {{ $brief->isOverdue() ? '(dépassée)' : '' }}
                                </div>
                                <div class="text-xs {{ $brief->isOverdue() ? 'text-red-500' : 'text-gray-500' }}">
                                    {{ $brief->deadline->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Meta Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Informations</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">ID Brief</dt>
                        <dd class="font-medium text-gray-900">#{{ $brief->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Slug</dt>
                        <dd class="font-medium text-gray-900">{{ $brief->slug }}</dd>
                    </div>
                    @if($brief->category)
                        <div>
                            <dt class="text-gray-500">Catégorie</dt>
                            <dd class="font-medium text-gray-900">{{ $brief->category }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Dernière modification</dt>
                        <dd class="font-medium text-gray-900">{{ $brief->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Assigner le brief</h3>
        <form method="POST" action="{{ route('admin.briefs.assign', $brief) }}">
            @csrf
            <div class="mb-4">
                <label for="assign_to" class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner un rédacteur
                </label>
                <select id="assign_to" name="assigned_to" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Choisir --</option>
                    @foreach($teamWriters as $writer)
                        <option value="{{ $writer->id }}" {{ $brief->assigned_to == $writer->id ? 'selected' : '' }}>
                            {{ $writer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Assigner
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Revision Modal -->
<div id="revisionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Demander une révision</h3>
        <form method="POST" action="{{ route('admin.briefs.request-revision', $brief) }}">
            @csrf
            <div class="mb-4">
                <label for="revision_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Indiquer les modifications attendues
                </label>
                <textarea id="revision_notes"
                          name="notes"
                          rows="4"
                          required
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Détaillez les changements à effectuer..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
