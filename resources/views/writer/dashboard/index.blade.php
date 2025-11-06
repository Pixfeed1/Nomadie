@extends('vendor.layouts.app')

@section('title', 'Dashboard Rédacteur')

@section('page-title', 'Dashboard')
@section('page-description', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Articles publiés -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Articles publiés</p>
                    <p class="text-2xl font-bold text-text-primary">{{ $articlesStats['published'] }}</p>
                    <p class="text-xs text-text-secondary mt-1">Total : {{ $articlesStats['total'] }}</p>
                </div>
                <div class="h-12 w-12 bg-primary/10 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Score SEO moyen -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Score SEO moyen</p>
                    <p class="text-2xl font-bold 
                        @if($seoStats['average_score'] >= 78) text-green-600
                        @elseif($seoStats['average_score'] >= 60) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ round($seoStats['average_score']) }}/100
                    </p>
                    <p class="text-xs text-text-secondary mt-1">Meilleur : {{ round($seoStats['best_score']) }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statut DoFollow -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Statut des liens</p>
                    <p class="text-2xl font-bold {{ $seoStats['dofollow_status'] ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $seoStats['dofollow_status'] ? 'DoFollow' : 'NoFollow' }}
                    </p>
                    @if(!$seoStats['dofollow_status'])
                    <p class="text-xs text-text-secondary mt-1">Progression : {{ $doFollowProgress }}%</p>
                    @else
                    <p class="text-xs text-green-600 mt-1">✓ Activé</p>
                    @endif
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Badges débloqués -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Badges</p>
                    <p class="text-2xl font-bold text-text-primary">
                        {{ $badges['unlocked'] }}/{{ $badges['total'] }}
                    </p>
                    <p class="text-xs text-text-secondary mt-1">
                        {{ round(($badges['unlocked'] / $badges['total']) * 100) }}% complété
                    </p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Progression DoFollow (si pas encore atteint) -->
    @if(!$seoStats['dofollow_status'])
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-text-primary mb-4">Progression vers DoFollow</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Progression globale</span>
                    <span class="font-medium">{{ $doFollowProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full" style="width: {{ $doFollowProgress }}%"></div>
                </div>
            </div>
            <p class="text-sm text-text-secondary">
                Continuez à publier des articles de qualité pour débloquer les liens DoFollow !
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
                    <a href="{{ route('writer.articles.index') }}" class="text-sm text-primary hover:text-primary-dark">
                        Voir tout →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentArticles as $article)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-text-primary">{{ $article->title }}</h4>
                            <p class="text-sm text-text-secondary mt-1">
                                {{ $article->created_at->format('d M Y') }} • 
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($article->status === 'published') bg-green-100 text-green-800
                                    @elseif($article->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($article->status) }}
                                </span>
                            </p>
                        </div>
                        @if($article->seoAnalysis)
                        <div class="ml-4">
                            <span class="text-lg font-bold 
                                @if($article->seoAnalysis->global_score >= 78) text-green-600
                                @elseif($article->seoAnalysis->global_score >= 60) text-yellow-600
                                @else text-red-600
                                @endif">
                                {{ round($article->seoAnalysis->global_score) }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-text-secondary">Aucun article pour le moment</p>
                    <a href="{{ route('writer.articles.create') }}" class="inline-block mt-4 text-primary hover:text-primary-dark">
                        Créer votre premier article →
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
                    <a href="{{ route('writer.badges.index') }}" class="text-sm text-primary hover:text-primary-dark">
                        Voir tout →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($badges['recent']->count() > 0)
                <div class="space-y-4">
                    @foreach($badges['recent'] as $badge)
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">{{ $badge->icon }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">{{ $badge->name }}</p>
                            <p class="text-xs text-text-secondary">{{ $badge->pivot->unlocked_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-text-secondary text-center">Aucun badge débloqué</p>
                @endif
                
                @if($badges['next'])
                <div class="mt-6 pt-6 border-t border-border">
                    <p class="text-xs text-text-secondary mb-2">Prochain badge</p>
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xl opacity-50">{{ $badges['next']->icon }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-text-primary">{{ $badges['next']->name }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection