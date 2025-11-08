@extends('layouts.public')

@section('title', 'Paiement - Réservation #' . $booking->booking_number)

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-text-primary">Finaliser votre paiement</h1>
            <p class="mt-2 text-text-secondary">Réservation #{{ $booking->booking_number }}</p>
        </div>

        @if(session('success'))
        <div class="mb-6">
            <x-alert type="success" :message="session('success')" />
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6">
            <x-alert type="error" :message="session('error')" />
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-6">
            <x-alert type="warning" :message="session('warning')" />
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations de paiement -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 bg-primary text-white">
                        <h2 class="text-xl font-bold">Paiement sécurisé</h2>
                        <p class="text-white/80 text-sm mt-1">Par carte bancaire via Stripe</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Informations importantes -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Vous serez redirigé vers une page de paiement sécurisée Stripe. Aucune donnée bancaire n'est stockée sur nos serveurs.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Détails du voyage -->
                        <div>
                            <h3 class="text-lg font-semibold text-text-primary mb-4">Détails de votre réservation</h3>

                            @if($booking->trip && $booking->trip->images && count($booking->trip->images) > 0)
                            <div class="mb-4">
                                <img src="{{ Storage::url($booking->trip->images[0]['path']) }}" alt="{{ $booking->trip->title }}" class="w-full h-48 object-cover rounded-lg">
                            </div>
                            @endif

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Offre</span>
                                    <span class="text-text-primary font-medium">{{ $booking->trip->title ?? 'N/A' }}</span>
                                </div>

                                @if($booking->availability)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Dates</span>
                                    <span class="text-text-primary font-medium">
                                        {{ $booking->availability->start_date->format('d/m/Y') }} - {{ $booking->availability->end_date->format('d/m/Y') }}
                                    </span>
                                </div>
                                @endif

                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Participants</span>
                                    <span class="text-text-primary font-medium">{{ $booking->number_of_travelers }} {{ $booking->number_of_travelers > 1 ? 'personnes' : 'personne' }}</span>
                                </div>

                                @if($booking->nights)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Nuits</span>
                                    <span class="text-text-primary font-medium">{{ $booking->nights }}</span>
                                </div>
                                @endif

                                @if($booking->special_requests)
                                <div class="pt-3 border-t border-border">
                                    <span class="text-text-secondary text-sm">Demandes spéciales:</span>
                                    <p class="text-text-primary text-sm mt-1">{{ $booking->special_requests }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bouton de paiement -->
                        <div class="border-t border-border pt-6">
                            <button id="payment-button" type="button" class="w-full inline-flex items-center justify-center px-6 py-4 border border-transparent text-lg font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none transition-colors">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Payer {{ number_format($booking->total_amount, 2, ',', ' ') }} €
                            </button>

                            <p class="text-xs text-center text-text-secondary mt-3">
                                En cliquant sur ce bouton, vous acceptez nos <a href="{{ route('terms') }}" class="text-primary hover:text-primary-dark" target="_blank">conditions générales de vente</a>
                            </p>
                        </div>

                        <!-- Garanties -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-border">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">Paiement sécurisé</p>
                                    <p class="text-xs text-text-secondary">SSL & 3D Secure</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">Remboursement</p>
                                    <p class="text-xs text-text-secondary">Jusqu'à 48h avant</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">Support 7j/7</p>
                                    <p class="text-xs text-text-secondary">Équipe disponible</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif du montant -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-text-primary mb-4">Récapitulatif</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">Sous-total</span>
                                <span class="text-text-primary">{{ number_format($booking->subtotal, 2, ',', ' ') }} €</span>
                            </div>

                            @if($booking->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600">Réduction</span>
                                <span class="text-green-600">- {{ number_format($booking->discount_amount, 2, ',', ' ') }} €</span>
                            </div>
                            @endif

                            <div class="border-t border-border pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-text-primary">Total</span>
                                    <span class="text-lg font-bold text-primary">{{ number_format($booking->total_amount, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de réservation -->
                        <div class="mt-6 pt-6 border-t border-border">
                            <p class="text-xs text-text-secondary mb-2">Numéro de réservation</p>
                            <p class="font-mono text-sm font-semibold text-text-primary">{{ $booking->booking_number }}</p>
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <p class="text-xs text-text-secondary mb-2">Statut</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                En attente de paiement
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.getElementById('payment-button').addEventListener('click', async function() {
        const button = this;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Redirection...';

        try {
            const response = await fetch('{{ route('bookings.payment.initiate', $booking->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.error) {
                alert('Erreur: ' + data.error);
                button.disabled = false;
                button.innerHTML = '<svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Payer {{ number_format($booking->total_amount, 2, ',', ' ') }} €';
                return;
            }

            // Rediriger vers Stripe Checkout
            window.location.href = data.url;

        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
            button.disabled = false;
            button.innerHTML = '<svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Payer {{ number_format($booking->total_amount, 2, ',', ' ') }} €';
        }
    });
</script>
@endpush
