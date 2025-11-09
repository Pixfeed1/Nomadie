@extends('vendor.layouts.app')

@section('title', 'Créer un article')

@section('page-title', 'Créer un article')
@section('page-description', 'Rédigez et optimisez votre contenu avec NomadSEO')

@section('content')
<div x-data="articleEditor()" class="max-w-7xl mx-auto">
    <form id="article-form" method="POST" action="{{ route('writer.articles.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Formulaire principal -->
            <div class="flex-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Titre -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-text-primary mb-2">
                                Titre de l'article
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   x-model="article.title"
                                   @input="debounceAnalyze()"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                   placeholder="Ex: Guide complet pour visiter Bali en 2024"
                                   required>
                            <p class="mt-1 text-xs text-text-secondary">
                                <span x-text="article.title.length"></span>/60 caractères
                            </p>
                        </div>

                        <!-- URL personnalisée (slug) -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-text-primary mb-2">
                                URL de l'article
                            </label>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">{{ url('/blog') }}/</span>
                                <input type="text" 
                                       id="slug" 
                                       name="slug" 
                                       x-model="article.slug"
                                       class="flex-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="mon-article-voyage">
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-text-primary mb-2">
                                Meta Description (SEO)
                            </label>
                            <textarea id="meta_description"
                                      name="meta_description"
                                      x-model="article.meta_description"
                                      @input="debounceAnalyze()"
                                      rows="3"
                                      maxlength="160"
                                      class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                      placeholder="Description courte pour Google (max 160 caractères)"></textarea>
                            <p class="mt-1 text-xs text-text-secondary">
                                <span x-text="article.meta_description.length"></span>/160 caractères
                            </p>
                        </div>

                        <!-- Catégorie et Tags en ligne -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Catégorie -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-text-primary mb-2">
                                    Catégorie
                                </label>
                                <select id="category" 
                                        name="category"
                                        x-model="article.category"
                                        class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="destinations">Destinations</option>
                                    <option value="conseils">Conseils</option>
                                    <option value="gastronomie">Gastronomie</option>
                                    <option value="ecotourisme">Écotourisme</option>
                                    <option value="culture">Culture</option>
                                </select>
                            </div>

                            <!-- Tags -->
                            <div>
                                <label for="tags" class="block text-sm font-medium text-text-primary mb-2">
                                    Tags
                                </label>
                                <input type="text"
                                       id="tags"
                                       name="tags"
                                       x-model="article.tags"
                                       class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="voyage, bali, plage (virgules)">
                            </div>
                        </div>

                        <!-- Image à la une -->
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">
                                Image à la une
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       id="featured_image" 
                                       name="featured_image"
                                       @change="handleImageUpload"
                                       accept="image/*"
                                       class="hidden"
                                       x-ref="imageInput">
                                
                                <div @click="$refs.imageInput.click()" 
                                     class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                                    <template x-if="!imagePreview">
                                        <div>
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-2 text-sm text-text-secondary">
                                                Cliquez pour télécharger ou glissez-déposez
                                            </p>
                                            <p class="text-xs text-text-secondary mt-1">
                                                PNG, JPG, GIF jusqu'à 2MB
                                            </p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="imagePreview">
                                        <div class="relative">
                                            <img :src="imagePreview" alt="Preview" class="max-h-48 mx-auto rounded">
                                            <button type="button" 
                                                    @click.stop="imagePreview = null; $refs.imageInput.value = ''" 
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu avec TinyMCE -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-text-primary mb-2">
                                Contenu
                            </label>
                            <textarea id="content" name="content" class="w-full"></textarea>
                            <div class="mt-2 flex items-center justify-between text-xs text-text-secondary">
                                <div>
                                    <span id="word-count" x-text="wordCount">0</span> mots • 
                                    Temps de lecture : <span id="reading-time" x-text="readingTime">0</span> min
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-bg-alt px-6 py-4 border-t border-border flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button type="submit" 
                                    name="status" 
                                    value="draft"
                                    class="px-4 py-2 bg-white border border-border text-text-primary hover:bg-gray-50 font-medium rounded-lg transition-colors">
                                Enregistrer comme brouillon
                            </button>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('writer.articles.index') }}" 
                               class="px-4 py-2 text-text-secondary hover:text-text-primary transition-colors">
                                Annuler
                            </a>
                            <button type="submit" 
                                    name="status" 
                                    value="published"
                                    @click.prevent="submitForm"
                                    :disabled="seoScore < 60"
                                    :class="seoScore >= 78 ? 'bg-primary hover:bg-primary-dark text-white' : seoScore >= 60 ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                    class="px-4 py-2 font-medium rounded-lg transition-colors">
                                <span x-show="seoScore >= 78">Publier (DoFollow)</span>
                                <span x-show="seoScore >= 60 && seoScore < 78">Publier</span>
                                <span x-show="seoScore < 60">Score SEO insuffisant</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panneau NomadSEO -->
            <div class="w-full lg:w-96">
                <div class="bg-white rounded-lg shadow-sm sticky top-6">
                    <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-4 rounded-t-lg">
                        <h3 class="text-lg font-bold flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            NomadSEO Analysis
                        </h3>
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
                                                'text-red-500': seoScore < 50,
                                                'text-yellow-500': seoScore >= 50 && seoScore < 78,
                                                'text-green-500': seoScore >= 78
                                            }"
                                            class="transition-all duration-500"></circle>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-3xl font-bold"
                                          :class="{
                                              'text-red-500': seoScore < 50,
                                              'text-yellow-500': seoScore >= 50 && seoScore < 78,
                                              'text-green-500': seoScore >= 78
                                          }">
                                        <span x-text="seoScore"></span>
                                    </span>
                                </span>
                            </div>
                            <p class="mt-3 text-sm font-medium" 
                               :class="{
                                   'text-red-600': seoScore < 50,
                                   'text-yellow-600': seoScore >= 50 && seoScore < 78,
                                   'text-green-600': seoScore >= 78
                               }">
                                <span x-show="seoScore >= 78">✓ Éligible DoFollow</span>
                                <span x-show="seoScore >= 50 && seoScore < 78">Score correct</span>
                                <span x-show="seoScore < 50">Score faible</span>
                            </p>
                            <p class="text-xs text-text-secondary mt-1">
                                Min. 78/100 pour le DoFollow
                            </p>
                        </div>

                        <!-- Scores détaillés -->
                        <div class="space-y-3">
                            <!-- Titre -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Titre</span>
                                    <span class="text-xs font-bold" x-text="scores.title + '/20'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-primary h-1.5 rounded-full transition-all duration-300" 
                                         :style="`width: ${scores.title * 5}%`"></div>
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Contenu</span>
                                    <span class="text-xs font-bold" x-text="scores.content + '/30'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-accent h-1.5 rounded-full transition-all duration-300" 
                                         :style="`width: ${scores.content * 3.33}%`"></div>
                                </div>
                            </div>

                            <!-- Meta -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Meta Description</span>
                                    <span class="text-xs font-bold" x-text="scores.meta + '/15'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-success h-1.5 rounded-full transition-all duration-300" 
                                         :style="`width: ${scores.meta * 6.67}%`"></div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Images</span>
                                    <span class="text-xs font-bold" x-text="scores.images + '/15'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-yellow-500 h-1.5 rounded-full transition-all duration-300" 
                                         :style="`width: ${scores.images * 6.67}%`"></div>
                                </div>
                            </div>

                            <!-- Lisibilité -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-text-secondary">Lisibilité</span>
                                    <span class="text-xs font-bold" x-text="scores.readability + '/20'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-300" 
                                         :style="`width: ${scores.readability * 5}%`"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques -->
                        <div class="bg-bg-alt rounded-lg p-3 text-xs">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-text-secondary">Mots:</span>
                                    <span class="font-medium ml-1" x-text="wordCount">0</span>
                                </div>
                                <div>
                                    <span class="text-text-secondary">Lecture:</span>
                                    <span class="font-medium ml-1"><span x-text="readingTime">0</span> min</span>
                                </div>
                            </div>
                        </div>

                        <!-- Suggestions -->
                        <div class="border-t border-border pt-4">
                            <h4 class="text-sm font-semibold text-text-primary mb-3">Suggestions</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-if="allSuggestions.length === 0">
                                    <p class="text-xs text-text-secondary italic">Commencez à écrire pour voir les suggestions...</p>
                                </template>
                                <template x-for="suggestion in allSuggestions" :key="suggestion">
                                    <div class="flex items-start text-xs">
                                        <svg class="h-3 w-3 mr-1.5 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-text-secondary" x-text="suggestion"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Preview Google -->
                        <div class="border-t border-border pt-4">
                            <h4 class="text-sm font-semibold text-text-primary mb-2">Aperçu Google</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded p-3 text-xs">
                                <div class="text-blue-700 font-medium truncate" x-text="article.title || 'Titre de votre article'"></div>
                                <div class="text-green-700 text-xs mt-0.5">{{ url('/blog') }}/<span x-text="article.slug || 'url-article'"></span></div>
                                <div class="text-gray-600 mt-1 line-clamp-2" x-text="article.meta_description || 'Description de votre article qui apparaîtra dans les résultats de recherche...'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Fonction Alpine.js AVANT TinyMCE
