@extends('customer.layouts.app')

@section('title', 'Conversation')

@section('page-title', 'Messages')

@section('content')
<div class="space-y-6">
    <!-- En-tête de la conversation -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('customer.messages') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                    @php
                        $vendor = $otherParticipant->vendor;
                    @endphp
                    @if($vendor && $vendor->logo)
                        <img src="{{ Storage::url($vendor->logo) }}" class="h-10 w-10 rounded-full object-cover">
                    @elseif($otherParticipant->avatar)
                        <img src="{{ Storage::url($otherParticipant->avatar) }}" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <span class="text-sm font-bold text-gray-600">
                            {{ strtoupper(substr($vendor ? $vendor->company_name : $otherParticipant->name, 0, 2)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <p class="font-medium text-gray-900">
                        {{ $vendor ? $vendor->company_name : $otherParticipant->name }}
                    </p>
                    @if($trip)
                        <p class="text-xs text-gray-500">{{ $trip->title }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Info sur l'offre à droite -->
            @if($trip)
            <div class="bg-gray-50 rounded-lg p-3 max-w-xs">
                <p class="text-xs text-gray-500 mb-1">Discussion concernant :</p>
                <p class="text-sm font-medium text-gray-900">{{ $trip->title }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ number_format($trip->price, 0, ',', ' ') }} € / pers</p>
                <a href="{{ route('trips.show', $trip->slug) }}" 
                   target="_blank"
                   class="text-xs text-primary hover:underline mt-2 inline-block">
                    Voir l'offre →
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Zone des messages -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="h-96 overflow-y-auto p-6 space-y-4" id="messages-container">
            @foreach($messages as $message)
                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                    @if($message->sender_id != Auth::id())
                        <!-- Photo pour messages reçus (vendor) -->
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-2 flex-shrink-0">
                            @php
                                $senderVendor = $message->sender->vendor;
                            @endphp
                            @if($senderVendor && $senderVendor->logo)
                                <img src="{{ Storage::url($senderVendor->logo) }}" class="h-8 w-8 rounded-full object-cover">
                            @elseif($message->sender->avatar)
                                <img src="{{ Storage::url($message->sender->avatar) }}" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr($senderVendor ? $senderVendor->company_name : $message->sender->name, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <div class="max-w-xs lg:max-w-md">
                        <div class="text-gray-900 px-4 py-2">
                            <p class="text-sm">{{ $message->content }}</p>
                            
                            <!-- Si pièce jointe -->
                            @if($message->attachment)
                                <div class="mt-2 p-2 bg-gray-50 rounded-lg">
                                    <a href="{{ route('customer.messages.download', $message->id) }}" 
                                       class="flex items-center space-x-2 text-primary hover:text-primary-dark">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        <span class="text-xs">{{ $message->attachment_name ?? 'Pièce jointe' }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 {{ $message->sender_id == Auth::id() ? 'text-right' : '' }}">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id == Auth::id() && $message->is_read)
                                <span class="text-primary ml-1">✓✓</span>
                            @elseif($message->sender_id == Auth::id())
                                <span class="text-gray-400 ml-1">✓</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($message->sender_id == Auth::id())
                        <!-- Photo pour messages envoyés (customer) -->
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ml-2 flex-shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Zone de saisie -->
        <div class="p-4 border-t border-gray-200">
            <form action="{{ route('customer.messages.reply', $tripSlug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-end space-x-2">
                    <!-- Bouton pièce jointe -->
                    <label for="attachment" class="cursor-pointer text-gray-500 hover:text-gray-700 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <input type="file" 
                               id="attachment" 
                               name="attachment" 
                               class="hidden"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               max="5242880"
                               onchange="showFileName(this)">
                    </label>
                    
                    <!-- Champ de texte -->
                    <div class="flex-1">
                        <input type="text" 
                               name="content"
                               placeholder="Écrivez votre message..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                               required>
                        <span id="file-name" class="text-xs text-gray-500 mt-1 hidden"></span>
                    </div>
                    
                    <!-- Bouton envoyer -->
                    <button type="submit" 
                            class="p-2 bg-primary text-white rounded-full hover:bg-primary-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Info sur les pièces jointes -->
                <p class="text-xs text-gray-500 mt-2">
                    Fichiers acceptés : PDF, Word, Images (max 5MB)
                </p>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-scroll vers le bas
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});

// Afficher le nom du fichier sélectionné avec vérification de taille
function showFileName(input) {
    if (input.files[0]) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            alert('Le fichier est trop gros. Maximum 5MB.');
            input.value = '';
            return;
        }
        
        const fileNameSpan = document.getElementById('file-name');
        fileNameSpan.textContent = 'Fichier : ' + file.name;
        fileNameSpan.classList.remove('hidden');
    }
}
</script>
@endsection