@extends('layouts.admin')

@section('title', 'Nouveau Brief')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Créer un nouveau Brief</h1>
            <p class="text-gray-600 mt-1">Définir les instructions pour un article de contenu</p>
        </div>
        <a href="{{ route('admin.briefs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            ← Retour
        </a>
    </div>

    <!-- Template Selection -->
    @if(!$selectedTemplate && $templates->isNotEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">🚀 Démarrer avec un template ?</h3>
            <p class="text-blue-700 mb-4">Gagnez du temps en utilisant un template prédéfini</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($templates as $template)
                    <a href="{{ route('admin.briefs.create', ['template_id' => $template->id]) }}"
                       class="block bg-white p-4 rounded-lg border-2 border-blue-200 hover:border-blue-500 hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $template->name }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ $template->description }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $template->min_words }} mots min</span>
                            <span>Score: {{ $template->target_score }}/100</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-4">
                <button onclick="document.getElementById('brief-form').scrollIntoView({ behavior: 'smooth' })"
                        class="text-sm text-blue-600 hover:text-blue-800">
                    Ou créer un brief personnalisé →
                </button>
            </div>
        </div>
    @endif

    @if($selectedTemplate)
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-green-900">✅ Template sélectionné: {{ $selectedTemplate->name }}</h3>
                    <p class="text-green-700 text-sm mt-1">{{ $selectedTemplate->description }}</p>
                </div>
                <a href="{{ route('admin.briefs.create') }}" class="text-sm text-green-600 hover:text-green-800">
                    ✖ Changer de template
                </a>
            </div>
        </div>
    @endif

    <!-- Brief Form -->
    <form id="brief-form" method="POST" action="{{ route('admin.briefs.store') }}" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">📋 Informations de base</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre du brief <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $selectedTemplate->name ?? '') }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ex: Guide complet de Tokyo - Visiter la capitale japonaise">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type d'article <span class="text-red-500">*</span>
                    </label>
                    <select id="type"
                            name="type"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionnez un type</option>
                        <option value="destination" {{ old('type', $selectedTemplate->type ?? '') === 'destination' ? 'selected' : '' }}>Destination</option>
                        <option value="guide_pratique" {{ old('type', $selectedTemplate->type ?? '') === 'guide_pratique' ? 'selected' : '' }}>Guide Pratique</option>
                        <option value="culture" {{ old('type', $selectedTemplate->type ?? '') === 'culture' ? 'selected' : '' }}>Culture</option>
                        <option value="gastronomie" {{ old('type', $selectedTemplate->type ?? '') === 'gastronomie' ? 'selected' : '' }}>Gastronomie</option>
                        <option value="hebergement" {{ old('type', $selectedTemplate->type ?? '') === 'hebergement' ? 'selected' : '' }}>Hébergement</option>
                        <option value="transport" {{ old('type', $selectedTemplate->type ?? '') === 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="budget" {{ old('type', $selectedTemplate->type ?? '') === 'budget' ? 'selected' : '' }}>Budget</option>
                        <option value="custom" {{ old('type', $selectedTemplate->type ?? '') === 'custom' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Catégorie (optionnel)
                    </label>
                    <input type="text"
                           id="category"
                           name="category"
                           value="{{ old('category') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ex: Asie, Europe, Amérique...">
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description / Contexte
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Contexte, angle éditorial, public cible...">{{ old('description', $selectedTemplate->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Content Requirements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">✍️ Exigences de contenu</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sections / Structure attendue
                    </label>
                    <textarea name="content_requirements[structure]"
                              rows="6"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                              placeholder="Listez les sections attendues, une par ligne...">{{ old('content_requirements.structure', $selectedTemplate ? json_encode($selectedTemplate->content_requirements['sections'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Exemple: Introduction, Comment y aller, Où dormir, Que faire, etc.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ton & Style
                    </label>
                    <input type="text"
                           name="content_requirements[tone]"
                           value="{{ old('content_requirements.tone', $selectedTemplate->content_requirements['ton'] ?? '') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ex: Informatif et inspirant, accessible à tous">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_words" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de mots minimum
                        </label>
                        <input type="number"
                               id="min_words"
                               name="min_words"
                               value="{{ old('min_words', $selectedTemplate->min_words ?? 1500) }}"
                               min="500"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="target_score" class="block text-sm font-medium text-gray-700 mb-1">
                            Score NomadSEO cible
                        </label>
                        <input type="number"
                               id="target_score"
                               name="target_score"
                               value="{{ old('target_score', $selectedTemplate->target_score ?? 85) }}"
                               min="60"
                               max="100"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO & Keywords -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🎯 SEO & Mots-clés</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mots-clés principaux
                    </label>
                    <textarea name="keywords_text"
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Séparez les mots-clés par des virgules...">{{ old('keywords_text', $selectedTemplate ? implode(', ', $selectedTemplate->keywords ?? []) : '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Ex: tokyo, japon, voyage tokyo, visiter tokyo, guide tokyo</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Références / URLs sources
                    </label>
                    <textarea name="references_text"
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                              placeholder="URLs de référence, une par ligne...">{{ old('references_text') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Sites concurrents, sources officielles, articles de référence...</p>
                </div>
            </div>
        </div>

        <!-- Assignment & Workflow -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">👥 Attribution & Planification</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Assigned Writer -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                        Assigner à
                    </label>
                    <select id="assigned_to"
                            name="assigned_to"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Non assigné</option>
                        @foreach($teamWriters as $writer)
                            <option value="{{ $writer->id }}" {{ old('assigned_to') == $writer->id ? 'selected' : '' }}>
                                {{ $writer->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Laisser vide pour assigner plus tard</p>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                        Priorité <span class="text-red-500">*</span>
                    </label>
                    <select id="priority"
                            name="priority"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                        Date limite
                    </label>
                    <input type="date"
                           id="deadline"
                           name="deadline"
                           value="{{ old('deadline') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6">
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes internes (visibles uniquement par les admins)
                </label>
                <textarea id="admin_notes"
                          name="admin_notes"
                          rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Notes, instructions spéciales, contexte additionnel...">{{ old('admin_notes') }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.briefs.index') }}" class="text-gray-600 hover:text-gray-800">
                Annuler
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>Créer le Brief</span>
            </button>
        </div>
    </form>
</div>

<script>
// Convert comma-separated keywords to array on submit
document.getElementById('brief-form').addEventListener('submit', function(e) {
    // Convert keywords text to array
    const keywordsText = document.querySelector('[name="keywords_text"]').value;
    if (keywordsText) {
        const keywordsArray = keywordsText.split(',').map(k => k.trim()).filter(k => k);
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'keywords';
        input.value = JSON.stringify(keywordsArray);
        this.appendChild(input);
    }

    // Convert references text to array
    const referencesText = document.querySelector('[name="references_text"]').value;
    if (referencesText) {
        const referencesArray = referencesText.split('\n').map(r => r.trim()).filter(r => r);
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'references';
        input.value = JSON.stringify(referencesArray);
        this.appendChild(input);
    }

    // Convert content_requirements to proper format
    const structure = document.querySelector('[name="content_requirements[structure]"]').value;
    const tone = document.querySelector('[name="content_requirements[tone]"]').value;
    if (structure || tone) {
        const contentReqs = {};
        if (structure) contentReqs.structure = structure;
        if (tone) contentReqs.tone = tone;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'content_requirements';
        input.value = JSON.stringify(contentReqs);
        this.appendChild(input);
    }
});
</script>
@endsection
