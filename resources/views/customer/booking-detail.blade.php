@extends('customer.layouts.app')

@section('title', 'Détail de la réservation #' . $booking->reference)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Réservation #{{ $booking->reference }}</h1>
            <p class="text-text-secondary mt-1">{{ $booking->trip->title }}</p>
        </div>

        <div>
            @if($booking->status === 'confirmed')
                <span class="badge badge-success">Confirmée</span>
            @elseif($booking->status === 'pending')
                <span class="badge badge-warning">En attente</span>
            @elseif($booking->status === 'cancelled')
                <span class="badge badge-error">Annulée</span>
            @elseif($booking->status === 'completed')
                <span class="badge badge-info">Terminée</span>
            @endif
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- Détails du voyage -->
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Détails du voyage</h2>

                <div class="space-y-4">
                    @if($booking->trip->images && count($booking->trip->images) > 0)
                    <div class="h-48 rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($booking->trip->images[0]['path']) }}" 
                             alt="{{ $booking->trip->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-text-secondary">Destination</p>
                            <p class="font-medium">{{ $booking->trip->destination->name ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-text-secondary">Type d'offre</p>
                            <p class="font-medium">
                                @if($booking->trip->offer_type === 'accommodation')
                                    Hébergement
                                @elseif($booking->trip->offer_type === 'organized_trip')
                                    Séjour organisé
                                @elseif($booking->trip->offer_type === 'activity')
                                    Activité
                                @else
                                    Sur mesure
                                @endif
                            </p>
                        </div>

                        @if($booking->availability)
                        <div>
                            <p class="text-sm text-text-secondary">Date de début</p>
                            <p class="font-medium">{{ $booking->availability->start_date->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-text-secondary">Date de fin</p>
                            <p class="font-medium">{{ $booking->availability->end_date->format('d/m/Y') }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm text-text-secondary">Nombre de personnes</p>
                            <p class="font-medium">{{ $booking->travelers_count }}</p>
                        </div>

                        @if($booking->nights)
                        <div>
                            <p class="text-sm text-text-secondary">Nombre de nuits</p>
                            <p class="font-medium">{{ $booking->nights }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations participants -->
            @if($booking->participants && count($booking->participants) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Participants</h2>
                <div class="space-y-3">
                    @foreach($booking->participants as $index => $participant)
                    <div class="border-b pb-3">
                        <p class="font-medium">Participant {{ $index + 1 }}</p>
                        <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                            <p><span class="text-text-secondary">Nom :</span> {{ $participant['name'] ?? 'N/A' }}</p>
                            @if(isset($participant['email']))
                            <p><span class="text-text-secondary">Email :</span> {{ $participant['email'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Demandes spéciales -->
            @if($booking->special_requests)
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Demandes spéciales</h2>
                <p class="text-text-secondary">{{ $booking->special_requests }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <!-- Résumé financier -->
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Résumé</h2>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Prix de base</span>
                        <span class="font-medium">{{ number_format($booking->price_per_unit, 2) }} {{ $booking->currency }}</span>
                    </div>

                    @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-success">
                        <span>Réduction</span>
                        <span>-{{ number_format($booking->discount_amount, 2) }} {{ $booking->currency }}</span>
                    </div>
                    @endif

                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold">Total</span>
                            <span class="text-2xl font-bold text-primary">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</span>
                        </div>
                    </div>

                    <div class="text-sm text-text-secondary">
                        @if($booking->payment_status === 'paid')
                            <span class="text-success">✓ Payé le {{ $booking->payment_date ? $booking->payment_date->format('d/m/Y') : 'N/A' }}</span>
                        @elseif($booking->payment_status === 'pending')
                            <span class="text-warning">En attente de paiement</span>
                        @elseif($booking->payment_status === 'refunded')
                            <span class="text-info">Remboursé</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Organisateur -->
            @if($booking->vendor)
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Organisateur</h2>
                <p class="font-medium mb-2">{{ $booking->vendor->company_name }}</p>
                @if($booking->vendor->email)
                <p class="text-sm text-text-secondary">{{ $booking->vendor->email }}</p>
                @endif
                @if($booking->vendor->phone)
                <p class="text-sm text-text-secondary">{{ $booking->vendor->phone }}</p>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 card">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>

                <div class="space-y-2">
                    @if($booking->payment_status === 'pending')
                    <a href="{{ route('bookings.payment', $booking) }}" class="block w-full btn bg-primary text-white py-2 rounded-lg text-center">
                        Payer maintenant
                    </a>
                    @endif

                    @if($booking->canBeCancelled())
                    <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?')">
                        @csrf
                        <button type="submit" class="w-full btn bg-error text-white py-2 rounded-lg">
                            Annuler la réservation
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('trips.show', $booking->trip->slug) }}" class="block w-full btn bg-gray-200 py-2 rounded-lg text-center">
                        Voir l'offre
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Retour -->
    <div class="text-center">
        <a href="{{ route('customer.bookings') }}" class="text-text-secondary hover:text-primary">
            ← Retour à mes réservations
        </a>
    </div>
</div>
@endsection