function articleEditor() {
    return {
        article: {
            title: '',
            slug: '',
            content: '',
            meta_description: '',
            category: '',
            tags: '',
            featured_image: null
        },
        seoScore: 0,
        scores: {
            title: 0,
            content: 0,
            meta: 0,
            images: 0,
            readability: 0
        },
        suggestions: {
            title: [],
            content: [],
            meta: [],
            images: [],
            readability: []
        },
        allSuggestions: [],
        wordCount: 0,
        readingTime: 0,
        isAnalyzing: false,
        imagePreview: null,
        oldTitle: '',
        analyzeTimeout: null,
        
        init() {
            // Générer le slug automatiquement
            this.$watch('article.title', (value) => {
                if (!this.article.slug || this.article.slug === this.slugify(this.oldTitle)) {
                    this.article.slug = this.slugify(value);
                }
                this.oldTitle = value;
            });
        },
        
        slugify(text) {
            if (!text) return '';
            return text
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        
        debounceAnalyze() {
            clearTimeout(this.analyzeTimeout);
            this.analyzeTimeout = setTimeout(() => {
                this.analyzeContent();
            }, 500);
        },
        
        async analyzeContent() {
            if (this.isAnalyzing) return;
            
            // Récupérer le contenu de TinyMCE si disponible
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                this.article.content = tinymce.get('content').getContent();
            }
            
            // Calcul local des scores
            this.calculateLocalScores();
            
            // Si on a assez de contenu, envoyer au serveur
            if (this.article.title.length > 5 || this.article.content.length > 50) {
                this.isAnalyzing = true;
                try {
                    const response = await fetch('{{ route("writer.articles.analyze") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            title: this.article.title,
                            content: this.article.content,
                            meta_description: this.article.meta_description
                        })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.analysis) {
                            // Utiliser les scores du serveur s'ils sont disponibles
                            this.seoScore = Math.round(data.analysis.global_score || this.seoScore);
                        }
                    }
                } catch (error) {
                    console.error('Erreur analyse:', error);
                }
                this.isAnalyzing = false;
            }
        },
        
        calculateLocalScores() {
            const plainText = this.stripHtml(this.article.content);
            this.wordCount = plainText.split(/\s+/).filter(word => word.length > 0).length;
            this.readingTime = Math.ceil(this.wordCount / 200);
            
            // Reset suggestions
            this.suggestions = {
                title: [],
                content: [],
                meta: [],
                images: [],
                readability: []
            };
            
            // Analyse du titre
            let titleScore = 0;
            if (this.article.title) {
                if (this.article.title.length >= 30 && this.article.title.length <= 60) {
                    titleScore = 15;
                } else {
                    titleScore = 8;
                    this.suggestions.title.push(this.article.title.length < 30 ? 
                        'Titre trop court (min. 30 caractères)' : 
                        'Titre trop long (max. 60 caractères)');
                }
            } else {
                this.suggestions.title.push('Ajoutez un titre');
            }
            this.scores.title = Math.min(20, titleScore);
            
            // Analyse du contenu
            let contentScore = 0;
            if (this.wordCount >= 1500) {
                contentScore = 25;
            } else if (this.wordCount >= 800) {
                contentScore = 15;
                this.suggestions.content.push(`Ajoutez ${1500 - this.wordCount} mots pour l'optimal`);
            } else {
                contentScore = 5;
                this.suggestions.content.push('Contenu trop court (min. 1500 mots recommandés)');
            }
            this.scores.content = Math.min(30, contentScore);
            
            // Meta description
            let metaScore = 0;
            if (this.article.meta_description) {
                if (this.article.meta_description.length >= 120 && this.article.meta_description.length <= 160) {
                    metaScore = 12;
                } else {
                    metaScore = 6;
                    this.suggestions.meta.push(this.article.meta_description.length < 120 ?
                        'Meta description trop courte' :
                        'Meta description trop longue');
                }
            } else {
                this.suggestions.meta.push('Ajoutez une meta description');
            }
            this.scores.meta = Math.min(15, metaScore);
            
            // Images
            let imagesScore = 0;
            if (this.imagePreview) {
                imagesScore += 8;
            } else {
                this.suggestions.images.push('Ajoutez une image à la une');
            }
            const contentImages = (this.article.content.match(/<img/gi) || []).length;
            imagesScore += Math.min(7, contentImages * 2);
            if (contentImages === 0) {
                this.suggestions.images.push('Ajoutez des images dans le contenu');
            }
            this.scores.images = Math.min(15, imagesScore);
            
            // Lisibilité
            let readabilityScore = 10; // Score de base
            const hasH2 = /<h2/i.test(this.article.content);
            if (!hasH2 && this.wordCount > 300) {
                this.suggestions.readability.push('Ajoutez des sous-titres H2');
                readabilityScore -= 5;
            }
            this.scores.readability = Math.min(20, readabilityScore);
            
            // Score total
            this.seoScore = Math.round(
                this.scores.title +
                this.scores.content +
                this.scores.meta +
                this.scores.images +
                this.scores.readability
            );
            
            // Compiler les suggestions
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
            
            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 5MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
                this.analyzeContent();
            };
            reader.readAsDataURL(file);
            
            this.article.featured_image = file;
        },
        
        stripHtml(html) {
            const tmp = document.createElement('DIV');
            tmp.innerHTML = html || '';
            return tmp.textContent || tmp.innerText || '';
        },
        
        async submitForm() {
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                // Mettre le contenu TinyMCE dans le textarea
                document.getElementById('content').value = tinymce.get('content').getContent();
            }
            
            if (this.seoScore < 60) {
                if (!confirm('Votre score SEO est très faible. Voulez-vous vraiment publier?')) {
                    return;
                }
            }
            
            document.getElementById('article-form').submit();
        }
    }
}
</script>

<!-- TinyMCE auto-hébergé APRÈS Alpine.js -->
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
    selector: '#content',
    license_key: 'gpl',
    base_url: '/vendor/tinymce',
    suffix: '.min',
    language: 'fr_FR',
    height: 600,

    // Traductions personnalisées pour compléter fr_FR
    language_url: false,
    setup: function(editor) {
        // Traductions manuelles des éléments manquants
        if (tinymce.util.I18n) {
            tinymce.util.I18n.add('fr_FR', {
                'Insert link': 'Insérer un lien',
                'Add link': 'Ajouter un lien',
                'Insert/edit link': 'Insérer/modifier un lien',
                'Link': 'Lien',
                'URL': 'URL',
                'Text to display': 'Texte à afficher',
                'Title': 'Titre',
                'Target': 'Cible',
                'Open link in...': 'Ouvrir le lien dans...',
                'None': 'Aucun',
                'New window': 'Nouvelle fenêtre',
                'Remove link': 'Supprimer le lien',
                'Insert/edit image': 'Insérer/modifier une image',
                'Insert image': 'Insérer une image',
                'Image': 'Image',
                'Source': 'Source',
                'Alternative description': 'Description alternative',
                'Dimensions': 'Dimensions',
                'Width': 'Largeur',
                'Height': 'Hauteur',
                'Constrain proportions': 'Conserver les proportions',
            });
        }

        // Forcer l'alt à l'insertion d'image
        editor.on('BeforeSetContent', function(e) {
            if (e.content.includes('<img')) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(e.content, 'text/html');
                const images = doc.querySelectorAll('img:not([alt])');

                images.forEach(img => {
                    img.setAttribute('alt', '');
                });

                e.content = doc.body.innerHTML;
            }
        });

        // Vérifier l'alt après insertion d'image
        editor.on('NodeChange', function(e) {
            if (e.element && e.element.tagName === 'IMG') {
                const currentAlt = e.element.getAttribute('alt');
                if (!currentAlt || currentAlt === '') {
                    setTimeout(() => {
                        editor.windowManager.open({
                            title: 'Description de l\'image requise (SEO)',
                            body: {
                                type: 'panel',
                                items: [
                                    {
                                        type: 'input',
                                        name: 'alt',
                                        label: 'Texte alternatif',
                                        placeholder: 'Décrivez cette image pour le SEO'
                                    },
                                    {
                                        type: 'htmlpanel',
                                        html: '<p style="font-size: 12px; color: #666;">Le texte alternatif est crucial pour le SEO et l\'accessibilité. Décrivez ce que montre l\'image.</p>'
                                    }
                                ]
                            },
                            buttons: [
                                {
                                    type: 'cancel',
                                    text: 'Annuler'
                                },
                                {
                                    type: 'submit',
                                    text: 'Valider',
                                    primary: true
                                }
                            ],
                            onSubmit: function(dialog) {
                                const data = dialog.getData();
                                if (data.alt && data.alt.trim() !== '') {
                                    e.element.setAttribute('alt', data.alt.trim());
                                    dialog.close();
                                } else {
                                    editor.windowManager.alert('Le texte alternatif est obligatoire pour le SEO');
                                    return false;
                                }
                            }
                        });
                    }, 100);
                }
            }
        });

        // Déclencher l'analyse Alpine
        editor.on('change keyup', function() {
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            if (alpineData) {
                alpineData.debounceAnalyze();
            }
        });
    },

    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'wordcount', 'emoticons'
    ],
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | removeformat fullscreen',
    menubar: 'file edit view insert format tools table',
    block_formats: 'Paragraphe=p; Titre 2=h2; Titre 3=h3; Titre 4=h4; Citation=blockquote',
    content_style: `
        body { 
            font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
            font-size: 16px; 
            line-height: 1.8;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 { font-size: 1.8em; margin-top: 1.5em; color: #2D3748; }
        h3 { font-size: 1.4em; margin-top: 1.3em; color: #4A5568; }
        p { margin: 1em 0; }
        img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.5em 0; }
        blockquote { 
            border-left: 4px solid #38B2AC; 
            padding-left: 1.5em; 
            margin: 1.5em 0;
            font-style: italic;
            color: #4A5568;
        }
    `,
    branding: false,
    promotion: false,
    statusbar: true,
    elementpath: false,
    automatic_uploads: true,
    images_upload_credentials: true,
    image_caption: true,
    image_advtab: true,
    images_upload_handler: function (blobInfo, success, failure) {
        let formData = new FormData();
        formData.append('image', blobInfo.blob(), blobInfo.filename());
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('{{ route("writer.articles.upload-image") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.location) {
                success(result.location);
            } else {
                failure('Erreur lors de l\'upload');
            }
        })
        .catch(() => {
            failure('Erreur lors de l\'upload de l\'image');
        });
    },
    setup: function(editor) {
        // Forcer l'alt à l'insertion d'image
        editor.on('BeforeSetContent', function(e) {
            if (e.content.includes('<img')) {
                // Parser le contenu pour trouver les images sans alt
                const parser = new DOMParser();
                const doc = parser.parseFromString(e.content, 'text/html');
                const images = doc.querySelectorAll('img:not([alt])');
                
                images.forEach(img => {
                    img.setAttribute('alt', '');
                });
                
                e.content = doc.body.innerHTML;
            }
        });
        
        // Vérifier l'alt après insertion d'image
        editor.on('NodeChange', function(e) {
            if (e.element && e.element.tagName === 'IMG') {
                const currentAlt = e.element.getAttribute('alt');
                if (!currentAlt || currentAlt === '') {
                    setTimeout(() => {
                        editor.windowManager.open({
                            title: 'Description de l\'image requise (SEO)',
                            body: {
                                type: 'panel',
                                items: [
                                    {
                                        type: 'input',
                                        name: 'alt',
                                        label: 'Texte alternatif',
                                        placeholder: 'Décrivez cette image pour le SEO'
                                    },
                                    {
                                        type: 'htmlpanel',
                                        html: '<p style="font-size: 12px; color: #666;">Le texte alternatif est crucial pour le SEO et l\'accessibilité. Décrivez ce que montre l\'image.</p>'
                                    }
                                ]
                            },
                            buttons: [
                                {
                                    type: 'cancel',
                                    text: 'Annuler'
                                },
                                {
                                    type: 'submit',
                                    text: 'Valider',
                                    primary: true
                                }
                            ],
                            onSubmit: function(dialog) {
                                const data = dialog.getData();
                                if (data.alt && data.alt.trim() !== '') {
                                    e.element.setAttribute('alt', data.alt.trim());
                                    dialog.close();
                                } else {
                                    editor.windowManager.alert('Le texte alternatif est obligatoire pour le SEO');
                                    return false;
                                }
                            }
                        });
                    }, 100);
                }
            }
        });
        
        // Déclencher l'analyse Alpine
        editor.on('change keyup', function() {
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            if (alpineData) {
                alpineData.debounceAnalyze();
            }
        });
    }
});
</script>
@endpush
@endsection