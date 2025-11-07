@extends('layouts.vendor')

@section('title', 'Bienvenue - Dashboard Organisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête de bienvenue -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-8 text-white mb-8">
            <h1 class="text-4xl font-bold mb-4">🎉 Bienvenue {{ $vendor->company_name }} !</h1>
            <p class="text-xl">Votre compte organisateur est maintenant actif et prêt à l'emploi.</p>
        </div>

        <!-- Informations du compte -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Informations de votre compte</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Plan d'abonnement</p>
                    <p class="text-xl font-bold text-blue-600">{{ ucfirst($vendor->subscription_plan) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Taux de commission</p>
                    <p class="text-xl font-bold text-gray-900">{{ $vendor->commission_rate }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Voyages autorisés</p>
                    <p class="text-xl font-bold text-gray-900">{{ $vendor->max_trips }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Destinations autorisées</p>
                    <p class="text-xl font-bold text-gray-900">{{ $vendor->max_destinations }}</p>
                </div>
            </div>
        </div>

        <!-- Prochaines étapes -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Prochaines étapes</h2>
            <div class="space-y-4">
                <!-- Étape 1 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        1
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Complétez votre profil</h3>
                        <p class="text-gray-600 mt-1">Ajoutez votre logo, description et informations de contact pour inspirer confiance aux voyageurs.</p>
                        <a href="{{ route('vendor.settings.profile') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-700 font-medium">
                            Compléter mon profil →
                        </a>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        2
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Créez votre premier voyage</h3>
                        <p class="text-gray-600 mt-1">Partagez votre première expérience avec la communauté Nomadie.</p>
                        <a href="{{ route('vendor.trips.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-700 font-medium">
                            Créer un voyage →
                        </a>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        3
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Ajoutez des disponibilités</h3>
                        <p class="text-gray-600 mt-1">Une fois votre voyage créé, ajoutez des dates de départ pour permettre les réservations.</p>
                    </div>
                </div>

                <!-- Étape 4 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        4
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Suivez vos performances</h3>
                        <p class="text-gray-600 mt-1">Consultez vos statistiques et optimisez vos offres grâce à nos outils d'analytics.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ressources utiles -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ressources utiles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="border border-gray-200 rounded-lg p-4 hover:border-blue-600 transition">
                    <h3 class="font-semibold text-gray-900 mb-2">📚 Guide de démarrage</h3>
                    <p class="text-sm text-gray-600">Apprenez les bases pour bien démarrer sur Nomadie.</p>
                </a>
                <a href="#" class="border border-gray-200 rounded-lg p-4 hover:border-blue-600 transition">
                    <h3 class="font-semibold text-gray-900 mb-2">💡 Meilleures pratiques</h3>
                    <p class="text-sm text-gray-600">Découvrez comment optimiser vos annonces.</p>
                </a>
                <a href="#" class="border border-gray-200 rounded-lg p-4 hover:border-blue-600 transition">
                    <h3 class="font-semibold text-gray-900 mb-2">🆘 Support</h3>
                    <p class="text-sm text-gray-600">Besoin d'aide ? Notre équipe est là pour vous.</p>
                </a>
            </div>
        </div>

        <!-- Bouton pour commencer -->
        <div class="text-center">
            <a href="{{ route('vendor.dashboard.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-4 rounded-lg shadow-lg transition text-lg">
                Accéder à mon dashboard →
            </a>
        </div>
    </div>
</div>
@endsection
