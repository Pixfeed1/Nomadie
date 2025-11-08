@extends('layouts.public')

@section('title', 'Créer un compte')

@section('content')
<!-- Hero Banner -->
<div class="relative text-white" style="background-color: #1d5554;">
    <div class="absolute inset-0 overflow-hidden">
        <!-- Image d'arrière-plan - À remplacer par une vraie image plus tard -->
        <img src="{{ asset('images/register-bg.jpg') }}" alt="Créer un compte" class="w-full h-full object-cover" onerror="this.style.display='none'">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Rejoignez Nomadie</h1>
            <p class="text-lg md:text-xl text-white/90">Choisissez votre profil et commencez votre aventure</p>
        </div>
    </div>
</div>

<!-- Content Section -->
<div class="bg-bg-main py-12">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="space-y-4">
                    <!-- Option Client -->
                    <a href="{{ route('register') }}" class="block p-6 border-2 border-gray-200 rounded-lg hover:border-primary hover:bg-gray-50 transition-all">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-text-primary mb-1">Je veux réserver</h3>
                                <p class="text-sm text-text-secondary">
                                    Découvrez et réservez des expériences uniques
                                </p>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Option Organisateur -->
                    <a href="{{ route('vendor.register') }}" class="block p-6 border-2 border-gray-200 rounded-lg hover:border-primary hover:bg-gray-50 transition-all">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-text-primary mb-1">Je veux proposer</h3>
                                <p class="text-sm text-text-secondary">
                                    Devenez organisateur et proposez vos services
                                </p>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="px-6 py-4 bg-bg-alt border-t border-border text-center">
                <p class="text-sm text-text-secondary">
                    Déjà inscrit ?
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">
                        Connectez-vous à votre compte
                    </a>
                </p>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-text-primary mb-4">Pourquoi rejoindre Nomadie ?</h2>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-text-primary">Expériences authentiques</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Découvrez des voyages uniques et hors des sentiers battus.
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
                        <h3 class="text-sm font-medium text-text-primary">Paiements sécurisés</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Toutes vos transactions sont protégées et sécurisées.
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
                        <h3 class="text-sm font-medium text-text-primary">Support disponible</h3>
                        <p class="text-xs text-text-secondary mt-1">
                            Notre équipe est là pour vous accompagner 7j/7.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
