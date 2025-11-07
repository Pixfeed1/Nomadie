@extends('layouts.public')

@section('title', 'Catégorie ' . ucfirst($category) . ' - Blog')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-text-primary">{{ ucfirst($category) }}</h1>
            <p class="mt-4 text-lg text-text-secondary">Tous nos articles sur la thématique {{ $category }}</p>
        </div>

        <!-- Catégories -->
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <a href="{{ route('blog') }}" class="px-4 py-2 rounded-full bg-white border border-border text-text-primary hover:bg-bg-alt transition-colors">
                Tous les articles
            </a>
            <a href="{{ route('blog.category', 'destinations') }}" class="px-4 py-2 rounded-full {{ $category == 'destinations' ? 'bg-primary text-white' : 'bg-white border border-border text-text-primary hover:bg-bg-alt' }} transition-colors">
                Destinations
            </a>
            <a href="{{ route('blog.category', 'conseils') }}" class="px-4 py-2 rounded-full {{ $category == 'conseils' ? 'bg-primary text-white' : 'bg-white border border-border text-text-primary hover:bg-bg-alt' }} transition-colors">
                Conseils
            </a>
            <a href="{{ route('blog.category', 'gastronomie') }}" class="px-4 py-2 rounded-full {{ $category == 'gastronomie' ? 'bg-primary text-white' : 'bg-white border border-border text-text-primary hover:bg-bg-alt' }} transition-colors">
                Gastronomie
            </a>
            <a href="{{ route('blog.category', 'ecotourisme') }}" class="px-4 py-2 rounded-full {{ $category == 'ecotourisme' ? 'bg-primary text-white' : 'bg-white border border-border text-text-primary hover:bg-bg-alt' }} transition-colors">
                Écotourisme
            </a>
            <a href="{{ route('blog.category', 'culture') }}" class="px-4 py-2 rounded-full {{ $category == 'culture' ? 'bg-primary text-white' : 'bg-white border border-border text-text-primary hover:bg-bg-alt' }} transition-colors">
                Culture
            </a>
        </div>

        <!-- Liste des articles de la catégorie -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($articles as $article)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden card">
                    <a href="{{ route('blog.show', $article['slug']) }}" class="block overflow-hidden aspect-video">
                        <img src="{{ asset('/images/' . ($article['image'] ?? 'blog/placeholder.jpg')) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" onerror="this.src='/api/placeholder/400/250';this.onerror=null;">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center mb-2">
                            <span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">{{ $article['category'] }}</span>
                            <span class="ml-auto text-xs text-text-secondary">{{ $article['reading_time'] }} min</span>
                        </div>
                        <h2 class="text-lg font-bold text-text-primary mb-2">
                            <a href="{{ route('blog.show', $article['slug']) }}" class="hover:text-primary transition-colors">{{ $article['title'] }}</a>
                        </h2>
                        <p class="text-sm text-text-secondary mb-3">{{ Str::limit($article['excerpt'], 120) }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-text-secondary">{{ \Carbon\Carbon::parse($article['date'])->locale('fr')->isoFormat('LL') }}</span>
                            <a href="{{ route('blog.show', $article['slug']) }}" class="text-primary hover:text-primary-dark text-xs font-medium">Lire la suite</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-text-secondary text-lg">Aucun article dans cette catégorie pour le moment.</p>
                    <a href="{{ route('blog') }}" class="mt-4 inline-block text-primary hover:text-primary-dark font-medium">
                        Voir tous les articles
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination si nécessaire -->
        @if(isset($articles) && method_exists($articles, 'hasPages') && $articles->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
