@extends('layouts.public')

@section('title', 'Inscription Confirmée - Bienvenue !')

@section('styles')
<style>
    /* Animation de succès */
    @keyframes checkmark {
        0% {
            transform: scale(0) rotate(45deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.2) rotate(45deg);
            opacity: 1;
        }
        100% {
            transform: scale(1) rotate(45deg);
            opacity: 1;
        }
    }
    
    .checkmark {
        animation: checkmark 0.6s ease-in-out;
    }
    
    /* Animation de pulsation */
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.8;
        }
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    /* Animation d'apparition */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    
    /* Styles pour les cartes d'information */
    .info-card {
        transition: all 0.3s ease;
        border: 1px solid var(--color-border);
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--color-primary);
    }
    
    /* Style pour les badges de statut */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #16a34a;
    }
    
    .status-payment-required {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    /* Timeline styles */
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 2rem;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 2rem;
        width: 2px;
        height: calc(100% - 1rem);
        background-color: #e5e7eb;
    }
    
    .timeline-icon {
        position: absolute;
        left: 0;
        top: 0.25rem;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }
    
    .timeline-icon.completed {
        background-color: var(--color-success);
        color: white;
    }
    
    .timeline-icon.pending {
        background-color: var(--color-warning);
        color: white;
    }
    
    .timeline-icon.waiting {
        background-color: #e5e7eb;
        color: #6b7280;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .grid-responsive {
            grid-template-columns: 1fr;
        }
        
        .timeline-item {
            padding-left: 1.5rem;
        }
        
        .timeline-icon {
            width: 1.25rem;
            height: 1.25rem;
            font-size: 0.625rem;
        }
    }
</style>
@endsection

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- En-tête de confirmation -->
        <div class="text-center mb-12 fade-in-up">
            <div class="mx-auto mb-6 w-24 h-24 bg-success rounded-full flex items-center justify-center 
pulse-animation">
                <svg class="w-12 h-12 text-white checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 
24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 
7"></path>
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold text-text-primary mb-4">Félicitations !</h1>
            <p class="text-xl text-text-secondary mb-2">Votre inscription a été soumise avec succès</p>
            
            <!-- Statut conditionnel -->
            @if(isset($vendor))
                <div class="status-badge status-active mb-4">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 
00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Inscription finalisée
                </div>
            @else
                <div class="status-badge status-pending mb-4">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 
