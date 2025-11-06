@extends('layouts.public')

@section('title', 'Ajouter un avis - ' . $trip->title)

@section('content')
<div class="bg-bg-main min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-lg shadow-sm p-8 mb-10">
            <div class="flex items-center gap-2 mb-6">
                <a href="{{ route('trips.show', $trip->id) }}" class="inline-flex items-center text-text-secondary hover:text-primary text-sm font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au voyage
                </a>
            </div>
            
            <h1 class="text-3xl font-bold text-text-primary mb-6">Partagez votre expérience</h1>
            <p class="text-text-secondary mb-8">Votre avis sur "{{ $trip->title }}" aidera d'autres voyageurs à faire leur choix. Merci de partager votre expérience !</p>
            
            <!-- Formulaire d'avis -->
            <form action="{{ route('trips.review.store', $trip->id) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Note -->
                <div>
                    <label for="rating" class="block text-text-primary font-medium mb-2">Votre note</label>
                    <x-rating-stars :interactive="true" name="rating" :required="true" size="lg" />
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Date du voyage -->
                <div>
                    <label for="travel_date" class="block text-text-primary font-medium mb-2">Date de votre voyage</label>
                    <input 
                        type="date" 
                        id="travel_date" 
                        name="travel_date" 
                        class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary" 
                        required
                        max="{{ date('Y-m-d') }}"
                        value="{{ old('travel_date') }}"
                    >
                    @error('travel_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-text-secondary mt-1">La date à laquelle vous avez effectué ce voyage</p>
                </div>
                
                <!-- Avis -->
                <div>
                    <label for="content" class="block text-text-primary font-medium mb-2">Votre avis</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="6" 
                        class="w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary" 
                        placeholder="Partagez votre expérience... Qu'avez-vous aimé ? Qu'est-ce qui aurait pu être amélioré ?"
                        required
                        minlength="10"
                        maxlength="1000"
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
<p class="text-xs text-text-secondary mt-1">10 à 1000 caractères</p>
                </div>
                
                <!-- Bouton de soumission -->
                <div class="pt-4">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none transition-colors">
                        Publier mon avis
                    </button>
                </div>
            </form>
            
            <!-- Conseils pour un bon avis -->
            <div class="mt-12 bg-bg-alt p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Conseils pour rédiger un avis utile</h3>
                <ul class="space-y-2 text-text-secondary">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0 mt-0.5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Soyez spécifique : mentionnez ce que vous avez aimé ou non, et pourquoi.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0 mt-0.5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Partagez votre expérience personnelle : activités, hébergements, guide...</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0 mt-0.5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Soyez équilibré : incluez à la fois des aspects positifs et des points à améliorer.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0 mt-0.5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Partagez des conseils utiles pour les futurs voyageurs.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script pour les étoiles interactives -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script pour les étoiles interactives
        const ratingInputs = document.querySelectorAll('input[name="rating"]');
        
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const rating = parseInt(this.value);
                
                // Mettre à jour toutes les étoiles
                ratingInputs.forEach((radio, index) => {
                    const star = radio.nextElementSibling;
                    if (index < rating) {
                        star.classList.add('text-yellow-400');
                        star.classList.remove('text-gray-300');
                    } else {
                        star.classList.add('text-gray-300');
                        star.classList.remove('text-yellow-400');
                    }
                });
            });
            
            // Effet de survol
            const star = input.nextElementSibling;
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(input.value);
                
                // Mettre en surbrillance les étoiles jusqu'à celle survolée
                ratingInputs.forEach((radio, index) => {
                    const s = radio.nextElementSibling;
                    if (index < rating) {
                        s.classList.add('text-yellow-400');
                        s.classList.remove('text-gray-300');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                // Réinitialiser à l'état sélectionné
                ratingInputs.forEach((radio, index) => {
                    const s = radio.nextElementSibling;
                    if (radio.checked) {
                        if (index < parseInt(radio.value)) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-300');
                        } else {
                            s.classList.add('text-gray-300');
                            s.classList.remove('text-yellow-400');
                        }
                    } else {
                        s.classList.add('text-gray-300');
                        s.classList.remove('text-yellow-400');
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection