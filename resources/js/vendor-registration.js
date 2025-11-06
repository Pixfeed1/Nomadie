// Fichier: resources/js/vendor-registration.js - VERSION COMPLÈTE MISE À JOUR

// Importer Alpine correctement pour Vite
import Alpine from 'alpinejs';

// Rendre Alpine disponible globalement
window.Alpine = Alpine;

// Fonction de vérification d'email améliorée - N'affiche que les erreurs
async function checkEmailAvailability(email, field) {
    try {
        const response = await fetch('/api/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                email: email,
                field: field,
                check_only_active: true
            })
        });

        const data = await response.json();
        
        // Sélectionner le bon champ d'input
        const inputField = document.getElementById(field);
        const errorContainer = inputField.closest('.mb-6') || inputField.closest('div');
        
        // Supprimer les messages existants
        const existingError = errorContainer.querySelector('.email-availability-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Si l'email est disponible, on ne montre RIEN
        if (data.available) {
            inputField.classList.remove('border-error');
            inputField.classList.remove('border-success');
            return true;
        } else {
            // Si l'email est déjà pris, on montre l'erreur
            const messageDiv = document.createElement('div');
            messageDiv.className = 'email-availability-message text-sm mt-1';
            messageDiv.innerHTML = '<span class="text-error">✗ ' + (data.message || 'Cet email est déjà utilisé') + '</span>';
            
            inputField.classList.add('border-error');
            inputField.classList.remove('border-success');
            
            // Ajouter le message après le champ
            inputField.parentElement.appendChild(messageDiv);
            
            return false;
        }
        
    } catch (error) {
        console.error('Erreur lors de la vérification email:', error);
        return true; // En cas d'erreur, on laisse passer
    }
}

