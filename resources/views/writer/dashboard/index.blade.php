@extends('vendor.layouts.app')

@section('title', 'Dashboard Rédacteur')

@section('page-title', 'Dashboard')
@section('page-description', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Articles publiés"
            :value="$articlesStats['published']"
            icon="book"
            color="primary"
        />

        <x-stat-card
            title="Score SEO moyen"
            :value="round($seoStats['average_score']) . '/100'"
            icon="chart"
            color="success"
        />

        <x-stat-card
            title="Statut des liens"
            :value="$seoStats['dofollow_status'] ? 'DoFollow' : 'NoFollow'"
            icon="link"
            color="info"
        />

        <x-stat-card
            title="Badges"
            :value="$badges['unlocked'] . '/' . $badges['total']"
            icon="star"
            color="warning"
        />
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