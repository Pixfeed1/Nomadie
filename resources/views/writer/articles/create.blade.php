@extends('layouts.writer')

@section('title', 'Nouvel article')
{{-- Fichier nettoyé - Version Editor.js --}}

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
<script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest/dist/link.umd.min.js"></script>

<!-- Alpine.js Store Global -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('article', {
        settingsSidebarOpen: false,
        showScheduleModal: false,
        showPreview: false,
        previewDevice: 'desktop', // desktop, tablet, mobile
        status: 'draft',
        seoScore: 0,

        toggleSidebar() {
            this.settingsSidebarOpen = !this.settingsSidebarOpen;
        },

        setStatus(status) {
            this.status = status;
        },

        setPreviewDevice(device) {
            this.previewDevice = device;
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
        text-align: center;
    }

    .gutenberg-title::placeholder {
        color: #D1D5DB;
        text-align: center;
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
        text-align: center;
    }

    .gutenberg-subtitle::placeholder {
        color: #D1D5DB;
        text-align: center;
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
    <div class="flex items-center space-x-3">
        <!-- Flèche retour -->
        <a href="{{ route('writer.articles.index') }}"
           class="flex items-center space-x-2 text-text-secondary hover:text-text-primary transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="hidden sm:inline text-sm font-medium">Articles</span>
        </a>

        <!-- Séparateur vertical -->
        <div class="h-6 w-px bg-gray-300"></div>

        <!-- Boutons Undo/Redo -->
        <div class="flex items-center space-x-1">
            <!-- Bouton Undo (Annuler) -->
            <button type="button"
                    @click="undo()"
                    x-ref="undoButton"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Annuler (Ctrl+Z)">
                <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </button>

            <!-- Bouton Redo (Refaire) -->
            <button type="button"
                    @click="redo()"
                    x-ref="redoButton"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Refaire (Ctrl+Y)">
                <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
                </svg>
            </button>
        </div>
    </div>
@endsection

@section('header-center')
    {{-- Vide maintenant - boutons déplacés à gauche --}}
@endsection

@section('header-actions')
    <!-- Bouton Aperçu -->
    <button type="button"
            @click="$store.article.showPreview = true"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
            title="Aperçu">
        <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    </button>

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
        <input type="hidden" name="subtitle" x-model="article.subtitle">
        <input type="hidden" name="focus_keyphrase" x-model="focusKeyphrase">

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

                    <!-- Mot-clé principal -->
                    <div class="mb-4">
                        <label for="focus_keyphrase" class="block text-xs font-medium text-text-secondary mb-1">
                            Mot-clé principal
                            <span class="text-xs text-text-secondary ml-1">(Focus keyphrase)</span>
                        </label>
                        <input type="text"
                               id="focus_keyphrase"
                               x-model="focusKeyphrase"
                               @input="debounceAnalyze()"
                               class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                               placeholder="ex: voyage à Bali">
                        <p class="mt-1 text-xs text-text-secondary">Le mot-clé que vous ciblez pour cet article</p>
                    </div>

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

                    <!-- Détails SEO (Nomad SEO) -->
                    <div class="mt-4 space-y-3">
                        <h5 class="text-xs font-semibold text-text-primary">Analyse Nomad SEO</h5>

                        <!-- Mot-clé principal -->
                        <template x-if="focusKeyphrase">
                            <div class="text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-text-secondary">Mot-clé dans le titre</span>
                                    <span :class="seoDetails.keyphraseInTitle ? 'text-success' : 'text-error'">
                                        <span x-show="seoDetails.keyphraseInTitle">✓</span>
                                        <span x-show="!seoDetails.keyphraseInTitle">✗</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-text-secondary">Mot-clé dans meta</span>
                                    <span :class="seoDetails.keyphraseInMeta ? 'text-success' : 'text-error'">
                                        <span x-show="seoDetails.keyphraseInMeta">✓</span>
                                        <span x-show="!seoDetails.keyphraseInMeta">✗</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-text-secondary">Mot-clé dans sous-titres</span>
                                    <span :class="seoDetails.keyphraseInHeadings ? 'text-success' : 'text-error'">
                                        <span x-show="seoDetails.keyphraseInHeadings">✓</span>
                                        <span x-show="!seoDetails.keyphraseInHeadings">✗</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-text-secondary">Densité mot-clé</span>
                                    <span :class="{
                                        'text-success': seoDetails.keyphraseDensity >= 0.5 && seoDetails.keyphraseDensity <= 2.5,
                                        'text-accent': seoDetails.keyphraseDensity > 0 && seoDetails.keyphraseDensity < 3,
                                        'text-error': seoDetails.keyphraseDensity === 0 || seoDetails.keyphraseDensity >= 3
                                    }">
                                        <span x-text="seoDetails.keyphraseDensity.toFixed(2)"></span>%
                                    </span>
                                </div>
                            </div>
                        </template>

                        <!-- Mots de transition -->
                        <div class="text-xs flex items-center justify-between">
                            <span class="text-text-secondary">Mots de transition</span>
                            <span :class="{
                                'text-success': seoDetails.transitionsPercentage >= 20,
                                'text-accent': seoDetails.transitionsPercentage >= 10,
                                'text-error': seoDetails.transitionsPercentage < 10
                            }">
                                <span x-text="Math.round(seoDetails.transitionsPercentage)"></span>%
                            </span>
                        </div>

                        <!-- Liens -->
                        <div class="text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-text-secondary">Liens internes</span>
                                <span :class="seoDetails.internalLinks >= 1 ? 'text-success' : 'text-error'" x-text="seoDetails.internalLinks"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-text-secondary">Liens externes</span>
                                <span :class="seoDetails.externalLinks >= 1 ? 'text-success' : 'text-error'" x-text="seoDetails.externalLinks"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Gestion des liens (NoFollow/DoFollow) -->
                <div x-data="{ showLinks: false }">
                    <button @click="showLinks = !showLinks" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <h4 class="text-sm font-semibold text-text-primary">Attributs des liens</h4>
                        </div>
                        <svg class="w-4 h-4 text-text-secondary transition-transform" :class="showLinks ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="showLinks" x-collapse class="mt-2 space-y-2">
                        <p class="text-xs text-text-secondary mb-2">
                            Pour ajouter nofollow/dofollow, sélectionnez le texte du lien dans l'éditeur, puis utilisez les boutons ci-dessous.
                        </p>

                        <div class="space-y-1">
                            <button @click="addLinkAttribute('nofollow')"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-medium text-text-primary hover:bg-gray-50 transition-colors">
                                🚫 Ajouter rel="nofollow"
                            </button>
                            <button @click="addLinkAttribute('dofollow')"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-medium text-text-primary hover:bg-gray-50 transition-colors">
                                ✅ Ajouter rel="dofollow"
                            </button>
                            <button @click="addLinkAttribute('')"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-medium text-text-secondary hover:bg-gray-50 transition-colors">
                                🗑️ Supprimer attribut rel
                            </button>
                        </div>

                        <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                            <strong>💡 Conseil SEO:</strong> Utilisez <code>nofollow</code> pour les liens externes non fiables ou publicitaires, et <code>dofollow</code> pour les liens de qualité.
                        </div>
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

    <!-- Modal Aperçu Responsive -->
    <div x-show="$store.article.showPreview"
         x-cloak
         @click.self="$store.article.showPreview = false"
         class="fixed inset-0 bg-black bg-opacity-75 z-50 flex flex-col">

        <!-- Header modal -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h3 class="text-lg font-semibold text-text-primary">Aperçu de l'article</h3>

                <!-- Sélecteur de device -->
                <div class="flex items-center space-x-1 bg-gray-100 rounded-lg p-1">
                    <!-- Desktop -->
                    <button @click="$store.article.setPreviewDevice('desktop')"
                            :class="$store.article.previewDevice === 'desktop' ? 'bg-white shadow-sm' : ''"
                            class="p-2 rounded hover:bg-white transition-colors"
                            title="Desktop">
                        <svg class="h-5 w-5" :class="$store.article.previewDevice === 'desktop' ? 'text-primary' : 'text-text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    <!-- Tablette -->
                    <button @click="$store.article.setPreviewDevice('tablet')"
                            :class="$store.article.previewDevice === 'tablet' ? 'bg-white shadow-sm' : ''"
                            class="p-2 rounded hover:bg-white transition-colors"
                            title="Tablette">
                        <svg class="h-5 w-5" :class="$store.article.previewDevice === 'tablet' ? 'text-primary' : 'text-text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    <!-- Mobile -->
                    <button @click="$store.article.setPreviewDevice('mobile')"
                            :class="$store.article.previewDevice === 'mobile' ? 'bg-white shadow-sm' : ''"
                            class="p-2 rounded hover:bg-white transition-colors"
                            title="Mobile">
                        <svg class="h-5 w-5" :class="$store.article.previewDevice === 'mobile' ? 'text-primary' : 'text-text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button @click="$store.article.showPreview = false" class="text-text-secondary hover:text-text-primary transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Contenu preview -->
        <div class="flex-1 overflow-y-auto bg-bg-main p-8">
            <div class="transition-all duration-300 mx-auto"
                 :class="{
                     'max-w-4xl': $store.article.previewDevice === 'desktop',
                     'max-w-3xl': $store.article.previewDevice === 'tablet',
                     'max-w-sm': $store.article.previewDevice === 'mobile'
                 }">

                <!-- Breadcrumb -->
                <div class="mb-8">
                    <div class="flex items-center text-sm text-text-secondary">
                        <span>Blog</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span x-text="article.category || 'Catégorie'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-text-primary font-medium truncate" x-text="article.title || 'Titre de l\'article'"></span>
                    </div>
                </div>

                <!-- Contenu principal (comme blog/show.blade.php) -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Image principale -->
                    <div class="w-full aspect-video overflow-hidden" x-show="imagePreview">
                        <img :src="imagePreview" alt="Image article" class="w-full h-full object-cover">
                    </div>

                    <!-- Contenu article -->
                    <div class="p-8">
                        <!-- En-tête -->
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full" x-text="article.category || 'Catégorie'"></span>
                                <span class="ml-4 text-sm text-text-secondary">Aujourd'hui</span>
                                <span class="ml-auto text-sm text-text-secondary flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="readingTime"></span> min de lecture
                                </span>
                            </div>
                            <h1 class="text-3xl font-bold text-text-primary mb-4" x-text="article.title || 'Titre de l\'article'"></h1>
                            <p class="text-lg text-text-secondary italic" x-text="article.subtitle || article.meta_description || 'Extrait de l\'article...'"></p>
                        </div>

                        <!-- Info auteur -->
                        <div class="flex items-center border-t border-b border-border py-4 mb-8">
                            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-text-primary">Par {{ auth()->user()->name ?? 'Auteur' }}</p>
                                <p class="text-xs text-text-secondary">Publié aujourd'hui</p>
                            </div>
                            <div class="ml-auto flex space-x-3">
                                <!-- Boutons partage social (liens réels comme dans blog/show.blade.php) -->
                                <a href="#"
                                   target="_blank"
                                   class="text-text-secondary hover:text-primary transition-colors"
                                   title="Partager sur X (Twitter)"
                                   @click.prevent="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(article.title), '_blank')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                                <a href="#"
                                   target="_blank"
                                   class="text-text-secondary hover:text-primary transition-colors"
                                   title="Partager sur Facebook"
                                   @click.prevent="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                    </svg>
                                </a>
                                <a href="#"
                                   target="_blank"
                                   class="text-text-secondary hover:text-primary transition-colors"
                                   title="Partager sur LinkedIn"
                                   @click.prevent="window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.location.href), '_blank')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                                    </svg>
                                </a>
                                <a href="#"
                                   target="_blank"
                                   class="text-text-secondary hover:text-primary transition-colors"
                                   title="Partager sur WhatsApp"
                                   @click.prevent="window.open('https://wa.me/?text=' + encodeURIComponent(article.title + ' ' + window.location.href), '_blank')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Corps de l'article -->
                        <div class="prose max-w-none prose-primary">
                            <div x-ref="previewContent" class="article-content">
                                <p class="text-text-secondary italic">Votre contenu apparaîtra ici une fois rédigé...</p>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mt-10 pt-6 border-t border-border" x-show="article.tags">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="tag in (article.tags || '').split(',').filter(t => t.trim())" :key="tag">
                                    <span class="px-3 py-1 bg-bg-alt text-text-secondary text-sm rounded-full hover:bg-primary/10 hover:text-primary transition-colors">
                                        #<span x-text="tag.trim()"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
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
        focusKeyphrase: '',
        seoScore: 0,
        scores: {
            title: 0,
            content: 0,
            meta: 0,
            images: 0,
            readability: 0,
            keyphrase: 0,
            links: 0,
            transitions: 0
        },
        seoDetails: {
            keyphraseInTitle: false,
            keyphraseInMeta: false,
            keyphraseInHeadings: false,
            keyphraseDensity: 0,
            transitionsPercentage: 0,
            internalLinks: 0,
            externalLinks: 0
        },
        wordCount: 0,
        readingTime: 0,
        analyzeTimeout: null,
        minDateTime: '',
        editor: null,
        editorReady: false,  // Flag pour éviter double initialisation

        init() {
            // Éviter double initialisation
            if (this.editorReady) {
                console.warn('⚠️ Editor.js déjà initialisé, abandon');
                return;
            }

            console.log('🚀 Initialisation Editor.js...');

            // Initialiser Editor.js
            this.editor = new EditorJS({
                holder: 'editorjs',
                autofocus: true,
                placeholder: 'Commencez à écrire votre article...',
                minHeight: 0,

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
                            },
                            "inlineToolbar": {
                                "converter": {
                                    "Convert to": "Convertir en"
                                }
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
                            },
                            "link": {
                                "Add a link": "Ajouter un lien"
                            },
                            "stub": {
                                "The block can not be displayed correctly.": "Le bloc ne peut pas être affiché correctement."
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

                onChange: (api, event) => {
                    console.log('🔄 Changement détecté dans l\'éditeur');
                    this.debounceAnalyze();
                },

                onReady: () => {
                    this.editorReady = true;
                    console.log('✅ Editor.js prêt et initialisé');
                    // Lancer l'analyse initiale après un court délai pour s'assurer que tout est prêt
                    setTimeout(() => {
                        console.log('🚀 Lancement de l\'analyse initiale');
                        this.analyzeSEO();
                    }, 500);
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

            // Initialiser les valeurs par défaut
            this.wordCount = 0;
            this.readingTime = 0;
            this.seoScore = 0;
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
                // Vérifier que l'éditeur est prêt
                if (!this.editorReady || !this.editor || !this.editor.save) {
                    console.warn('⚠️ Éditeur pas encore prêt, analyse ignorée');
                    return;
                }

                console.log('📊 Début analyse SEO...');

                const editorData = await this.editor.save();
                console.log('📝 Données Editor.js:', editorData);

                // Compter les mots et extraire le contenu
                let textContent = '';
                let headings = [];
                let sentences = [];

                if (editorData && editorData.blocks) {
                    editorData.blocks.forEach(block => {
                        if (block.type === 'paragraph' && block.data && block.data.text) {
                            textContent += block.data.text + ' ';
                            // Diviser en phrases
                            const blockSentences = block.data.text.split(/[.!?]+/).filter(s => s.trim().length > 0);
                            sentences.push(...blockSentences);
                        } else if (block.type === 'header' && block.data && block.data.text) {
                            textContent += block.data.text + ' ';
                            headings.push(block.data.text.toLowerCase());
                        } else if (block.type === 'list' && block.data && block.data.items) {
                            textContent += block.data.items.join(' ') + ' ';
                        }
                    });
                }

                const words = textContent.trim().split(/\s+/).filter(word => word.length > 0);
                this.wordCount = words.length;
                this.readingTime = this.wordCount > 0 ? Math.ceil(this.wordCount / 200) : 0;

                // Analyser mot-clé principal
                if (this.focusKeyphrase) {
                    this.analyzeKeyphrase(textContent, headings);
                }

                // Analyser mots de transition
                this.analyzeTransitions(textContent);

                // Analyser liens (approximatif - on compte les balises <a>)
                this.analyzeLinks(textContent);

                // Calculer scores SEO
                this.scores.title = this.scoreTitleSEO();
                this.scores.meta = this.scoreMetaSEO();
                this.scores.content = this.scoreContentSEO();
                this.scores.images = editorData.blocks.some(b => b.type === 'image') ? 15 : 0;
                this.scores.readability = this.wordCount >= 300 ? 10 : Math.floor((this.wordCount / 300) * 10);
                this.scores.keyphrase = this.scoreKeyphraseSEO();
                this.scores.links = this.scoreLinksSEO();
                this.scores.transitions = this.scoreTransitionsSEO();

                // Score total (sur 100)
                this.seoScore = Math.round(
                    this.scores.title +
                    this.scores.meta +
                    this.scores.content +
                    this.scores.images +
                    this.scores.readability +
                    this.scores.keyphrase +
                    this.scores.links +
                    this.scores.transitions
                );

                console.log('✅ Analyse SEO terminée:', {
                    score: this.seoScore,
                    wordCount: this.wordCount,
                    readingTime: this.readingTime,
                    scores: this.scores
                });

            } catch (error) {
                console.error('❌ Erreur analyse SEO:', error);
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
            if (this.wordCount >= 800) return 15;
            if (this.wordCount >= 500) return 10;
            if (this.wordCount >= 300) return 5;
            if (this.wordCount > 0) return 2;
            return 0;
        },

        // Analyser mot-clé principal
        analyzeKeyphrase(textContent, headings) {
            if (!this.focusKeyphrase) return;

            const keyphrase = this.focusKeyphrase.toLowerCase();
            const contentLower = textContent.toLowerCase();
            const titleLower = this.article.title.toLowerCase();
            const metaLower = this.article.meta_description.toLowerCase();

            // Vérifier présence dans titre
            this.seoDetails.keyphraseInTitle = titleLower.includes(keyphrase);

            // Vérifier présence dans meta description
            this.seoDetails.keyphraseInMeta = metaLower.includes(keyphrase);

            // Vérifier présence dans sous-titres
            this.seoDetails.keyphraseInHeadings = headings.some(h => h.includes(keyphrase));

            // Calculer densité (% du nombre total de mots)
            const keyphraseWords = keyphrase.split(/\s+/);
            const keyphraseLength = keyphraseWords.length;
            let occurrences = 0;

            // Compter occurrences
            const regex = new RegExp('\\b' + keyphrase.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + '\\b', 'gi');
            const matches = contentLower.match(regex);
            occurrences = matches ? matches.length : 0;

            // Densité optimale : 0.5% - 2.5%
            this.seoDetails.keyphraseDensity = this.wordCount > 0 ? (occurrences / this.wordCount) * 100 : 0;
        },

        // Analyser mots de transition français
        analyzeTransitions(textContent) {
            const transitionWords = [
                'mais', 'donc', 'or', 'ni', 'car', 'cependant', 'toutefois', 'néanmoins',
                'pourtant', 'en effet', 'par conséquent', 'ainsi', 'alors', 'ensuite',
                'puis', 'd\'abord', 'enfin', 'finalement', 'premièrement', 'deuxièmement',
                'notamment', 'en outre', 'de plus', 'par ailleurs', 'également', 'aussi',
                'en revanche', 'au contraire', 'tandis que', 'alors que', 'bien que',
                'quoique', 'malgré', 'en dépit de', 'grâce à', 'à cause de', 'pour',
                'afin de', 'en conclusion', 'bref', 'en somme', 'en résumé'
            ];

            const contentLower = textContent.toLowerCase();
            let transitionCount = 0;

            transitionWords.forEach(word => {
                const regex = new RegExp('\\b' + word + '\\b', 'gi');
                const matches = contentLower.match(regex);
                if (matches) transitionCount += matches.length;
            });

            // % de phrases avec mots de transition (estimation)
            const sentenceCount = textContent.split(/[.!?]+/).filter(s => s.trim().length > 0).length;
            this.seoDetails.transitionsPercentage = sentenceCount > 0 ? (transitionCount / sentenceCount) * 100 : 0;
        },

        // Analyser liens (Editor.js stocke les liens dans les balises <a> du HTML)
        analyzeLinks(textContent) {
            // Réinitialiser les compteurs
            this.seoDetails.internalLinks = 0;
            this.seoDetails.externalLinks = 0;

            // Compter balises <a> dans le contenu HTML
            const linkMatches = textContent.match(/<a[^>]*href=["']([^"']*)["'][^>]*>/gi);

            if (linkMatches) {
                linkMatches.forEach(linkTag => {
                    // Extraire l'URL du href
                    const hrefMatch = linkTag.match(/href=["']([^"']*)["']/i);
                    if (hrefMatch && hrefMatch[1]) {
                        const url = hrefMatch[1];
                        // Liens externes (http/https absolus)
                        if (url.match(/^https?:\/\//i)) {
                            this.seoDetails.externalLinks++;
                        } else {
                            // Liens internes (relatifs ou domaine actuel)
                            this.seoDetails.internalLinks++;
                        }
                    }
                });
            }

            console.log('Liens analysés:', {
                internal: this.seoDetails.internalLinks,
                external: this.seoDetails.externalLinks
            });
        },

        // Score mot-clé principal
        scoreKeyphraseSEO() {
            if (!this.focusKeyphrase) return 0;

            let score = 0;

            // Présence dans titre (+5)
            if (this.seoDetails.keyphraseInTitle) score += 5;

            // Présence dans meta (+5)
            if (this.seoDetails.keyphraseInMeta) score += 5;

            // Présence dans sous-titres (+5)
            if (this.seoDetails.keyphraseInHeadings) score += 5;

            // Densité optimale 0.5% - 2.5% (+10)
            const density = this.seoDetails.keyphraseDensity;
            if (density >= 0.5 && density <= 2.5) {
                score += 10;
            } else if (density > 0 && density < 3) {
                score += 5;
            }

            return score;
        },

        // Score liens
        scoreLinksSEO() {
            let score = 0;

            // Au moins 1 lien interne (+5)
            if (this.seoDetails.internalLinks >= 1) score += 5;

            // Au moins 1 lien externe (+5)
            if (this.seoDetails.externalLinks >= 1) score += 5;

            return score;
        },

        // Score mots de transition
        scoreTransitionsSEO() {
            // Minimum 20% de phrases avec transitions (+5)
            if (this.seoDetails.transitionsPercentage >= 20) {
                return 5;
            } else if (this.seoDetails.transitionsPercentage >= 10) {
                return 3;
            }
            return 0;
        },

        // Ajouter attribut rel aux liens sélectionnés
        addLinkAttribute(relValue) {
            // Récupérer la sélection actuelle
            const selection = window.getSelection();

            if (!selection || selection.rangeCount === 0) {
                alert('Veuillez sélectionner un lien d\'abord');
                return;
            }

            // Trouver le lien parent de la sélection
            let node = selection.anchorNode;
            let linkElement = null;

            // Remonter dans le DOM pour trouver la balise <a>
            while (node && node.parentElement) {
                if (node.parentElement.tagName === 'A') {
                    linkElement = node.parentElement;
                    break;
                }
                node = node.parentElement;
            }

            if (!linkElement) {
                alert('Aucun lien trouvé dans la sélection. Veuillez cliquer directement sur le lien.');
                return;
            }

            // Appliquer l'attribut rel
            if (relValue === '') {
                linkElement.removeAttribute('rel');
                console.log('✅ Attribut rel supprimé du lien:', linkElement.href);
            } else {
                linkElement.setAttribute('rel', relValue);
                console.log(`✅ Attribut rel="${relValue}" ajouté au lien:`, linkElement.href);
            }

            // Message de succès
            const messages = {
                'nofollow': '🚫 Lien marqué comme nofollow',
                'dofollow': '✅ Lien marqué comme dofollow',
                '': '🗑️ Attribut rel supprimé'
            };

            // Afficher un feedback temporaire
            const feedback = document.createElement('div');
            feedback.textContent = messages[relValue];
            feedback.className = 'fixed top-20 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity';
            document.body.appendChild(feedback);

            setTimeout(() => {
                feedback.style.opacity = '0';
                setTimeout(() => feedback.remove(), 300);
            }, 2000);

            // Rafraîchir l'analyse pour mettre à jour les compteurs
            this.debounceAnalyze();
        },

        // Fonction Undo (Annuler)
        undo() {
            if (this.editor && this.editor.blocks) {
                // Editor.js n'a pas d'API undo native, on utilise le comportement natif du navigateur
                document.execCommand('undo');
            }
        },

        // Fonction Redo (Refaire)
        redo() {
            if (this.editor && this.editor.blocks) {
                // Editor.js n'a pas d'API redo native, on utilise le comportement natif du navigateur
                document.execCommand('redo');
            }
        }
    };
}
</script>
@endsection
