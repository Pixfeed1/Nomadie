@extends('vendor.layouts.app')

@section('title', 'Modifier l\'article')

@section('page-title', 'Modifier l\'article')
@section('page-description', 'Optimisez votre contenu avec NomadSEO')

@section('content')
<div x-data="articleEditor" class="max-w-7xl mx-auto">
    <form method="POST" action="{{ route('writer.articles.update', $article->id) }}" enctype="multipart/form-data" @submit.prevent="submitForm">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Formulaire principal -->
            <div class="flex-1">
                <!-- Card du formulaire -->
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
                                   @input="analyzeContent()"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                   value="{{ old('title', $article->title) }}"
                                   required>
                            <p class="mt-1 text-xs text-text-secondary">
                                <span x-text="article.title.length"></span>/60 caractères (optimal : 50-60)
                            </p>
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
                                     class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary cursor-pointer transition-colors">
                                    <template x-if="!imagePreview && !existingImage">
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
                                    
                                    <template x-if="imagePreview || existingImage">
                                        <div class="relative">
                                            <img :src="imagePreview || existingImage" alt="Preview" class="max-h-48 mx-auto rounded">
                                            <button type="button" 
                                                    @click.stop="clearImage()" 
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-text-primary mb-2">
                                Contenu
                            </label>
                            <div class="border border-border rounded-lg overflow-hidden">
                                <!-- Toolbar simple -->
                                <div class="bg-bg-alt border-b border-border p-2 flex flex-wrap gap-1">
                                    <button type="button" @click="formatText('bold')" class="p-2 hover:bg-white rounded transition-colors" title="Gras">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="formatText('italic')" class="p-2 hover:bg-white rounded transition-colors" title="Italique">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4M8 20h4m-2-16l-2 16" />
                                        </svg>
                                    </button>
                                    <div class="w-px bg-border mx-1"></div>
                                    <button type="button" @click="formatText('h2')" class="px-2 py-1 hover:bg-white rounded transition-colors text-sm font-medium">
                                        H2
                                    </button>
                                    <button type="button" @click="formatText('h3')" class="px-2 py-1 hover:bg-white rounded transition-colors text-sm font-medium">
                                        H3
                                    </button>
                                    <div class="w-px bg-border mx-1"></div>
                                    <button type="button" @click="formatText('ul')" class="p-2 hover:bg-white rounded transition-colors" title="Liste">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="formatText('link')" class="p-2 hover:bg-white rounded transition-colors" title="Lien">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Editeur -->
                                <div contenteditable="true"
                                     @input="updateContent($event)"
                                     @paste="handlePaste($event)"
                                     x-ref="editor"
                                     class="min-h-[400px] p-4 focus:outline-none">
                                    {!! old('content', $article->content) !!}
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-text-secondary">
                                <span x-text="wordCount"></span> mots • 
                                Temps de lecture : <span x-text="readingTime"></span> min
                            </p>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-text-primary mb-2">
                                Meta Description
                            </label>
                            <textarea id="meta_description" 
                                      name="meta_description"
                                      x-model="article.meta_description"
                                      @input="analyzeContent()"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                      placeholder="Description qui apparaîtra dans les résultats de recherche...">{{ old('meta_description', $article->meta_data['description'] ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-text-secondary">
                                <span x-text="article.meta_description.length"></span>/160 caractères
                            </p>
                        </div>

                        <!-- Mots-clés -->
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">
                                Mots-clés
                            </label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-for="(keyword, index) in keywords" :key="index">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary">
                                        <span x-text="keyword"></span>
                                        <button type="button" @click="removeKeyword(index)" class="ml-2 text-primary hover:text-primary-dark">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            <input type="text" 
                                   @keydown.enter.prevent="addKeyword($event)"
                                   placeholder="Tapez un mot-clé et appuyez sur Entrée"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-text-primary mb-2">
                                Statut
                            </label>
                            <select id="status" 
                                    name="status"
                                    x-model="article.status"
                                    class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="draft" {{ $article->status === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending" {{ $article->status === 'pending' ? 'selected' : '' }}>En attente de validation</option>
                                <option value="published" {{ $article->status === 'published' ? 'selected' : '' }} :disabled="seoScore < 78">
                                    Publié {{ $article->latestSeoAnalysis && $article->latestSeoAnalysis->global_score < 78 ? '(Score SEO insuffisant)' : '' }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-bg-alt px-6 py-4 border-t border-border flex justify-between items-center">
                        <div class="text-sm text-text-secondary">
                            Dernière modification : {{ $article->updated_at->diffForHumans() }}
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('writer.articles.index') }}" 
                               class="px-4 py-2 text-text-secondary hover:text-text-primary transition-colors">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                                Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panneau NomadSEO -->
            <div class="w-full lg:w-96">
                <!-- Historique des scores -->
                @if($analysis)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                    <div class="p-4 border-b border-border">
                        <h4 class="text-sm font-semibold text-text-primary">Historique SEO</h4>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-text-secondary">Dernière analyse :</span>
                            <span class="font-medium text-text-primary">{{ $analysis->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-text-secondary">Score actuel :</span>
                            <span class="font-bold {{ $analysis->global_score >= 78 ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ round($analysis->global_score) }}/100
                            </span>
                        </div>
                        @if($analysis->is_dofollow)
                        <div class="mt-3 p-2 bg-green-50 rounded text-xs text-green-800">
                            ✓ Cet article a un lien DoFollow
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-6">
                    <!-- En-tête -->
                    <div class="bg-primary text-white p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="font-semibold">NomadSEO</span>
                            </div>
                            <button type="button" @click="analyzeContent()" class="text-white/80 hover:text-white">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Score global -->
                    <div class="p-6 border-b border-border">
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="transform -rotate-90 h-32 w-32">
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200" />
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none"
                                            :stroke-dasharray="351.86"
                                            :stroke-dashoffset="351.86 - (351.86 * seoScore / 100)"
                                            :class="{
                                                'text-red-500': seoScore < 50,
                                                'text-yellow-500': seoScore >= 50 && seoScore < 78,
                                                'text-green-500': seoScore >= 78
                                            }"
                                            class="transition-all duration-500" />
                                </svg>
                                <span class="absolute text-3xl font-bold" 
                                      :class="{
                                          'text-red-500': seoScore < 50,
                                          'text-yellow-500': seoScore >= 50 && seoScore < 78,
                                          'text-green-500': seoScore >= 78
                                      }">
                                    <span x-text="seoScore"></span>
                                </span>
                            </div>
                            <p class="mt-4 text-sm font-medium text-text-primary">Score SEO Global</p>
                            <p class="text-xs text-text-secondary mt-1">
                                <template x-if="seoScore < 78">
                                    <span>Minimum requis : 78/100</span>
                                </template>
                                <template x-if="seoScore >= 78">
                                    <span class="text-green-600">✓ Score suffisant pour publier</span>
                                </template>
                            </p>
                        </div>
                    </div>

                    <!-- Scores détaillés -->
                    <div class="p-4 space-y-3">
                        <div class="space-y-2">
                            <!-- Score Contenu -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-text-secondary">Contenu</span>
                                    <span class="text-xs font-medium" x-text="scores.content + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + scores.content + '%'"></div>
                                </div>
                            </div>

                            <!-- Score Technique -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-text-secondary">Technique</span>
                                    <span class="text-xs font-medium" x-text="scores.technical + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-accent h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + scores.technical + '%'"></div>
                                </div>
                            </div>

                            <!-- Score Images -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-text-secondary">Images</span>
                                    <span class="text-xs font-medium" x-text="scores.images + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-success h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + scores.images + '%'"></div>
                                </div>
                            </div>

                            <!-- Score Engagement -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-text-secondary">Engagement</span>
                                    <span class="text-xs font-medium" x-text="scores.engagement + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + scores.engagement + '%'"></div>
                                </div>
                            </div>

                            <!-- Score Authenticité -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-text-secondary">Authenticité</span>
                                    <span class="text-xs font-medium" x-text="scores.authenticity + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full transition-all duration-300" 
                                         :style="'width: ' + scores.authenticity + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestions -->
                    <div class="border-t border-border">
                        <div class="p-4">
                            <h4 class="text-sm font-semibold text-text-primary mb-3">Suggestions d'amélioration</h4>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <template x-for="suggestion in suggestions" :key="suggestion.id">
                                    <div class="flex items-start p-2 rounded-lg" 
                                         :class="suggestion.passed ? 'bg-green-50' : 'bg-yellow-50'">
                                        <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" 
                                             :class="suggestion.passed ? 'text-green-500' : 'text-yellow-600'"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <template x-if="suggestion.passed">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </template>
                                            <template x-if="!suggestion.passed">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </template>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium" 
                                               :class="suggestion.passed ? 'text-green-800' : 'text-yellow-800'"
                                               x-text="suggestion.message"></p>
                                        </div>
                                    </div>
                                </template>
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
function articleEditor() {
    return {
        article: {
            title: '{{ $article->title }}',
            content: `{!! addslashes($article->content) !!}`,
            meta_description: '{{ $article->meta_data['description'] ?? '' }}',
            status: '{{ $article->status }}'
        },
        keywords: {!! json_encode($article->meta_data['keywords'] ?? []) !!},
        imagePreview: null,
        existingImage: @json($article->featured_image ? asset('storage/' . $article->featured_image) : null),
        seoScore: {{ $analysis ? round($analysis->global_score) : 0 }},
        scores: {
            content: {{ $analysis ? round($analysis->content_score) : 0 }},
            technical: {{ $analysis ? round($analysis->technical_score) : 0 }},
            images: {{ $analysis ? round($analysis->images_score) : 0 }},
            engagement: {{ $analysis ? round($analysis->engagement_score) : 0 }},
            authenticity: {{ $analysis ? round($analysis->authenticity_score) : 0 }}
        },
        suggestions: [],
        wordCount: 0,
        readingTime: 0,
        analyzeTimer: null,

        init() {
            // Calculer les stats initiales
            this.updateWordCount();
            
            // Analyse initiale après chargement
            this.$nextTick(() => {
                this.analyzeContent();
            });
        },

        formatText(command) {
            const editor = this.$refs.editor;
            
            if (command === 'bold') {
                document.execCommand('bold', false, null);
            } else if (command === 'italic') {
                document.execCommand('italic', false, null);
            } else if (command === 'h2') {
                document.execCommand('formatBlock', false, '<h2>');
            } else if (command === 'h3') {
                document.execCommand('formatBlock', false, '<h3>');
            } else if (command === 'ul') {
                document.execCommand('insertUnorderedList', false, null);
            } else if (command === 'link') {
                const url = prompt('URL du lien :');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            }
            
            editor.focus();
            this.updateContent({ target: editor });
        },

        updateContent(event) {
            this.article.content = event.target.innerHTML;
            this.updateWordCount();
            
            // Déclencher l'analyse après 1 seconde d'inactivité
            clearTimeout(this.analyzeTimer);
            this.analyzeTimer = setTimeout(() => {
                this.analyzeContent();
            }, 1000);
        },

        updateWordCount() {
            const text = this.$refs.editor ? this.$refs.editor.innerText : '';
            this.wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
            this.readingTime = Math.ceil(this.wordCount / 200);
        },

        handlePaste(event) {
            event.preventDefault();
            const text = event.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        },

        addKeyword(event) {
            const keyword = event.target.value.trim();
            if (keyword && !this.keywords.includes(keyword)) {
                this.keywords.push(keyword);
                event.target.value = '';
            }
        },

        removeKeyword(index) {
            this.keywords.splice(index, 1);
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                    this.existingImage = null;
                };
                reader.readAsDataURL(file);
            }
        },

        clearImage() {
            this.imagePreview = null;
            this.existingImage = null;
            this.$refs.imageInput.value = '';
        },

        async analyzeContent() {
            if (this.article.title.length < 10 && this.article.content.length < 100) {
                return;
            }

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

                const data = await response.json();
                
                if (data.success) {
                    this.seoScore = Math.round(data.analysis.global_score);
                    this.scores = {
                        content: Math.round(data.analysis.content_score),
                        technical: Math.round(data.analysis.technical_score),
                        images: Math.round(data.analysis.images_score),
                        engagement: Math.round(data.analysis.engagement_score),
                        authenticity: Math.round(data.analysis.authenticity_score)
                    };
                    
                    // Transformer les détails en suggestions
                    this.suggestions = data.analysis.details.map((detail, index) => ({
                        id: index,
                        message: detail.feedback.message || detail.criterion,
                        passed: detail.passed
                    }));
                }
            } catch (error) {
                console.error('Erreur analyse SEO:', error);
            }
        },

        submitForm(event) {
            // Ajouter les mots-clés au formulaire
            const keywordsInput = document.createElement('input');
            keywordsInput.type = 'hidden';
            keywordsInput.name = 'keywords';
            keywordsInput.value = JSON.stringify(this.keywords);
            event.target.appendChild(keywordsInput);
            
            // Soumettre le formulaire
            event.target.submit();
        }
    }
}
</script>
@endpush
@endsection