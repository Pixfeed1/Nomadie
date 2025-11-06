@extends('customer.layouts.app')

@section('title', 'Mes messages')

@section('page-title', 'Mes messages')

@section('content')
<div class="space-y-6">
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Ma messagerie
        </h2>
        <p class="text-text-secondary mt-1">
            Consultez vos messages et échanges avec les prestataires.
        </p>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <div class="alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($messages->isEmpty())
        {{-- Message si aucun message --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun message</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de message.</p>
                <div class="mt-6">
                    <a href="{{ route('customer.bookings') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Voir mes réservations
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Liste des messages --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($messages as $message)
                    @php
                        $isReceived = $message->recipient_id == Auth::id();
                        $otherUser = $isReceived ? $message->sender : $message->recipient;
                    @endphp
                    
                    <a href="{{ route('customer.messages.show', $message->id) }}" 
                       class="block hover:bg-gray-50 transition-colors {{ !$isReceived || $message->is_read ? '' : 'bg-blue-50/30' }}">
                        <div class="px-6 py-4">
                            <div class="flex items-start space-x-3">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    @if($otherUser && $otherUser->avatar)
                                        <img src="{{ asset('storage/' . $otherUser->avatar) }}" 
                                             alt="{{ $otherUser->name }}"
                                             class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Contenu du message --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                @if($otherUser)
                                                    {{ $otherUser->name ?? $otherUser->firstname . ' ' . $otherUser->lastname }}
                                                @else
                                                    Utilisateur inconnu
                                                @endif
                                                @if(!$isReceived)
                                                    <span class="text-xs text-gray-500 font-normal ml-2">
                                                        (Message envoyé)
                                                    </span>
                                                @endif
                                            </p>
                                            
                                            {{-- Sujet --}}
                                            @if($message->subject)
                                                <p class="text-sm font-medium text-gray-700 mt-1">
                                                    {{ $message->subject }}
                                                </p>
                                            @endif
                                            
                                            {{-- Aperçu du message --}}
                                            <p class="text-sm text-gray-600 mt-1 truncate">
                                                {{ Str::limit($message->content, 100) }}
                                            </p>

                                            {{-- Informations sur la réservation ou l'offre --}}
                                            <div class="flex items-center space-x-4 mt-2">
                                                @if($message->booking_id)
                                                    <span class="inline-flex items-center text-xs text-gray-500">
                                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                        Réservation #{{ $message->booking_id }}
                                                    </span>
                                                @endif
                                                
                                                @if($message->trip_id)
                                                    <span class="inline-flex items-center text-xs text-gray-500">
                                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                        </svg>
                                                        Concernant une offre
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Date et statut --}}
                                        <div class="flex flex-col items-end ml-4">
                                            <p class="text-xs text-gray-500">
                                                {{ $message->created_at->format('d/m/Y') }}
                                                <br>
                                                <span class="text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                                            </p>
                                            
                                            @if($isReceived && !$message->is_read)
                                                <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Non lu
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @endif

    {{-- Actions --}}
    <div class="mt-6 flex justify-between">
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au tableau de bord
        </a>

        {{-- Bouton nouveau message si nécessaire --}}
        @if(false) {{-- Activer si vous avez une fonctionnalité de nouveau message --}}
        <button type="button" 
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau message
        </button>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh pour les nouveaux messages (optionnel)
    // setInterval(function() {
    //     window.location.reload();
    // }, 60000); // Rafraîchir toutes les minutes
</script>
@endpush