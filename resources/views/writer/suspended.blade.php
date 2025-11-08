@extends('layouts.app')

@section('title', 'Compte rédacteur suspendu')

@section('content')
<div class="min-h-screen bg-bg-primary flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-lg shadow-sm p-8 md:p-12 text-center card">
            <!-- Icon -->
            <div class="h-20 w-20 rounded-full bg-warning/10 flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Titre -->
            <h1 class="text-3xl font-bold text-text-primary mb-4">Accès rédacteur suspendu</h1>

            <!-- Message -->
            <div class="text-text-secondary mb-8 space-y-4">
                <p class="text-lg font-medium">
                    Votre compte rédacteur a été temporairement suspendu.
                </p>
                <p>
                    Vous ne pouvez pas accéder à l'espace rédacteur pour le moment. Cette suspension peut être due à un non-respect de nos conditions d'utilisation ou à une activité inhabituelle.
                </p>

                @if(auth()->user()->writer_suspension_reason)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
                    <p class="font-medium text-text-primary mb-2">Motif de la suspension :</p>
                    <p class="text-sm text-text-secondary">{{ auth()->user()->writer_suspension_reason }}</p>
                </div>
                @endif

                @if(auth()->user()->writer_suspended_until)
                <div class="bg-info/10 border border-info/30 rounded-lg p-4 mt-4">
                    <p class="font-medium text-text-primary mb-1">Durée de la suspension :</p>
                    <p class="text-sm text-text-secondary">
                        Jusqu'au {{ auth()->user()->writer_suspended_until->format('d/m/Y à H:i') }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Informations importantes -->
            <div class="bg-warning/10 border border-warning/30 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-text-primary mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informations importantes
                </h3>
                <ul class="space-y-2 text-sm text-text-secondary">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-warning mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Vous ne pouvez pas accepter de nouveaux briefs pendant la suspension</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-warning mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Les articles en cours ne sont pas affectés</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-warning mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Vous pouvez toujours utiliser Nomadie en tant que voyageur</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Contactez notre équipe pour plus d'informations sur votre situation</span>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-6">
                <a href="mailto:support@nomadie.com?subject=Suspension compte rédacteur" class="btn bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contacter le support
                </a>
                <a href="{{ route('home') }}" class="btn bg-gray-100 hover:bg-gray-200 text-text-primary px-6 py-3 rounded-lg transition-colors">
                    Retour à l'accueil
                </a>
            </div>

            <!-- Note de révision -->
            <div class="text-sm text-text-secondary bg-gray-50 rounded-lg p-4">
                <p class="font-medium mb-1">Révision de la suspension</p>
                <p>Si vous estimez que cette suspension est une erreur, veuillez nous contacter immédiatement. Nous examinerons votre cas dans les plus brefs délais.</p>
            </div>
        </div>

        <!-- Contact -->
        <div class="text-center mt-6">
            <p class="text-sm text-text-secondary">
                Support disponible 7j/7 :
                <a href="mailto:support@nomadie.com" class="text-primary hover:underline font-medium">support@nomadie.com</a>
            </p>
        </div>
    </div>
</div>
@endsection
