@extends('layouts.admin')

@section('title', 'Modifier Brief - ' . $brief->title)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier le Brief</h1>
            <p class="text-gray-600 mt-1">{{ $brief->title }}</p>
        </div>
        <a href="{{ route('admin.briefs.show', $brief) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            ← Retour
        </a>
    </div>

    <!-- Brief Form -->
    <form method="POST" action="{{ route('admin.briefs.update', $brief) }}" class="space-y-6">
        @csrf
        @method('PUT')

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
                           value="{{ old('title', $brief->title) }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                        <option value="destination" {{ old('type', $brief->type) === 'destination' ? 'selected' : '' }}>Destination</option>
                        <option value="guide_pratique" {{ old('type', $brief->type) === 'guide_pratique' ? 'selected' : '' }}>Guide Pratique</option>
                        <option value="culture" {{ old('type', $brief->type) === 'culture' ? 'selected' : '' }}>Culture</option>
                        <option value="gastronomie" {{ old('type', $brief->type) === 'gastronomie' ? 'selected' : '' }}>Gastronomie</option>
                        <option value="hebergement" {{ old('type', $brief->type) === 'hebergement' ? 'selected' : '' }}>Hébergement</option>
                        <option value="transport" {{ old('type', $brief->type) === 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="budget" {{ old('type', $brief->type) === 'budget' ? 'selected' : '' }}>Budget</option>
                        <option value="custom" {{ old('type', $brief->type) === 'custom' ? 'selected' : '' }}>Personnalisé</option>
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
                           value="{{ old('category', $brief->category) }}"
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
                              placeholder="Contexte, angle éditorial, public cible...">{{ old('description', $brief->description) }}</textarea>
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
                              placeholder="Listez les sections attendues...">{{ old('content_requirements.structure', isset($brief->content_requirements['structure']) ? (is_array($brief->content_requirements['structure']) ? json_encode($brief->content_requirements['structure'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $brief->content_requirements['structure']) : (isset($brief->content_requirements['sections']) ? json_encode($brief->content_requirements['sections'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '')) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ton & Style
                    </label>
                    <input type="text"
                           name="content_requirements[tone]"
                           value="{{ old('content_requirements.tone', $brief->content_requirements['tone'] ?? $brief->content_requirements['ton'] ?? '') }}"
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
                               value="{{ old('min_words', $brief->min_words) }}"
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
                               value="{{ old('target_score', $brief->target_score) }}"
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
                              placeholder="Séparez les mots-clés par des virgules...">{{ old('keywords_text', $brief->keywords ? implode(', ', $brief->keywords) : '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Ex: tokyo, japon, voyage tokyo, visiter tokyo, guide tokyo</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Références / URLs sources
                    </label>
                    <textarea name="references_text"
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                              placeholder="URLs de référence, une par ligne...">{{ old('references_text', $brief->references ? implode("\n", $brief->references) : '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Sites concurrents, sources officielles, articles de référence...</p>
                </div>
            </div>
        </div>

        <!-- Workflow Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">⚙️ Paramètres</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                        Priorité <span class="text-red-500">*</span>
                    </label>
                    <select id="priority"
                            name="priority"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="low" {{ old('priority', $brief->priority) === 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="normal" {{ old('priority', $brief->priority) === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ old('priority', $brief->priority) === 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority', $brief->priority) === 'urgent' ? 'selected' : '' }}>Urgente</option>
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
                           value="{{ old('deadline', $brief->deadline ? $brief->deadline->format('Y-m-d') : '') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Statut
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $brief->status) === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="assigned" {{ old('status', $brief->status) === 'assigned' ? 'selected' : '' }}>Assigné</option>
                        <option value="in_progress" {{ old('status', $brief->status) === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="pending_review" {{ old('status', $brief->status) === 'pending_review' ? 'selected' : '' }}>En attente de review</option>
                        <option value="revision_requested" {{ old('status', $brief->status) === 'revision_requested' ? 'selected' : '' }}>Révision demandée</option>
                        <option value="completed" {{ old('status', $brief->status) === 'completed' ? 'selected' : '' }}>Complété</option>
                        <option value="cancelled" {{ old('status', $brief->status) === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                    </select>
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
                          placeholder="Notes, instructions spéciales, contexte additionnel...">{{ old('admin_notes', $brief->admin_notes) }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ route('admin.briefs.show', $brief) }}" class="text-gray-600 hover:text-gray-800">
                    Annuler
                </a>
                <form method="POST" action="{{ route('admin.briefs.destroy', $brief) }}" onsubmit="return confirm('Supprimer définitivement ce brief ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
            <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>Enregistrer les modifications</span>
            </button>
        </div>
    </form>
</div>

<script>
// Convert comma-separated keywords to array on submit
document.querySelector('form').addEventListener('submit', function(e) {
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
