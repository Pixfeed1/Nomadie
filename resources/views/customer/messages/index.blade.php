@extends('customer.layouts.app')

@section('title', 'Mes messages')

@section('page-title', 'Mes messages')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm">
        @if($conversations && $conversations->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($conversations as $conversation)
                    @php
                        $otherUser = $conversation->sender_id == Auth::id() 
                            ? \App\Models\User::find($conversation->recipient_id)
                            : \App\Models\User::find($conversation->sender_id);
                        $isUnread = !$conversation->is_read && $conversation->recipient_id == Auth::id();
                    @endphp
                    
                    <a href="{{ route('customer.messages.show', $conversation->trip->slug ?? 'default') }}"
                       class="block p-4 hover:bg-gray-50 transition-colors {{ $isUnread ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start space-x-3">
                            <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary">
                                    {{ substr($otherUser->name ?? 'U', 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium {{ $isUnread ? 'font-bold text-black' : 'text-text-primary' }}">
                                        {{ $otherUser->name ?? 'Utilisateur' }}
                                    </p>
                                    <p class="text-xs text-text-secondary">
                                        {{ $conversation->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if($conversation->trip)
                                    <p class="text-sm text-primary mt-1">
                                        {{ $conversation->trip->title }}
                                    </p>
                                @endif
                                <p class="text-sm text-text-secondary mt-1 line-clamp-1">
                                    {{ $conversation->content }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-text-primary">Aucun message</h3>
                <p class="mt-1 text-sm text-text-secondary">Commencez à explorer les offres pour contacter les organisateurs.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    Explorer les offres
                </a>
            </div>
        @endif
    </div>
</div>
@endsection