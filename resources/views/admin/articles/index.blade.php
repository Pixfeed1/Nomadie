@extends('layouts.admin')

@section('title', 'Gestion des Articles')
@section('page-title', 'Articles')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Total Articles</p>
                    <p class="text-2xl font-bold text-text-primary">{{ $stats['total'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Publiés</p>
                    <p class="text-2xl font-bold text-success">{{ $stats['published'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Brouillons</p>
                    <p class="text-2xl font-bold text-warning">{{ $stats['draft'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-warning/10 flex items-center justify-center text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">En attente</p>
                    <p class="text-2xl font-bold text-info">{{ $stats['pending'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-info/10 flex items-center justify-center text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" placeholder="Rechercher par titre ou description..."
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">

            <select name="status" class="px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Tous les statuts</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publiés</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillons</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-border">
            <thead class="bg-bg-alt">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Article</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Auteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-border">
                @forelse($articles as $article)
                <tr class="hover:bg-bg-alt transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($article->featured_image)
                            <div class="h-12 w-20 rounded overflow-hidden mr-4 flex-shrink-0">
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-text-primary">{{ Str::limit($article->title, 50) }}</div>
                                @if($article->meta_description)
                                <div class="text-xs text-text-secondary mt-1">{{ Str::limit($article->meta_description, 60) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-text-primary">{{ $article->user->name ?? 'N/A' }}</div>
                        <div class="text-xs text-text-secondary">{{ $article->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-text-primary">{{ $article->destination->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-text-secondary">{{ $article->created_at->format('d/m/Y') }}</div>
                        @if($article->published_at)
                        <div class="text-xs text-text-secondary">Publié le {{ $article->published_at->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($article->status === 'published')
                            <span class="badge badge-success">Publié</span>
                        @elseif($article->status === 'draft')
                            <span class="badge badge-warning">Brouillon</span>
                        @elseif($article->status === 'pending')
                            <span class="badge badge-info">En attente</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($article->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.articles.show', $article) }}" class="text-primary hover:text-primary-dark">Voir</a>

                            @if($article->status !== 'published')
                            <form action="{{ route('admin.articles.publish', $article) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-success hover:text-success/80">Publier</button>
                            </form>
                            @else
                            <form action="{{ route('admin.articles.unpublish', $article) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-warning hover:text-warning/80">Dépublier</button>
                            </form>
                            @endif

                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-error/80">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-text-secondary">Aucun article trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
</div>
@endsection
