@extends('layouts.app')

@section('title', 'Nouveau message')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Retour -->
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>

        <div class="bg-white rounded-lg shadow-sm">
            <!-- En-tête -->
            <div class="border-b border-gray-200 px-6 py-4">
                <h1 class="text-xl font-semibold text-gray-900">Nouveau message</h1>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                <!-- Info destinataire et offre -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-start space-x-4">
                        @if($vendor->logo)
                            <img src="{{ Storage::url($vendor->logo) }}" 
                                 alt="{{ $vendor->company_name }}" 
                                 class="h-16 w-16 rounded-lg object-cover">
                        @else
                            <div class="h-16 w-16 rounded-lg bg-primary/10 flex items-center justify-center">
                                <span class="text-xl font-bold text-primary">
                                    {{ substr($vendor->company_name, 0, 2) }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $vendor->company_name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">Concernant : {{ $trip->title }}</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ $trip->destination->name }}
                                <span class="mx-2">•</span>
                                {{ $trip->duration_formatted }}
                                <span class="mx-2">•</span>
                                {{ number_format($trip->price, 0, ',', ' ') }}€
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire -->
                <form action="{{ route('customer.messages.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $vendor->user_id }}">
                    <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                    
                    <!-- Message prédéfinis (optionnel) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Message rapide (optionnel)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <button type="button" onclick="setQuickMessage('Bonjour, cette offre est-elle toujours disponible ?')" 
                                    class="text-left px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Cette offre est-elle disponible ?
                            </button>
                            <button type="button" onclick="setQuickMessage('Bonjour, j\'aimerais avoir plus d\'informations sur cette offre.')" 
                                    class="text-left px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Plus d'informations
                            </button>
                            <button type="button" onclick="setQuickMessage('Bonjour, proposez-vous des tarifs de groupe ?')" 
                                    class="text-left px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Tarifs de groupe ?
                            </button>
                            <button type="button" onclick="setQuickMessage('Bonjour, est-il possible de personnaliser cette offre ?')" 
                                    class="text-left px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Personnalisation possible ?
                            </button>
                        </div>
                    </div>
                    
                    <!-- Textarea -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Votre message
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="8" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                  placeholder="Écrivez votre message ici..."
                                  required>{{ old('content') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Soyez précis dans votre demande pour obtenir une réponse rapide et pertinente.
                        </p>
                    </div>

                    <!-- Informations importantes -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Important :</strong> Ne partagez jamais vos informations de paiement en dehors de la plateforme.
                                    Toutes les transactions doivent être effectuées via notre système sécurisé.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-between">
                        <a href="{{ url()->previous() }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Infos supplémentaires -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>L'organisateur répond généralement sous 24h</p>
            <p class="mt-1">Vous recevrez une notification par email dès qu'il vous répondra</p>
        </div>
    </div>
</div>

<script>
function setQuickMessage(message) {
    document.getElementById('content').value = message;
    document.getElementById('content').focus();
}
</script>
@endsection