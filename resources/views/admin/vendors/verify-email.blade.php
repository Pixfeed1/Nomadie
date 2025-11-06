@extends('layouts.public')

@section('title', 'Vérification email requise')

@section('content')
<div class="min-h-screen bg-bg-main flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- En-tête avec icône email -->
            <div class="bg-accent/10 px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-accent/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text-primary">Vérifiez votre email</h1>
            </div>
            
            <!-- Contenu principal -->
            <div class="px-6 py-8">
                <div class="space-y-6">
                    <!-- Message principal -->
                    <div class="text-center">
                        <p class="text-text-secondary">
                            Pour accéder à votre espace organisateur, vous devez d'abord vérifier votre adresse email.
                        </p>
                    </div>
                    
                    <!-- Email concerné -->
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-text-secondary">Email envoyé à :</p>
                        <p class="text-lg font-medium text-text-primary mt-1">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-text-primary">Comment procéder ?</h2>
                        
                        <ol class="space-y-3 text-sm text-text-secondary">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-primary/10 text-primary rounded-full flex items-center justify-center text-xs font-semibold mr-3">1</span>
                                <span>Consultez votre boîte de réception</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-primary/10 text-primary rounded-full flex items-center justify-center text-xs font-semibold mr-3">2</span>
                                <span>Ouvrez l'email de confirmation envoyé par {{ config('app.name') }}</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-primary/10 text-primary rounded-full flex items-center justify-center text-xs font-semibold mr-3">3</span>
                                <span>Cliquez sur le lien de vérification</span>
                            </li>
                        </ol>
                    </div>
                    
                    <!-- Alerte spam -->
                    <x-alert type="warning" title="Pas reçu l'email ?">
                        Vérifiez votre dossier spam ou courrier indésirable.
                    </x-alert>
                </div>
                
                <!-- Actions -->
                <div class="mt-8 space-y-3">
                    <form method="POST" action="{{ route('verification.resend') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Renvoyer l'email de vérification
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Se déconnecter
                        </button>
                    </form>
                </div>
                
                <!-- Support -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-text-secondary">
                        Besoin d'aide ? <a href="{{ route('contact') }}" class="font-medium text-primary hover:text-primary-dark">Contactez notre support</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Message de confirmation si email renvoyé -->
        @if (session('resent'))
            <div class="mt-4">
                <x-alert type="success">
                    Un nouvel email de vérification vous a été envoyé !
                </x-alert>
            </div>
        @endif
    </div>
</div>
@endsection