@extends('vendor.layouts.app')

@section('title', 'Mes badges')

@section('page-title', 'Mes badges')
@section('page-description', 'Suivez votre progression et débloquez des récompenses')

@section('content')
<div x-data="{ 
    activeTab: 'all',
    showBadgeDetails: null 
}">
    <!-- Statistiques en haut -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Badges débloqués"
            :value="$stats['unlocked_count'] . '/' . $stats['total_badges']"
            icon="star"
            color="primary"
        />

        <x-stat-card
            title="Progression globale"
            :value="$stats['completion_percentage'] . '%'"
            icon="chart"
            color="accent"
        />

        <!-- Prochain badge -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-text-secondary uppercase">Prochain badge</p>
                    @if($stats['next_badge'])
                        <p class="text-sm font-bold text-text-primary mt-1 truncate">
                            {{ $stats['next_badge']['badge']->name }}
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-primary h-1.5 rounded-full transition-all duration-300" 
                                 style="width: {{ $stats['next_badge']['progress'] }}%"></div>
                        </div>
                    @else
                        <p class="text-sm text-text-secondary mt-1">Tous débloqués ! 🎉</p>
                    @endif
                </div>
                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center ml-3">
                    <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Badge en vedette -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-text-secondary uppercase">Badge en vedette</p>
                    @if($stats['featured_badge'])
                        <p class="text-sm font-bold text-text-primary mt-1 truncate">
                            <span class="text-xl mr-1">{{ $stats['featured_badge']->icon }}</span>
                            {{ $stats['featured_badge']->name }}
                        </p>
                    @else
                        <p class="text-sm text-text-secondary mt-1">Aucun sélectionné</p>
                    @endif
                </div>
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center ml-3">
                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs pour filtrer les badges -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="border-b border-border">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Tous les badges
                </button>
                <button @click="activeTab = 'unlocked'" 
                        :class="activeTab === 'unlocked' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Débloqués ({{ $stats['unlocked_count'] }})
                </button>
                <button @click="activeTab = 'locked'" 
                        :class="activeTab === 'locked' ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    En progression ({{ $stats['total_badges'] - $stats['unlocked_count'] }})
                </button>
            </nav>
        </div>
    </div>

    <!-- Grille des badges -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($badgesWithProgress as $item)
            <div x-show="activeTab === 'all' || (activeTab === 'unlocked' && {{ $item['is_unlocked'] ? 'true' : 'false' }}) || (activeTab === 'locked' && {{ !$item['is_unlocked'] ? 'true' : 'false' }})"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
                 @click="showBadgeDetails = {{ $item['badge']->id }}">
                
                <!-- Header du badge -->
                <div class="p-4 {{ $item['is_unlocked'] ? 'bg-gradient-to-br from-' . $item['badge']->color . '-50 to-white' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-4xl {{ !$item['is_unlocked'] ? 'grayscale opacity-50' : '' }}">
                            {{ $item['badge']->icon }}
                        </span>
                        @if($item['is_unlocked'])
                            <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @endif
                    </div>
                    
                    <h3 class="font-semibold text-text-primary {{ !$item['is_unlocked'] ? 'opacity-75' : '' }}">
                        {{ $item['badge']->name }}
                    </h3>
                    <p class="text-xs text-text-secondary mt-1 {{ !$item['is_unlocked'] ? 'opacity-75' : '' }}">
                        {{ Str::limit($item['badge']->description, 60) }}
                    </p>
                </div>

                <!-- Progression ou date de déblocage -->
                <div class="px-4 pb-4">
                    @if($item['is_unlocked'])
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-green-600 font-medium">✓ Débloqué</span>
                            <span class="text-text-secondary">{{ \Carbon\Carbon::parse($item['unlocked_at'])->format('d/m/Y') }}</span>
                        </div>
                        
                        @if($stats['featured_badge'] && $stats['featured_badge']->id === $item['badge']->id)
                            <div class="mt-2 px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded text-center">
                                Badge en vedette
                            </div>
                        @else
                            <form action="{{ route('writer.badges.feature', $item['badge']->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" 
                                        @click.stop="$event.target.form.submit()"
                                        class="w-full px-2 py-1 text-xs text-primary hover:bg-primary/5 rounded transition-colors">
                                    Mettre en vedette
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-text-secondary">Progression</span>
                                <span class="font-medium text-text-primary">{{ $item['progress'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $item['progress'] }}%"></div>
                            </div>
                            @if($item['progress_data'])
                                <p class="text-xs text-text-secondary mt-1">
                                    @if(isset($item['progress_data']['current_articles']))
                                        {{ $item['progress_data']['current_articles'] }}/{{ $item['progress_data']['required_articles'] }} articles
                                    @elseif(isset($item['progress_data']['current_streak']))
                                        Série : {{ $item['progress_data']['current_streak'] }}/{{ $item['progress_data']['required_streak'] }}
                                    @elseif(isset($item['progress_data']['current_comments']))
                                        {{ $item['progress_data']['current_comments'] }} commentaires
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal détails du badge -->
    @foreach($badgesWithProgress as $item)
        <div x-show="showBadgeDetails === {{ $item['badge']->id }}"
             x-cloak
             @click.away="showBadgeDetails = null"
             @keydown.escape="showBadgeDetails = null"
             class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showBadgeDetails === {{ $item['badge']->id }}"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <!-- Modal panel -->
                <div x-show="showBadgeDetails === {{ $item['badge']->id }}"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    
                    <div class="text-center">
                        <!-- Icon -->
                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full {{ $item['is_unlocked'] ? 'bg-' . $item['badge']->color . '-100' : 'bg-gray-100' }}">
                            <span class="text-6xl {{ !$item['is_unlocked'] ? 'grayscale opacity-50' : '' }}">
                                {{ $item['badge']->icon }}
                            </span>
                        </div>
                        
                        <!-- Badge info -->
                        <h3 class="mt-4 text-lg font-medium text-text-primary">
                            {{ $item['badge']->name }}
                        </h3>
                        
                        @if($item['is_unlocked'])
                            <p class="mt-1 text-sm text-green-600">
                                ✓ Débloqué le {{ \Carbon\Carbon::parse($item['unlocked_at'])->format('d F Y') }}
                            </p>
                        @endif
                        
                        <p class="mt-3 text-sm text-text-secondary">
                            {{ $item['badge']->description }}
                        </p>
                    </div>

                    <!-- Conditions -->
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-text-primary mb-2">Conditions</h4>
                        <ul class="space-y-2 text-sm text-text-secondary">
                            @php
                                $conditions = $item['badge']->conditions;
                            @endphp
                            
                            @if($conditions['type'] === 'articles_published')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 {{ $item['is_unlocked'] ? 'text-green-500' : 'text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Publier {{ $conditions['count'] }} article(s) avec un score ≥ {{ $conditions['min_score'] }}/100
                                </li>
                            @endif
                            
                            @if($conditions['type'] === 'consecutive_high_score')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 {{ $item['is_unlocked'] ? 'text-green-500' : 'text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $conditions['count'] }} articles consécutifs avec score ≥ {{ $conditions['min_score'] }}/100
                                </li>
                            @endif
                            
                            @if(isset($conditions['min_months']))
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 {{ $item['is_unlocked'] ? 'text-green-500' : 'text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Ancienneté minimum de {{ $conditions['min_months'] }} mois
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Récompenses -->
                    @if($item['badge']->rewards)
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-text-primary mb-2">Récompenses</h4>
                            <ul class="space-y-2 text-sm text-text-secondary">
                                @foreach($item['badge']->rewards as $key => $reward)
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ is_string($reward) ? $reward : $key }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="showBadgeDetails = null"
                                class="px-4 py-2 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
                            Fermer
                        </button>
                        
                        @if($item['is_unlocked'] && (!$stats['featured_badge'] || $stats['featured_badge']->id !== $item['badge']->id))
                            <form action="{{ route('writer.badges.feature', $item['badge']->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                                    Mettre en vedette
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Action de vérification manuelle -->
    <div class="mt-8 text-center">
        <form action="{{ route('writer.badges.check') }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                    class="px-4 py-2 bg-white border border-border text-text-primary hover:bg-bg-alt font-medium rounded-lg transition-colors">
                <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Vérifier ma progression
            </button>
        </form>
        <p class="mt-2 text-xs text-text-secondary">
            La vérification se fait automatiquement après chaque action
        </p>
    </div>
</div>
@endsection