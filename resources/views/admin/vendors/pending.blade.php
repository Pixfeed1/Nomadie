@extends('layouts.public')

@section('title', 'Inscription en cours de validation')

@section('content')
<div class="min-h-screen bg-bg-main flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- En-tête avec icône -->
            <div class="bg-primary/10 px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-primary/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text-primary">Inscription en cours de validation</h1>
            </div>
            
            <!-- Contenu principal -->
            <div class="px-6 py-8">
                <div class="space-y-6">
                    <!-- Message principal -->
                    <div class="text-center">
                        <p class="text-text-secondary">
                            Merci d'avoir soumis votre demande d'inscription en tant qu'organisateur de voyages.
                        </p>
                    </div>
                    
                    <!-- Étapes du processus -->
                    <div class="bg-bg-alt/50 rounded-lg p-4 space-y-3">
                        <h2 class="font-semibold text-text-primary mb-3">Prochaines étapes :</h2>
                        
                        <!-- Étape 1 : Email de confirmation -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-accent/20 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-accent">1</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-text-primary">Confirmez votre email</h3>
                                <p class="text-sm text-text-secondary mt-1">
                                    Un email de confirmation a été envoyé à votre adresse. Veuillez cliquer sur le lien pour valider votre compte.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Étape 2 : Vérification -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-primary">2</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-text-primary">Vérification de votre dossier</h3>
                                <p class="text-sm text-text-secondary mt-1">
                                    Notre équipe va examiner votre demande et vérifier les informations fournies.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Étape 3 : Activation -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-gray-500">3</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-text-primary">Activation de votre compte</h3>
                                <p class="text-sm text-text-secondary mt-1">
                                    Une fois validé, vous recevrez un email de confirmation et pourrez accéder à votre espace vendeur.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations complémentaires -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Bon à savoir</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>La validation prend généralement 24 à 48 heures</li>
                                        <li>Vérifiez vos spams si vous ne recevez pas l'email</li>
                                        <li>Vous pouvez nous contacter en cas de question</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- État du paiement si applicable -->
                    @if(session('payment_completed'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0016 0zm-2 0a6 6 0 100-12 6 6 0 0012 0zm-1.293-2.293a1 1 0 010-1.414l3-3a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-3-3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Paiement confirmé</h3>
                                <p class="mt-1 text-sm text-green-700">
                                    Votre paiement a été traité avec succès. Votre abonnement sera activé dès la validation de votre compte.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Actions -->
                <div class="mt-8 space-y-3">
                    <a href="{{ route('home') }}" class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Retour à l'accueil
                    </a>
                    
                    <a href="{{ route('contact') }}" class="block w-full text-center px-4 py-2 border border-primary text-sm font-medium rounded-md text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Nous contacter
                    </a>
                </div>
                
                <!-- Note de bas de page -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-text-secondary">
                        Vous avez des questions ? N'hésitez pas à nous contacter à 
                        <a href="mailto:support@marketplace-voyages.com" class="text-primary hover:text-primary-dark">support@marketplace-voyages.com</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Informations de connexion -->
        <div class="mt-6 text-center">
            <p class="text-sm text-text-secondary">
                Déjà activé ? 
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary-dark">
                    Se connecter
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Styles spécifiques si nécessaire */
    .bg-bg-main {
        background-color: var(--color-bg-main, #f9fafb);
    }
    
    .bg-bg-alt {
        background-color: var(--color-bg-alt, #f3f4f6);
    }
</style>
@endsection