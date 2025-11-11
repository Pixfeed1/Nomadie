@extends('layouts.admin')

@section('title', 'Conversation')

@section('header-left')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.messages.index') }}" class="text-text-secondary hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Conversation</h1>
            @if($messages->isNotEmpty())
                <p class="text-sm text-text-secondary mt-1">
                    Entre {{ $messages->first()->sender->name ?? 'Utilisateur' }} et {{ $messages->first()->recipient->name ?? 'Utilisateur' }}
                </p>
            @endif
        </div>
    </div>
@endsection

@section('header-right')
    <div class="flex items-center space-x-3">
        <form action="{{ route('admin.messages.markAsRead', $messages->first()->conversation_id ?? '') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm">
                Marquer comme lu
            </button>
        </form>
        <form action="{{ route('admin.messages.archive', $messages->first()->conversation_id ?? '') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                Archiver
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Contexte de la conversation -->
    @if($trip ?? false)
    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <h3 class="text-lg font-semibold text-text-primary mb-4">Contexte</h3>
        <div class="flex items-start space-x-4">
            @if($trip->main_image)
            <img src="{{ Storage::url($trip->main_image) }}" alt="{{ $trip->title }}" class="h-20 w-20 rounded-lg object-cover">
            @else
            <div class="h-20 w-20 rounded-lg bg-gray-200"></div>
            @endif
            <div class="flex-1">
                <h4 class="text-md font-semibold text-text-primary">{{ $trip->title }}</h4>
                <p class="text-sm text-text-secondary mt-1">{{ $trip->destination->name ?? '-' }}, {{ $trip->country->name ?? '-' }}</p>
                <div class="flex items-center space-x-4 mt-2">
                    <span class="text-sm text-text-secondary">
                        <span class="font-medium">Prix:</span> {{ number_format($trip->price, 0, ',', ' ') }} €
                    </span>
                    @if($booking ?? false)
                    <span class="text-sm text-text-secondary">
                        <span class="font-medium">Réservation:</span> {{ $booking->booking_number }}
                    </span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'confirmed' ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                    @endif
                </div>
                <a href="{{ route('admin.trips.show', $trip) }}" class="inline-block mt-3 text-primary hover:text-primary-dark text-sm font-medium">
                    Voir l'expérience →
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Participants -->
    @if($participants ?? false)
    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <h3 class="text-lg font-semibold text-text-primary mb-4">Participants</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($participants as $participant)
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="bg-primary/10 p-2 rounded-full">
                    <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-text-primary">{{ $participant->name }}</p>
                    <p class="text-xs text-text-secondary">{{ $participant->email }}</p>
                    @if($participant->vendor)
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                        Organisateur
                    </span>
                    @else
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">
                        Voyageur
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Messages -->
    <div class="bg-white rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border">
            <h2 class="text-lg font-semibold text-text-primary">Messages ({{ $messages->count() }})</h2>
        </div>

        <div class="p-6 space-y-6 max-h-[600px] overflow-y-auto">
            @forelse($messages as $message)
                <div class="flex items-start space-x-4 {{ $loop->last ? '' : 'pb-6 border-b border-border' }}">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="bg-primary/10 p-2 rounded-full">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="flex-1">
                        <div class="flex items-baseline justify-between mb-2">
                            <div>
                                <span class="text-sm font-medium text-text-primary">
                                    {{ $message->sender->name ?? 'Utilisateur' }}
                                </span>
                                @if($message->sender->vendor)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                    Organisateur
                                </span>
                                @endif
                                <span class="text-sm text-text-secondary ml-2">→</span>
                                <span class="text-sm font-medium text-text-secondary ml-2">
                                    {{ $message->recipient->name ?? 'Utilisateur' }}
                                </span>
                            </div>
                            <span class="text-xs text-text-secondary">
                                {{ $message->created_at->format('d/m/Y à H:i') }}
                            </span>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-text-primary whitespace-pre-wrap">{{ $message->content }}</p>
                        </div>

                        <div class="flex items-center space-x-4 mt-2">
                            @if($message->is_read)
                            <span class="flex items-center text-xs text-success">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Lu
                            </span>
                            @else
                            <span class="flex items-center text-xs text-accent">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Non lu
                            </span>
                            @endif

                            <span class="text-xs text-text-secondary">
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-text-secondary">
                    Aucun message dans cette conversation
                </div>
            @endforelse
        </div>
    </div>

    <!-- Stats de la conversation -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Messages Totaux</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ $messages->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Messages Non Lus</p>
            <p class="text-3xl font-bold text-accent mt-2">{{ $messages->where('is_read', false)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Premier Message</p>
            <p class="text-sm font-medium text-text-primary mt-2">{{ $messages->first()?->created_at->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Dernier Message</p>
            <p class="text-sm font-medium text-text-primary mt-2">{{ $messages->last()?->created_at->format('d/m/Y') ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