// Déclarer notre composant principal
document.addEventListener('alpine:init', () => {
    Alpine.data('vendorRegistration', () => ({
        // État centralisé
        activeStep: 1,
        subscription: 'essential',
        formData: {},
        isSubmitting: false,
        logoPreview: null,
        searchText: '',
        
        // 5 étapes totales
        totalSteps: 5,
        
        // Suivi des étapes complétées
        completedSteps: [false, false, false, false, false],
        
        // Limites selon l'abonnement - MODIFIÉ pour les séjours
        subscriptionLimits: {
            'free': { sejours: 5, name: 'Gratuit' },
            'essential': { sejours: 50, name: 'Essentiel' },
            'pro': { sejours: 9999, name: 'Pro' }
        },
        
        init() {
            console.log('🚀 Initialisation formulaire multi-étapes pour devenir organisateur de séjours');
            
            // Détecter l'étape appropriée selon les erreurs Laravel
            const targetStep = this.detectErrorStep();
            this.activeStep = targetStep;
            
            // Persistance d'étape avec localStorage
            const savedStep = Number(localStorage.getItem('activeStep'));
            if (savedStep && savedStep > this.activeStep) {
                this.activeStep = savedStep;
            }
            
            console.log(`✅ Initialisation à l'étape ${this.activeStep}`);
            
            // Configuration initiale
            this.$nextTick(() => {
                this.setupComponents();
                this.initializeCounters();
                this.setupEmailValidation();
            });
            
            // Observer les changements d'étape
            this.$watch('activeStep', (newStep) => {
                localStorage.setItem('activeStep', newStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
                this.$nextTick(() => {
                    this.updateAllCounters();
                });
            });
            
            // Observer les changements dans la recherche
            this.$watch('searchText', (value) => {
                this.filterDestinations();
            });
            
            // Observer les changements d'abonnement
            this.$watch('subscription', (value) => {
                this.updateSubscriptionInput();
            });
        },
        
        // Configuration de la vérification email en temps réel
        setupEmailValidation() {
            // Pour l'email de l'entreprise
            const emailInput = document.getElementById('email');
            if (emailInput) {
                let emailTimeout;
                emailInput.addEventListener('input', function() {
                    clearTimeout(emailTimeout);
                    const email = this.value.trim();
                    
                    if (email && email.includes('@')) {
                        emailTimeout = setTimeout(() => {
                            checkEmailAvailability(email, 'email');
                        }, 500); // Délai de 500ms pour éviter trop de requêtes
                    }
                });
            }
            
            // Pour l'email du représentant
            const repEmailInput = document.getElementById('rep_email');
            if (repEmailInput) {
                let repEmailTimeout;
                repEmailInput.addEventListener('input', function() {
                    clearTimeout(repEmailTimeout);
                    const email = this.value.trim();
                    
                    if (email && email.includes('@')) {
                        repEmailTimeout = setTimeout(() => {
                            checkEmailAvailability(email, 'rep_email');
                        }, 500);
                    }
                });
            }
        },
        
        // Initialiser tous les compteurs
        initializeCounters() {
            this.updateDestinationCounters();
            this.updateContinentCounters();
            this.updateFinalCounters();
            this.updateServiceCategoryCounters();
        },
        
        // Mettre à jour tous les compteurs
        updateAllCounters() {
            this.updateDestinationCounters();
            this.updateContinentCounters();
            this.updateFinalCounters();
            this.updateServiceCategoryCounters();
        },
        
        // Mettre à jour l'input caché subscription
        updateSubscriptionInput() {
            const subscriptionInput = document.querySelector('input[name="subscription"]');
            if (subscriptionInput) {
                subscriptionInput.value = this.subscription;
                console.log('📝 Input subscription mis à jour:', this.subscription);
            }
        },
        
        // Détecter l'étape appropriée selon les erreurs
        detectErrorStep() {
            const step1Errors = document.querySelectorAll('[data-field="company_name"], [data-field="email"], [data-field="siret"]');
            const step2Errors = document.querySelectorAll('[data-field="subscription"]');
            const step3Errors = document.querySelectorAll('[data-field="destinations"]');
            const step4Errors = document.querySelectorAll('[data-field="service_categories"]');
            const step5Errors = document.querySelectorAll('[data-field="terms"]');
            const paymentErrors = document.querySelector('.payment-error, .stripe-error');
            
            if (paymentErrors) return 5;
            if (step5Errors.length > 0) return 5;
            if (step4Errors.length > 0) return 4;
            if (step3Errors.length > 0) return 3;
            if (step2Errors.length > 0) return 2;
            if (step1Errors.length > 0) return 1;
            
            return 1;
        },
        
        // Configurer tous les composants
        setupComponents() {
            this.bindLogo();
            this.makeDestinationsClickable();
            this.setupFormForAjax();
            this.setupAccordions();
            this.setupServiceCategoryLimit();
            this.setupFieldValidation();
            this.setupSubscriptionButtons();
            this.setupGlobalEventListeners();
        },
        
        // Événements globaux pour surveiller les changements
        setupGlobalEventListeners() {
            document.addEventListener('change', (e) => {
                if (e.target.matches('input[name="destinations[]"]')) {
                    this.updateAllCounters();
                }
                if (e.target.matches('input[name="service_categories[]"]') || e.target.matches('input[name="service_attributes[]"]')) {
                    this.updateServiceCategoryCounters();
                    this.updateFinalCounters();
                }
            });
        },
        
        // Navigation avec AJAX pour toutes les étapes
        async nextStep() {
            console.log(`🔄 Navigation depuis l'étape ${this.activeStep}`);
            
            if (this.isSubmitting) {
                console.log('⏳ Soumission déjà en cours, ignorer');
                return;
            }
            
            if (!await this.validateCurrentStep()) {
                console.log('❌ Validation échouée pour l\'étape', this.activeStep);
                return;
            }
            
            this.isSubmitting = true;
            
            try {
                if (this.activeStep === 5) {
                    // Étape finale - Finalisation de l'inscription
                    console.log('🏁 Étape finale - Finalisation de l\'inscription comme organisateur de séjours');
                    
                    // Vérifier que les CGV sont acceptées
                    const termsInput = document.getElementById('terms');
                    if (!termsInput || !termsInput.checked) {
                        this.showAlert('Vous devez accepter les conditions générales.', 'error');
                        this.isSubmitting = false;
                        return;
                    }
                    
                    // Collecter les données de l'étape 5
                    const stepData = this.collectCurrentStepData();
                    
                    // D'abord sauvegarder l'étape 5
                    await this.saveStepToServer();
                    
                    // Ensuite, gérer selon le type d'abonnement
                    if (this.subscription === 'free') {
                        // Abonnement gratuit : finaliser directement
                        const response = await fetch('/devenir-organisateur/finaliser', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                token: this.getOrCreateToken(),
                                ...stepData
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showAlert('Inscription réussie ! Vous pouvez maintenant créer vos séjours.', 'success');
                            this.cleanupLocalData();
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Erreur lors de la finalisation');
                        }
                    } else {
                        // Abonnement payant : rediriger vers le paiement SANS appeler finalSubmit
                        const response = await fetch('/vendor/payment/initiate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                token: this.getOrCreateToken()
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.sessionUrl) {
                            this.showAlert('Redirection vers le paiement sécurisé...', 'success');
                            
                            // NE PAS nettoyer les données locales ici !
                            // Elles seront nettoyées après le retour de Stripe
                            
                            setTimeout(() => {
                                window.location.href = data.sessionUrl;
                            }, 1000);
                        } else {
                            throw new Error(data.error || 'Erreur lors de la création de la session de paiement');
                        }
                    }
                    
                    return; // Important : sortir de la fonction ici
                } else {
                    // Étapes intermédiaires : sauvegarde AJAX
                    await this.saveStepToServer();
                    
                    // Marquer comme complétée et avancer
                    this.completedSteps[this.activeStep - 1] = true;
                    this.collectFormData();
                    this.goToStep(this.activeStep + 1);
                }
            } catch (error) {
                console.error('❌ Erreur dans nextStep:', error);
                this.showAlert(error.message || 'Une erreur est survenue. Veuillez réessayer.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        
        // Sauvegarder les données sur le serveur (étapes 1-4)
        async saveStepToServer() {
            try {
                const stepData = this.collectCurrentStepData();
                
                console.log('📤 Sauvegarde étape:', this.activeStep, stepData);
                
                const response = await fetch('/devenir-organisateur/etape', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        step: this.activeStep,
                        token: this.getOrCreateToken(),
                        ...stepData
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('❌ Erreur sauvegarde:', errorData);
                    
                    if (errorData.errors) {
                        const errorMessages = Object.values(errorData.errors).flat();
                        this.showAlert(errorMessages.join(', '), 'error');
                    } else {
                        this.showAlert(errorData.message || 'Erreur de validation.', 'error');
                    }
                    
                    throw new Error('Validation failed');
                }
                
                const result = await response.json();
                console.log('✅ Étape sauvegardée:', result);
                
                // Stocker le token retourné
                if (result.token) {
                    this.storeToken(result.token);
                }
                
                return true;
                
            } catch (error) {
                console.error('❌ Erreur sauvegarde:', error);
                
                if (!error.message.includes('Validation failed')) {
                    this.showAlert('Erreur lors de la sauvegarde. Veuillez réessayer.', 'error');
                }
                
                throw error;
            }
        },
        
        // Collecter les données de l'étape actuelle
        collectCurrentStepData() {
            const stepElement = document.querySelector(`[id="step-${this.activeStep}-content"]`);
            const stepData = {};
            
            if (!stepElement) return stepData;
            
            const inputs = stepElement.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.name && input.name !== '_token') {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.checked) {
                            if (input.name.endsWith('[]')) {
                                const name = input.name.slice(0, -2);
                                if (!stepData[name]) {
                                    stepData[name] = [];
                                }
                                stepData[name].push(input.value);
                            } else {
                                stepData[input.name] = input.value;
                            }
                        }
                    } else if (input.type !== 'file') {
                        // Nettoyer le SIRET
                        if (input.name === 'siret') {
                            stepData[input.name] = input.value.replace(/\s/g, '');
                        } else {
                            stepData[input.name] = input.value;
                        }
                    }
                }
            });
            
            // Ajouter l'abonnement pour l'étape 2
            if (this.activeStep === 2) {
                stepData.subscription = this.subscription;
            }
            
            return stepData;
        },
        
        // Obtenir ou créer un token
        getOrCreateToken() {
            let token = localStorage.getItem('vendor_token');
            if (!token) {
                token = 'token_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('vendor_token', token);
            }
            return token;
        },
        
        // Stocker le token
        storeToken(token) {
            localStorage.setItem('vendor_token', token);
            
            // Mettre à jour l'input caché aussi
            let tokenInput = document.querySelector('input[name="token"]');
            if (!tokenInput) {
                tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = 'token';
                const form = document.querySelector('form');
                if (form) form.appendChild(tokenInput);
            }
            tokenInput.value = token;
        },
        
        // Nettoyer les données locales
        cleanupLocalData() {
            localStorage.removeItem('vendor_token');
            localStorage.removeItem('activeStep');
            this.formData = {};
            console.log('🧹 Données locales nettoyées');
        },
        
        // Retour à l'étape précédente
        prevStep() {
            console.log(`⬅️ Retour depuis l'étape ${this.activeStep}`);
            
            if (this.activeStep > 1) {
                this.goToStep(this.activeStep - 1);
            }
        },
        
        // Navigation sécurisée entre les étapes
        goToStep(step) {
            console.log(`🎯 Navigation vers l'étape ${step}`);
            
            if (step < 1 || step > this.totalSteps) {
                console.warn(`❌ Étape ${step} hors limites (1-${this.totalSteps})`);
                return;
            }
            
            // Si on va en arrière, ne pas valider
            if (step < this.activeStep) {
                this.activeStep = step;
                console.log(`✅ Navigation arrière vers l'étape ${step}`);
                return;
            }
            
            // Si on tente d'avancer, vérifier la validation
            if (step > this.activeStep) {
                if (!this.validateCurrentStep()) {
                    console.warn(`❌ Validation de l'étape ${this.activeStep} échouée`);
                    return;
                }
                
                // Marquer l'étape actuelle comme complétée
                this.completedSteps[this.activeStep - 1] = true;
                this.collectFormData();
            }
            
            this.activeStep = step;
            console.log(`✅ Navigation réussie vers l'étape ${step}`);
        },
        
        // Configuration pour AJAX uniquement
        setupFormForAjax() {
            const form = document.querySelector('form');
            if (form) {
                // Empêcher la soumission classique du formulaire
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    console.log('🚫 Soumission classique empêchée - utilisation AJAX uniquement');
                    return false;
                });
                
                // S'assurer que l'input subscription existe
                let subscriptionInput = form.querySelector('input[name="subscription"]');
                if (!subscriptionInput) {
                    subscriptionInput = document.createElement('input');
                    subscriptionInput.type = 'hidden';
                    subscriptionInput.name = 'subscription';
                    form.appendChild(subscriptionInput);
                }
                
                // Mettre à jour la valeur initiale
                subscriptionInput.value = this.subscription;
                
                this.$watch('subscription', value => {
                    subscriptionInput.value = value;
                    console.log('📝 Abonnement mis à jour:', value);
                });
                
                console.log('✅ Formulaire configuré pour AJAX uniquement');
            }
        },
        
        // Validation de l'étape courante avec vérification email
        async validateCurrentStep() {
            console.log(`🔍 Validation de l'étape ${this.activeStep}`);
            
            // Réinitialiser les messages d'erreur
            document.querySelectorAll('.border-error').forEach(el => el.classList.remove('border-error'));
            document.querySelectorAll('.js-error-message').forEach(el => el.remove());
            
            let valid = false;
            
            switch (this.activeStep) {
                case 1:
                    valid = await this.validateStep1();
                    break;
                case 2:
                    valid = this.validateStep2();
                    break;
                case 3:
                    valid = this.validateStep3();
                    break;
                case 4:
                    valid = this.validateStep4();
                    break;
                case 5:
                    valid = this.validateStep5();
                    break;
                default:
                    valid = true;
            }
            
            console.log(`${valid ? '✅' : '❌'} Validation étape ${this.activeStep}:`, valid);
            return valid;
        },
        
        // Validation étape 1 : Informations générales avec vérification email
        async validateStep1() {
            const requiredFields = [
                'company_name', 'legal_status', 'siret', 'email', 
                'phone', 'address', 'postal_code', 'city', 'country',
                'rep_firstname', 'rep_lastname', 'rep_position', 'rep_email',
                'description', 'experience'
            ];
            
            let valid = true;
            
            for (const field of requiredFields) {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    if (input) {
                        input.classList.add('border-error');
                        this.addErrorMessage(input, 'Ce champ est obligatoire');
                    }
                    valid = false;
                }
            }
            
            // Validation email entreprise
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value) {
                if (!this.isValidEmail(emailInput.value)) {
                    emailInput.classList.add('border-error');
                    this.addErrorMessage(emailInput, 'Adresse email invalide');
                    valid = false;
                } else {
                    // Vérifier la disponibilité
                    const emailAvailable = await checkEmailAvailability(emailInput.value, 'email');
                    if (!emailAvailable) {
                        valid = false;
                    }
                }
            }
            
            // Validation email représentant
            const repEmailInput = document.getElementById('rep_email');
            if (repEmailInput && repEmailInput.value) {
                if (!this.isValidEmail(repEmailInput.value)) {
                    repEmailInput.classList.add('border-error');
                    this.addErrorMessage(repEmailInput, 'Adresse email invalide');
                    valid = false;
                } else {
                    // Vérifier la disponibilité
                    const repEmailAvailable = await checkEmailAvailability(repEmailInput.value, 'rep_email');
                    if (!repEmailAvailable) {
                        valid = false;
                    }
                }
            }
            
            // Validation site web
            const websiteInput = document.getElementById('website');
            if (websiteInput && websiteInput.value && !this.isValidUrl(websiteInput.value)) {
                websiteInput.classList.add('border-error');
                this.addErrorMessage(websiteInput, 'URL invalide');
                valid = false;
            }
            
            // Validation SIRET
            const siretInput = document.getElementById('siret');
            if (siretInput && siretInput.value) {
                const siretClean = siretInput.value.replace(/\s/g, '');
                if (siretClean.length !== 14 || !/^\d{14}$/.test(siretClean)) {
                    siretInput.classList.add('border-error');
                    this.addErrorMessage(siretInput, 'Le SIRET doit contenir exactement 14 chiffres');
                    valid = false;
                }
            }
            
            if (!valid) {
                this.showAlert('Veuillez corriger les erreurs dans le formulaire.', 'error');
            }
            
            return valid;
        },
        
        // Validation étape 2 : Choix d'abonnement
        validateStep2() {
            if (!this.subscription) {
                this.showAlert('Veuillez choisir un abonnement.', 'error');
                return false;
            }
            console.log('✅ Abonnement validé:', this.subscription);
            return true;
        },
        
        // Validation étape 3 : Destinations (SIMPLIFIÉ - plus de limite)
        validateStep3() {
            const destinationChecks = document.querySelectorAll('input[name="destinations[]"]:checked');
            
            if (destinationChecks.length === 0) {
                this.showAlert('Veuillez sélectionner au moins une destination où vous proposez des séjours.', 'error');
                return false;
            }
            
            console.log(`✅ ${destinationChecks.length} destinations sélectionnées pour vos séjours`);
            return true;
        },
        
        // Validation étape 4 : Services
        validateStep4() {
            const serviceCategoryChecks = document.querySelectorAll('input[name="service_categories[]"]:checked');
            
            if (serviceCategoryChecks.length === 0) {
                this.showAlert('Veuillez sélectionner au moins un type de séjour.', 'error');
                return false;
            }
            
            if (serviceCategoryChecks.length > 3) {
                this.showAlert('Vous ne pouvez sélectionner que 3 types de séjours maximum.', 'error');
                return false;
            }
            
            console.log(`✅ ${serviceCategoryChecks.length} types de séjours sélectionnés`);
            return true;
        },
        
        // Validation étape 5
        validateStep5() {
            console.log('🔍 Validation étape 5');
            
            const termsCheck = document.getElementById('terms');
            
            if (!termsCheck || !termsCheck.checked) {
                console.log('❌ Validation échouée: conditions non acceptées');
                this.showAlert('Veuillez accepter les conditions générales.', 'error');
                return false;
            }
            
            console.log('✅ Étape 5 validée');
            return true;
        },
        
        // Collecte des données du formulaire
        collectFormData() {
            console.log(`📝 Collecte des données pour l'étape ${this.activeStep}`);
            
            const visibleStep = document.querySelector(`[id="step-${this.activeStep}-content"]`);
            if (!visibleStep) return;
            
            const inputs = visibleStep.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.name) {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.checked) {
                            if (input.name.endsWith('[]')) {
                                const name = input.name.slice(0, -2);
                                if (!this.formData[name]) {
                                    this.formData[name] = [];
                                }
                                if (!this.formData[name].includes(input.value)) {
                                    this.formData[name].push(input.value);
                                }
                            } else {
                                this.formData[input.name] = input.value;
                            }
                        }
                    } else if (input.type !== 'file') {
                        if (input.name === 'siret') {
                            this.formData[input.name] = input.value.replace(/\s/g, '');
                        } else {
                            this.formData[input.name] = input.value;
                        }
                    }
                }
            });
            
            // S'assurer que l'abonnement est toujours dans les données
            this.formData.subscription = this.subscription;
        },
        
        // Méthodes utilitaires pour la gestion des étapes
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        isStepAccessible(step) {
            if (step === 1) return true;
            return step <= this.activeStep || this.completedSteps[step - 2];
        },
        
        handleValidationErrors(errors) {
            console.log('🔍 Gestion des erreurs de validation:', errors);
            
            // Effacer les erreurs précédentes
            document.querySelectorAll('.text-error[data-field]').forEach(el => el.remove());
            document.querySelectorAll('.border-error').forEach(el => el.classList.remove('border-error'));
            
            // Afficher les nouvelles erreurs
            if (errors && typeof errors === 'object') {
                for (const [field, messages] of Object.entries(errors)) {
                    const input = document.getElementById(field);
                    if (input && Array.isArray(messages) && messages.length > 0) {
                        input.classList.add('border-error');
                        
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'text-xs text-error mt-1';
                        errorDiv.setAttribute('data-field', field);
                        errorDiv.textContent = messages[0];
                        
                        // Insérer l'erreur après le champ
                        input.parentElement.appendChild(errorDiv);
                        
                        // Faire défiler vers le premier champ en erreur
                        if (input.getBoundingClientRect().top < window.pageYOffset) {
                            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }
            }
        },
        
        // Mise à jour des compteurs finaux
        updateFinalCounters() {
            const destinationsCount = document.querySelectorAll('input[name="destinations[]"]:checked').length;
            const finalDestinationsCounter = document.getElementById('final-destinations-count');
            if (finalDestinationsCounter) {
                finalDestinationsCounter.textContent = destinationsCount;
            }
            
            const categoriesCount = document.querySelectorAll('input[name="service_categories[]"]:checked').length;
            const finalCategoriesCounter = document.getElementById('final-categories-count');
            if (finalCategoriesCounter) {
                finalCategoriesCounter.textContent = categoriesCount;
            }
        },
        
        // Mise à jour compteur catégories de services
        updateServiceCategoryCounters() {
            const categoriesCount = document.querySelectorAll('input[name="service_categories[]"]:checked').length;
            const attributesCount = document.querySelectorAll('input[name="service_attributes[]"]:checked').length;
            
            console.log('📊 Types de séjours:', categoriesCount, 'Attributs:', attributesCount);
        },
        
        // Configuration des boutons d'abonnement
        setupSubscriptionButtons() {
            this.$nextTick(() => {
                const subscriptionCards = document.querySelectorAll('.subscription-plan');
                subscriptionCards.forEach(card => {
                    if (!card.dataset.bound) {
                        card.dataset.bound = 'true';
                        card.addEventListener('click', () => {
                            const planType = card.dataset.plan || 'essential';
                            this.subscription = planType;
                            
                            // Mettre à jour visuellement
                            subscriptionCards.forEach(c => c.classList.remove('selected'));
                            card.classList.add('selected');
                            
                            this.updateSubscriptionInput();
                            
                            console.log('📋 Abonnement sélectionné:', planType);
                        });
                    }
                });
                
                // Sélectionner visuellement l'abonnement initial
                const initialCard = document.querySelector(`[data-plan="${this.subscription}"]`);
                if (initialCard) {
                    initialCard.classList.add('selected');
                }
            });
        },
        
        // Configuration des accordéons de continent
        setupAccordions() {
            this.$nextTick(() => {
                const accordions = document.querySelectorAll('.continent-accordion');
                accordions.forEach(accordion => {
                    const content = accordion.querySelector('.accordion-content');
                    if (content) {
                        content.classList.add('hidden');
                        accordion.classList.remove('accordion-open');
                    }
                    
                    if (!accordion.dataset.initialized) {
                        accordion.dataset.initialized = 'true';
                        
                        const header = accordion.querySelector('.accordion-header');
                        if (header) {
                            header.addEventListener('click', (e) => {
                                if (e.target.tagName !== 'BUTTON') {
                                    this.toggleAccordion(accordion);
                                }
                            });
                        }
                        
                        // Boutons continent
                        const selectBtn = accordion.querySelector('.select-continent-btn');
                        const deselectBtn = accordion.querySelector('.deselect-continent-btn');
                        
                        if (selectBtn) {
                            selectBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                this.selectAllInContinent(accordion.dataset.continentId);
                            });
                        }
                        
                        if (deselectBtn) {
                            deselectBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                this.deselectAllInContinent(accordion.dataset.continentId);
                            });
                        }
                    }
                });
                
                this.updateContinentCounters();
            });
        },
        
        // Basculer l'état ouvert/fermé d'un accordéon
        toggleAccordion(accordion) {
            const content = accordion.querySelector('.accordion-content');
            const isOpen = !content.classList.contains('hidden');
            
            if (isOpen) {
                content.classList.add('hidden');
                accordion.classList.remove('accordion-open');
            } else {
                if (this.searchText.trim() === '') {
                    document.querySelectorAll('.continent-accordion').forEach(other => {
                        if (other !== accordion) {
                            const otherContent = other.querySelector('.accordion-content');
                            if (otherContent) {
                                otherContent.classList.add('hidden');
                                other.classList.remove('accordion-open');
                            }
                        }
                    });
                }
                
                content.classList.remove('hidden');
                accordion.classList.add('accordion-open');
            }
        },
        
        // Sélectionner toutes les destinations d'un continent (SIMPLIFIÉ)
        selectAllInContinent(continentId) {
            const destinations = document.querySelectorAll(`[data-continent-id="${continentId}"] [data-destination-id]`);
            destinations.forEach(item => {
                const id = item.getAttribute('data-destination-id');
                const checkbox = document.querySelector(`input[name="destinations[]"][value="${id}"]`);
                
                if (checkbox) {
                    checkbox.checked = true;
                    item.classList.add('destination-selected');
                }
            });
            
            this.updateAllCounters();
        },
        
        // Désélectionner toutes les destinations d'un continent
        deselectAllInContinent(continentId) {
            const destinations = document.querySelectorAll(`[data-continent-id="${continentId}"] [data-destination-id]`);
            destinations.forEach(item => {
                const id = item.getAttribute('data-destination-id');
                const checkbox = document.querySelector(`input[name="destinations[]"][value="${id}"]`);
                
                if (checkbox) {
                    checkbox.checked = false;
                    item.classList.remove('destination-selected');
                }
            });
            
            this.updateAllCounters();
        },
        
        // Mise à jour des compteurs de destinations
        updateDestinationCounters() {
            const selectedCount = document.querySelectorAll('input[name="destinations[]"]:checked').length;
            
            const counter = document.getElementById('selected-destinations-counter');
            if (counter) {
                counter.textContent = selectedCount;
            }
        },
        
        // Mise à jour des compteurs par continent
        updateContinentCounters() {
            document.querySelectorAll('.continent-accordion').forEach(accordion => {
                const continentId = accordion.getAttribute('data-continent-id');
                const counter = accordion.querySelector('.continent-counter');
                
                if (counter) {
                    const checkedBoxes = document.querySelectorAll(`[data-continent-id="${continentId}"] input[type="checkbox"]:checked`);
                    counter.textContent = checkedBoxes.length;
                    
                    const header = accordion.querySelector('.accordion-header');
                    if (header) {
                        if (checkedBoxes.length > 0) {
                            header.classList.add('has-selections');
                        } else {
                            header.classList.remove('has-selections');
                        }
                    }
                }
            });
        },
        
        // Filtrer les destinations selon la recherche
        filterDestinations() {
            const searchText = this.searchText.toLowerCase();
            
            document.querySelectorAll('[data-destination-id]').forEach(dest => {
                const destName = dest.getAttribute('data-destination-name').toLowerCase();
                
                if (searchText === '' || destName.includes(searchText)) {
                    dest.style.display = 'block';
                } else {
                    dest.style.display = 'none';
                }
            });
            
            if (searchText.trim() !== '') {
                this.expandRelevantAccordions();
            } else {
                document.querySelectorAll('.continent-accordion').forEach(accordion => {
                    const content = accordion.querySelector('.accordion-content');
                    if (content) {
                        content.classList.add('hidden');
                        accordion.classList.remove('accordion-open');
                    }
                });
            }
            
            this.updateVisibleCounters();
        },
        
        // Ouvrir les accordéons contenant des résultats de recherche
        expandRelevantAccordions() {
            const searchText = this.searchText.toLowerCase();
            const continentsWithMatches = new Set();
            
            document.querySelectorAll('[data-destination-id]').forEach(dest => {
                const destName = dest.getAttribute('data-destination-name').toLowerCase();
                const continentId = dest.getAttribute('data-continent-id');
                
                if (destName.includes(searchText) && continentId) {
                    continentsWithMatches.add(continentId);
                }
            });
            
            document.querySelectorAll('.continent-accordion').forEach(accordion => {
                const continentId = accordion.getAttribute('data-continent-id');
                const content = accordion.querySelector('.accordion-content');
                
                if (continentsWithMatches.has(continentId)) {
                    content.classList.remove('hidden');
                    accordion.classList.add('accordion-open');
                } else {
                    content.classList.add('hidden');
                    accordion.classList.remove('accordion-open');
                }
            });
        },
        
        // Mettre à jour les compteurs de destinations visibles
        updateVisibleCounters() {
            document.querySelectorAll('.continent-accordion').forEach(accordion => {
                const continentId = accordion.getAttribute('data-continent-id');
                const visibleCounter = accordion.querySelector('.visible-counter');
                
                if (visibleCounter) {
                    const visibleDestinations = Array.from(document.querySelectorAll(`[data-continent-id="${continentId}"][data-destination-id]`))
                        .filter(dest => dest.style.display !== 'none');
                    
                    visibleCounter.textContent = visibleDestinations.length;
                    
                    if (this.searchText.trim() !== '' && visibleDestinations.length === 0) {
                        accordion.style.display = 'none';
                    } else {
                        accordion.style.display = 'block';
                    }
                }
            });
        },
        
        // Configuration de la limitation du nombre de catégories de services
        setupServiceCategoryLimit() {
            this.$nextTick(() => {
                const maxCategories = 3;
                const checkboxes = document.querySelectorAll('.service-category-checkbox');
                const warningElement = document.getElementById('category-limit-warning');
                
                if (checkboxes.length === 0) return;
                
                checkboxes.forEach(checkbox => {
                    if (!checkbox.dataset.bound) {
                        checkbox.dataset.bound = 'true';
                        checkbox.addEventListener('change', () => {
                            const checkedCount = document.querySelectorAll('.service-category-checkbox:checked').length;
                            
                            if (checkedCount >= maxCategories) {
                                checkboxes.forEach(cb => {
                                    if (!cb.checked) {
                                        cb.disabled = true;
                                    }
                                });
                                if (warningElement) warningElement.classList.remove('hidden');
                            } else {
                                checkboxes.forEach(cb => {
                                    cb.disabled = false;
                                });
                                if (warningElement) warningElement.classList.add('hidden');
                            }
                            
                            const categoryItem = checkbox.closest('.service-category-item');
                            if (categoryItem) {
                                if (checkbox.checked) {
                                    categoryItem.classList.add('bg-primary/5', 'border-primary');
                                } else {
                                    categoryItem.classList.remove('bg-primary/5', 'border-primary');
                                }
                            }
                            
                            this.updateAllCounters();
                        });
                    }
                });
                
                const categoryItems = document.querySelectorAll('.service-category-item');
                categoryItems.forEach(item => {
                    if (!item.dataset.bound) {
                        item.dataset.bound = 'true';
                        item.addEventListener('click', (e) => {
                            if (e.target.type === 'checkbox') return;
                            
                            const checkbox = item.querySelector('input[type="checkbox"]');
                            if (checkbox && !checkbox.disabled) {
                                checkbox.checked = !checkbox.checked;
                                checkbox.dispatchEvent(new Event('change'));
                            }
                        });
                    }
                });
            });
        },
        
        // Rendre les destinations cliquables (SIMPLIFIÉ)
        makeDestinationsClickable() {
            this.$nextTick(() => {
                const destinationContainers = document.querySelectorAll('[data-destination-id]');
                
                destinationContainers.forEach(container => {
                    if (container.dataset.bound === 'true') return;
                    container.dataset.bound = 'true';
                    
                    const destinationId = container.getAttribute('data-destination-id');
                    const checkbox = document.querySelector(`input[name="destinations[]"][value="${destinationId}"]`);
                    
                    if (!checkbox) {
                        console.error(`❌ Checkbox non trouvée pour destination ${destinationId}`);
                        return;
                    }
                    
                    // État initial
                    if (checkbox.checked) {
                        container.classList.add('destination-selected');
                    }
                    
                    // Clic sur le conteneur (SIMPLIFIÉ - plus de vérification de limite)
                    container.addEventListener('click', (e) => {
                        if (e.target === checkbox) return;
                        
                        checkbox.checked = !checkbox.checked;
                        
                        if (checkbox.checked) {
                            container.classList.add('destination-selected');
                        } else {
                            container.classList.remove('destination-selected');
                        }
                        
                        this.updateAllCounters();
                    });
                    
                    // Changement direct de la checkbox
                    checkbox.addEventListener('change', () => {
                        if (checkbox.checked) {
                            container.classList.add('destination-selected');
                        } else {
                            container.classList.remove('destination-selected');
                        }
                        
                        this.updateAllCounters();
                    });
                });
                
                this.setupSelectionButtons();
            });
        },
        
        // Configuration des boutons de sélection/désélection
        setupSelectionButtons() {
            // Boutons globaux
            const selectAllBtn = document.getElementById('select-all-btn');
            if (selectAllBtn && !selectAllBtn.dataset.bound) {
                selectAllBtn.dataset.bound = 'true';
                selectAllBtn.addEventListener('click', () => {
                    const visibleDestinations = Array.from(document.querySelectorAll('[data-destination-id]'))
                        .filter(item => item.style.display !== 'none');
                    
                    visibleDestinations.forEach(item => {
                        const id = item.getAttribute('data-destination-id');
                        const checkbox = document.querySelector(`input[name="destinations[]"][value="${id}"]`);
                        
                        if (checkbox && !checkbox.checked) {
                            checkbox.checked = true;
                            item.classList.add('destination-selected');
                        }
                    });
                    
                    this.updateAllCounters();
                });
            }
            
            const deselectAllBtn = document.getElementById('deselect-all-btn');
            if (deselectAllBtn && !deselectAllBtn.dataset.bound) {
                deselectAllBtn.dataset.bound = 'true';
                deselectAllBtn.addEventListener('click', () => {
                    const visibleDestinations = Array.from(document.querySelectorAll('[data-destination-id]'))
                        .filter(item => item.style.display !== 'none');
                    
                    visibleDestinations.forEach(item => {
                        const id = item.getAttribute('data-destination-id');
                        const checkbox = document.querySelector(`input[name="destinations[]"][value="${id}"]`);
                        
                        if (checkbox) {
                            checkbox.checked = false;
                            item.classList.remove('destination-selected');
                        }
                    });
                    
                    this.updateAllCounters();
                });
            }
            
            // Boutons pour les services
            const selectAllServiceBtn = document.getElementById('select-all-service-attributes-btn');
            if (selectAllServiceBtn && !selectAllServiceBtn.dataset.bound) {
                selectAllServiceBtn.dataset.bound = 'true';
                selectAllServiceBtn.addEventListener('click', () => {
                    document.querySelectorAll('input[name="service_attributes[]"]').forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    this.updateAllCounters();
                });
            }
            
            const deselectAllServiceBtn = document.getElementById('deselect-all-service-attributes-btn');
            if (deselectAllServiceBtn && !deselectAllServiceBtn.dataset.bound) {
                deselectAllServiceBtn.dataset.bound = 'true';
                deselectAllServiceBtn.addEventListener('click', () => {
                    document.querySelectorAll('input[name="service_attributes[]"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    this.updateAllCounters();
                });
            }
        },
        
        // Validation stricte des champs
        setupFieldValidation() {
            // SIRET : que des chiffres, 14 max
            const siretInput = document.getElementById('siret');
            if (siretInput) {
                siretInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9]/g, '');
                    
                    if (value.length > 14) {
                        value = value.slice(0, 14);
                    }
                    
                    // Formatage visuel avec espaces
                    if (value.length > 0) {
                        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})(\d{3})/, '$1 $2 $3 $4 $5');
                    }
                    
                    this.value = value;
                });
            }
            
            // Téléphone : format international
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^+0-9\s\-()]/g, '');
                });
            }
            
            // URL site web : format https://
            const websiteInput = document.getElementById('website');
            if (websiteInput) {
                websiteInput.addEventListener('blur', function(e) {
                    if (this.value && !this.value.startsWith('http://') && !this.value.startsWith('https://')) {
                        this.value = 'https://' + this.value;
                    }
                });
            }
        },
        
        // Gestion du logo
        bindLogo() {
            const logoInput = document.getElementById('logo');
            if (logoInput && !logoInput.dataset.bound) {
                logoInput.dataset.bound = 'true';
                logoInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    if (!this.validateFile(file)) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logoPreview = e.target.result;
                        
                        const previewContainer = document.querySelector('#logo-preview-container');
                        if (previewContainer) {
                            previewContainer.innerHTML = '';
                            previewContainer.classList.remove('hidden');
                            
                            const img = document.createElement('img');
                            img.src = this.logoPreview;
                            img.alt = 'Logo sélectionné';
                            img.classList.add('h-20', 'w-auto', 'object-contain');
                            previewContainer.appendChild(img);
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        },
        
        // Validation du fichier logo
        validateFile(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!validTypes.includes(file.type)) {
                this.showAlert('Type de fichier non valide. Utilisez JPG, PNG ou GIF.', 'error');
                return false;
            }
            
            if (file.size > maxSize) {
                this.showAlert('Le fichier est trop volumineux. Taille maximale: 2MB', 'error');
                return false;
            }
            
            return true;
        },
        
        // Ajout de messages d'erreur
        addErrorMessage(input, message) {
            if (!input) return;
            
            const parent = input.parentNode;
            if (!parent) return;
            
            // Ne pas ajouter si un message existe déjà
            const existingError = parent.querySelector('.js-error-message');
            if (existingError) return;
            
            const errorMsg = document.createElement('p');
            errorMsg.classList.add('text-xs', 'text-error', 'mt-1', 'js-error-message');
            errorMsg.textContent = message;
            parent.appendChild(errorMsg);
        },
        
        // Affichage des alertes amélioré
        showAlert(message, type = 'success') {
            // Supprimer les alertes existantes
            document.querySelectorAll('.js-alert').forEach(alert => alert.remove());
            
            const alertContainer = document.createElement('div');
            alertContainer.classList.add('fixed', 'top-4', 'right-4', 'z-50', 'js-alert', 'max-w-md');
            
            const alertContent = document.createElement('div');
            alertContent.classList.add('p-4', 'rounded-lg', 'shadow-lg', 'flex', 'items-start', 'space-x-3');
            
            // Icône selon le type
            const icon = document.createElement('div');
            icon.classList.add('flex-shrink-0');
            
            if (type === 'error') {
                alertContent.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
                icon.innerHTML = '❌';
            } else if (type === 'warning') {
                alertContent.classList.add('bg-yellow-100', 'border', 'border-yellow-400', 'text-yellow-700');
                icon.innerHTML = '⚠️';
            } else {
                alertContent.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
                icon.innerHTML = '✅';
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('flex-1', 'text-sm');
            messageDiv.textContent = message;
            
            const closeBtn = document.createElement('button');
            closeBtn.classList.add('flex-shrink-0', 'text-gray-400', 'hover:text-gray-600');
            closeBtn.innerHTML = '✕';
            closeBtn.onclick = () => alertContainer.remove();
            
            alertContent.appendChild(icon);
            alertContent.appendChild(messageDiv);
            alertContent.appendChild(closeBtn);
            alertContainer.appendChild(alertContent);
            
            document.body.appendChild(alertContainer);
            
            // Animation d'entrée
            alertContainer.style.opacity = '0';
            alertContainer.style.transform = 'translateX(100%)';
            setTimeout(() => {
                alertContainer.style.transition = 'all 0.3s ease';
                alertContainer.style.opacity = '1';
                alertContainer.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto-suppression
            setTimeout(() => {
                if (alertContainer.parentNode) {
                    alertContainer.style.opacity = '0';
                    alertContainer.style.transform = 'translateX(100%)';
                    setTimeout(() => alertContainer.remove(), 300);
                }
            }, 5000);
        },
        
        // Fonctions utilitaires
        isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        
        isValidUrl(url) {
            try {
                const urlObj = new URL(url);
                return urlObj.protocol === 'http:' || urlObj.protocol === 'https:';
            } catch (e) {
                try {
                    const urlObj = new URL('https://' + url);
                    return true;
                } catch (e2) {
                    return false;
                }
            }
        }
    }));
});

// Fonctions utilitaires globales
function updateDestinationCounter() {
    const counter = document.getElementById('selected-destinations-counter');
    if (counter) {
        const count = document.querySelectorAll('input[name="destinations[]"]:checked').length;
        counter.textContent = count;
    }
}

// Démarrer Alpine
Alpine.start();

// Export par défaut pour Vite
export default {};