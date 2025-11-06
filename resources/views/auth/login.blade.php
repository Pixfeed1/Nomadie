@extends('layouts.public')

@section('title', 'Connexion')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-text-primary">Connexion</h1>
            <p class="mt-3 text-lg text-text-secondary">Accédez à votre compte</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <form class="p-6 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                
                @if ($errors->any())
                <div class="bg-error/10 text-error p-4 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div>
                    <label for="email" class="block text-sm font-medium text-text-primary mb-1">Adresse email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="votre@email.com" required autofocus>
                </div>
                
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-text-primary mb-1">Mot de passe</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:text-primary-dark">
                            Mot de passe oublié ?
                        </a>
                    </div>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="••••••••" required>
                </div>
                
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                    <label for="remember" class="ml-2 block text-sm text-text-primary">
                        Se souvenir de moi
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="w-full px-4 py-3 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors">
                        Se connecter
                    </button>
                </div>
                
                <div class="relative flex py-3 items-center">
                    <div class="flex-grow border-t border-border"></div>
                    <span class="flex-shrink mx-4 text-text-secondary text-sm">ou</span>
                    <div class="flex-grow border-t border-border"></div>
                </div>
                
                <div>
                    <a href="{{ route('login.google') }}" class="flex items-center justify-center w-full px-4 py-3 border border-border rounded-lg hover:bg-bg-alt transition-colors text-text-primary">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                        Continuer avec Google
                    </a>
                </div>
            </form>
            
            <div class="px-6 py-4 bg-bg-alt border-t border-border text-center">
                <p class="text-sm text-text-secondary">
                    Vous n'avez pas de compte ? 
                    <a href="{{ route('register') }}" class="text-primary hover:text-primary-dark font-medium">
                        Créer un compte
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Informations supplémentaires -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-text-primary mb-4">Informations</h2>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-text-primary">Vous êtes un organisateur ?</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Pour accéder à votre espace organisateur, connectez-vous avec les identifiants fournis lors de votre inscription.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-text-primary">Connexion sécurisée</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Toutes les données sont chiffrées et sécurisées selon les normes les plus strictes.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-text-primary">Besoin d'aide ?</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Notre équipe de support est disponible 7j/7 pour vous aider. <a href="{{ route('contact') }}" class="text-primary hover:text-primary-dark">Contactez-nous</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection