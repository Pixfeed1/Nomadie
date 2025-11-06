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
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Pas reçu l'email ?</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Vérifiez votre dossier spam ou courrier indésirable.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0016 0zm-2 0a6 6 0 100-12 6 6 0 0012 0zm-1.293-2.293a1 1 0 010-1.414l3-3a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-3-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Un nouvel email de vérification vous a été envoyé !
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection