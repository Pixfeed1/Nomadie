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

<!-- Alpine.js Store Global -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('article', {
        settingsSidebarOpen: false,
        showScheduleModal: false,
        status: 'draft',
        seoScore: 0,

        toggleSidebar() {
            this.settingsSidebarOpen = !this.settingsSidebarOpen;
        },

        setStatus(status) {
            this.status = status;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Style Gutenberg - Interface épurée */
    body {
        background: #fff;
    }

    /* Editor.js */
    #editorjs {
        background: transparent;
        border: none;
        padding: 0;
        min-height: 500px;
    }

    .ce-block__content,
    .ce-toolbar__content {
        max-width: 740px;
    }

    .codex-editor__redactor {
        padding-bottom: 300px !important;
    }

    .ce-paragraph {
        font-size: 18px;
        line-height: 1.8;
        color: #1F2937;
    }

    .ce-header {
        font-weight: 600;
        color: #111827;
    }

    /* Inputs style Gutenberg */
    .gutenberg-title {
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
        border: none;
        outline: none;
        padding: 12px 0;
        margin: 0;
        width: 100%;
        color: #1F2937;
        background: transparent;
    }

    .gutenberg-title::placeholder {
        color: #D1D5DB;
    }

    .gutenberg-subtitle {
        font-size: 16px;
        font-weight: 400;
        line-height: 1.6;
        border: none;
        outline: none;
        padding: 8px 0;
        margin: 0;
        width: 100%;
        color: #6B7280;
        background: transparent;
    }

    .gutenberg-subtitle::placeholder {
        color: #D1D5DB;
    }

    /* Sidebar settings */
    .settings-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        width: 380px;
        background: white;
        border-left: 1px solid #E5E7EB;
        z-index: 50;
        overflow-y: auto;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    }

    .settings-sidebar.open {
        transform: translateX(0);
    }

    /* Sur desktop : sidebar pousse le contenu */
    @media (min-width: 1024px) {
        .content-area {
            transition: margin-right 0.3s ease;
        }

        .content-area.sidebar-open {
            margin-right: 380px;
        }

        .settings-sidebar {
            box-shadow: -2px 0 4px rgba(0, 0, 0, 0.05);
        }
    }

    /* Sur mobile : sidebar en overlay */
    @media (max-width: 1024px) {
        .settings-sidebar {
            width: 100%;
            max-width: 380px;
        }
    }
</style>
@endpush

{{-- Injection des éléments dans le header du layout --}}
@section('header-left')
    <a href="{{ route('writer.articles.index') }}"
       class="flex items-center space-x-2 text-text-secondary hover:text-text-primary transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline text-sm font-medium">Articles</span>
    </a>
@endsection

@section('header-center')
    <div class="flex items-center space-x-2 text-sm text-text-secondary">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span x-text="$store.article.status === 'draft' ? 'Brouillon' : 'Publié'"></span>
    </div>
@endsection

@section('header-actions')
    <!-- Bouton Paramètres -->
    <button type="button"
            @click="$store.article.toggleSidebar()"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
            title="Paramètres">
        <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </button>

    <!-- Bouton Sauvegarder brouillon -->
    <button type="submit"
            form="article-form"
            @click="$store.article.setStatus('draft')"
            class="px-4 py-2 text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-gray-100 rounded-lg transition-colors">
        Sauvegarder
    </button>

    <!-- Dropdown Publier avec Alpine.js -->
    <div x-data="{ publishOpen: false }" @click.away="publishOpen = false" class="relative">
        <button type="button"
                @click="publishOpen = !publishOpen"
                :disabled="$store.article.seoScore < 78"
                :class="$store.article.seoScore >= 78 ? 'bg-primary hover:bg-primary-dark text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center space-x-1">
            <span>Publier</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Menu déroulant -->
        <div x-show="publishOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">

            <!-- Publier maintenant -->
            <button type="submit"
                    form="article-form"
                    @click="$store.article.setStatus('published'); publishOpen = false"
                    class="w-full text-left px-4 py-2 text-sm text-text-primary hover:bg-gray-50 flex items-center space-x-2">
                <svg class="h-4 w-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Publier maintenant</span>
            </button>

            <!-- Planifier -->
            <button type="button"
                    @click="$store.article.setStatus('scheduled'); $store.article.showScheduleModal = true; publishOpen = false"
                    class="w-full text-left px-4 py-2 text-sm text-text-primary hover:bg-gray-50 flex items-center space-x-2">
                <svg class="h-4 w-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Planifier pour plus tard</span>
            </button>
        </div>
    </div>
@endsection

@section('content')
<div x-data="articleEditor()" x-init="init()" class="flex-1 flex flex-col overflow-hidden">
    <form id="article-form" action="{{ route('writer.articles.store') }}" method="POST" enctype="multipart/form-data" @submit="saveEditorContent" class="flex-1 flex flex-col overflow-hidden">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="content" x-ref="contentInput">
        <input type="hidden" name="status" x-model="$store.article.status">

        <!-- Contenu principal centré (style Gutenberg) -->
        <div class="flex-1 overflow-y-auto content-area bg-white" :class="$store.article.settingsSidebarOpen ? 'sidebar-open' : ''">
            <div class="max-w-[740px] mx-auto px-8 py-16">
                <!-- Titre -->
                <div class="mb-2">
                    <input type="text"
                           id="title"
                           name="title"
                           x-model="article.title"
                           @input="debounceAnalyze()"
                           class="gutenberg-title"
                           placeholder="Ajouter un titre"
                           required>
                </div>

                <!-- Sous-titre -->
                <div class="mb-6">
                    <input type="text"
                           id="subtitle"
                           name="subtitle"
                           x-model="article.subtitle"
                           class="gutenberg-subtitle"
                           placeholder="Ajouter un sous-titre (optionnel)">
                </div>

                <!-- Editor.js -->
                <div id="editorjs" class="mt-8"></div>
            </div>
        </div>

        <!-- Sidebar Paramètres (slide depuis la droite) -->
        <div class="settings-sidebar" :class="$store.article.settingsSidebarOpen ? 'open' : ''" x-cloak>
            <!-- Header sidebar -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between z-10">
                <h3 class="text-lg font-semibold text-text-primary">Paramètres</h3>
                <button type="button"
                        @click="$store.article.toggleSidebar()"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Contenu sidebar -->
            <div class="p-6 space-y-6">
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
                         class="relative border-2 border-dashed border-border rounded-lg p-4 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all group">
                        <template x-if="!imagePreview">
                            <div class="text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-1 text-sm text-text-secondary">Cliquer pour ajouter</p>
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <div class="relative">
                                <img :src="imagePreview" alt="Aperçu" class="w-full h-40 object-cover rounded">
                                <button type="button"
                                        @click.stop="imagePreview = null; $refs.imageInput.value = ''"
                                        class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Catégorie -->
                <div>
                    <label for="category" class="block text-sm font-medium text-text-primary mb-2">
                        Catégorie
                    </label>
                    <input type="text"
                           id="category"
                           name="category"
                           list="category-suggestions"
                           x-model="article.category"
                           class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                           placeholder="Choisir ou créer...">
                    <datalist id="category-suggestions">
                        <option value="Destinations">
                        <option value="Conseils de voyage">
                        <option value="Gastronomie">
                        <option value="Écotourisme">
                        <option value="Culture & Traditions">
                        <option value="Activités & Sports">
                    </datalist>
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-text-primary mb-2">
                        Étiquettes (Tags)
                    </label>
                    <input type="text"
                           id="tags"
                           name="tags"
                           list="tag-suggestions"
                           x-model="article.tags"
                           class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                           placeholder="voyage, bali, plage">
                    <datalist id="tag-suggestions">
                        <option value="voyage">
                        <option value="destination">
                        <option value="plage">
                        <option value="culture">
                        <option value="gastronomie">
                    </datalist>
                    <p class="mt-1 text-xs text-text-secondary">Séparez par des virgules</p>
                </div>

                <hr class="border-gray-200">

                <!-- SEO Section -->
                <div>
                    <h4 class="text-sm font-semibold text-text-primary mb-3">Référencement (SEO)</h4>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="block text-xs font-medium text-text-secondary mb-1">
                            URL (Slug)
                        </label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               x-model="article.slug"
                               class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                               placeholder="guide-bali-2024">
                    </div>

                    <!-- Meta Description -->
                    <div class="mb-4">
                        <label for="meta_description" class="block text-xs font-medium text-text-secondary mb-1">
                            Meta Description
                        </label>
                        <textarea id="meta_description"
                                  name="meta_description"
                                  x-model="article.meta_description"
                                  @input="debounceAnalyze()"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm resize-none"
                                  placeholder="Description courte pour les moteurs de recherche..."></textarea>
                        <div class="mt-1 text-xs text-text-secondary">
                            <span x-text="article.meta_description.length"></span>/160 caractères
                        </div>
                    </div>

                    <!-- Score SEO -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-text-secondary">Score SEO</span>
                            <span class="text-lg font-bold" :class="{
                                'text-success': seoScore >= 78,
                                'text-accent': seoScore >= 50 && seoScore < 78,
                                'text-error': seoScore < 50
                            }" x-text="seoScore"></span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500"
                                 :style="`width: ${seoScore}%`"
                                 :class="{
                                     'bg-success': seoScore >= 78,
                                     'bg-accent': seoScore >= 50 && seoScore < 78,
                                     'bg-error': seoScore < 50
                                 }"></div>
                        </div>
                        <p class="mt-2 text-xs text-text-secondary">
                            <span x-show="seoScore >= 78" class="text-success">✓ Excellent - Éligible DoFollow</span>
                            <span x-show="seoScore >= 50 && seoScore < 78" class="text-accent">À améliorer pour DoFollow</span>
                            <span x-show="seoScore < 50" class="text-error">Optimisation nécessaire</span>
                        </p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Statistiques -->
                <div>
                    <h4 class="text-sm font-semibold text-text-primary mb-3">Statistiques</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Mots:</dt>
                            <dd class="font-medium text-text-primary" x-text="wordCount"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-secondary">Temps de lecture:</dt>
                            <dd class="font-medium text-text-primary" x-text="readingTime + ' min'"></dd>
                        </div>
                    </dl>
                </div>

                <hr class="border-gray-200">

                <!-- Publication -->
                <div>
                    <h4 class="text-sm font-semibold text-text-primary mb-3">Publication</h4>

                    <!-- Date de publication (si planifié) -->
                    <div x-show="$store.article.status === 'scheduled'" class="mb-4">
                        <label for="scheduled_at" class="block text-xs font-medium text-text-secondary mb-1">
                            Date de publication
                        </label>
                        <input type="datetime-local"
                               id="scheduled_at"
                               name="scheduled_at"
                               x-model="article.scheduled_at"
                               :min="minDateTime"
                               class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay quand sidebar est ouverte (mobile uniquement) -->
        <div x-show="$store.article.settingsSidebarOpen"
             @click="$store.article.toggleSidebar()"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-25 z-40 lg:hidden"
             x-cloak></div>
    </form>

    <!-- Barre fixe indicateurs SEO en bas -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-30 transition-all"
         :class="$store.article.settingsSidebarOpen ? 'lg:mr-[380px]' : ''"
         x-cloak>
        <div class="max-w-[740px] mx-auto px-8 py-3">
            <div class="flex items-center justify-between">
                <!-- Score SEO -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-medium text-text-secondary">Score Nomad SEO:</span>
                        <div class="flex items-center space-x-1">
                            <span class="text-lg font-bold" :class="{
                                'text-success': seoScore >= 78,
                                'text-accent': seoScore >= 50 && seoScore < 78,
                                'text-error': seoScore < 50
                            }" x-text="seoScore"></span>
                            <span class="text-xs text-text-secondary">/100</span>
                        </div>
                    </div>

                    <!-- Barre de progression mini -->
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500"
                             :style="`width: ${seoScore}%`"
                             :class="{
                                 'bg-success': seoScore >= 78,
                                 'bg-accent': seoScore >= 50 && seoScore < 78,
                                 'bg-error': seoScore < 50
                             }"></div>
                    </div>

                    <!-- Statistiques -->
                    <div class="hidden md:flex items-center space-x-3 text-xs text-text-secondary">
                        <span><span x-text="wordCount"></span> mots</span>
                        <span>•</span>
                        <span><span x-text="readingTime"></span> min de lecture</span>
                    </div>
                </div>

                <!-- Points à améliorer -->
                <div class="flex items-center space-x-2">
                    <template x-if="seoScore < 78">
                        <div class="flex items-center space-x-2 text-xs">
                            <svg class="h-4 w-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-text-secondary">
                                <template x-if="scores.title < 20"><span>Titre trop court</span></template>
                                <template x-if="scores.meta < 20 && scores.title >= 20"><span>Meta description manquante</span></template>
                                <template x-if="scores.content < 10 && scores.meta >= 20 && scores.title >= 20"><span>Contenu insuffisant</span></template>
                            </span>
                            <button @click="$store.article.toggleSidebar()" class="ml-2 text-primary hover:underline text-xs font-medium">
                                Voir les détails
                            </button>
                        </div>
                    </template>
                    <template x-if="seoScore >= 78">
                        <div class="flex items-center space-x-1 text-xs text-success">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Éligible DoFollow</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Planification -->
    <div x-show="$store.article.showScheduleModal"
         x-cloak
         @click.self="$store.article.showScheduleModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div @click.away="$store.article.showScheduleModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-text-primary">Planifier la publication</h3>
                <button @click="$store.article.showScheduleModal = false" class="text-text-secondary hover:text-text-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-text-primary mb-2">
                        Date et heure de publication
                    </label>
                    <input type="datetime-local"
                           id="schedule_date"
                           x-model="article.scheduled_at"
                           :min="minDateTime"
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button"
                            @click="$store.article.showScheduleModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-text-secondary rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            form="article-form"
                            @click="$store.article.showScheduleModal = false"
                            class="flex-1 px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                        Planifier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function articleEditor() {
    return {
        article: {
            title: '',
            subtitle: '',
            content: '',
            meta_description: '',
            slug: '',
            category: '',
            tags: '',
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
                                byFile: '{{ route("writer.articles.upload-image") }}'
                            },
                            additionalRequestHeaders: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        class: CodeTool
                    },

                    table: {
                        class: Table,
                        inlineToolbar: true,
                        config: {
                            rows: 2,
                            cols: 2
                        }
                    },

                    delimiter: Delimiter,

                    inlineCode: {
                        class: InlineCode
                    },

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
                            "Link": "Lien",
                            "Bold": "Gras",
                            "Italic": "Italique",
                            "InlineCode": "Code en ligne",
                            "Image": "Image"
                        },
                        tools: {
                            "header": {
                                "Header": "Titre"
                            },
                            "list": {
                                "Ordered": "Numérotée",
                                "Unordered": "À puces"
                            },
                            "quote": {
                                "Align Left": "Aligner à gauche",
                                "Align Center": "Centrer"
                            },
                            "image": {
                                "Caption": "Légende",
                                "Select an Image": "Sélectionner une image",
                                "With border": "Avec bordure",
                                "Stretch image": "Étirer l'image",
                                "With background": "Avec arrière-plan"
                            },
                            "table": {
                                "Add row above": "Ajouter une ligne au-dessus",
                                "Add row below": "Ajouter une ligne en-dessous",
                                "Delete row": "Supprimer la ligne",
                                "Add column to left": "Ajouter une colonne à gauche",
                                "Add column to right": "Ajouter une colonne à droite",
                                "Delete column": "Supprimer la colonne",
                                "With headings": "Avec en-têtes"
                            }
                        },
                        blockTunes: {
                            "delete": {
                                "Delete": "Supprimer",
                                "Click to delete": "Cliquer pour supprimer"
                            },
                            "moveUp": {
                                "Move up": "Déplacer vers le haut"
                            },
                            "moveDown": {
                                "Move down": "Déplacer vers le bas"
                            }
                        }
                    }
                },

                onChange: () => {
                    this.debounceAnalyze();
                }
            });

            // Date min pour planification
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            this.minDateTime = now.toISOString().slice(0, 16);

            // Auto-générer slug depuis titre
            this.$watch('article.title', (title) => {
                if (!this.article.slug || this.article.slug === '') {
                    this.article.slug = this.generateSlug(title);
                }
            });

            // Synchroniser seoScore avec le store
            this.$watch('seoScore', (value) => {
                Alpine.store('article').seoScore = value;
            });
        },

        saveEditorContent(e) {
            e.preventDefault();

            this.editor.save().then((outputData) => {
                this.$refs.contentInput.value = JSON.stringify(outputData);
                e.target.submit();
            }).catch((error) => {
                console.error('Erreur sauvegarde Editor.js:', error);
            });
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        generateSlug(text) {
            return text
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        },

        debounceAnalyze() {
            clearTimeout(this.analyzeTimeout);
            this.analyzeTimeout = setTimeout(() => {
                this.analyzeSEO();
            }, 500);
        },

        async analyzeSEO() {
            try {
                const editorData = await this.editor.save();

                // Compter les mots
                let textContent = '';
                editorData.blocks.forEach(block => {
                    if (block.type === 'paragraph' || block.type === 'header') {
                        textContent += block.data.text + ' ';
                    } else if (block.type === 'list') {
                        textContent += block.data.items.join(' ') + ' ';
                    }
                });

                const words = textContent.trim().split(/\s+/).filter(word => word.length > 0);
                this.wordCount = words.length;
                this.readingTime = Math.ceil(this.wordCount / 200);

                // Calculer scores SEO
                this.scores.title = this.scoreTitleSEO();
                this.scores.meta = this.scoreMetaSEO();
                this.scores.content = this.scoreContentSEO();
                this.scores.images = editorData.blocks.some(b => b.type === 'image') ? 20 : 0;
                this.scores.readability = this.wordCount >= 300 ? 20 : Math.floor((this.wordCount / 300) * 20);

                // Score total
                this.seoScore = Math.round(
                    this.scores.title +
                    this.scores.meta +
                    this.scores.content +
                    this.scores.images +
                    this.scores.readability
                );

            } catch (error) {
                console.error('Erreur analyse SEO:', error);
            }
        },

        scoreTitleSEO() {
            const length = this.article.title.length;
            if (length >= 30 && length <= 60) return 20;
            if (length >= 20 && length <= 70) return 15;
            if (length > 0) return 10;
            return 0;
        },

        scoreMetaSEO() {
            const length = this.article.meta_description.length;
            if (length >= 120 && length <= 160) return 20;
            if (length >= 80 && length <= 180) return 15;
            if (length > 0) return 10;
            return 0;
        },

        scoreContentSEO() {
            if (this.wordCount >= 800) return 20;
            if (this.wordCount >= 500) return 15;
            if (this.wordCount >= 300) return 10;
            if (this.wordCount > 0) return 5;
            return 0;
        }
    };
}
</script>
@endsection
