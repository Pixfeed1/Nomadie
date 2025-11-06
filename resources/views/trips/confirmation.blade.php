@extends('layouts.public')

@section('title', 'Confirmation de réservation - ' . $trip->title)

@section('content')
<div class="bg-bg-main min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-lg shadow-sm p-8 mb-10 text-center">
            <div class="mb-6">
                <div class="rounded-full bg-green-100 p-4 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-text-primary mb-4">Demande envoyée avec succès !</h1>
            <p class="text-lg text-text-secondary mb-6">Nous avons bien reçu votre demande de réservation pour le voyage "{{ $trip->title }}".</p>
            <p class="text-md text-text-secondary mb-8">Un conseiller va vous contacter dans les plus brefs délais pour finaliser votre réservation.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('trips.show', $trip->id) }}" class="inline-flex items-center justify-center px-6 py-3 border border-primary text-base font-medium rounded-md text-primary hover:bg-primary/5 focus:outline-none transition-colors">
                    Retour au voyage
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection