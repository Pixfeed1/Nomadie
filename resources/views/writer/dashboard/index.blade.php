@extends('layouts.writer')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Articles publiés -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Articles publiés</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $articlesStats['published'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Score SEO moyen -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Score SEO moyen</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ round($seoStats['average_score']) }}/100</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statut des liens -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Statut des liens</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">
                        {{ $seoStats['dofollow_status'] ? 'DoFollow' : 'NoFollow' }}
                    </p>
                </div>
                <div class="h-10 w-10 rounded-full {{ $seoStats['dofollow_status'] ? 'bg-success/10' : 'bg-accent/10' }} flex items-center justify-center">
                    <svg class="h-5 w-5 {{ $seoStats['dofollow_status'] ? 'text-success' : 'text-accent' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Badges -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Badges</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $badges['unlocked'] }}/{{ $badges['total'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Progression DoFollow (si pas encore atteint) -->
    @if(!$seoStats['dofollow_status'])
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <div class="flex items-center mb-4">
            <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-text-primary">Progression vers DoFollow</h3>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium text-text-primary">Progression globale</span>
                    <span class="font-bold text-primary">{{ $doFollowProgress }}%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-primary-dark h-3 rounded-full transition-all duration-500" style="width: {{ $doFollowProgress }}%"></div>
                </div>
            </div>
            <p class="text-sm text-text-secondary">
                Continuez à publier des articles de qualité pour débloquer les liens DoFollow ! Plus votre score SEO est élevé, plus vite vous y arriverez.
            </p>
        </div>
    </div>
    @endif

    <!-- Articles récents et badges -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Articles récents -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-border">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-text-primary">Articles récents</h3>
                    <a href="{{ route('writer.articles.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium transition-colors">
                        Voir tout →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentArticles as $article)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-text-primary hover:text-primary transition-colors">
                                <a href="{{ route('writer.articles.edit', $article) }}">{{ $article->title }}</a>
                            </h4>
                            <p class="text-sm text-text-secondary mt-1">
                                {{ $article->created_at->format('d M Y') }} •
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($article->status === 'published') bg-success/10 text-success
                                    @elseif($article->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-accent/10 text-accent
                                    @endif">
                                    @if($article->status === 'published') Publié
                                    @elseif($article->status === 'draft') Brouillon
                                    @else En attente
                                    @endif
                                </span>
                            </p>
                        </div>
                        @if($article->seoAnalysis)
                        <div class="ml-4 flex items-center">
                            <div class="text-center">
                                <span class="text-2xl font-bold
                                    @if($article->seoAnalysis->global_score >= 78) text-success
                                    @elseif($article->seoAnalysis->global_score >= 60) text-accent
                                    @else text-error
                                    @endif">
                                    {{ round($article->seoAnalysis->global_score) }}
                                </span>
                                <p class="text-xs text-text-secondary">SEO</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                        <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-text-secondary mb-4">Aucun article pour le moment</p>
                    <a href="{{ route('writer.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer votre premier article
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Badges récents -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-border">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-text-primary">Badges récents</h3>
                    <a href="{{ route('writer.badges.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium transition-colors">
                        Voir tout →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($badges['recent']->count() > 0)
                <div class="space-y-4">
                    @foreach($badges['recent'] as $badge)
                    <div class="flex items-center p-3 bg-gradient-to-r from-accent/5 to-accent/10 rounded-lg border border-accent/20">
                        <div class="h-12 w-12 rounded-full bg-accent/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">{{ $badge->icon }}</span>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-text-primary truncate">{{ $badge->name }}</p>
                            <p class="text-xs text-text-secondary">{{ $badge->pivot->unlocked_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-text-secondary">Aucun badge débloqué</p>
                </div>
                @endif

                @if($badges['next'])
                <div class="mt-6 pt-6 border-t border-border">
                    <p class="text-xs text-text-secondary mb-3 font-medium uppercase">Prochain badge</p>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                            <span class="text-xl opacity-50 filter grayscale">{{ $badges['next']->icon }}</span>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-text-primary truncate">{{ $badges['next']->name }}</p>
                            <p class="text-xs text-text-secondary">{{ $badges['next']->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action rapide -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-text-primary mb-4">Actions rapides</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('writer.articles.create') }}" class="flex items-center p-4 bg-gradient-to-r from-primary/5 to-primary/10 hover:from-primary/10 hover:to-primary/15 rounded-lg border border-primary/20 transition-all group">
                <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-text-primary group-hover:text-primary transition-colors">Nouvel article</p>
                    <p class="text-xs text-text-secondary">Créer un article</p>
                </div>
            </a>

            <a href="{{ route('writer.briefs.index') }}" class="flex items-center p-4 bg-gradient-to-r from-accent/5 to-accent/10 hover:from-accent/10 hover:to-accent/15 rounded-lg border border-accent/20 transition-all group">
                <div class="h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center mr-3">
                    <svg class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-text-primary group-hover:text-accent transition-colors">Briefs disponibles</p>
                    <p class="text-xs text-text-secondary">Commandes d'articles</p>
                </div>
            </a>

            <a href="{{ route('writer.badges.index') }}" class="flex items-center p-4 bg-gradient-to-r from-success/5 to-success/10 hover:from-success/10 hover:to-success/15 rounded-lg border border-success/20 transition-all group">
                <div class="h-10 w-10 rounded-full bg-success/20 flex items-center justify-center mr-3">
                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-text-primary group-hover:text-success transition-colors">Mes badges</p>
                    <p class="text-xs text-text-secondary">{{ $badges['unlocked'] }}/{{ $badges['total'] }} débloqués</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
