@extends('layouts.writer')

@section('title', 'Nouvel article')

@push('head')
<!-- Editor.js Scripts - Bundles UMD -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest/dist/header.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest/dist/list.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest/dist/image.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest/dist/quote.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest/dist/code.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest/dist/table.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest/dist/delimiter.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest/dist/inline-code.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest/dist/embed.umd.min.js"></script>
@endpush

@push('styles')
<style>
    /* Editor.js - Style Nomadie */
    #editorjs {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 1.5rem;
        min-height: 500px;
    }

    .ce-block__content,
    .ce-toolbar__content {
        max-width: 800px;
    }

    .codex-editor__redactor {
        padding-bottom: 200px !important;
    }

    /* Personnalisation des blocs */
    .ce-paragraph {
        font-size: 16px;
        line-height: 1.6;
        color: #374151;
    }

    .ce-header {
        font-weight: 600;
        color: #1F2937;
    }

    .ce-header[contentEditable=true][data-placeholder]:empty::before {
        color: #9CA3AF;
    }

    /* H2 style Nomadie */
    .ce-header h2 {
        color: #38B2AC;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(56, 178, 172, 0.2);
    }

    .ce-header h3 {
        color: #2C9A94;
    }

    /* Images */
    .image-tool__image {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Citations */
    .cdx-quote {
        border-left: 4px solid #38B2AC;
        background: #F3F4F6;
        border-radius: 6px;
    }

    .cdx-quote__text {
        color: #6B7280;
        font-style: italic;
    }

    /* Listes */
    .cdx-list__item {
        color: #374151;
    }

    .cdx-list--unordered .cdx-list__item::before {
        background-color: #38B2AC;
    }

    /* Code */
    .ce-code__textarea {
        background: #F3F4F6;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        color: #EF4444;
    }

    /* Tables */
    .tc-table {
        border-collapse: collapse;
    }

    .tc-table th {
        background: #38B2AC;
        color: white;
        font-weight: 600;
    }

    .tc-table td,
    .tc-table th {
        border: 1px solid #E5E7EB;
        padding: 0.75rem;
    }

    .tc-table tr:hover {
        background: #F9FAFB;
    }

    /* Delimiter */
    .ce-delimiter {
        border-top: 2px solid #E5E7EB;
    }

    /* Toolbar */
    .ce-toolbar__plus,
    .ce-toolbar__settings-btn {
        color: #38B2AC;
    }

    .ce-toolbar__plus:hover,
    .ce-toolbar__settings-btn:hover {
        background: #38B2AC;
        color: white;
    }

    .ce-inline-toolbar {
        background: #38B2AC;
    }

    .ce-inline-tool {
        color: white;
    }

    .ce-inline-tool:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@section('content')
<div x-data="articleEditor()" class="max-w-7xl mx-auto px-4 py-8">
    <form method="POST" action="{{ route('writer.articles.store') }}" enctype="multipart/form-data" @submit="handleSubmit">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Formulaire principal (3/5) -->
            <div class="lg:col-span-3">
                <!-- Navigation par onglets -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="border-b border-border">
                        <nav class="flex -mb-px">
                            <button type="button"
                                    @click="activeTab = 'content'"
                                    :class="activeTab === 'content' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                                    class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Contenu
                            </button>
                            <button type="button"
                                    @click="activeTab = 'seo'"
                                    :class="activeTab === 'seo' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                                    class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                SEO
                            </button>
                            <button type="button"
                                    @click="activeTab = 'publish'"
                                    :class="activeTab === 'publish' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                                    class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Publication
                            </button>
                        </nav>
                    </div>

                    <!-- Contenu des onglets -->
                    <div class="p-6">
                        <!-- ONGLET CONTENU -->
                        <div x-show="activeTab === 'content'" class="space-y-6">
                            <!-- Titre -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-text-primary mb-2">
                                    Titre de l'article <span class="text-error">*</span>
                                </label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       x-model="article.title"
                                       @input="debounceAnalyze()"
                                       class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-lg"
                                       placeholder="Ex: Guide complet pour visiter Bali en 2024"
                                       required>
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-text-secondary">
                                        <span x-text="article.title.length"></span>/60 caractères recommandés
                                    </span>
                                    <span x-show="article.title.length >= 30 && article.title.length <= 60" class="text-success">✓ Longueur optimale</span>
                                    <span x-show="article.title.length > 60" class="text-error">Titre trop long</span>
                                </div>
                            </div>

                            <!-- Sous-titre -->
                            <div>
                                <label for="subtitle" class="block text-sm font-medium text-text-primary mb-2">
                                    Sous-titre
                                </label>
                                <input type="text"
                                       id="subtitle"
                                       name="subtitle"
                                       x-model="article.subtitle"
                                       class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="Ex: Découvrez les meilleurs spots et conseils pratiques">
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-text-secondary">
                                        <span x-text="article.subtitle ? article.subtitle.length : 0"></span>/120 caractères recommandés
                                    </span>
                                    <span x-show="article.subtitle && article.subtitle.length >= 50 && article.subtitle.length <= 120" class="text-success">✓ Longueur optimale</span>
                                    <span x-show="article.subtitle && article.subtitle.length > 120" class="text-error">Sous-titre trop long</span>
                                </div>
                            </div>

                            <!-- Image à la une -->
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">
                                    Image à la une
                                </label>
                                <input type="file"
                                       id="featured_image"
                                       name="featured_image"
                                       @change="handleImageUpload"
                                       accept="image/*"
                                       class="hidden"
                                       x-ref="imageInput">

                                <div @click="$refs.imageInput.click()"
                                     class="relative border-2 border-dashed border-border rounded-lg p-6 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all group">
                                    <template x-if="!imagePreview">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="mt-2 text-sm text-text-secondary">
                                                Cliquez pour télécharger une image
                                            </p>
                                            <p class="text-xs text-text-secondary mt-1">
                                                PNG, JPG jusqu'à 2MB
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="imagePreview">
                                        <div class="relative">
                                            <img :src="imagePreview" alt="Preview" class="max-h-64 mx-auto rounded-lg">
                                            <button type="button"
                                                    @click.stop="imagePreview = null; $refs.imageInput.value = ''"
                                                    class="absolute top-2 right-2 bg-error text-white rounded-full p-2 hover:bg-red-600 transition-colors shadow-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Éditeur de contenu -->
                            <div>
                                <label for="editorjs" class="block text-sm font-medium text-text-primary mb-2">
                                    Contenu de l'article <span class="text-error">*</span>
                                </label>
                                <div id="editorjs"></div>
                                <input type="hidden" name="content" x-model="article.content" required>
                                <div class="mt-2 flex items-center justify-between text-xs text-text-secondary">
                                    <span>
                                        <span x-text="wordCount" class="font-medium"></span> mots
                                        <span x-show="wordCount >= 1500" class="text-success ml-2">✓ Longueur excellente</span>
                                        <span x-show="wordCount < 1500 && wordCount > 0" class="text-accent ml-2">⚠ Min. 1500 mots recommandés</span>
                                    </span>
                                    <span x-text="readingTime + ' min de lecture'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ONGLET SEO -->
                        <div x-show="activeTab === 'seo'" class="space-y-6">
                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-text-primary mb-2">
                                    Meta Description (pour Google)
                                </label>
                                <textarea id="meta_description"
                                          name="meta_description"
                                          x-model="article.meta_description"
                                          @input="debounceAnalyze()"
                                          rows="3"
                                          maxlength="160"
                                          class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                                          placeholder="Résumé court de votre article qui apparaîtra dans les résultats Google (120-160 caractères recommandés)"></textarea>
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-text-secondary">
                                        <span x-text="article.meta_description.length"></span>/160 caractères
                                    </span>
                                    <span x-show="article.meta_description.length >= 120 && article.meta_description.length <= 160" class="text-success">✓ Longueur optimale</span>
                                </div>
                            </div>

                            <!-- URL (Slug) -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-text-primary mb-2">
                                    URL de l'article
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-text-secondary">{{ url('/blog') }}/</span>
                                    <input type="text"
                                           id="slug"
                                           name="slug"
                                           x-model="article.slug"
                                           class="flex-1 px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                           placeholder="mon-article-voyage">
                                </div>
                                <p class="mt-1 text-xs text-text-secondary">Laissez vide pour générer automatiquement depuis le titre</p>
                            </div>

                            <!-- Catégorie et Tags -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="category" class="block text-sm font-medium text-text-primary mb-2">
                                        Catégorie
                                    </label>
                                    <input type="text"
                                           id="category"
                                           name="category"
                                           list="category-suggestions"
                                           x-model="article.category"
                                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                           placeholder="Choisir ou créer une catégorie...">
                                    <datalist id="category-suggestions">
                                        <option value="Destinations">
                                        <option value="Conseils de voyage">
                                        <option value="Gastronomie">
                                        <option value="Écotourisme">
                                        <option value="Culture & Traditions">
                                        <option value="Activités & Sports">
                                    </datalist>
                                    <p class="mt-1 text-xs text-text-secondary">💡 Vous pouvez créer une nouvelle catégorie</p>
                                </div>

                                <div>
                                    <label for="tags" class="block text-sm font-medium text-text-primary mb-2">
                                        Étiquettes (Tags)
                                    </label>
                                    <input type="text"
                                           id="tags"
                                           name="tags"
                                           list="tag-suggestions"
                                           x-model="article.tags"
                                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                           placeholder="voyage, bali, plage">
                                    <datalist id="tag-suggestions">
                                        <option value="voyage">
                                        <option value="destination">
                                        <option value="plage">
                                        <option value="culture">
                                        <option value="gastronomie">
                                        <option value="écotourisme">
                                        <option value="aventure">
                                        <option value="famille">
                                        <option value="budget">
                                        <option value="luxe">
                                    </datalist>
                                    <p class="mt-1 text-xs text-text-secondary">💡 Séparez les tags par des virgules - vous pouvez créer de nouveaux tags</p>
                                </div>
                            </div>

                            <!-- Mots-clés -->
                            <div>
                                <label for="keywords" class="block text-sm font-medium text-text-primary mb-2">
                                    Mots-clés SEO
                                </label>
                                <input type="text"
                                       id="keywords"
                                       name="keywords"
                                       class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                       placeholder="voyage bali, que faire à bali, guide bali">
                                <p class="mt-1 text-xs text-text-secondary">Mots-clés principaux pour le référencement</p>
                            </div>
                        </div>

                        <!-- ONGLET PUBLICATION -->
                        <div x-show="activeTab === 'publish'" class="space-y-6">
                            <!-- Statut -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-text-primary mb-2">
                                    Statut de publication
                                </label>
                                <select id="status"
                                        name="status"
                                        x-model="article.status"
                                        class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <option value="draft">📝 Brouillon - Sauvegarder sans publier</option>
                                    <option value="scheduled">📅 Planifié - Publier à une date précise</option>
                                    <option value="pending">⏳ En attente - Soumettre pour validation</option>
                                    <option value="published" :disabled="seoScore < 78">
                                        ✅ Publié - Mise en ligne immédiate
                                        <span x-show="seoScore < 78">(Score SEO insuffisant: <span x-text="seoScore"></span>/78)</span>
                                    </option>
                                </select>
                            </div>

                            <!-- Date de planification (affiché si statut = scheduled) -->
                            <div x-show="article.status === 'scheduled'" x-transition>
                                <label for="scheduled_at" class="block text-sm font-medium text-text-primary mb-2">
                                    Date et heure de publication
                                </label>
                                <input type="datetime-local"
                                       id="scheduled_at"
                                       name="scheduled_at"
                                       x-model="article.scheduled_at"
                                       :min="minDateTime"
                                       class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <p class="mt-1 text-xs text-text-secondary">L'article sera automatiquement publié à cette date</p>
                            </div>

                            <!-- Résumé avant publication -->
                            <div class="bg-bg-alt rounded-lg p-4 border border-border">
                                <h3 class="font-medium text-text-primary mb-3 flex items-center">
                                    <svg class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Récapitulatif
                                </h3>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-text-secondary">Score SEO:</dt>
                                        <dd class="font-medium" :class="{
                                            'text-success': seoScore >= 78,
                                            'text-accent': seoScore >= 50 && seoScore < 78,
                                            'text-error': seoScore < 50
                                        }">
                                            <span x-text="seoScore"></span>/100
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-text-secondary">Nombre de mots:</dt>
                                        <dd class="font-medium text-text-primary" x-text="wordCount"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-text-secondary">Temps de lecture:</dt>
                                        <dd class="font-medium text-text-primary" x-text="readingTime + ' min'"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-text-secondary">Catégorie:</dt>
                                        <dd class="font-medium text-text-primary" x-text="article.category || 'Non définie'"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-text-secondary">Éligible DoFollow:</dt>
                                        <dd class="font-medium" :class="seoScore >= 78 ? 'text-success' : 'text-text-secondary'">
                                            <span x-show="seoScore >= 78">✓ Oui</span>
                                            <span x-show="seoScore < 78">✗ Non</span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
                    <a href="{{ route('writer.articles.index') }}"
                       class="text-text-secondary hover:text-text-primary transition-colors flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Annuler
                    </a>

                    <div class="flex gap-3">
                        <button type="submit"
                                @click="article.status = 'draft'"
                                class="px-6 py-2 border border-border rounded-lg text-text-primary hover:bg-bg-alt transition-colors">
                            Sauvegarder brouillon
                        </button>
                        <button type="submit"
                                :disabled="seoScore < 78 && article.status === 'published'"
                                class="px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="article.status === 'draft'">Sauvegarder</span>
                            <span x-show="article.status === 'scheduled'">Planifier</span>
                            <span x-show="article.status === 'pending'">Soumettre</span>
                            <span x-show="article.status === 'published'">Publier</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Analyse SEO (2/5) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm sticky top-6">
                    @if(auth()->user()->writer_type === 'team')
                        <!-- Version Équipe Nomadie - Focus qualité -->
                        <div class="bg-primary p-4 border-b border-primary/20">
                            <h3 class="text-lg font-bold flex items-center text-white">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Qualité de l'article
                            </h3>
                            <p class="text-sm text-white/90 mt-1">Indicateurs en temps réel</p>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Badge DoFollow discret -->
                            <div class="bg-green-50 border border-green-300 rounded-lg p-3 text-center">
                                <p class="text-xs text-green-600 flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    DoFollow activé automatiquement
                                </p>
                            </div>

                            <!-- Indicateurs de qualité -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-text-primary">Indicateurs de qualité</h4>

                                <!-- Titre -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-text-secondary">Titre</span>
                                        <span class="text-xs" :class="{
                                            'text-success': scores.title >= 15,
                                            'text-accent': scores.title >= 10 && scores.title < 15,
                                            'text-text-secondary': scores.title < 10
                                        }">
                                            <span x-text="scores.title >= 15 ? '✓ Optimal' : scores.title >= 10 ? '⚠ À améliorer' : '· Incomplet'"></span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                             :style="`width: ${scores.title * 5}%`"></div>
                                    </div>
                                </div>

                                <!-- Contenu -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-text-secondary">Contenu</span>
                                        <span class="text-xs font-medium text-text-primary" x-text="wordCount + ' mots'"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                             :style="`width: ${Math.min(100, scores.content * 3.33)}%`"></div>
                                    </div>
                                    <p class="text-xs text-text-secondary" x-show="wordCount < 1500">
                                        Recommandé : 1500 mots minimum
                                    </p>
                                </div>

                                <!-- Meta Description -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-text-secondary">Meta Description</span>
                                        <span class="text-xs" :class="{
                                            'text-success': scores.meta >= 12,
                                            'text-accent': scores.meta >= 8 && scores.meta < 12,
                                            'text-text-secondary': scores.meta < 8
                                        }">
                                            <span x-text="scores.meta >= 12 ? '✓' : scores.meta >= 8 ? '⚠' : '·'"></span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                             :style="`width: ${scores.meta * 6.67}%`"></div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-text-secondary">Images</span>
                                        <span class="text-xs" :class="{
                                            'text-success': scores.images >= 12,
                                            'text-accent': scores.images >= 8 && scores.images < 12,
                                            'text-text-secondary': scores.images < 8
                                        }">
                                            <span x-text="scores.images >= 12 ? '✓' : scores.images >= 8 ? '⚠' : '·'"></span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                             :style="`width: ${scores.images * 6.67}%`"></div>
                                    </div>
                                </div>

                                <!-- Lisibilité -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-text-secondary">Lisibilité</span>
                                        <span class="text-xs" :class="{
                                            'text-success': scores.readability >= 15,
                                            'text-accent': scores.readability >= 10 && scores.readability < 15,
                                            'text-text-secondary': scores.readability < 10
                                        }">
                                            <span x-text="scores.readability >= 15 ? '✓' : scores.readability >= 10 ? '⚠' : '·'"></span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                             :style="`width: ${scores.readability * 5}%`"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Temps de lecture -->
                            <div class="border-t border-border pt-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary">Temps de lecture</span>
                                    <span class="font-medium text-indigo-600" x-text="readingTime + ' min'"></span>
                                </div>
                            </div>

                            <!-- Suggestions qualité -->
                            <div x-show="allSuggestions.length > 0" class="border-t border-border pt-4">
                                <h4 class="text-sm font-medium text-text-primary mb-3">💡 Suggestions qualité</h4>
                                <ul class="space-y-2">
                                    <template x-for="suggestion in allSuggestions.slice(0, 3)" :key="suggestion">
                                        <li class="flex items-start text-xs text-text-secondary">
                                            <svg class="h-4 w-4 mr-2 mt-0.5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span x-text="suggestion"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    @else
                        <!-- Version standard avec analyse SEO -->
                        <div class="bg-gradient-to-r from-primary to-primary-dark p-4 text-white">
                            <h3 class="text-lg font-bold flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Analyse SEO
                            </h3>
                            <p class="text-sm opacity-90 mt-1">En temps réel</p>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Score global -->
                        <div class="text-center">
                            <div class="relative inline-flex">
                                <svg class="transform -rotate-90 w-32 h-32">
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200"></circle>
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none"
                                            :stroke-dasharray="351.86"
                                            :stroke-dashoffset="351.86 - (351.86 * seoScore / 100)"
                                            :class="{
                                                'text-error': seoScore < 50,
                                                'text-accent': seoScore >= 50 && seoScore < 78,
                                                'text-success': seoScore >= 78
                                            }"
                                            class="transition-all duration-500"></circle>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-3xl font-bold"
                                          :class="{
                                              'text-error': seoScore < 50,
                                              'text-accent': seoScore >= 50 && seoScore < 78,
                                              'text-success': seoScore >= 78
                                          }">
                                        <span x-text="seoScore"></span>
                                    </span>
                                </span>
                            </div>
                            <p class="mt-3 text-sm font-medium"
                               :class="{
                                   'text-error': seoScore < 50,
                                   'text-accent': seoScore >= 50 && seoScore < 78,
                                   'text-success': seoScore >= 78
                               }">
                                <span x-show="seoScore >= 78">✓ Éligible DoFollow</span>
                                <span x-show="seoScore >= 50 && seoScore < 78">Améliorable</span>
                                <span x-show="seoScore < 50">À améliorer</span>
                            </p>
                            <p class="text-xs text-text-secondary mt-1">
                                Min. 78/100 pour le DoFollow
                            </p>
                        </div>

                        <!-- Scores détaillés -->
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Titre</span>
                                    <span class="text-xs font-bold text-text-primary" x-text="scores.title + '/20'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-primary h-1.5 rounded-full transition-all duration-300"
                                         :style="`width: ${scores.title * 5}%`"></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Contenu</span>
                                    <span class="text-xs font-bold text-text-primary" x-text="scores.content + '/30'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-accent h-1.5 rounded-full transition-all duration-300"
                                         :style="`width: ${scores.content * 3.33}%`"></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Meta Description</span>
                                    <span class="text-xs font-bold text-text-primary" x-text="scores.meta + '/15'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-success h-1.5 rounded-full transition-all duration-300"
                                         :style="`width: ${scores.meta * 6.67}%`"></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Images</span>
                                    <span class="text-xs font-bold text-text-primary" x-text="scores.images + '/15'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-yellow-500 h-1.5 rounded-full transition-all duration-300"
                                         :style="`width: ${scores.images * 6.67}%`"></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Lisibilité</span>
                                    <span class="text-xs font-bold text-text-primary" x-text="scores.readability + '/20'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300"
                                         :style="`width: ${scores.readability * 5}%`"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Suggestions -->
                        <div x-show="allSuggestions.length > 0" class="border-t border-border pt-4">
                            <h4 class="text-sm font-medium text-text-primary mb-3">Recommandations</h4>
                            <ul class="space-y-2">
                                <template x-for="suggestion in allSuggestions.slice(0, 5)" :key="suggestion">
                                    <li class="flex items-start text-xs text-text-secondary">
                                        <svg class="h-4 w-4 mr-2 mt-0.5 text-accent flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="suggestion"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function articleEditor() {
    return {
        activeTab: 'content',
        article: {
            title: '',
            subtitle: '',
            content: '',
            meta_description: '',
            slug: '',
            category: '',
            tags: '',
            status: 'draft',
            scheduled_at: ''
        },
        imagePreview: null,
        seoScore: 0,
        scores: {
            title: 0,
            content: 0,
            meta: 0,
            images: 0,
            readability: 0
        },
        wordCount: 0,
        readingTime: 0,
        allSuggestions: [],
        suggestions: {
            title: [],
            content: [],
            meta: [],
            images: [],
            readability: []
        },
        analyzeTimeout: null,
        minDateTime: '',

        editor: null,

        init() {
            // Initialiser Editor.js
            this.editor = new EditorJS({
                holder: 'editorjs',
                autofocus: true,
                placeholder: 'Commencez à écrire votre article...',

                tools: {
                    header: {
                        class: Header,
                        config: {
                            placeholder: 'Titre de section',
                            levels: [2, 3, 4],
                            defaultLevel: 2
                        },
                        inlineToolbar: true
                    },

                    list: {
                        class: List,
                        inlineToolbar: true,
                        config: {
                            defaultStyle: 'unordered'
                        }
                    },

                    image: {
                        class: ImageTool,
                        config: {
                            endpoints: {
                                byFile: '/writer/articles/upload-image',
                            },
                            additionalRequestHeaders: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            field: 'image',
                            types: 'image/*',
                            captionPlaceholder: 'Légende de l\'image',
                            buttonContent: 'Sélectionner une image',
                            uploader: {
                                uploadByFile(file) {
                                    return new Promise((resolve, reject) => {
                                        const formData = new FormData();
                                        formData.append('image', file);

                                        fetch('/writer/articles/upload-image', {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            resolve({
                                                success: 1,
                                                file: {
                                                    url: data.location
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            reject(error);
                                        });
                                    });
                                }
                            }
                        }
                    },

                    quote: {
                        class: Quote,
                        inlineToolbar: true,
                        config: {
                            quotePlaceholder: 'Citation',
                            captionPlaceholder: 'Auteur'
                        }
                    },

                    code: {
                        class: CodeTool,
                        config: {
                            placeholder: 'Entrez votre code...'
                        }
                    },

                    table: {
                        class: Table,
                        inlineToolbar: true,
                        config: {
                            rows: 2,
                            cols: 3,
                        }
                    },

                    delimiter: Delimiter,

                    embed: {
                        class: Embed,
                        config: {
                            services: {
                                youtube: true,
                                vimeo: true,
                                instagram: true,
                                twitter: true,
                            }
                        }
                    },

                    inlineCode: {
                        class: InlineCode
                    }
                },

                onChange: async () => {
                    await this.saveEditorContent();
                },

                i18n: {
                    messages: {
                        ui: {
                            "blockTunes": {
                                "toggler": {
                                    "Click to tune": "Cliquer pour configurer",
                                    "Click to delete": "Cliquer pour supprimer"
                                }
                            },
                            "inlineToolbar": {
                                "converter": {
                                    "Convert to": "Convertir en"
                                }
                            },
                            "toolbar": {
                                "toolbox": {
                                    "Add": "Ajouter",
                                    "Filter": "Filtrer"
                                }
                            },
                            "popover": {
                                "Filter": "Filtrer",
                                "Nothing found": "Rien trouvé"
                            }
                        },
                        toolNames: {
                            "Text": "Paragraphe",
                            "Heading": "Titre",
                            "List": "Liste",
                            "Quote": "Citation",
                            "Code": "Code",
                            "Delimiter": "Séparateur",
                            "Table": "Tableau",
                            "Image": "Image",
                            "Embed": "Intégration",
                            "Bold": "Gras",
                            "Italic": "Italique",
                            "Link": "Lien",
                            "Inline Code": "Code en ligne"
                        },
                        tools: {
                            "header": {
                                "Header": "Titre",
                                "Heading 1": "Titre 1",
                                "Heading 2": "Titre 2",
                                "Heading 3": "Titre 3",
                                "Heading 4": "Titre 4"
                            },
                            "list": {
                                "Ordered": "Numérotée",
                                "Unordered": "À puces"
                            },
                            "link": {
                                "Add a link": "Ajouter un lien"
                            },
                            "stub": {
                                "The block can not be displayed correctly.": "Le bloc ne peut pas être affiché correctement."
                            },
                            "image": {
                                "Caption": "Légende",
                                "Select an Image": "Sélectionner une image",
                                "With border": "Avec bordure",
                                "Stretch image": "Étirer l'image",
                                "With background": "Avec arrière-plan"
                            },
                            "code": {
                                "Code": "Code"
                            },
                            "table": {
                                "Table": "Tableau",
                                "With headings": "Avec en-têtes",
                                "Without headings": "Sans en-têtes",
                                "Add row above": "Ajouter une ligne au-dessus",
                                "Add row below": "Ajouter une ligne en-dessous",
                                "Add column to left": "Ajouter une colonne à gauche",
                                "Add column to right": "Ajouter une colonne à droite",
                                "Delete row": "Supprimer la ligne",
                                "Delete column": "Supprimer la colonne"
                            },
                            "quote": {
                                "Quote": "Citation"
                            }
                        },
                        blockTunes: {
                            "delete": {
                                "Delete": "Supprimer",
                                "Click to delete": "Cliquer pour supprimer"
                            },
                            "moveUp": {
                                "Move up": "Monter"
                            },
                            "moveDown": {
                                "Move down": "Descendre"
                            }
                        }
                    }
                }
            });

            // Date minimum pour planification (maintenant)
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            this.minDateTime = now.toISOString().slice(0, 16);
        },

        async saveEditorContent() {
            if (!this.editor) return;

            try {
                const outputData = await this.editor.save();

                // Convertir JSON en HTML
                const html = this.convertEditorDataToHTML(outputData);
                this.article.content = html;

                // Analyser SEO
                this.debounceAnalyze();
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
            }
        },

        convertEditorDataToHTML(data) {
            if (!data || !data.blocks) return '';

            return data.blocks.map(block => {
                switch(block.type) {
                    case 'header':
                        return `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;

                    case 'paragraph':
                        return `<p>${block.data.text}</p>`;

                    case 'list':
                        const tag = block.data.style === 'ordered' ? 'ol' : 'ul';
                        const items = block.data.items.map(item => `<li>${item}</li>`).join('');
                        return `<${tag}>${items}</${tag}>`;

                    case 'image':
                        const caption = block.data.caption ? `<figcaption>${block.data.caption}</figcaption>` : '';
                        return `<figure><img src="${block.data.file.url}" alt="${block.data.caption || ''}" />${caption}</figure>`;

                    case 'quote':
                        const cite = block.data.caption ? `<cite>${block.data.caption}</cite>` : '';
                        return `<blockquote>${block.data.text}${cite}</blockquote>`;

                    case 'code':
                        return `<pre><code>${block.data.code}</code></pre>`;

                    case 'table':
                        const rows = block.data.content.map((row, idx) => {
                            const tag = idx === 0 && block.data.withHeadings ? 'th' : 'td';
                            const cells = row.map(cell => `<${tag}>${cell}</${tag}>`).join('');
                            return `<tr>${cells}</tr>`;
                        }).join('');
                        return `<table>${rows}</table>`;

                    case 'delimiter':
                        return '<hr />';

                    case 'embed':
                        return block.data.embed || '';

                    default:
                        return '';
                }
            }).join('\n');
        },

        debounceAnalyze() {
            clearTimeout(this.analyzeTimeout);
            this.analyzeTimeout = setTimeout(() => this.analyzeSEO(), 500);
        },

        analyzeSEO() {
            // Calculer nombre de mots
            const plainText = this.article.content.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            this.wordCount = plainText.split(' ').filter(word => word.length > 0).length;
            this.readingTime = Math.max(1, Math.ceil(this.wordCount / 200));

            // Reset suggestions
            this.suggestions = {
                title: [],
                content: [],
                meta: [],
                images: [],
                readability: []
            };

            // Analyse titre
            let titleScore = 0;
            if (this.article.title) {
                if (this.article.title.length >= 30 && this.article.title.length <= 60) {
                    titleScore = 20;
                } else if (this.article.title.length >= 20) {
                    titleScore = 12;
                    this.suggestions.title.push(this.article.title.length < 30 ?
                        'Titre trop court (30-60 caractères recommandés)' :
                        'Titre trop long (max. 60 caractères)');
                } else {
                    titleScore = 5;
                    this.suggestions.title.push('Titre trop court');
                }
            } else {
                this.suggestions.title.push('Ajoutez un titre');
            }
            this.scores.title = titleScore;

            // Analyse contenu
            let contentScore = 0;
            if (this.wordCount >= 1500) {
                contentScore = 30;
            } else if (this.wordCount >= 1000) {
                contentScore = 20;
                this.suggestions.content.push(`Ajoutez ${1500 - this.wordCount} mots pour atteindre l'optimal`);
            } else if (this.wordCount > 0) {
                contentScore = 10;
                this.suggestions.content.push('Contenu trop court (min. 1500 mots)');
            } else {
                this.suggestions.content.push('Commencez à rédiger votre article');
            }
            this.scores.content = contentScore;

            // Meta description
            let metaScore = 0;
            if (this.article.meta_description) {
                if (this.article.meta_description.length >= 120 && this.article.meta_description.length <= 160) {
                    metaScore = 15;
                } else if (this.article.meta_description.length >= 80) {
                    metaScore = 10;
                    this.suggestions.meta.push(this.article.meta_description.length < 120 ?
                        'Meta description trop courte (120-160 car.)' :
                        'Meta description trop longue (max. 160 car.)');
                } else {
                    metaScore = 5;
                    this.suggestions.meta.push('Meta description trop courte');
                }
            } else {
                this.suggestions.meta.push('Ajoutez une meta description');
            }
            this.scores.meta = metaScore;

            // Images
            let imagesScore = 0;
            if (this.imagePreview) {
                imagesScore += 8;
            } else {
                this.suggestions.images.push('Ajoutez une image à la une');
            }
            const contentImages = (this.article.content.match(/<img/gi) || []).length;
            imagesScore += Math.min(7, contentImages * 2);
            if (contentImages === 0 && this.wordCount > 500) {
                this.suggestions.images.push('Ajoutez des images dans le contenu');
            }
            this.scores.images = imagesScore;

            // Lisibilité
            let readabilityScore = 15;
            const hasH2 = /<h2/i.test(this.article.content);
            const hasH3 = /<h3/i.test(this.article.content);
            if (!hasH2 && this.wordCount > 300) {
                this.suggestions.readability.push('Ajoutez des sous-titres (H2)');
                readabilityScore -= 5;
            }
            if (!hasH3 && this.wordCount > 800) {
                readabilityScore -= 3;
            }
            const hasList = /<ul|<ol/i.test(this.article.content);
            if (!hasList && this.wordCount > 500) {
                this.suggestions.readability.push('Ajoutez des listes à puces');
                readabilityScore -= 2;
            }
            this.scores.readability = Math.max(0, readabilityScore);

            // Score total
            this.seoScore = Math.round(
                this.scores.title +
                this.scores.content +
                this.scores.meta +
                this.scores.images +
                this.scores.readability
            );

            // Compiler suggestions
            this.allSuggestions = [
                ...this.suggestions.title,
                ...this.suggestions.content,
                ...this.suggestions.meta,
                ...this.suggestions.images,
                ...this.suggestions.readability
            ].slice(0, 5);
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner une image');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
                this.debounceAnalyze();
            };
            reader.readAsDataURL(file);
        },

        async handleSubmit(e) {
            // Sauvegarder le contenu Editor.js avant soumission
            if (this.editor) {
                await this.saveEditorContent();
            }

            // Vérifier qu'il y a du contenu
            if (!this.article.content || this.article.content.trim() === '') {
                e.preventDefault();
                alert('Veuillez ajouter du contenu à votre article');
                return false;
            }
        }
    };
}
</script>
@endsection
