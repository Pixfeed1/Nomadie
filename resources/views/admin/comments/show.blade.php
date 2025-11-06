@extends('layouts.admin')

@section('title', 'Détail du commentaire')

@section('page-title', 'Détail du commentaire')

@section('content')
<div x-data="commentDetails()" class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('admin.comments.index') }}" class="mr-3 text-primary hover:text-primary-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 
0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-text-primary">Commentaire #{{ $comment->id }}</h2>
                <p class="text-sm text-text-secondary mt-1">
                    Posté le {{ $comment->created_at->format('d/m/Y à H:i') }} par {{ $comment->author_name }}
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @if($comment->status === 'pending' || $comment->status === 'spam')
                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-success/10 text-success hover:bg-success/20 
font-medium rounded-lg transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 
24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 
4L19 7" />
                        </svg>
                        Approuver
                    </button>
                </form>
            @endif

            @if($comment->status !== 'spam')
                <form method="POST" action="{{ route('admin.comments.reject', $comment) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-error/10 text-error hover:bg-error/20 font-medium 
rounded-lg transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 
24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 
18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Marquer spam
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce commentaire 
?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 hover:bg-red-200 font-medium 
rounded-lg transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 
12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 
7h16" />
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Informations principales et contenu -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu du commentaire -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Commentaire principal -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                <div class="p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-text-primary">Contenu du commentaire</h3>
                        @switch($comment->status)
                            @case('approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
bg-success/15 text-success">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 
01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Approuvé
                                </span>
                                @break
                            @case('pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
bg-accent/15 text-accent-dark">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 
0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    En attente
                                </span>
                                @break
                            @case('spam')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
bg-error/15 text-error">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 
8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Spam
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($comment->content)) !!}
                    </div>
                </div>
                @if($comment->article)
                    <div class="px-6 py-4 bg-bg-alt border-t border-border">
                        <div class="flex items-center text-sm text-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 
0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 
12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 
2z" />
                            </svg>
                            <span>Commentaire sur l'article : </span>
                            <a href="#" class="text-primary hover:text-primary-dark font-medium ml-1">
                                {{ $comment->article->title }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Analyse spam -->
            @if($comment->spam_score > 0 || ($comment->spam_flags && count($comment->spam_flags) > 0))
                <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                    <div class="p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-text-primary">Analyse anti-spam</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Score spam -->
                            <div>
                                <h4 class="text-sm font-medium text-text-primary mb-3">Score de spam</h4>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $comment->spam_score >= 10 ? 
'bg-red-600' : ($comment->spam_score >= 5 ? 'bg-accent' : 'bg-success') }}" 
                                             style="width: {{ min(($comment->spam_score / 15) * 100, 100) 
}}%"></div>
                                    </div>
                                    <span class="ml-3 text-lg font-bold {{ $comment->spam_score >= 10 ? 
