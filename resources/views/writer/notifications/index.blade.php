@extends('layouts.writer')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Notifications</h1>
            <p class="text-text-secondary mt-1">Restez informé de vos activités et actualités</p>
        </div>

        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('writer.notifications.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                Tout marquer comme lu
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-success/10 border border-success/20 text-success rounded-lg p-4 mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($notifications->isEmpty())
    <!-- État vide -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-text-primary mb-2">Aucune notification</h3>
        <p class="text-text-secondary">Vous n'avez pas encore de notifications.</p>
    </div>
    @else
    <!-- Liste des notifications -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="divide-y divide-border">
            @foreach($notifications as $notification)
            <div class="p-4 hover:bg-bg-main transition-colors {{ is_null($notification->read_at) ? 'bg-primary/5' : '' }}">
                <div class="flex items-start">
                    <!-- Icône -->
                    <div class="flex-shrink-0 mr-4">
                        <div class="h-10 w-10 rounded-full {{ is_null($notification->read_at) ? 'bg-primary/20' : 'bg-gray-100' }} flex items-center justify-center">
                            @if(isset($notification->data['type']))
                                @if($notification->data['type'] === 'badge')
                                    <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-primary' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @elseif($notification->data['type'] === 'article')
                                    <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-primary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($notification->data['type'] === 'dofollow')
                                    <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-primary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-primary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                @endif
                            @else
                                <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-primary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                @if(isset($notification->data['title']))
                                    <p class="text-sm font-medium text-text-primary {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                                        {{ $notification->data['title'] }}
                                    </p>
                                @endif

                                @if(isset($notification->data['message']))
                                    <p class="text-sm text-text-secondary mt-1">
                                        {{ $notification->data['message'] }}
                                    </p>
                                @endif

                                <p class="text-xs text-text-secondary mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            @if(is_null($notification->read_at))
                            <div class="ml-4 flex-shrink-0">
                                <div class="h-2 w-2 rounded-full bg-primary"></div>
                            </div>
                            @endif
                        </div>

                        <!-- Action button -->
                        @if(isset($notification->data['action_url']))
                        <div class="mt-3">
                            <a href="{{ $notification->data['action_url'] }}"
                               class="inline-flex items-center text-sm text-primary hover:text-primary-dark font-medium">
                                {{ $notification->data['action_text'] ?? 'Voir détails' }}
                                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