00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    En cours de validation
                </div>
            @endif
        </div>

        <!-- Informations de l'inscription -->
        @if(isset($vendor))
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 fade-in-up">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Récapitulatif de votre inscription</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Informations entreprise -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-text-primary border-b border-border pb-2">Informations 
entreprise</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Entreprise :</span>
                            <span class="font-medium">{{ $vendor->company_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Email :</span>
                            <span class="font-medium">{{ $vendor->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Téléphone :</span>
                            <span class="font-medium">{{ $vendor->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">SIRET :</span>
                            <span class="font-medium">{{ $vendor->siret }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Abonnement -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-text-primary border-b border-border 
pb-2">Abonnement</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Formule :</span>
                            <span class="font-medium capitalize">
                                @switch($vendor->subscription_plan)
                                    @case('free')
                                        <span class="text-success">Gratuit</span>
                                        @break
                                    @case('essential')
                                        <span class="text-accent">Essentiel</span>
                                        @break
                                    @case('pro')
                                        <span class="text-primary">Pro</span>
                                        @break
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Prix mensuel :</span>
                            <span class="font-medium">
                                @switch($vendor->subscription_plan)
                                    @case('free')
                                        Gratuit
                                        @break
                                    @case('essential')
                                        49,00 € TTC
                                        @break
                                    @case('pro')
                                        99,00 € TTC
                                        @break
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Commission :</span>
                            <span class="font-medium">
                                @switch($vendor->subscription_plan)
                                    @case('free')
                                        20%
                                        @break
                                    @case('essential')
                                        10%
                                        @break
                                    @case('pro')
                                        5%
                                        @break
                                    @default
                                        10%
                                @endswitch
                            </span>
                        </div>
                        @if($vendor->countries_count || $vendor->destinations_count || $vendor->countries)
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Destinations :</span>
                            <span class="font-medium">{{ $vendor->countries_count ?? $vendor->destinations_count 
?? $vendor->countries()->count() }} sélectionnées</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Prochaines étapes -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 fade-in-up">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Prochaines étapes</h2>
            
            <div class="space-y-6">
                @if(isset($vendor))
                    @php
                        // Vérifier si le paiement a été effectué
                        $paymentCompleted = session()->has('payment_success') || 
                                          session()->has('payment_completed') || 
                                          $vendor->stripe_subscription_id || 
                                          $vendor->subscription_plan === 'free';
                    @endphp

                    @if($paymentCompleted)
                        <!-- Inscription et paiement finalisés -->
                        <div class="timeline-item">
                            <div class="timeline-icon completed">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 
01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">1. Inscription finalisée</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    @if($vendor->subscription_plan === 'free')
                                        Votre compte gratuit est activé.
                                    @else
                                        Votre inscription et paiement ont été traités avec succès.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Création du mot de passe -->
                        <div class="timeline-item">
                            <div class="timeline-icon pending">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 
2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">2. Créer votre mot de passe</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    Consultez votre email et cliquez sur le lien pour créer votre mot de passe 
sécurisé.
                                </p>
                            </div>
                        </div>

                        <!-- Accès à l'espace vendeur -->
                        <div class="timeline-item">
                            <div class="timeline-icon waiting">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L3 
11.414V18a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1v-6.586l1.293 
1.293a1 1 0 001.414-1.414l-9-9z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">3. Accès à votre espace 
vendeur</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    Une fois votre mot de passe créé, vous pourrez immédiatement accéder à 
votre tableau de bord et commencer à créer vos offres.
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Paiement requis (ne devrait pas se produire normalement) -->
                        <div class="timeline-item">
                            <div class="timeline-icon pending">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 
1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">1. Finaliser le paiement</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    Finalisez votre paiement pour activer votre abonnement {{ 
ucfirst($vendor->subscription_plan ?? '') }}.
                                </p>
                                <div class="mt-3">
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-primary text-white 
text-sm font-medium rounded-md hover:bg-primary-dark transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 
002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" 
clip-rule="evenodd"></path>
                                        </svg>
                                        Procéder au paiement
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Étapes suivantes -->
                        <div class="timeline-item">
                            <div class="timeline-icon waiting">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 
2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">2. Créer votre mot de passe</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    Après le paiement, vous recevrez un email pour créer votre mot de passe.
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-icon waiting">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L3 
11.414V18a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1v-6.586l1.293 
1.293a1 1 0 001.414-1.414l-9-9z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-text-primary">3. Accès à votre espace 
vendeur</h3>
                                <p class="text-text-secondary text-sm mt-1">
                                    Une fois toutes les étapes complétées, vous pourrez accéder à votre 
tableau de bord.
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 grid-responsive fade-in-up">
            <!-- Email de confirmation -->
            <div class="info-card bg-white rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-blue-100 text-blue-600 rounded-full flex items-center 
justify-center mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Email envoyé</h3>
                <p class="text-text-secondary text-sm mb-4">Un email avec le lien pour créer votre mot de passe 
a été envoyé.</p>
            </div>

            <!-- Guide de démarrage -->
            <div class="info-card bg-white rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-green-100 text-green-600 rounded-full flex items-center 
justify-center mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 
012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Guide de démarrage</h3>
                <p class="text-text-secondary text-sm mb-4">Découvrez comment optimiser votre présence sur la 
plateforme.</p>
                <a href="#" class="text-primary hover:text-primary-dark font-medium text-sm">
                    Consulter le guide
                </a>
            </div>

            <!-- Support -->
            <div class="info-card bg-white rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-purple-100 text-purple-600 rounded-full flex items-center 
justify-center mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 
17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 
9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Besoin d'aide ?</h3>
                <p class="text-text-secondary text-sm mb-4">Notre équipe support est là pour vous 
accompagner.</p>
                <a href="{{ route('contact') }}" class="text-primary hover:text-primary-dark font-medium 
text-sm">
                    Nous contacter
                </a>
            </div>
        </div>

        <!-- Actions principales -->
        <div class="text-center space-y-4 fade-in-up">
            <div class="space-y-3">
                <p class="text-text-secondary">
                    <strong>Prochaine étape :</strong> Consultez votre email pour créer votre mot de passe
                </p>
            </div>
            
            <div class="pt-4">
                <a href="{{ route('home') }}" class="inline-flex items-center text-text-secondary 
hover:text-primary transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0L2.586 11l3.707-3.707a1 1 0 
011.414 1.414L5.414 11l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        <path fill-rule="evenodd" d="M4.586 11H17a1 1 0 110 2H4.586l2.707 2.707a1 1 0 01-1.414 
1.414L2.172 12.414a1 1 0 010-1.414l3.707-3.707a1 1 0 011.414 1.414L4.586 11z" clip-rule="evenodd"></path>
                    </svg>
                    Retour à l'accueil
                </a>
            </div>
        </div>

        <!-- Information complémentaire -->
        <div class="mt-12 bg-blue-50 rounded-lg p-6 fade-in-up">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 mt-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 
012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Informations importantes</h3>
                    <ul class="text-blue-800 text-sm space-y-1">
                        <li>• Votre numéro de référence vendeur : <strong>{{ isset($vendor) ? 'VND-' . 
str_pad($vendor->id, 6, '0', STR_PAD_LEFT) : 'En cours d\'attribution' }}</strong></li>
                        <li>• Un email de confirmation avec le lien pour créer votre mot de passe vous a 
été envoyé</li>
                        <li>• Vous pourrez modifier vos informations à tout moment depuis votre espace 
vendeur</li>
                        <li>• Votre compte sera actif dès la création de votre mot de passe</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation séquentielle des éléments
    const elements = document.querySelectorAll('.fade-in-up');
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.2}s`;
    });
});
</script>
@endpush
