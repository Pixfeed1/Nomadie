@extends('layouts.admin')

@section('title', 'Modération des commentaires')

@section('page-title', 'Modération des commentaires')

@section('content')
<div x-data="commentsModeration()" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Total</p>
                    <p class="text-2xl font-bold text-text-primary">{{ $stats['total'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 
0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 
12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 
12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">En attente</p>
                    <p class="text-2xl font-bold text-accent">{{ $stats['pending'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 
24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 
3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Approuvés</p>
                    <p class="text-2xl font-bold text-success">{{ $stats['approved'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 
0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 
2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Spam</p>
                    <p class="text-2xl font-bold text-error">{{ $stats['spam'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-error/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-error" fill="none" viewBox="0 0 
24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 
18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Spam élevé</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['high_spam'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 
0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 
4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.732 15.5c-.77.833.192 
2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow-sm p-6 card">
        <form method="GET" class="space-y-4 md:space-y-0 md:grid md:grid-cols-5 md:gap-4">
            <!-- Filtre statut -->
            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">Statut</label>
                <select name="status" class="w-full border border-border rounded-lg px-3 py-2 focus:outline-none 
focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="all" {{ request('status', 'pending') === 'all' ? 'selected' : '' 
}}>Tous</option>
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' 
}}>En attente</option>
                    <option value="approved" {{ request('status', 'pending') === 'approved' ? 'selected' : '' 
}}>Approuvés</option>
                    <option value="spam" {{ request('status', 'pending') === 'spam' ? 'selected' : '' 
}}>Spam</option>
                </select>
            </div>

            <!-- Filtre article -->
            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">Article</label>
                <select name="article_id" class="w-full border border-border rounded-lg px-3 py-2 
focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Tous les articles</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' 
: '' }}>
                            {{ Str::limit($article->title, 50) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtre spam -->
            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">Niveau spam</label>
                <select name="spam_level" class="w-full border border-border rounded-lg px-3 py-2 
focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Tous niveaux</option>
                    <option value="none" {{ request('spam_level') === 'none' ? 'selected' : '' }}>Aucun 
(0)</option>
                    <option value="low" {{ request('spam_level') === 'low' ? 'selected' : '' }}>Faible 
(1-4)</option>
                    <option value="medium" {{ request('spam_level') === 'medium' ? 'selected' : '' }}>Moyen 
(5-9)</option>
                    <option value="high" {{ request('spam_level') === 'high' ? 'selected' : '' }}>Élevé 
(10+)</option>
                </select>
            </div>

            <!-- Recherche -->
            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contenu, nom, 
email..." 
                       class="w-full border border-border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 
focus:ring-primary/20 focus:border-primary">
            </div>

            <!-- Boutons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg 
transition-colors btn flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 
21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['status', 'article_id', 'spam_level', 'search']))
                    <a href="{{ route('admin.comments.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 
text-white rounded-lg transition-colors btn">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Actions en lot -->
    <div x-show="selectedComments.length > 0" x-cloak class="bg-white rounded-lg shadow-sm p-4 border-l-4 
border-accent card">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <p class="text-sm font-medium text-text-primary">
                    <span x-text="selectedComments.length"></span> commentaire(s) sélectionné(s)
                </p>
                <div class="flex space-x-2">
                    <button @click="bulkAction('approve')" class="px-3 py-1 bg-success/10 text-success 
hover:bg-success/20 rounded-md transition-colors text-sm">
                        Approuver
                    </button>
                    <button @click="bulkAction('reject')" class="px-3 py-1 bg-error/10 text-error 
hover:bg-error/20 rounded-md transition-colors text-sm">
                        Rejeter
                    </button>
                    <button @click="bulkAction('spam')" class="px-3 py-1 bg-red-100 text-red-600 
hover:bg-red-200 rounded-md transition-colors text-sm">
                        Marquer spam
                    </button>
                    <button @click="bulkAction('delete')" class="px-3 py-1 bg-gray-100 text-gray-600 
hover:bg-gray-200 rounded-md transition-colors text-sm">
                        Supprimer
                    </button>
                </div>
            </div>
            <button @click="selectedComments = []" class="text-text-secondary hover:text-text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 
12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Liste des commentaires -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-text-primary">
                    Commentaires ({{ $comments->total() }})
                </h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" @change="toggleAll()" 
                           :checked="selectedComments.length === {{ $comments->count() }} && {{ 
$comments->count() }} > 0"
                           class="rounded border-border text-primary focus:ring-primary/20">
                    <label class="text-sm text-text-secondary">Tout sélectionner</label>
                </div>
            </div>
        </div>

        @if($comments->count() > 0)
            <div class="divide-y divide-border">
                @foreach($comments as $comment)
                    <div class="p-6 hover:bg-bg-alt/30 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Checkbox -->
                            <div class="pt-1">
                                <input type="checkbox" :value="{{ $comment->id }}" x-model="selectedComments" 
                                       class="rounded border-border text-primary focus:ring-primary/20">
                            </div>

                            <!-- Contenu principal -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- En-tête -->
                                        <div class="flex items-center space-x-2 text-sm text-text-secondary 
mb-2">
                                            <span class="font-medium text-text-primary">{{ $comment->author_name 
}}</span>
                                            <span>•</span>
                                            <span>{{ $comment->author_email }}</span>
                                            <span>•</span>
                                            <span>{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                            @if($comment->spam_score > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full 
text-xs font-medium bg-red-100 text-red-600">
                                                    Spam: {{ $comment->spam_score }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Contenu -->
                                        <div class="prose prose-sm max-w-none mb-3">
                                            {!! Str::limit(nl2br(e($comment->content)), 300) !!}
                                        </div>

                                        <!-- Meta info -->
                                        <div class="flex items-center text-xs text-text-secondary space-x-4">
                                            <span>Article: 
                                                <a href="{{ route('admin.comments.show', $comment) }}" 
class="text-primary hover:text-primary-dark">
                                                    {{ Str::limit($comment->article->title ?? 'N/A', 40) }}
                                                </a>
                                            </span>
                                            <span>IP: {{ $comment->ip_address }}</span>
                                            @if($comment->spam_flags && count($comment->spam_flags) > 0)
                                                <span class="text-red-600">{{ count($comment->spam_flags) }} 
flag(s) spam</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Statut -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        @switch($comment->status)
                                            @case('approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full 
text-xs font-medium bg-success/15 text-success">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 
20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 
1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" 
clip-rule="evenodd"/>
                                                    </svg>
                                                    Approuvé
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full 
text-xs font-medium bg-accent/15 text-accent-dark">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 
20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 
000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    En attente
                                                </span>
                                                @break
                                            @case('spam')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full 
text-xs font-medium bg-error/15 text-error">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 
20 20">
                                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 
6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" 
clip-rule="evenodd"/>
                                                    </svg>
                                                    Spam
                                                </span>
                                                @break
                                        @endswitch
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 mt-3 pt-3 border-t border-border">
                                    <a href="{{ route('admin.comments.show', $comment) }}" 
                                       class="text-primary hover:text-primary-dark bg-primary/5 
hover:bg-primary/10 px-3 py-1 rounded-md transition-colors text-sm flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" 
viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" 
stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" 
stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 
7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Détails
                                    </a>

                                    @if($comment->status === 'pending' || $comment->status === 'spam')
                                        <form method="POST" action="{{ route('admin.comments.approve', $comment) 
}}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-success hover:text-success/80 
bg-success/5 hover:bg-success/10 px-3 py-1 rounded-md transition-colors text-sm flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" 
fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" 
stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approuver
                                            </button>
                                        </form>
                                    @endif

                                    @if($comment->status !== 'spam')
                                        <form method="POST" action="{{ route('admin.comments.reject', $comment) 
}}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-error hover:text-error/80 
bg-error/5 hover:bg-error/10 px-3 py-1 rounded-md transition-colors text-sm flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" 
fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" 
stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rejeter
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" 
class="inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer 
définitivement ce commentaire ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 bg-red-50 
hover:bg-red-100 px-3 py-1 rounded-md transition-colors text-sm flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" 
fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" 
stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 
00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-border bg-bg-alt">
                {{ $comments->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="mx-auto h-16 w-16 rounded-full bg-accent/10 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 
24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 
12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 
12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-text-primary mb-2">Aucun commentaire trouvé</h3>
                <p class="text-text-secondary">
                    @if(request()->hasAny(['status', 'article_id', 'spam_level', 'search']))
                        Aucun commentaire ne correspond à vos critères de recherche.
                    @else
                        Il n'y a actuellement aucun commentaire à modérer.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Formulaire caché pour actions en lot -->
<form x-ref="bulkForm" method="POST" action="{{ route('admin.comments.bulk-action') }}" style="display: none;">
    @csrf
    <input x-ref="bulkAction" type="hidden" name="action">
    <template x-for="commentId in selectedComments" :key="commentId">
        <input type="hidden" name="comment_ids[]" :value="commentId">
    </template>
</form>
@endsection

@push('scripts')
<script>
function commentsModeration() {
    return {
        selectedComments: [],
        
        init() {
            // Initialisation si nécessaire
        },
        
        toggleAll() {
            if (this.selectedComments.length === {{ $comments->count() }}) {
                this.selectedComments = [];
            } else {
                this.selectedComments = [{{ $comments->pluck('id')->implode(',') }}];
            }
        },
        
        bulkAction(action) {
            if (this.selectedComments.length === 0) {
                alert('Veuillez sélectionner au moins un commentaire.');
                return;
            }
            
            const actionLabels = {
                'approve': 'approuver',
                'reject': 'rejeter', 
                'spam': 'marquer comme spam',
                'delete': 'supprimer définitivement'
            };
            
            const message = `Êtes-vous sûr de vouloir ${actionLabels[action]} ${this.selectedComments.length} 
commentaire(s) ?`;
            
            if (confirm(message)) {
                this.$refs.bulkAction.value = action;
                this.$refs.bulkForm.submit();
            }
        }
    }
}
</script>
@endpush
