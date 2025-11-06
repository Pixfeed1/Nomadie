@extends('layouts.public')

@section('title', 'Créer un compte')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-text-primary mb-4">Rejoignez Nomadie</h1>
            <p class="text-lg text-text-secondary">Choisissez le type de compte qui correspond à vos besoins</p>
        </div>

        <!-- Cartes de choix -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Carte Client -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-primary to-primary-dark p-6 text-white">
                    <div class="flex justify-center mb-4">
                        <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-center">Je veux réserver</h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <p class="text-text-secondary text-center">
                        Découvrez et réservez des expériences uniques
                    </p>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-success mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Hébergements, séjours et activités disponibles</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-success mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Réservation en ligne sécurisée</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-success mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Historique de vos réservations</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-success mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Avis et recommandations personnalisés</span>
                        </li>
                    </ul>
                    
                    <div class="pt-4">
                        <a href="{{ route('register') }}" 
                           class="block w-full py-3 px-4 border-2 border-primary rounded-md shadow-sm text-center text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                            Créer mon compte membre
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carte Organisateur -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-2 border-primary/20">
                <div class="bg-gradient-to-r from-primary-dark to-primary p-6 text-white relative overflow-hidden">
                    <!-- Motif subtil pour différencier -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                            </pattern>
                            <rect width="100" height="100" fill="url(#grid)" />
                        </svg>
                    </div>
                    <div class="flex justify-center mb-4 relative">
                        <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-center relative">Je veux proposer</h2>
                    <span class="absolute top-3 right-3 bg-white/20 backdrop-blur text-white text-xs px-2 py-1 rounded-full font-medium">Professionnel</span>
                </div>
                
                <div class="p-6 space-y-4">
                    <p class="text-text-secondary text-center">
                        Devenez organisateur et proposez vos services
                    </p>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Créez et gérez vos offres (hébergements, séjours, activités)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Tableau de bord professionnel complet</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Gestion des réservations et paiements</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-primary mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-text-secondary">Visibilité auprès de milliers de clients</span>
                        </li>
                    </ul>
                    
                    <div class="pt-4">
                        <a href="{{ route('vendor.register') }}" 
                           class="block w-full py-3 px-4 border-2 border-primary rounded-md shadow-sm text-center text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                            Devenir organisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lien connexion -->
        <div class="mt-12 text-center">
            <p class="text-text-secondary">
                Déjà inscrit ? 
                <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">
                    Connectez-vous à votre compte
                </a>
            </p>
        </div>
    </div>
</div>
@endsection