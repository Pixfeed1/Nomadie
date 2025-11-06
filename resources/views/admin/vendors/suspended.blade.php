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
                    <div class="bg-error/5 border border-error/20 rounded-lg p-4 space-y-3">
                        <h2 class="font-semibold text-text-primary">Raisons possibles :</h2>
                        
                        <ul class="space-y-2 text-sm text-text-secondary">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-error mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0016 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Non-respect des conditions d'utilisation
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-error mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0016 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Réclamations clients non résolues
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-error mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0016 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Problème de paiement ou d'abonnement
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Actions à entreprendre -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Que faire ?</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Pour comprendre les raisons de cette suspension et la faire lever, veuillez contacter notre équipe support.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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