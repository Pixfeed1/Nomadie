@extends('layouts.public')

@section('title', $article['title'] . ' - Blog')

{{-- Meta tags Open Graph et Twitter Card --}}
@section('meta')
    {{-- Meta description standard --}}
    <meta name="description" content="{{ $article['excerpt'] }}">
    
    {{-- Open Graph tags --}}
    <meta property="og:title" content="{{ $article['title'] }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:description" content="{{ $article['excerpt'] }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Nomadie') }}">
    <meta property="og:locale" content="fr_FR">
    
    @if(isset($article['image']) && $article['image'])
        <meta property="og:image" content="{{ asset('/images/' . $article['image']) }}">
        <meta property="og:image:alt" content="{{ $article['title'] }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    
    @if(isset($article['date']))
        <meta property="article:published_time" content="{{ \Carbon\Carbon::parse($article['date'])->toISOString() }}">
    @endif
    
    @if(isset($article['author']))
        <meta property="article:author" content="{{ $article['author'] }}">
    @endif
    
    @if(isset($article['category']))
        <meta property="article:section" content="{{ $article['category'] }}">
    @endif
    
    {{-- Twitter Card tags --}}
    <meta name="twitter:card" content="{{ isset($article['image']) && $article['image'] ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $article['title'] }}">
    <meta name="twitter:description" content="{{ $article['excerpt'] }}">
    
    @if(isset($article['image']) && $article['image'])
        <meta name="twitter:image" content="{{ asset('/images/' . $article['image']) }}">
        <meta name="twitter:image:alt" content="{{ $article['title'] }}">
    @endif
    
    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $article['title'] }}",
        "description": "{{ $article['excerpt'] }}",
        "url": "{{ request()->url() }}",
        "datePublished": "{{ isset($article['date']) ? \Carbon\Carbon::parse($article['date'])->toISOString() : '' }}",
        "author": {
            "@type": "Person",
            "name": "{{ $article['author'] ?? 'Anonyme' }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ config('app.name', 'Nomadie') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}"
            }
        }@if(isset($article['image']) && $article['image']),
        "image": {
            "@type": "ImageObject",
            "url": "{{ asset('/images/' . $article['image']) }}",
            "width": 1200,
            "height": 630
        }@endif@if(isset($article['category'])),
        "articleSection": "{{ $article['category'] }}"@endif@if(isset($article['reading_time'])),
        "timeRequired": "PT{{ $article['reading_time'] }}M"@endif
    }
    </script>