'text-red-600' : ($comment->spam_score >= 5 ? 'text-accent' : 'text-success') }}">
                                        {{ $comment->spam_score }}/15
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary mt-2">
                                    @if($comment->spam_score >= 10)
                                        Score élevé - Très probablement du spam
                                    @elseif($comment->spam_score >= 5)
                                        Score modéré - Suspect, nécessite vérification
                                    @elseif($comment->spam_score > 0)
                                        Score faible - Quelques indicateurs suspects
                                    @else
                                        Aucun indicateur de spam détecté
                                    @endif
                                </p>
                            </div>

                            <!-- Flags spam -->
                            @if($comment->spam_flags && count($comment->spam_flags) > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-text-primary mb-3">
                                        Indicateurs détectés ({{ count($comment->spam_flags) }})
                                    </h4>
                                    <div class="space-y-1">
                                        @foreach($comment->spam_flags as $flag)
                                            <div class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                                {{ $flag }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Autres commentaires du même auteur -->
            @if($otherComments && $otherComments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                    <div class="p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-text-primary">
                            Autres commentaires de {{ $comment->author_name }}
                        </h3>
                    </div>
                    <div class="divide-y divide-border">
                        @foreach($otherComments as $otherComment)
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="text-sm text-text-secondary mb-1">
                                            {{ $otherComment->created_at->format('d/m/Y H:i') }} - 
                                            {{ Str::limit($otherComment->article->title ?? 'Article supprimé', 
40) }}
                                        </div>
                                        <div class="text-sm text-text-primary">
                                            {{ Str::limit($otherComment->content, 150) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        @switch($otherComment->status)
                                            @case('approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full 
text-xs font-medium bg-success/15 text-success">
                                                    Approuvé
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full 
text-xs font-medium bg-accent/15 text-accent-dark">
                                                    En attente
                                                </span>
                                                @break
                                            @case('spam')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full 
text-xs font-medium bg-error/15 text-error">
                                                    Spam
                                                </span>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 bg-bg-alt border-t border-border">
                        <a href="{{ route('admin.comments.index', ['search' => $comment->author_email]) }}" 
                           class="text-sm text-primary hover:text-primary-dark font-medium">
                            Voir tous les commentaires de cet auteur
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar avec informations -->
        <div class="space-y-6">
            <!-- Informations auteur -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-text-primary">Informations auteur</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-text-secondary">Nom</p>
                            <p class="text-sm font-medium text-text-primary">{{ $comment->author_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-text-secondary">Email</p>
                            <p class="text-sm font-medium text-text-primary">{{ $comment->author_email }}</p>
                        </div>
                        @if($comment->user)
                            <div>
                                <p class="text-sm text-text-secondary">Utilisateur inscrit</p>
                                <p class="text-sm font-medium text-success">Oui</p>
                                <p class="text-xs text-text-secondary">Inscrit le {{ 
$comment->user->created_at->format('d/m/Y') }}</p>
                            </div>
                        @else
                            <div>
                                <p class="text-sm text-text-secondary">Utilisateur inscrit</p>
                                <p class="text-sm font-medium text-text-secondary">Non (visiteur anonyme)</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-text-secondary">Date du commentaire</p>
                            <p class="text-sm font-medium text-text-primary">{{ 
$comment->created_at->format('d/m/Y à H:i') }}</p>
                            <p class="text-xs text-text-secondary">Il y a {{ 
$comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations techniques -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-text-primary">Informations techniques</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-text-secondary">Adresse IP</p>
                            <p class="text-sm font-medium text-text-primary font-mono">{{ $comment->ip_address 
}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-text-secondary">User Agent</p>
                            <p class="text-xs text-text-primary break-all">{{ $comment->user_agent ?: 'Non 
disponible' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-text-secondary">Score spam</p>
                            <div class="flex items-center">
                                <p class="text-sm font-medium {{ $comment->spam_score >= 10 ? 'text-red-600' : 
($comment->spam_score >= 5 ? 'text-accent' : 'text-success') }}">
                                    {{ $comment->spam_score }}/15
                                </p>
                            </div>
                        </div>
                        @if($comment->spam_flags && count($comment->spam_flags) > 0)
                            <div>
                                <p class="text-sm text-text-secondary mb-2">Flags détectés</p>
                                <p class="text-sm font-medium text-red-600">{{ count($comment->spam_flags) }} 
indicateur(s)</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Historique IP -->
            @if($ipHistory && $ipHistory->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
                    <div class="p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-text-primary">Historique de cette IP</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($ipHistory as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-text-secondary capitalize">
                                        @switch($status)
                                            @case('approved') Approuvés @break
                                            @case('pending') En attente @break
                                            @case('spam') Spam @break
                                            @default {{ $status }}
                                        @endswitch
                                    </span>
                                    <span class="text-sm font-medium text-text-primary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        @php
                            $totalIpComments = $ipHistory->sum();
                            $spamRate = $totalIpComments > 0 ? round(($ipHistory['spam'] ?? 0) / 
$totalIpComments * 100, 1) : 0;
                        @endphp
                        
                        @if($spamRate > 50)
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2" 
fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.732 
15.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <span class="text-sm text-red-600 font-medium">IP suspecte</span>
                                </div>
                                <p class="text-xs text-red-600 mt-1">{{ $spamRate }}% des commentaires de cette 
IP sont du spam</p>
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 bg-bg-alt border-t border-border">
                        <a href="{{ route('admin.comments.index', ['search' => $comment->ip_address]) }}" 
                           class="text-sm text-primary hover:text-primary-dark font-medium">
                            Voir tous les commentaires de cette IP
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function commentDetails() {
    return {
        init() {
            // Initialisation si nécessaire
        }
    }
}
</script>
@endpush
