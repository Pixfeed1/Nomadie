@extends('layouts.vendor')

@section('title', 'Promouvoir l\'offre')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-text-primary">Promouvoir votre offre</h1>
        <p class="text-text-secondary mt-2">{{ $trip->title }}</p>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <p class="text-blue-700">Les options de promotion vous permettent d'augmenter la visibilité de votre offre sur Nomadie.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-transparent hover:border-primary transition-all">
            <div class="h-12 w-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Mise en avant Standard</h2>
            <p class="text-text-secondary mb-4">Votre offre apparaîtra dans les premiers résultats pendant 7 jours.</p>
            <div class="text-3xl font-bold text-primary mb-4">29€</div>
            <button class="w-full btn bg-primary text-white py-3 rounded-lg">Sélectionner</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-transparent hover:border-primary transition-all">
            <div class="h-12 w-12 bg-success/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Mise en avant Premium</h2>
            <p class="text-text-secondary mb-4">Position prioritaire + badge "Recommandé" pendant 14 jours.</p>
            <div class="text-3xl font-bold text-success mb-4">49€</div>
            <button class="w-full btn bg-success text-white py-3 rounded-lg">Sélectionner</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-transparent hover:border-primary transition-all">
            <div class="h-12 w-12 bg-warning/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Featured Homepage</h2>
            <p class="text-text-secondary mb-4">Affichage en page d'accueil pendant 3 jours.</p>
            <div class="text-3xl font-bold text-warning mb-4">99€</div>
            <button class="w-full btn bg-warning text-white py-3 rounded-lg">Sélectionner</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-transparent hover:border-primary transition-all">
            <div class="h-12 w-12 bg-info/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Newsletter Spotlight</h2>
            <p class="text-text-secondary mb-4">Mise en avant dans notre newsletter hebdomadaire.</p>
            <div class="text-3xl font-bold text-info mb-4">149€</div>
            <button class="w-full btn bg-info text-white py-3 rounded-lg">Sélectionner</button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4">Historique des promotions</h2>
        <p class="text-text-secondary">Aucune promotion active pour le moment.</p>
    </div>

    <div class="text-center">
        <a href="{{ route('vendor.trips.show', $trip) }}" class="text-text-secondary hover:text-primary">← Retour à l'offre</a>
    </div>
</div>
@endsection
