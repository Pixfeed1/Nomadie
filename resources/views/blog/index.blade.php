@extends('layouts.public')

@section('title', 'Blog - Conseils et Inspirations Voyage')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-text-primary">Blog Voyage</h1>
            <p class="mt-4 text-lg text-text-secondary">Conseils, inspirations et guides pratiques pour vos prochaines aventures</p>
        </div>

        <!-- Catégories -->
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <a href="{{ route('blog') }}" class="px-4 py-2 rounded-full bg-primary text-white hover:bg-primary-dark transition-colors">
                Tous les articles
            </a>
            <a href="{{ route('blog.category', 'destinations') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Destinations
            </a>
            <a href="{{ route('blog.category', 'conseils') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Conseils
            </a>
            <a href="{{ route('blog.category', 'gastronomie') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Gastronomie
            </a>
            <a href="{{ route('blog.category', 'ecotourisme') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Écotourisme
            </a>
            <a href="{{ route('blog.category', 'culture') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Culture
            </a>
        </div>
        
        <!-- Articles Vedettes (les 2 premiers articles) -->
        @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            @foreach($articles->take(2) as $article)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card">
                <a href="{{ route('blog.show', $article->slug) }}" class="block overflow-hidden aspect-video">
                    <img src="{{ $article->image_url ?? asset('images/blog/placeholder.jpg') }}" 
                         alt="{{ $article->title }}" 
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" 
                         onerror="this.src='/api/placeholder/800/450';this.onerror=null;">
                </a>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                            {{ $article->category ?? 'Non catégorisé' }}
                        </span>
                        <span class="ml-2 text-xs text-text-secondary">
                            {{ $article->created_at->locale('fr')->isoFormat('LL') }}
                        </span>
                        <span class="ml-auto text-xs text-text-secondary flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $article->reading_time ?? 5 }} min
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-text-primary mb-2">
                        <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-primary transition-colors">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-text-secondary mb-4">
                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 150) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-secondary">
                            Par {{ $article->author->display_name ?? $article->author->name ?? 'Anonyme' }}
                        </span>
                        <a href="{{ route('blog.show', $article->slug) }}" class="text-primary hover:text-primary-dark text-sm font-medium flex items-center">
                            Lire la suite
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        
        <!-- Autres Articles (reste des articles) -->
        @if($articles->count() > 2)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($articles->skip(2) as $article)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card">
                <a href="{{ route('blog.show', $article->slug) }}" class="block overflow-hidden aspect-video">
                    <img src="{{ $article->image_url ?? asset('images/blog/placeholder.jpg') }}" 
                         alt="{{ $article->title }}" 
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" 
                         onerror="this.src='/api/placeholder/400/250';this.onerror=null;">
                </a>
                <div class="p-5">
                    <div class="flex items-center mb-2">
                        <span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                            {{ $article->category ?? 'Non catégorisé' }}
                        </span>
                        <span class="ml-auto text-xs text-text-secondary">
                            {{ $article->reading_time ?? 5 }} min
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-text-primary mb-2">
                        <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-primary transition-colors">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-sm text-text-secondary mb-3">
                        {{ Str::limit($article->excerpt ?? strip_tags($article->content), 120) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-text-secondary">
                            {{ $article->created_at->locale('fr')->isoFormat('LL') }}
                        </span>
                        <a href="{{ route('blog.show', $article->slug) }}" class="text-primary hover:text-primary-dark text-xs font-medium">
                            Lire la suite
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        
        <!-- Message si aucun article -->
        @if($articles->isEmpty())
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun article disponible</h3>
            <p class="text-gray-500">Les articles apparaîtront ici une fois publiés.</p>
        </div>
        @endif
        
        <!-- Pagination -->
        @if($articles->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $articles->links() }}
        </div>
        @endif
        
        <!-- Newsletter -->
        <div class="mt-16 bg-primary/5 rounded-lg p-8 border border-primary/20">
            <div class="max-w-xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-text-primary mb-2">Abonnez-vous à notre newsletter</h2>
                <p class="text-text-secondary mb-6">Recevez nos derniers articles et conseils de voyage directement dans votre boîte mail.</p>
                <form class="flex flex-col sm:flex-row gap-3" method="POST" action="{{ route('newsletter.subscribe') }}">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Votre adresse email" 
                           class="flex-1 px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                           required>
                    <button type="submit" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                        S'abonner
                    </button>
                </form>
                <p class="mt-3 text-xs text-text-secondary">
                    En vous abonnant, vous acceptez notre politique de confidentialité. Vous pouvez vous désabonner à tout moment.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection