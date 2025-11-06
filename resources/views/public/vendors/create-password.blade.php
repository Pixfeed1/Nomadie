@extends('layouts.public')

@section('title', 'Créer votre mot de passe')

@section('styles')
<style>
    /* Animation de succès pour la validation */
    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .checkmark {
        animation: checkmark 0.3s ease-in-out;
    }

    /* Styles pour les critères de mot de passe */
    .password-criteria {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    
    .criteria-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.25rem;
        transition: all 0.3s ease;
    }
    
    .criteria-item.valid {
        color: #059669;
    }
    
    .criteria-item.invalid {
        color: #dc2626;
    }
    
    .criteria-icon {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
    
    .criteria-icon.valid {
        background-color: #d1fae5;
        color: #059669;
    }
    
    .criteria-icon.invalid {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Animation pour les champs de saisie */
    .form-input {
        transition: all 0.3s ease;
    }
    
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }
    
    .form-input.valid {
        border-color: #059669;
    }
    
    .form-input.invalid {
        border-color: #dc2626;
    }

    /* Style du bouton avec état de validation */
    .btn-submit {
        transition: all 0.3s ease;
    }
    
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .btn-submit.valid {
        background-color: #059669;
    }
    
    .btn-submit.valid:hover:not(:disabled) {
        background-color: #047857;
    }

    /* Animation de chargement */
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Message d'erreur */
    .error-message {
        background-color: #fee2e2;
        border: 1px solid #dc2626;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
        
        .form-container {
            padding: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="bg-bg-main min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- En-tête -->
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-text-primary">Créez votre mot de passe</h1>
            <p class="mt-2 text-text-secondary">
                Bonjour <strong>{{ $vendor->rep_firstname }} {{ $vendor->rep_lastname }}</strong>, 
                dernière étape pour activer votre compte !
            </p>
        </div>

        <!-- Message d'erreur global -->
        <div id="error-message" class="error-message"></div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-lg p-8 form-container">
            <form method="POST" action="{{ route('vendor.store-password', $token) }}" id="password-form">
                @csrf
                
                <!-- Champ mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-text-primary mb-2">
                        Nouveau mot de passe
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-lg"
                            placeholder="Votre mot de passe sécurisé"
                            required
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-text-primary"
                        >
                            <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Champ confirmation mot de passe -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-2">
                        Confirmer le mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-lg"
                        placeholder="Retapez votre mot de passe"
                        required
                        autocomplete="new-password"
                    >
                    <div id="password-match" class="text-xs mt-1 hidden">
                        <span class="text-success">✓ Les mots de passe correspondent</span>
                    </div>
                    <div id="password-nomatch" class="text-xs mt-1 hidden">
                        <span class="text-error">✗ Les mots de passe ne correspondent pas</span>
                    </div>
                </div>

                <!-- Critères de validation -->
                <div class="mb-6 password-criteria">
                    <p class="text-sm font-medium text-text-primary mb-2">Votre mot de passe doit contenir :</p>
                    <div class="space-y-1">
                        <div class="criteria-item invalid" id="length-criteria">
                            <span class="criteria-icon invalid">✗</span>
                            <span>Au moins 8 caractères</span>
                        </div>
                        <div class="criteria-item invalid" id="lowercase-criteria">
                            <span class="criteria-icon invalid">✗</span>
                            <span>Une lettre minuscule</span>
                        </div>
                        <div class="criteria-item invalid" id="uppercase-criteria">
                            <span class="criteria-icon invalid">✗</span>
                            <span>Une lettre majuscule</span>
                        </div>
                        <div class="criteria-item invalid" id="number-criteria">
                            <span class="criteria-icon invalid">✗</span>
                            <span>Un chiffre</span>
                        </div>
                        <div class="criteria-item invalid" id="special-criteria">
                            <span class="criteria-icon invalid">✗</span>
                            <span>Un caractère spécial (@$!%*?&)</span>
                        </div>
                    </div>
                </div>

                <!-- Bouton de soumission -->
                <button 
                    type="submit" 
                    id="submit-btn"
                    class="btn-submit w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    <span id="btn-text">Créer mon mot de passe</span>
                    <span id="btn-loading" class="loading-spinner ml-2 hidden"></span>
                </button>
            </form>
        </div>

        <!-- Information de sécurité -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Sécurité</h3>
                    <div class="mt-1 text-sm text-blue-700">
                        <p>Votre mot de passe sera chiffré et stocké de manière sécurisée. Une fois créé, vous pourrez vous connecter à votre espace organisateur.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lien de support -->
        <div class="text-center">
            <p class="text-text-secondary text-sm">
                Besoin d'aide ? 
                <a href="{{ route('contact') }}" class="text-primary hover:text-primary-dark font-medium">
                    Contactez notre support
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submit-btn');
    const toggleBtn = document.getElementById('toggle-password');
    const form = document.getElementById('password-form');
    const errorMessage = document.getElementById('error-message');
    
    // Éléments de validation
    const criteria = {
        length: document.getElementById('length-criteria'),
        lowercase: document.getElementById('lowercase-criteria'),
        uppercase: document.getElementById('uppercase-criteria'),
        number: document.getElementById('number-criteria'),
        special: document.getElementById('special-criteria')
    };
    
    const matchDiv = document.getElementById('password-match');
    const nomatchDiv = document.getElementById('password-nomatch');
    
    // État de validation
    let validationState = {
        length: false,
        lowercase: false,
        uppercase: false,
        number: false,
        special: false,
        match: false
    };
    
    // Toggle visibilité mot de passe
    toggleBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        confirmInput.setAttribute('type', type);
        
        document.getElementById('eye-open').classList.toggle('hidden');
        document.getElementById('eye-closed').classList.toggle('hidden');
    });
    
    // Validation en temps réel du mot de passe
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Vérifier chaque critère
        validationState.length = password.length >= 8;
        validationState.lowercase = /[a-z]/.test(password);
        validationState.uppercase = /[A-Z]/.test(password);
        validationState.number = /\d/.test(password);
        validationState.special = /[@$!%*?&]/.test(password);
        
        // Mettre à jour l'affichage
        updateCriteriaDisplay('length', validationState.length);
        updateCriteriaDisplay('lowercase', validationState.lowercase);
        updateCriteriaDisplay('uppercase', validationState.uppercase);
        updateCriteriaDisplay('number', validationState.number);
        updateCriteriaDisplay('special', validationState.special);
        
        // Vérifier la confirmation si elle existe
        if (confirmInput.value) {
            checkPasswordMatch();
        }
        
        updateSubmitButton();
    });
    
    // Vérification confirmation mot de passe
    confirmInput.addEventListener('input', checkPasswordMatch);
    
    function updateCriteriaDisplay(name, isValid) {
        const element = criteria[name];
        const icon = element.querySelector('.criteria-icon');
        
        if (isValid) {
            element.classList.remove('invalid');
            element.classList.add('valid');
            icon.classList.remove('invalid');
            icon.classList.add('valid', 'checkmark');
            icon.textContent = '✓';
        } else {
            element.classList.remove('valid');
            element.classList.add('invalid');
            icon.classList.remove('valid', 'checkmark');
            icon.classList.add('invalid');
            icon.textContent = '✗';
        }
    }
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        if (confirm === '') {
            matchDiv.classList.add('hidden');
            nomatchDiv.classList.add('hidden');
            validationState.match = false;
            confirmInput.classList.remove('valid', 'invalid');
            return;
        }
        
        validationState.match = password === confirm;
        
        if (validationState.match) {
            matchDiv.classList.remove('hidden');
            nomatchDiv.classList.add('hidden');
            confirmInput.classList.remove('invalid');
            confirmInput.classList.add('valid');
        } else {
            matchDiv.classList.add('hidden');
            nomatchDiv.classList.remove('hidden');
            confirmInput.classList.remove('valid');
            confirmInput.classList.add('invalid');
        }
        
        updateSubmitButton();
    }
    
    function updateSubmitButton() {
        const allValid = Object.values(validationState).every(v => v === true);
        
        submitBtn.disabled = !allValid;
        
        if (allValid) {
            submitBtn.classList.add('valid');
            passwordInput.classList.add('valid');
            passwordInput.classList.remove('invalid');
        } else {
            submitBtn.classList.remove('valid');
            if (passwordInput.value) {
                passwordInput.classList.add('invalid');
            }
            passwordInput.classList.remove('valid');
        }
    }
    
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.add('show');
        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    function hideError() {
        errorMessage.classList.remove('show');
    }
    
    // Gestion de soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        hideError();
        
        // Vérification finale
        if (!Object.values(validationState).every(v => v === true)) {
            showError('Veuillez respecter tous les critères du mot de passe.');
            return;
        }
        
        // Désactiver le bouton et afficher le loader
        submitBtn.disabled = true;
        document.getElementById('btn-text').textContent = 'Création en cours...';
        document.getElementById('btn-loading').classList.remove('hidden');
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Succès - rediriger
                window.location.href = data.redirect;
            } else {
                // Erreur
                showError(data.message || 'Une erreur est survenue lors de la création du mot de passe.');
                
                // Réactiver le bouton
                submitBtn.disabled = false;
                document.getElementById('btn-text').textContent = 'Créer mon mot de passe';
                document.getElementById('btn-loading').classList.add('hidden');
                updateSubmitButton();
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            showError('Une erreur de connexion est survenue. Veuillez réessayer.');
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            document.getElementById('btn-text').textContent = 'Créer mon mot de passe';
            document.getElementById('btn-loading').classList.add('hidden');
            updateSubmitButton();
        }
    });
    
    // Focus sur le premier champ
    passwordInput.focus();
});
</script>
@endpush