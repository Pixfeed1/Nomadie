@extends('layouts.admin')

@section('title', 'Messages')

@section('header-left')
    <h1 class="text-2xl font-bold text-text-primary">Messages</h1>
    <p class="text-sm text-text-secondary mt-1">Toutes les conversations de la plateforme</p>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Conversations</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ $stats['total_conversations'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Messages Totaux</p>
            <p class="text-3xl font-bold text-text-primary mt-2">{{ $stats['total_messages'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Non Lus</p>
            <p class="text-3xl font-bold text-accent mt-2">{{ $stats['unread_messages'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Aujourd'hui</p>
            <p class="text-3xl font-bold text-success mt-2">{{ $stats['messages_today'] }}</p>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div class="bg-white rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border">
            <h2 class="text-lg font-semibold text-text-primary">Conversations</h2>
        </div>

        <div class="divide-y divide-border">
            @forelse($conversations as $conv)
                @if($conv->last_message)
                <a href="{{ route('admin.messages.show', $conv->conversation_id) }}" class="block p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-primary/10 p-2 rounded-full">
                                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">
                                            {{ $conv->last_message->sender->name ?? 'Utilisateur' }} 
                                            <span class="text-text-secondary">→</span>
                                            {{ $conv->last_message->recipient->name ?? 'Utilisateur' }}
                                        </p>
                                        @if($conv->last_message->trip)
                                            <p class="text-xs text-text-secondary">Expérience: {{ $conv->last_message->trip->title }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($conv->unread_count > 0)
                                    <span class="px-2 py-1 text-xs font-medium bg-accent text-white rounded-full">
                                        {{ $conv->unread_count }} non lu{{ $conv->unread_count > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-text-primary line-clamp-2">{{ Str::limit($conv->last_message->content, 150) }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-xs text-text-secondary">{{ $conv->last_message_at->diffForHumans() }}</p>
                            <p class="text-xs text-text-secondary mt-1">{{ $conv->message_count }} message{{ $conv->message_count > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </a>
                @endif
            @empty
                <div class="p-12 text-center text-text-secondary">
                    Aucune conversation
                </div>
            @endforelse
        </div>

        <div class="p-6 border-t border-border">
            {{ $conversations->links() }}
        </div>
    </div>
</div>
@endsection
