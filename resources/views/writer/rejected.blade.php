@extends('layouts.app')

@section('title', 'Candidature refusée')

@section('content')
<div class="min-h-screen bg-bg-primary flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-lg shadow-sm p-8 md:p-12 text-center card">
            <!-- Icon -->
            <div class="h-20 w-20 rounded-full bg-error/10 flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <!-- Titre -->
            <h1 class="text-3xl font-bold text-text-primary mb-4">Candidature refusée</h1>

            <!-- Message -->
            <div class="text-text-secondary mb-8 space-y-4">
                <p class="text-lg">
                    Nous vous remercions de l'intérêt que vous portez à Nomadie en tant que rédacteur.
                </p>
                <p>
                    Après examen attentif de votre candidature, nous avons le regret de vous informer que nous ne pouvons pas donner suite à votre demande pour le moment.
                </p>

                @if(auth()->user()->writer_rejection_reason)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
                    <p class="font-medium text-text-primary mb-2">Motif :</p>
                    <p class="text-sm text-text-secondary">{{ auth()->user()->writer_rejection_reason }}</p>
                </div>
                @endif
            </div>

            <!-- Informations supplémentaires -->
            <div class="bg-info/10 border border-info/30 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-text-primary mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Que faire maintenant ?
                </h3>
                <ul class="space-y-2 text-sm text-text-secondary">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-info mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Vous pouvez continuer à utiliser Nomadie en tant que voyageur</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-info mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Si vous êtes organisateur de voyages, consultez notre <a href="{{ route('vendor.register') }}" class="text-primary hover:underline">espace organisateur</a></span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-info mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pour toute question, contactez-nous à <a href="mailto:support@nomadie.com" class="text-primary hover:underline">support@nomadie.com</a></span>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('home') }}" class="btn bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg transition-colors">
                    Retour à l'accueil
                </a>
                <a href="{{ route('trips.index') }}" class="btn bg-gray-100 hover:bg-gray-200 text-text-primary px-6 py-3 rounded-lg transition-colors">
                    Découvrir les offres
                </a>
            </div>
        </div>

        <!-- Contact -->
        <div class="text-center mt-6">
            <p class="text-sm text-text-secondary">
                Une question ? Contactez notre équipe à
                <a href="mailto:support@nomadie.com" class="text-primary hover:underline">support@nomadie.com</a>
            </p>
        </div>
    </div>
</div>
@endsection
