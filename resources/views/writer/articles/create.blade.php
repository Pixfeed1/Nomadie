@extends('layouts.writer')

@section('title', 'Nouvel article')

@section('content')
<div x-data="articleEditor()" class="max-w-7xl mx-auto px-4 py-8">
    <form method="POST" action="{{ route('writer.articles.store') }}" enctype="multipart/form-data" @submit="handleSubmit">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulaire principal (2/3) -->
            <div class="lg:col-span-2">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h1 class="text-2xl font-bold text-text-primary">Créer un article</h1>
                    <p class="text-sm text-text-secondary mt-1">Rédigez et optimisez votre contenu</p>
                </div>

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
                                <label for="content" class="block text-sm font-medium text-text-primary mb-2">
                                    Contenu de l'article <span class="text-error">*</span>
                                </label>
                                <textarea id="content"
                                          name="content"
                                          x-model="article.content"
                                          class="w-full"
                                          style="height: 500px;"></textarea>
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
                                    <select id="category"
                                            name="category"
                                            x-model="article.category"
                                            class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        <option value="">Sélectionner...</option>
                                        <option value="destinations">Destinations</option>
                                        <option value="conseils">Conseils de voyage</option>
                                        <option value="gastronomie">Gastronomie</option>
                                        <option value="ecotourisme">Écotourisme</option>
                                        <option value="culture">Culture & Traditions</option>
                                        <option value="activites">Activités & Sports</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="tags" class="block text-sm font-medium text-text-primary mb-2">
                                        Tags
                                    </label>
                                    <input type="text"
                                           id="tags"
                                           name="tags"
                                           x-model="article.tags"
                                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                           placeholder="voyage, bali, plage">
                                    <p class="mt-1 text-xs text-text-secondary">Séparez les tags par des virgules</p>
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

            <!-- Sidebar Analyse SEO (1/3) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm sticky top-6">
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
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TinyMCE et JavaScript -->
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
function articleEditor() {
    return {
        activeTab: 'content',
        article: {
            title: '',
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

        init() {
            // Initialiser TinyMCE en mode visuel WordPress
            tinymce.init({
                selector: '#content',
                license_key: 'gpl',
                base_url: '/vendor/tinymce',
                suffix: '.min',
                height: 500,
                menubar: 'edit view insert format tools table',
                menu: {
                    edit: { title: 'Édition', items: 'undo redo | cut copy paste | selectall | searchreplace' },
                    view: { title: 'Affichage', items: 'code | visualaid visualchars visualblocks | preview fullscreen' },
                    insert: { title: 'Insérer', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor | insertdatetime' },
                    format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | styles blocks fontfamily fontsize align lineheight | forecolor backcolor | language | removeformat' },
                    tools: { title: 'Outils', items: 'spellchecker spellcheckerlanguage | a11ycheck code wordcount' },
                    table: { title: 'Tableau', items: 'inserttable | cell row column | advtablesort | tableprops deletetable' }
                },
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'visualchars', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
                    'codesample', 'quickbars', 'autoresize'
                ],
                toolbar: 'undo redo | styles | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | emoticons charmap | removeformat code fullscreen',
                toolbar_mode: 'sliding',
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
                quickbars_insert_toolbar: 'quickimage quicktable',
                contextmenu: 'link image table',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; padding: 1rem; } img { max-width: 100%; height: auto; border-radius: 8px; }',
                language: 'fr_FR',

                // Options images améliorées
                images_upload_url: '/writer/articles/upload-image',
                automatic_uploads: true,
                image_caption: true,
                image_title: true,
                image_description: true,
                image_advtab: true,
                file_picker_types: 'image',

                // Palette de couleurs personnalisée Nomadie
                color_map: [
                    '#38B2AC', 'Primary (Teal)',
                    '#2C9A94', 'Primary Dark',
                    '#F59E0B', 'Accent (Amber)',
                    '#10B981', 'Success (Green)',
                    '#EF4444', 'Error (Red)',
                    '#1F2937', 'Text Primary',
                    '#6B7280', 'Text Secondary',
                    '#FFFFFF', 'White',
                    '#000000', 'Black',
                    '#F3F4F6', 'Background',
                    '#3B82F6', 'Blue',
                    '#8B5CF6', 'Purple',
                    '#EC4899', 'Pink',
                    '#F59E0B', 'Orange',
                ],
                color_cols: 7,
                // Upload d'images amélioré
                images_upload_handler: function (blobInfo, success, failure, progress) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/writer/articles/upload-image');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

                    xhr.upload.onprogress = function(e) {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403) {
                            failure('HTTP Error: ' + xhr.status, { remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            failure('HTTP Error: ' + xhr.status);
                            return;
                        }
                        const json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            failure('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        success(json.location);
                    };

                    xhr.onerror = function () {
                        failure('Erreur lors du téléchargement de l\'image. Code: ' + xhr.status);
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },

                // Style du dialogue d'insertion d'image
                image_class_list: [
                    {title: 'Responsive', value: 'img-responsive'},
                    {title: 'Arrondie', value: 'rounded-lg'},
                    {title: 'Ombre', value: 'shadow-lg'},
                    {title: 'Centrée', value: 'mx-auto block'}
                ],
                setup: (editor) => {
                    editor.on('change keyup', () => {
                        this.article.content = editor.getContent();
                        this.debounceAnalyze();
                    });
                }
            });

            // Date minimum pour planification (maintenant)
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            this.minDateTime = now.toISOString().slice(0, 16);
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

        handleSubmit(e) {
            // Sync TinyMCE avant soumission
            if (tinymce.get('content')) {
                this.article.content = tinymce.get('content').getContent();
            }
        }
    };
}
</script>
@endsection
