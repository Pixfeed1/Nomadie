@extends('layouts.public')

@section('title', 'Devenir Rédacteur - Nomadie')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-6xl">
    <!-- En-tête avec gradient -->
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-8 mb-8 border border-primary/20">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-text-primary mb-3">Rejoignez la Communauté Nomadie</h1>
            <p class="text-lg text-text-secondary">
                Partagez vos expériences de voyage et contribuez à la plus grande communauté de voyageurs authentiques
            </p>
        </div>
    </div>

    <!-- Bannière d'information -->
    <div class="bg-accent/10 border-l-4 border-accent rounded-r-lg p-5 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center">
                    <svg class="h-5 w-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm text-text-primary">
                    <strong class="font-semibold">Programme de réciprocité :</strong> Plus vous contribuez avec qualité, plus vous obtenez d'avantages (backlinks dofollow, visibilité, badges exclusifs).
                </p>
            </div>
        </div>
    </div>

    <!-- Formulaire d'inscription -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('writer.register.submit') }}" id="writerRegistrationForm">
            @csrf

            <div class="p-8 space-y-8">
                <!-- Choix du type de rédacteur -->
                <div>
                    <label class="block text-lg font-semibold text-text-primary mb-4">
                        Choisissez votre profil <span class="text-error">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Option 1: Rédacteur Communauté -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="writer_type" value="community" class="peer sr-only" {{ old('writer_type') === 'community' ? 'checked' : '' }} required>
                            <div class="h-full p-5 bg-white border-2 border-border rounded-lg transition-all peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50">
                                <div class="text-center mb-3">
                                    <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-success/10 mb-3">
                                        <span class="text-3xl">🌱</span>
                                    </div>
                                    <h3 class="font-semibold text-text-primary">Rédacteur Communauté</h3>
                                </div>
                                <p class="text-sm text-text-secondary mb-4">
                                    Passionné de voyage souhaitant partager ses expériences. Article test obligatoire.
                                </p>
                                <div class="space-y-1">
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Mode libre</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Progression dofollow</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Tous badges accessibles</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Option 2: Client-Contributeur -->
                        <label class="relative cursor-pointer group {{ !$hasBookings ? 'opacity-60 cursor-not-allowed' : '' }}">
                            <input type="radio" name="writer_type" value="client_contributor" class="peer sr-only" {{ old('writer_type') === 'client_contributor' ? 'checked' : '' }} {{ !$hasBookings ? 'disabled' : '' }} required>
                            <div class="h-full p-5 bg-white border-2 border-border rounded-lg transition-all peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50">
                                <div class="text-center mb-3">
                                    <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-accent/10 mb-3">
                                        <span class="text-3xl">✈️</span>
                                    </div>
                                    <h3 class="font-semibold text-text-primary flex items-center justify-center gap-2">
                                        Client-Contributeur
                                        @if(!$hasBookings)
                                            <span class="text-xs bg-border text-text-secondary px-2 py-0.5 rounded-full">Réservation requise</span>
                                        @endif
                                    </h3>
                                </div>
                                <p class="text-sm text-text-secondary mb-4">
                                    Vous avez réservé une expérience et souhaitez partager votre vécu authentique.
                                </p>
                                <div class="space-y-1">
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Badge "Voyageur Vérifié"</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Pas d'article test</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Authenticité garantie</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Option 3: Partenaire -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="writer_type" value="partner" class="peer sr-only" {{ old('writer_type') === 'partner' ? 'checked' : '' }} required>
                            <div class="h-full p-5 bg-white border-2 border-border rounded-lg transition-all peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50">
                                <div class="text-center mb-3">
                                    <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-primary/10 mb-3">
                                        <span class="text-3xl">🤝</span>
                                    </div>
                                    <h3 class="font-semibold text-text-primary">Partenaire-Rédacteur</h3>
                                </div>
                                <p class="text-sm text-text-secondary mb-4">
                                    Professionnel du voyage avec offre commerciale. Limité à 20% de promo.
                                </p>
                                <div class="space-y-1">
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-accent mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Max 20% auto-promo</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Mention "Partenaire"</span>
                                    </div>
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="h-4 w-4 text-success mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Contrôle qualité renforcé</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('writer_type')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Champs conditionnels Client-Contributeur -->
                <div id="clientContributorFields" class="hidden">
                    <label for="verified_booking_id" class="block text-sm font-semibold text-text-primary mb-2">
                        Sélectionnez votre réservation vérifiée <span class="text-error">*</span>
                    </label>
                    @if($hasBookings)
                        <select name="verified_booking_id" id="verified_booking_id" class="w-full border-border rounded-lg shadow-sm focus:border-primary focus:ring-primary focus:ring-2">
                            <option value="">-- Choisir une réservation --</option>
                            @foreach(Auth::user()->bookings()->whereIn('status', ['confirmed', 'completed'])->with('trip')->get() as $booking)
                                <option value="{{ $booking->id }}" {{ old('verified_booking_id') == $booking->id ? 'selected' : '' }}>
                                    {{ $booking->trip->title ?? 'Voyage' }} - {{ $booking->created_at->format('d/m/Y') }} ({{ ucfirst($booking->status) }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('verified_booking_id')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Champs conditionnels Partenaire -->
                <div id="partnerFields" class="hidden space-y-4">
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-text-primary mb-2">
                            Nom de votre entreprise <span class="text-error">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" class="w-full border-border rounded-lg shadow-sm focus:border-primary focus:ring-primary focus:ring-2" placeholder="Ex: Voyages Authentiques SARL">
                        @error('company_name')
                            <p class="mt-2 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="partner_offer_url" class="block text-sm font-semibold text-text-primary mb-2">
                            URL de votre offre commerciale <span class="text-error">*</span>
                        </label>
                        <input type="url" name="partner_offer_url" id="partner_offer_url" value="{{ old('partner_offer_url') }}" class="w-full border-border rounded-lg shadow-sm focus:border-primary focus:ring-primary focus:ring-2" placeholder="https://votresite.com/offres">
                        <p class="mt-1 text-xs text-text-secondary">Lien vers votre page d'offres ou votre site professionnel</p>
                        @error('partner_offer_url')
                            <p class="mt-2 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Motivation -->
                <div>
                    <label for="motivation" class="block text-sm font-semibold text-text-primary mb-2">
                        Parlez-nous de votre motivation <span class="text-error">*</span>
                    </label>
                    <textarea name="motivation" id="motivation" rows="6" class="w-full border-border rounded-lg shadow-sm focus:border-primary focus:ring-primary focus:ring-2" placeholder="Expliquez pourquoi vous souhaitez rejoindre Nomadie, vos expériences de voyage, vos domaines d'expertise... (minimum 100 caractères)" required>{{ old('motivation') }}</textarea>
                    <div class="mt-2 flex justify-between text-xs">
                        <span class="text-text-secondary">Minimum 100 caractères</span>
                        <span id="charCount" class="font-medium text-text-secondary">0 / 1000</span>
                    </div>
                    @error('motivation')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Engagement et avantages en 2 colonnes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-8 pb-8">
                <!-- Engagement -->
                <div class="bg-bg-alt rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-text-primary">Votre engagement</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong class="text-text-primary">Qualité :</strong> Score NomadSEO minimum 78/100</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong class="text-text-primary">Exclusivité :</strong> 3 mois sur Nomadie avant republication</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong class="text-text-primary">Engagement :</strong> Partage réseaux sociaux + réponse commentaires (80%)</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong class="text-text-primary">Authenticité :</strong> Contenu original basé sur expérience réelle</span>
                        </li>
                    </ul>
                </div>

                <!-- Avantages -->
                <div class="bg-gradient-to-br from-accent/10 via-primary/10 to-success/10 rounded-lg p-6 border border-primary/20">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-text-primary">Vos avantages</h3>
                    </div>
                    <ul class="grid grid-cols-1 gap-3">
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Backlinks dofollow après 3-5 articles qualité</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Outil NomadSEO gratuit pour optimiser vos articles</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Badges et progression gamifiée</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Visibilité auprès de milliers de voyageurs</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Mention dans newsletter mensuelle</span>
                        </li>
                        <li class="flex items-start text-sm text-text-secondary">
                            <svg class="h-5 w-5 text-primary mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Collaborations rémunérées possibles</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bouton de soumission -->
            <div class="px-8 pb-8">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md">
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
            charCount.classList.remove('text-success');
            charCount.classList.add('text-error');
        } else {
            charCount.classList.remove('text-error');
            charCount.classList.add('text-success');
        }
    });

    // Trigger initial count
    motivationTextarea.dispatchEvent(new Event('input'));
});
</script>
@endsection
