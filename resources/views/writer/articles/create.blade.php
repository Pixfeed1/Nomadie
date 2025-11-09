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
        font-size: 40px;
        font-weight: 700;
        line-height: 1.2;
        border: none;
        outline: none;
        padding: 0;
        margin: 0;
        width: 100%;
        color: #1F2937;
        background: transparent;
    }

    .gutenberg-title::placeholder {
        color: #D1D5DB;
    }

    .gutenberg-subtitle {
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        border: none;
        outline: none;
        padding: 0;
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
    }

    .settings-sidebar.open {
        transform: translateX(0);
    }

    @media (max-width: 1024px) {
        .settings-sidebar {
            width: 100%;
            max-width: 380px;
        }
    }
</style>
@endpush

@section('content')
<div x-data="articleEditor()" x-init="init()" class="h-screen flex flex-col">
    <form action="{{ route('writer.articles.store') }}" method="POST" enctype="multipart/form-data" @submit="saveEditorContent">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="content" x-ref="contentInput">

        <!-- Barre d'outils supérieure (style Gutenberg) -->
        <div class="sticky top-0 z-40 bg-white border-b border-gray-200">
            <div class="px-4 py-3 flex items-center justify-between">
                <!-- Gauche : Logo + Retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('writer.articles.index') }}"
                       class="flex items-center space-x-2 text-text-secondary hover:text-text-primary transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="hidden sm:inline text-sm font-medium">Articles</span>
                    </a>
                </div>

                <!-- Centre : Brouillon / Statut -->
                <div class="flex items-center space-x-2 text-sm text-text-secondary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Brouillon</span>
                </div>

                <!-- Droite : Actions -->
                <div class="flex items-center space-x-2">
                    <!-- Bouton Paramètres -->
                    <button type="button"
                            @click="settingsSidebarOpen = !settingsSidebarOpen"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            title="Paramètres">
                        <svg class="h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>

                    <!-- Bouton Sauvegarder -->
                    <button type="submit"
                            @click="article.status = 'draft'"
                            class="px-4 py-2 text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-gray-100 rounded-lg transition-colors">
                        Sauvegarder
                    </button>

                    <!-- Bouton Publier -->
                    <button type="submit"
                            @click="article.status = 'published'"
                            :disabled="seoScore < 78"
                            :class="seoScore >= 78 ? 'bg-primary hover:bg-primary-dark text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                        Publier
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenu principal centré (style Gutenberg) -->
        <div class="flex-1 overflow-y-auto">
            <div class="max-w-[840px] mx-auto px-6 py-12">
                <!-- Titre -->
                <div class="mb-6">
                    <input type="text"
                           id="title"
                           name="title"
                           x-model="article.title"
                           @input="debounceAnalyze()"
                           class="gutenberg-title"
                           placeholder="Ajouter un titre"
                           required>
                    <div class="mt-2 text-xs text-text-secondary">
                        <span x-text="article.title.length"></span>/60 caractères
                        <span x-show="article.title.length >= 30 && article.title.length <= 60" class="text-success ml-2">✓ Optimal</span>
                    </div>
                </div>

                <!-- Sous-titre -->
                <div class="mb-8">
                    <input type="text"
                           id="subtitle"
                           name="subtitle"
                           x-model="article.subtitle"
                           class="gutenberg-subtitle"
                           placeholder="Ajouter un sous-titre (optionnel)">
                </div>

                <!-- Editor.js -->
                <div id="editorjs"></div>
            </div>
        </div>

        <!-- Sidebar Paramètres (slide depuis la droite) -->
        <div class="settings-sidebar" :class="settingsSidebarOpen ? 'open' : ''" x-cloak>
            <!-- Header sidebar -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between z-10">
                <h3 class="text-lg font-semibold text-text-primary">Paramètres</h3>
                <button type="button"
                        @click="settingsSidebarOpen = false"
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

                    <!-- Statut -->
                    <div class="mb-4">
                        <label for="status" class="block text-xs font-medium text-text-secondary mb-1">
                            Statut
                        </label>
                        <select id="status"
                                name="status"
                                x-model="article.status"
                                class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm">
                            <option value="draft">📝 Brouillon</option>
                            <option value="pending">⏳ En attente</option>
                            <option value="scheduled">📅 Planifié</option>
                            <option value="published" :disabled="seoScore < 78">✅ Publié</option>
                        </select>
                    </div>

                    <!-- Date de publication (si planifié) -->
                    <div x-show="article.status === 'scheduled'" class="mb-4">
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

        <!-- Overlay quand sidebar est ouverte -->
        <div x-show="settingsSidebarOpen"
             @click="settingsSidebarOpen = false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-25 z-40"
             x-cloak></div>
    </form>
</div>

<script>
function articleEditor() {
    return {
        settingsSidebarOpen: false,
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
