@extends('vendor.layouts.app')

@section('title', 'Mes articles')

@section('page-title', 'Mes articles')
@section('page-description', 'Gérez vos articles et suivez leurs performances SEO')

@section('content')
<div x-data="{ 
    showFilters: false,
    filterStatus: 'all',
    filterScore: 'all',
    filterLink: 'all'
}">
    <!-- Statistiques en haut (du second fichier) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total articles -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Total articles</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $articles->total() }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Score moyen -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Score SEO moyen</p>
                    @php
                        $avgScore = $articles->filter(function($article) {
                            return $article->latestSeoAnalysis;
                        })->avg(function($article) {
                            return $article->latestSeoAnalysis->global_score;
                        });
                        $avgScore = $avgScore ? round($avgScore) : 0;
                    @endphp
                    <p class="text-2xl font-bold mt-1 {{ $avgScore >= 78 ? 'text-green-600' : ($avgScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $avgScore }}/100
                    </p>
                </div>
                <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Articles DoFollow -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Articles DoFollow</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">
                        {{ $articles->filter(function($article) {
                            return $article->latestSeoAnalysis && $article->latestSeoAnalysis->is_dofollow;
                        })->count() }}
                    </p>
                </div>
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Vues totales -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-text-secondary uppercase tracking-wider">Vues totales</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">
                        {{ number_format($articles->sum('views_count')) }}
                    </p>
                </div>
                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-text-primary">Articles publiés</h2>
            <p class="text-sm text-text-secondary mt-1">
                {{ $articles->total() }} article(s) au total
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-4 md:mt-0">
            <button @click="showFilters = !showFilters" 
                    class="flex items-center justify-center px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtrer
            </button>
            
            <a href="{{ route('writer.articles.create') }}" 
               class="flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nouvel article
            </a>
        </div>
    </div>

    <!-- Filtres améliorés -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Statut</label>
                <select x-model="filterStatus" 
                        class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="all">Tous les statuts</option>
                    <option value="published">Publié</option>
                    <option value="draft">Brouillon</option>
                    <option value="pending">En attente</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Score SEO</label>
                <select x-model="filterScore" 
                        class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="all">Tous les scores</option>
                    <option value="excellent">Excellent (>90)</option>
                    <option value="good">Bon (78-90)</option>
                    <option value="average">Moyen (60-77)</option>
                    <option value="poor">Faible (<60)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Type de lien</label>
                <select x-model="filterLink" 
                        class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="all">Tous</option>
                    <option value="dofollow">DoFollow</option>
                    <option value="nofollow">NoFollow</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Liste des articles -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-bg-alt">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Article
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Score SEO
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Lien
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Stats
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse($articles as $article)
                        <tr class="hover:bg-bg-alt/50 transition-colors"
                            x-show="
                                (filterStatus === 'all' || filterStatus === '{{ $article->status }}') &&
                                (filterScore === 'all' || 
                                 (filterScore === 'excellent' && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} > 90) ||
                                 (filterScore === 'good' && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} >= 78 && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} <= 90) ||
                                 (filterScore === 'average' && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} >= 60 && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} < 78) ||
                                 (filterScore === 'poor' && {{ $article->latestSeoAnalysis ? $article->latestSeoAnalysis->global_score : 0 }} < 60)
                                ) &&
                                (filterLink === 'all' || 
                                 (filterLink === 'dofollow' && {{ $article->latestSeoAnalysis && $article->latestSeoAnalysis->is_dofollow ? 'true' : 'false' }}) ||
                                 (filterLink === 'nofollow' && !{{ $article->latestSeoAnalysis && $article->latestSeoAnalysis->is_dofollow ? 'true' : 'false' }}))
                            ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($article->featured_image)
                                        <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                             alt="{{ $article->title }}"
                                             class="h-10 w-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">
                                            {{ Str::limit($article->title, 50) }}
                                        </p>
                                        <p class="text-xs text-text-secondary">
                                            {{ $article->created_at->format('d/m/Y') }}
                                            @if($article->latestSeoAnalysis && $article->latestSeoAnalysis->word_count)
                                                • {{ number_format($article->latestSeoAnalysis->word_count) }} mots
                                            @endif
                                            @if($article->meta_data && isset($article->meta_data['category']))
                                                • {{ $article->meta_data['category'] }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($article->latestSeoAnalysis)
                                    @php
                                        $score = $article->latestSeoAnalysis->global_score;
                                        $scoreClass = $score >= 78 ? 'text-green-600 bg-green-100' : 
                                                     ($score >= 60 ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100');
                                    @endphp
                                    <div class="inline-flex items-center">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $scoreClass }}">
                                            {{ round($score) }}/100
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm text-text-secondary">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusConfig = [
                                        'published' => ['label' => 'Publié', 'class' => 'bg-green-100 text-green-800'],
                                        'draft' => ['label' => 'Brouillon', 'class' => 'bg-gray-100 text-gray-800'],
                                        'pending' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800']
                                    ];
                                    $status = $statusConfig[$article->status] ?? $statusConfig['draft'];
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($article->latestSeoAnalysis && $article->latestSeoAnalysis->is_dofollow)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        DoFollow
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        NoFollow
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-text-secondary">
                                    <div class="flex items-center justify-center space-x-4">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($article->views_count ?? 0) }}
                                        </div>
                                        @if(isset($article->shares_count))
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 2.943-9.543 7a9.97 9.97 0 011.827 3.684m0 0A8.959 8.959 0 0112 21c4.474 0 8.268-2.943 9.543-7a9.97 9.97 0 00-1.827-3.684z" />
                                            </svg>
                                            {{ $article->shares_count }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('writer.articles.edit', $article->id) }}"
                                       class="text-primary hover:text-primary-dark transition-colors"
                                       title="Modifier">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    
                                    @if($article->status === 'published')
                                        <a href="{{ url('/articles/' . $article->slug) }}"
                                           target="_blank"
                                           class="text-accent hover:text-accent-dark transition-colors"
                                           title="Voir">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('writer.articles.destroy', $article->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                                title="Supprimer">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-text-secondary mb-4">Aucun article pour le moment</p>
                                    <a href="{{ route('writer.articles.create') }}" 
                                       class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                                        Créer votre premier article
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($articles->hasPages())
            <div class="px-6 py-4 bg-bg-alt border-t border-border">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection