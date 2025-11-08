@extends('layouts.public')

@section('title', 'Devenir Rédacteur - Nomadie')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Rejoindre la Communauté Nomadie</h1>
            <p class="text-gray-600">Partagez vos expériences de voyage et contribuez à la plus grande communauté de voyageurs authentiques</p>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Programme de réciprocité :</strong> Plus vous contribuez avec qualité, plus vous obtenez d'avantages (backlinks dofollow, visibilité, badges exclusifs).
                    </p>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('writer.register.submit') }}" id="writerRegistrationForm">
            @csrf

            <!-- Choix du type de rédacteur -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    Choisissez votre profil <span class="text-red-500">*</span>
                </label>

                <div class="space-y-4">
                    <!-- Option 1: Rédacteur Communauté -->
                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ old('writer_type') === 'community' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" name="writer_type" value="community" class="mt-1" {{ old('writer_type') === 'community' ? 'checked' : '' }} required>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌱</span>
                                <span class="font-semibold text-gray-900">Rédacteur Communauté</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Passionné de voyage souhaitant partager ses expériences. Article test obligatoire (validé par notre équipe).
                            </p>
                            <div class="mt-2 text-xs text-gray-500">
                                ✅ Mode libre • ✅ Progression nofollow → dofollow • ✅ Tous les badges accessibles
                            </div>
                        </div>
                    </label>

                    <!-- Option 2: Client-Contributeur -->
                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ old('writer_type') === 'client_contributor' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} {{ !$hasBookings ? 'opacity-50' : '' }}">
                        <input type="radio" name="writer_type" value="client_contributor" class="mt-1" {{ old('writer_type') === 'client_contributor' ? 'checked' : '' }} {{ !$hasBookings ? 'disabled' : '' }} required>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">✈️</span>
                                <span class="font-semibold text-gray-900">Client-Contributeur</span>
                                @if(!$hasBookings)
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">Réservation requise</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Vous avez réservé une expérience sur Nomadie et souhaitez partager votre vécu. Crédibilité renforcée.
                            </p>
                            <div class="mt-2 text-xs text-gray-500">
                                ✅ Badge "Voyageur Vérifié" • ✅ Pas d'article test • ✅ Authenticité garantie
                            </div>
                        </div>
                    </label>

                    <!-- Option 3: Partenaire -->
                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ old('writer_type') === 'partner' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" name="writer_type" value="partner" class="mt-1" {{ old('writer_type') === 'partner' ? 'checked' : '' }} required>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🤝</span>
                                <span class="font-semibold text-gray-900">Partenaire-Rédacteur</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Professionnel du voyage avec offre commerciale active. Contenu promotionnel limité à 20%.
                            </p>
                            <div class="mt-2 text-xs text-gray-500">
                                ⚠️ Max 20% auto-promo • ✅ Mention "Partenaire" visible • ✅ Contrôle renforcé
                            </div>
                        </div>
                    </label>
                </div>

                @error('writer_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Champs conditionnels Client-Contributeur -->
            <div id="clientContributorFields" class="mb-6 hidden">
                <label for="verified_booking_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionnez votre réservation vérifiée <span class="text-red-500">*</span>
                </label>
                @if($hasBookings)
                    <select name="verified_booking_id" id="verified_booking_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Choisir une réservation --</option>
                        @foreach(Auth::user()->bookings()->whereIn('status', ['confirmed', 'completed'])->with('trip')->get() as $booking)
                            <option value="{{ $booking->id }}" {{ old('verified_booking_id') == $booking->id ? 'selected' : '' }}>
                                {{ $booking->trip->title ?? 'Voyage' }} - {{ $booking->created_at->format('d/m/Y') }} ({{ ucfirst($booking->status) }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('verified_booking_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Champs conditionnels Partenaire -->
            <div id="partnerFields" class="mb-6 space-y-4 hidden">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de votre entreprise <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: Voyages Authentiques SARL">
                    @error('company_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="partner_offer_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de votre offre commerciale <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="partner_offer_url" id="partner_offer_url" value="{{ old('partner_offer_url') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://votresite.com/offres">
                    <p class="mt-1 text-xs text-gray-500">Lien vers votre page d'offres ou votre site professionnel</p>
                    @error('partner_offer_url')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Motivation -->
            <div class="mb-6">
                <label for="motivation" class="block text-sm font-medium text-gray-700 mb-2">
                    Parlez-nous de votre motivation <span class="text-red-500">*</span>
                </label>
                <textarea name="motivation" id="motivation" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Expliquez pourquoi vous souhaitez rejoindre Nomadie, vos expériences de voyage, vos domaines d'expertise... (minimum 100 caractères)" required>{{ old('motivation') }}</textarea>
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>Minimum 100 caractères</span>
                    <span id="charCount">0 / 1000</span>
                </div>
                @error('motivation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Engagement -->
            <div class="mb-8 bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">📋 Votre engagement</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span><strong>Qualité :</strong> Score NomadSEO minimum 78/100</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span><strong>Exclusivité :</strong> 3 mois sur Nomadie avant republication ailleurs</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span><strong>Engagement :</strong> Partage sur vos réseaux sociaux + réponse aux commentaires (80%)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span><strong>Authenticité :</strong> Contenu original basé sur votre expérience réelle</span>
                    </li>
                </ul>
            </div>

            <!-- Récompenses -->
            <div class="mb-8 bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-gray-900 mb-3">🎁 Vos avantages</h3>
                <div class="grid md:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Backlinks dofollow après 3-5 articles qualité</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Outil NomadSEO gratuit</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Badges et progression gamifiée</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Visibilité auprès de milliers de voyageurs</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Mention newsletter mensuelle</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-2">●</span>
                        <span>Collaborations rémunérées possibles</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    ← Retour à l'accueil
                </a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Soumettre ma candidature
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('writerRegistrationForm');
    const writerTypeRadios = document.querySelectorAll('input[name="writer_type"]');
    const clientFields = document.getElementById('clientContributorFields');
    const partnerFields = document.getElementById('partnerFields');
    const motivationTextarea = document.getElementById('motivation');
    const charCount = document.getElementById('charCount');

    // Toggle conditional fields
    writerTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            clientFields.classList.add('hidden');
            partnerFields.classList.add('hidden');

            if (this.value === 'client_contributor') {
                clientFields.classList.remove('hidden');
            } else if (this.value === 'partner') {
                partnerFields.classList.remove('hidden');
            }
        });
    });

    // Initialize on page load
    const selectedType = document.querySelector('input[name="writer_type"]:checked');
    if (selectedType) {
        selectedType.dispatchEvent(new Event('change'));
    }

    // Character counter
    motivationTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count + ' / 1000';

        if (count < 100) {
            charCount.classList.add('text-red-600');
            charCount.classList.remove('text-green-600');
        } else {
            charCount.classList.add('text-green-600');
            charCount.classList.remove('text-red-600');
        }
    });

    // Trigger initial count
    motivationTextarea.dispatchEvent(new Event('input'));
});
</script>
@endsection
