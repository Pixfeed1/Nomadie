@extends('vendor.layouts.app')

@section('title', $brief->title)

@section('page-title', $brief->title)
@section('page-description', 'Détails de votre mission de rédaction')

@section('content')
<div class="space-y-6">
    <!-- Back button -->
    <div>
        <a href="{{ route('writer.briefs.index') }}" class="text-primary hover:text-primary-dark text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour à mes briefs
        </a>
    </div>

    <!-- Header avec statut et actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    @if($brief->isOverdue())
                        <span class="text-2xl">⚠️</span>
                    @endif
                    <h1 class="text-2xl font-bold text-text-primary">{{ $brief->title }}</h1>
                </div>

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <!-- Statut -->
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $brief->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $brief->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $brief->status === 'pending_review' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $brief->status === 'revision_requested' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $brief->status === 'assigned' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        @switch($brief->status)
                            @case('assigned') Assigné @break
                            @case('in_progress') En cours @break
                            @case('pending_review') En review @break
                            @case('revision_requested') Révision demandée @break
                            @case('completed') Complété @break
                        @endswitch
                    </span>

                    <!-- Priorité -->
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $brief->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $brief->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $brief->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $brief->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                        Priorité {{ ucfirst($brief->priority) }}
                    </span>

                    <!-- Type -->
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                        {{ $brief->getTypeLabel() }}
                    </span>
                </div>

                <div class="text-sm text-text-secondary">
                    Assigné le {{ $brief->assigned_at->format('d/m/Y à H:i') }}
                    @if($brief->deadline)
                        • Deadline: <span class="{{ $brief->isOverdue() ? 'text-red-600 font-semibold' : 'text-text-primary font-medium' }}">
                            {{ $brief->deadline->format('d/m/Y') }}
                        </span>
                        @if($brief->isOverdue())
                            <span class="text-red-600 font-semibold">(en retard)</span>
                        @else
                            <span class="text-text-secondary">({{ $brief->deadline->diffForHumans() }})</span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                @if($brief->status === 'assigned' || $brief->status === 'revision_requested')
                    <form method="POST" action="{{ route('writer.briefs.start', $brief) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">
                            🚀 Démarrer le brief
                        </button>
                    </form>
                @endif

                @if($brief->status === 'in_progress')
                    <button onclick="document.getElementById('submitModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition">
                        ✅ Soumettre l'article
                    </button>
                @endif

                @if($brief->article)
                    <a href="{{ route('writer.articles.edit', $brief->article) }}"
                       class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark text-center font-medium text-sm transition">
                        ✏️ Éditer l'article
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes admin si révision demandée -->
    @if($brief->status === 'revision_requested' && $brief->admin_notes)
        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-orange-900 mb-3">📝 Modifications demandées par l'équipe</h3>
            <div class="text-orange-800 whitespace-pre-wrap">{{ $brief->admin_notes }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            @if($brief->description)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">📄 Description / Contexte</h3>
                    <div class="prose prose-sm max-w-none text-text-secondary">
                        <p class="whitespace-pre-wrap">{{ $brief->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Exigences de contenu -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">✍️ Exigences de contenu</h3>

                @if(isset($brief->content_requirements['structure']) || isset($brief->content_requirements['sections']))
                    <div class="mb-4">
                        <h4 class="font-medium text-text-primary mb-2">Structure attendue:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @if(isset($brief->content_requirements['sections']))
                                <div class="space-y-3">
                                    @foreach($brief->content_requirements['sections'] as $section => $desc)
                                        <div>
                                            <div class="font-semibold text-text-primary">{{ $section }}</div>
                                            <div class="text-sm text-text-secondary">{{ $desc }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <pre class="whitespace-pre-wrap text-sm text-text-secondary font-mono">{{ is_array($brief->content_requirements['structure']) ? json_encode($brief->content_requirements['structure'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $brief->content_requirements['structure'] }}</pre>
                            @endif
                        </div>
                    </div>
                @endif

                @if(isset($brief->content_requirements['tone']) || isset($brief->content_requirements['ton']))
                    <div class="mb-4">
                        <h4 class="font-medium text-text-primary mb-2">Ton & Style:</h4>
                        <div class="bg-blue-50 p-3 rounded-lg text-text-secondary">
                            {{ $brief->content_requirements['tone'] ?? $brief->content_requirements['ton'] }}
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                        <div class="text-sm text-green-700 font-medium mb-1">Mots minimum</div>
                        <div class="text-2xl font-bold text-green-900">{{ $brief->min_words ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
                        <div class="text-sm text-purple-700 font-medium mb-1">Score NomadSEO cible</div>
                        <div class="text-2xl font-bold text-purple-900">{{ $brief->target_score ?? 'N/A' }}/100</div>
                    </div>
                </div>
            </div>

            <!-- Mots-clés -->
            @if($brief->keywords && count($brief->keywords) > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">🎯 Mots-clés à intégrer</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($brief->keywords as $keyword)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-text-secondary mt-3">Intégrez naturellement ces mots-clés dans votre article pour optimiser le SEO.</p>
                </div>
            @endif

            <!-- Références -->
            @if($brief->references && count($brief->references) > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">🔗 Références & Sources</h3>
                    <ul class="space-y-2">
                        @foreach($brief->references as $reference)
                            <li>
                                <a href="{{ $reference }}" target="_blank" rel="noopener" class="text-primary hover:text-primary-dark hover:underline text-sm break-all flex items-center gap-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    {{ $reference }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Article associé -->
            @if($brief->article)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">📰 Article lié</h3>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-text-primary mb-2">{{ $brief->article->title }}</h4>
                                <div class="text-sm text-text-secondary mb-3">
                                    {{ $brief->article->word_count ?? 0 }} mots
                                    @if($brief->article->latestSeoAnalysis)
                                        • Score SEO: <span class="font-semibold {{ $brief->article->latestSeoAnalysis->overall_score >= 78 ? 'text-green-600' : ($brief->article->latestSeoAnalysis->overall_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $brief->article->latestSeoAnalysis->overall_score }}/100
                                        </span>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('writer.articles.edit', $brief->article) }}"
                                       class="text-sm text-primary hover:text-primary-dark font-medium">
                                        ✏️ Éditer l'article
                                    </a>
                                    <a href="{{ route('article.show', $brief->article->slug) }}" target="_blank"
                                       class="text-sm text-text-secondary hover:text-text-primary">
                                        👁️ Prévisualiser
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

            <!-- Mes notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">💬 Mes notes personnelles</h3>
                <form method="POST" action="{{ route('writer.briefs.update-notes', $brief) }}">
                    @csrf
                    <textarea name="writer_notes"
                              rows="4"
                              class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                              placeholder="Ajoutez des notes personnelles, des questions, des idées...">{{ old('writer_notes', $brief->writer_notes) }}</textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark text-sm transition">
                            💾 Enregistrer mes notes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">⏱️ Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <div class="text-sm font-medium text-text-primary">Assigné</div>
                            <div class="text-xs text-text-secondary">{{ $brief->assigned_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    @if($brief->started_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm font-medium text-text-primary">Commencé</div>
                                <div class="text-xs text-text-secondary">{{ $brief->started_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->submitted_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-purple-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm font-medium text-text-primary">Soumis</div>
                                <div class="text-xs text-text-secondary">{{ $brief->submitted_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->completed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm font-medium text-text-primary">Complété</div>
                                <div class="text-xs text-text-secondary">{{ $brief->completed_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($brief->deadline)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 {{ $brief->isOverdue() ? 'bg-red-400' : 'bg-gray-300' }} rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm font-medium {{ $brief->isOverdue() ? 'text-red-600' : 'text-text-primary' }}">
                                    Deadline {{ $brief->isOverdue() ? '(dépassée)' : '' }}
                                </div>
                                <div class="text-xs {{ $brief->isOverdue() ? 'text-red-500' : 'text-text-secondary' }}">
                                    {{ $brief->deadline->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Checklist rapide -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">✓ Checklist avant soumission</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Respecter le nombre de mots minimum ({{ $brief->min_words ?? 'N/A' }})</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Suivre la structure demandée</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Intégrer les mots-clés naturellement</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Viser le score SEO cible ({{ $brief->target_score ?? 'N/A' }}/100)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Ajouter des images de qualité</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-600 mt-0.5">✓</span>
                        <span class="text-blue-800">Relire et corriger les fautes</span>
                    </div>
                </div>
            </div>

            <!-- Contact admin -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-2">💬 Besoin d'aide ?</h3>
                <p class="text-sm text-text-secondary mb-3">
                    Des questions sur ce brief ? Contactez l'équipe admin.
                </p>
                <a href="mailto:admin@nomadie.fr" class="text-sm text-primary hover:text-primary-dark font-medium">
                    📧 admin@nomadie.fr
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal soumission article -->
<div id="submitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-text-primary mb-4">Soumettre l'article pour review</h3>
        <form method="POST" action="{{ route('writer.briefs.submit', $brief) }}">
            @csrf
            <div class="mb-4">
                <label for="article_id" class="block text-sm font-medium text-text-primary mb-2">
                    Sélectionner votre article <span class="text-red-500">*</span>
                </label>
                <select id="article_id" name="article_id" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    <option value="">-- Choisir un article --</option>
                    @php
                        $userArticles = Auth::user()->articles()->where('status', 'draft')->latest()->get();
                    @endphp
                    @foreach($userArticles as $article)
                        <option value="{{ $article->id }}" {{ $brief->article_id == $article->id ? 'selected' : '' }}>
                            {{ $article->title }}
                            @if($article->word_count)
                                ({{ $article->word_count }} mots)
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-text-secondary mt-1">Seuls vos articles en brouillon sont affichés</p>
            </div>

            <div class="mb-4">
                <label for="writer_notes_submit" class="block text-sm font-medium text-text-primary mb-2">
                    Message pour l'équipe (optionnel)
                </label>
                <textarea id="writer_notes_submit"
                          name="writer_notes"
                          rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                          placeholder="Ajoutez un message pour l'équipe admin...">{{ $brief->writer_notes }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('submitModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    ✅ Soumettre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
