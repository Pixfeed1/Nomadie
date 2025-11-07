@extends('layouts.public')

@section('title', 'Recherche: ' . $query . ' - Blog')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de recherche -->
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-text-primary mb-4">Résultats de recherche</h1>
            <p class="text-lg text-text-secondary">
                @if(isset($articles) && count($articles) > 0)
                    {{ count($articles) }} résultat(s) pour "<span class="text-text-primary font-medium">{{ $query }}</span>"
                @else
                    Aucun résultat pour "<span class="text-text-primary font-medium">{{ $query }}</span>"
                @endif
            </p>

            <!-- Formulaire de recherche -->
            <form action="{{ route('blog.search') }}" method="GET" class="mt-6 max-w-2xl">
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ $query ?? '' }}"
                           placeholder="Rechercher un article..."
                           class="w-full px-5 py-3 pr-12 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                           required>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if(isset($articles) && count($articles) > 0)
            <!-- Liste des résultats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden card">
                        <a href="{{ route('blog.show', $article['slug']) }}" class="block overflow-hidden aspect-video">
                            <img src="{{ asset('/images/' . ($article['image'] ?? 'blog/placeholder.jpg')) }}"
                                 alt="{{ $article['title'] }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 onerror="this.src='/api/placeholder/400/250';this.onerror=null;">
                        </a>
                        <div class="p-5">
                            <div class="flex items-center mb-2">
                                <span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">{{ $article['category'] }}</span>
                                <span class="ml-auto text-xs text-text-secondary">{{ $article['reading_time'] }} min</span>
                            </div>
                            <h2 class="text-lg font-bold text-text-primary mb-2">
                                <a href="{{ route('blog.show', $article['slug']) }}" class="hover:text-primary transition-colors">
                                    {!! str_replace($query, '<mark class="bg-yellow-200 px-1">' . $query . '</mark>', $article['title']) !!}
                                </a>
                            </h2>
                            <p class="text-sm text-text-secondary mb-3">
                                {!! str_replace($query, '<mark class="bg-yellow-200 px-1">' . $query . '</mark>', Str::limit($article['excerpt'], 120)) !!}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-text-secondary">{{ \Carbon\Carbon::parse($article['date'])->locale('fr')->isoFormat('LL') }}</span>
                                <a href="{{ route('blog.show', $article['slug']) }}" class="text-primary hover:text-primary-dark text-xs font-medium">Lire la suite</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination si nécessaire -->
            @if(method_exists($articles, 'hasPages') && $articles->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $articles->appends(['q' => $query])->links() }}
            </div>
            @endif
        @else
            <!-- Aucun résultat -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-text-secondary/30 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-text-primary mb-2">Aucun résultat trouvé</h2>
                <p class="text-text-secondary mb-8">
                    Nous n'avons trouvé aucun article correspondant à votre recherche.
                </p>

                <!-- Suggestions -->
                <div class="max-w-lg mx-auto bg-bg-alt rounded-lg p-6">
                    <h3 class="font-bold text-text-primary mb-3">Suggestions :</h3>
                    <ul class="text-left text-text-secondary space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Vérifiez l'orthographe de vos mots-clés
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Essayez des termes plus généraux
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Utilisez moins de mots-clés
                        </li>
                    </ul>
                </div>

                <!-- Lien retour -->
                <a href="{{ route('blog') }}" class="mt-8 inline-flex items-center text-primary hover:text-primary-dark font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour au blog
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