@endsection

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Catégories / Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center text-sm text-text-secondary">
                <a href="{{ route('blog') }}" class="hover:text-primary transition-colors">Blog</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('blog.category', $article['category']) }}" class="hover:text-primary transition-colors">{{ $article['category'] }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-text-primary font-medium truncate">{{ $article['title'] }}</span>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Image principale -->
            <div class="w-full aspect-video overflow-hidden">
                <img src="{{ asset('/images/' . ($article['image'] ?? 'blog/placeholder.jpg')) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover" onerror="this.src='/api/placeholder/1200/630';this.onerror=null;">
            </div>
            
            <!-- Contenu de l'article -->
            <div class="p-8">
                <!-- En-tête de l'article -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">{{ $article['category'] }}</span>
                        <span class="ml-4 text-sm text-text-secondary">{{ \Carbon\Carbon::parse($article['date'])->locale('fr')->isoFormat('LL') }}</span>
                        <span class="ml-auto text-sm text-text-secondary flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $article['reading_time'] }} min de lecture
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold text-text-primary mb-4">{{ $article['title'] }}</h1>
                    <p class="text-lg text-text-secondary italic">{{ $article['excerpt'] }}</p>
                </div>
                
                <!-- Info auteur -->
                <div class="flex items-center border-t border-b border-border py-4 mb-8">
                    <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        {{ substr($article['author'], 0, 2) }}
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-primary">Par {{ $article['author'] }}</p>
                        <p class="text-xs text-text-secondary">Publié le {{ \Carbon\Carbon::parse($article['date'])->locale('fr')->isoFormat('LL') }}</p>
                    </div>
                    <div class="ml-auto flex space-x-3">
                        {{-- Boutons de partage social améliorés --}}
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article['title']) }}"
                           target="_blank"
                           class="text-text-secondary hover:text-primary transition-colors"
                           title="Partager sur X (Twitter)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                           target="_blank" 
                           class="text-text-secondary hover:text-primary transition-colors"
                           title="Partager sur Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                           target="_blank" 
                           class="text-text-secondary hover:text-primary transition-colors"
                           title="Partager sur LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article['title'] . ' ' . request()->url()) }}" 
                           target="_blank" 
                           class="text-text-secondary hover:text-primary transition-colors"
                           title="Partager sur WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Corps de l'article -->
                <div class="prose max-w-none prose-primary">
                    {!! $article['content'] !!}
                </div>
                
                <!-- Tags -->
                <div class="mt-10 pt-6 border-t border-border">
                    <div class="flex flex-wrap gap-2">
                        @foreach($article['tags'] ?? [] as $tag)
                            <a href="#" class="px-3 py-1 bg-bg-alt text-text-secondary text-sm rounded-full hover:bg-primary/10 hover:text-primary transition-colors">
                                #{{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Articles connexes -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-text-primary mb-6">Articles connexes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles ?? [] as $relatedArticle)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden card">
                    <a href="{{ route('blog.show', $relatedArticle['slug']) }}" class="block overflow-hidden aspect-video">
                        <img src="{{ asset('/images/' . ($relatedArticle['image'] ?? 'blog/placeholder.jpg')) }}" alt="{{ $relatedArticle['title'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" onerror="this.src='/api/placeholder/400/250';this.onerror=null;">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center mb-2">
                            <span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">{{ $relatedArticle['category'] }}</span>
                            <span class="ml-auto text-xs text-text-secondary">{{ $relatedArticle['reading_time'] }} min</span>
                        </div>
                        <h3 class="text-lg font-bold text-text-primary mb-2">
                            <a href="{{ route('blog.show', $relatedArticle['slug']) }}" class="hover:text-primary transition-colors">{{ $relatedArticle['title'] }}</a>
                        </h3>
                        <p class="text-sm text-text-secondary mb-3">{{ Str::limit($relatedArticle['excerpt'], 100) }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-text-secondary">{{ \Carbon\Carbon::parse($relatedArticle['date'])->locale('fr')->isoFormat('LL') }}</span>
                            <a href="{{ route('blog.show', $relatedArticle['slug']) }}" class="text-primary hover:text-primary-dark text-xs font-medium">Lire la suite</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Commentaires -->
        <div class="mt-12 bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-text-primary mb-6">Commentaires ({{ $comments->total() }})</h2>

            @auth
            <!-- Formulaire de commentaire -->
            <div class="mb-8">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <form action="{{ route('comments.store', $article->slug) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <textarea name="content" rows="3" placeholder="Ajouter un commentaire..." class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                                    Publier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-8 p-4 bg-bg-alt rounded-lg text-center">
                <p class="text-text-secondary">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">Connectez-vous</a> pour laisser un commentaire
                </p>
            </div>
            @endauth
            
            <!-- Liste des commentaires -->
            <div class="space-y-6">
                @forelse($comments as $comment)
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold">
                                {{ strtoupper(substr($comment->author_name ?? $comment->user->name ?? 'A', 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-bg-alt p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-text-primary">{{ $comment->author_name ?? $comment->user->name ?? 'Anonyme' }}</span>
                                    <span class="text-xs text-text-secondary">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-text-secondary">{{ $comment->content }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-text-secondary/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-text-secondary">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination commentaires -->
            @if($comments->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $comments->links() }}
            </div>
            @endif
        </div>
        
        <!-- Newsletter -->
        <div class="mt-12 bg-primary/5 rounded-lg p-8 border border-primary/20">
            <div class="max-w-xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-text-primary mb-2">Restez informé</h2>
                <p class="text-text-secondary mb-6">Recevez nos derniers articles et conseils de voyage directement dans votre boîte mail.</p>
                <form class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="Votre adresse email" class="flex-1 px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                    <button type="submit" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                        S'abonner
                    </button>
                </form>
                <p class="mt-3 text-xs text-text-secondary">En vous abonnant, vous acceptez notre politique de confidentialité. Vous pouvez vous désabonner à tout moment.</p>
            </div>
        </div>
    </div>
</div>
@endsection