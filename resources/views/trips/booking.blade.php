@extends('layouts.public')

@section('title', 'Réserver - ' . $trip->title)

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex text-sm">
                <a href="{{ route('trips.index') }}" class="text-primary hover:text-primary-dark">Offres</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('trips.show', $trip->slug) }}" class="text-primary hover:text-primary-dark">{{ $trip->title }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-600">Réservation</span>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire de réservation -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 bg-primary text-white">
                        <h1 class="text-2xl font-bold">Finaliser votre réservation</h1>
                        <p class="text-white/80 mt-1">{{ $trip->title }}</p>
                    </div>

                    @if($errors->any())
                    <div class="p-6 bg-red-50 border-b border-red-200">
                        <x-alert type="error">
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('trips.booking.process', $trip->slug) }}" class="p-6 space-y-6">
                        @csrf

                        <input type="hidden" name="availability_id" value="{{ $selectedAvailability->id }}">

                        <!-- Informations de la réservation -->
                        <div>
                            <h2 class="text-lg font-semibold text-text-primary mb-4">Détails de la réservation</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Date -->
                                <div class="p-4 bg-bg-alt rounded-lg">
                                    <label class="block text-sm font-medium text-text-secondary mb-1">
                                        @if($trip->isActivity())
                                            Créneau
                                        @else
                                            Dates
                                        @endif
                                    </label>
                                    <p class="font-semibold text-text-primary">
                                        @if($trip->isActivity())
                                            {{ $selectedAvailability->start_date->format('d/m/Y H:i') }}
                                        @else
                                            {{ $selectedAvailability->start_date->format('d/m/Y') }} - {{ $selectedAvailability->end_date->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>

                                <!-- Durée -->
                                <div class="p-4 bg-bg-alt rounded-lg">
                                    <label class="block text-sm font-medium text-text-secondary mb-1">{{ $trip->duration_label }}</label>
                                    <p class="font-semibold text-text-primary">{{ $trip->duration_formatted }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nombre de voyageurs -->
                        <div>
                            <h2 class="text-lg font-semibold text-text-primary mb-4">Participants</h2>

                            @if($trip->isAccommodation())
                                <!-- Pour les hébergements -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input
                                        type="number"
                                        name="travelers"
                                        label="Nombre de personnes"
                                        :value="$travelers"
                                        min="1"
                                        :max="$trip->property_capacity"
                                        :required="true"
                                    />

                                    <x-input
                                        type="number"
                                        name="nights"
                                        label="Nombre de nuits"
                                        :value="$nights"
                                        :min="$trip->min_nights ?? 1"
                                        :required="true"
                                    />
                                </div>
                            @else
                                <!-- Pour les activités et séjours organisés -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input
                                        type="number"
                                        name="adults"
                                        label="Adultes"
                                        :value="old('adults', $travelers)"
                                        min="1"
                                        :required="true"
                                    />

                                    <x-input
                                        type="number"
                                        name="children"
                                        label="Enfants (0-17 ans)"
                                        :value="old('children', 0)"
                                        min="0"
                                    />
                                </div>

                                <input type="hidden" name="travelers" :value="document.getElementById('adults').value + document.getElementById('children').value">
                            @endif
                        </div>

                        <!-- Demandes spéciales -->
                        <div>
                            <x-textarea
                                name="special_requests"
                                label="Demandes spéciales (optionnel)"
                                :rows="4"
                                placeholder="Allergies, régimes alimentaires, besoins spécifiques..."
                            />
                        </div>

                        <!-- Conditions -->
                        <div class="border-t border-border pt-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="accept_terms" type="checkbox" class="w-4 h-4 border border-border rounded focus:ring-primary" name="accept_terms" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="accept_terms" class="font-medium text-text-primary">
                                        J'accepte les <a href="{{ route('terms') }}" class="text-primary hover:text-primary-dark" target="_blank">conditions générales de vente</a> et la <a href="{{ route('privacy') }}" class="text-primary hover:text-primary-dark" target="_blank">politique de confidentialité</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="pt-6">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none transition-colors">
                                Confirmer et procéder au paiement
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                            <p class="text-xs text-center text-text-secondary mt-2">
                                Aucun paiement ne sera effectué à cette étape
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Récapitulatif -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Récapitulatif</h2>

                        <!-- Image du voyage -->
                        @if($trip->images && count($trip->images) > 0)
                        <div class="mb-4">
                            <img src="{{ Storage::url($trip->images[0]['path']) }}" alt="{{ $trip->title }}" class="w-full h-40 object-cover rounded-lg">
                        </div>
                        @endif

                        <h3 class="font-semibold text-text-primary mb-2">{{ $trip->title }}</h3>
                        <p class="text-sm text-text-secondary mb-4">{{ $trip->destination->name }}</p>

                        <!-- Détails de la réservation -->
                        <div class="space-y-3 border-t border-border pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">
                                    @if($trip->isActivity())
                                        Créneau
                                    @else
                                        Dates
                                    @endif
                                </span>
                                <span class="text-text-primary font-medium">
                                    @if($trip->isActivity())
                                        {{ $selectedAvailability->start_date->format('d/m/Y') }}
                                    @else
                                        {{ $selectedAvailability->start_date->format('d/m/Y') }}
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">{{ $trip->duration_label }}</span>
                                <span class="text-text-primary font-medium">{{ $trip->duration_formatted }}</span>
                            </div>

                            @if(!$trip->isAccommodation())
                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">Participants</span>
                                <span class="text-text-primary font-medium">{{ $travelers }} {{ $travelers > 1 ? 'personnes' : 'personne' }}</span>
                            </div>
                            @else
                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">Nuits</span>
                                <span class="text-text-primary font-medium">{{ $nights }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Prix -->
                        <div class="border-t border-border mt-4 pt-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-text-secondary">Prix {{ $trip->price_unit }}</span>
                                <span class="text-text-primary">{{ number_format($selectedAvailability->adult_price, 0, ',', ' ') }} €</span>
                            </div>

                            @if($selectedAvailability->discount_percentage > 0 && (!$selectedAvailability->discount_ends_at || $selectedAvailability->discount_ends_at > now()))
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-green-600">Réduction (-{{ $selectedAvailability->discount_percentage }}%)</span>
                                <span class="text-green-600">- {{ number_format($selectedAvailability->adult_price * $selectedAvailability->discount_percentage / 100, 0, ',', ' ') }} €</span>
                            </div>
                            @endif

                            <div class="flex justify-between text-lg font-bold border-t border-border pt-3 mt-3">
                                <span class="text-text-primary">Total estimé</span>
                                <span class="text-primary">
                                    @php
                                        $basePrice = $selectedAvailability->adult_price;
                                        if ($selectedAvailability->discount_percentage > 0) {
                                            $basePrice *= (1 - $selectedAvailability->discount_percentage / 100);
                                        }
                                        if ($trip->isAccommodation()) {
                                            $total = $basePrice * $nights;
                                        } else {
                                            $total = $basePrice * $travelers;
                                        }
                                    @endphp
                                    {{ number_format($total, 0, ',', ' ') }} €
                                </span>
                            </div>
                            <p class="text-xs text-text-secondary mt-2">Prix final calculé après validation</p>
                        </div>

                        <!-- Badges -->
                        @if($selectedAvailability->is_guaranteed)
                        <div class="mt-4">
                            <x-badge variant="success" style="soft" size="sm">
                                @if($trip->isActivity())
                                    Séance garantie
                                @else
                                    Départ garanti
                                @endif
                            </x-badge>
                        </div>
                        @endif

                        <!-- Annulation gratuite -->
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Annulation gratuite</p>
                                    <p class="text-xs text-blue-700 mt-1">Jusqu'à 48h avant le départ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
