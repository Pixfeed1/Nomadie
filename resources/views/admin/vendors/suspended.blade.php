@extends('layouts.public')

@section('title', 'Compte suspendu')

@section('content')
<div class="min-h-screen bg-bg-main flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- En-tête avec icône d'alerte -->
            <div class="bg-error/10 px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-error/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text-primary">Compte suspendu</h1>
            </div>
            
            <!-- Contenu principal -->
            <div class="px-6 py-8">
                <div class="space-y-6">
                    <!-- Message principal -->
                    <div class="text-center">
                        <p class="text-text-secondary">
                            Votre compte organisateur a été temporairement suspendu.
                        </p>
                    </div>
                    
                    <!-- Raisons possibles -->
                    <x-alert type="error" title="Raisons possibles :">
                        <ul class="space-y-2 text-sm list-disc list-inside">
                            <li>Non-respect des conditions d'utilisation</li>
                            <li>Réclamations clients non résolues</li>
                            <li>Problème de paiement ou d'abonnement</li>
                        </ul>
                    </x-alert>
                    
                    <!-- Actions à entreprendre -->
                    <x-alert type="info" title="Que faire ?">
                        Pour comprendre les raisons de cette suspension et la faire lever, veuillez contacter notre équipe support.
                    </x-alert>
                </div>
                
                <!-- Actions -->
                <div class="mt-8 space-y-3">
                    <a href="{{ route('contact') }}" class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Contacter le support
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Se déconnecter
                        </button>
                    </form>
                </div>
                
                <!-- Note de bas de page -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-text-secondary">
                        Numéro de référence : {{ auth()->user()->vendor->id ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection