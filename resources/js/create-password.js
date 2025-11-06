// Fichier: public/js/create-password.js
// JavaScript spécialisé pour la page de création de mot de passe

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔐 Initialisation page création mot de passe');
    
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submit-btn');
    const toggleBtn = document.getElementById('toggle-password');
    const form = document.getElementById('password-form');
    
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
    
    // Toggle visibilité mot de passe
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (eyeOpen && eyeClosed) {
                eyeOpen.classList.toggle('hidden');
                eyeClosed.classList.toggle('hidden');
            }
        });
    }
    
    // Validation en temps réel du mot de passe
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Vérifier chaque critère
            validateCriteria('length', password.length >= 8);
            validateCriteria('lowercase', /[a-z]/.test(password));
            validateCriteria('uppercase', /[A-Z]/.test(password));
            validateCriteria('number', /\d/.test(password));
            validateCriteria('special', /[@$!%*?&]/.test(password));
            
            // Vérifier la confirmation si elle existe
            if (confirmInput && confirmInput.value) {
                checkPasswordMatch();
            }
            
            updateSubmitButton();
        });
    }
    
    // Vérification confirmation mot de passe
    if (confirmInput) {
        confirmInput.addEventListener('input', checkPasswordMatch);
    }
    
    function validateCriteria(name, isValid) {
        const element = criteria[name];
        if (!element) return;
        
        const icon = element.querySelector('.criteria-icon');
        
        if (isValid) {
            element.classList.remove('invalid');
            element.classList.add('valid');
            if (icon) {
                icon.classList.remove('invalid');
                icon.classList.add('valid', 'checkmark');
                icon.textContent = '✓';
            }
        } else {
            element.classList.remove('valid');
            element.classList.add('invalid');
            if (icon) {
                icon.classList.remove('valid', 'checkmark');
                icon.classList.add('invalid');
                icon.textContent = '✗';
            }
        }
    }
    
    function checkPasswordMatch() {
        if (!passwordInput || !confirmInput) return null;
        
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        if (confirm === '') {
            if (matchDiv) matchDiv.classList.add('hidden');
            if (nomatchDiv) nomatchDiv.classList.add('hidden');
            return null;
        }
        
        const matches = password === confirm;
        
        if (matches) {
            if (matchDiv) matchDiv.classList.remove('hidden');
            if (nomatchDiv) nomatchDiv.classList.add('hidden');
            confirmInput.classList.remove('invalid');
            confirmInput.classList.add('valid');
        } else {
            if (matchDiv) matchDiv.classList.add('hidden');
            if (nomatchDiv) nomatchDiv.classList.remove('hidden');
            confirmInput.classList.remove('valid');
            confirmInput.classList.add('invalid');
        }
        
        return matches;
    }
    
    function updateSubmitButton() {
        if (!passwordInput || !confirmInput || !submitBtn) return;
        
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        // Vérifier tous les critères
        const isLengthValid = password.length >= 8;
        const hasLowercase = /[a-z]/.test(password);
        const hasUppercase = /[A-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[@$!%*?&]/.test(password);
        const passwordsMatch = password === confirm && confirm !== '';
        
        const allValid = isLengthValid && hasLowercase && hasUppercase && hasNumber && hasSpecial && passwordsMatch;
        
        submitBtn.disabled = !allValid;
        
        if (allValid) {
            submitBtn.classList.add('valid');
            passwordInput.classList.add('valid');
        } else {
            submitBtn.classList.remove('valid');
            passwordInput.classList.remove('valid');
        }
    }
    
    // Soumission du formulaire avec AJAX
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (submitBtn.disabled) {
                console.log('⚠️ Formulaire non valide, soumission annulée');
                return;
            }
            
            console.log('📤 Soumission du formulaire de création de mot de passe');
            
            // Désactiver le bouton et afficher le loading
            submitBtn.disabled = true;
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            
            if (btnText) btnText.textContent = 'Création en cours...';
            if (btnLoading) btnLoading.classList.remove('hidden');
            
            // Préparer les données
            const formData = new FormData(form);
            const token = form.action.split('/').pop(); // Extraire le token de l'URL
            
            // Envoi AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                }
            })
            .then(response => {
                console.log('📨 Réponse reçue:', response.status);
                
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur de validation');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('✅ Succès:', data);
                
                // Afficher le message de succès
                showAlert(data.message || 'Mot de passe créé avec succès !', 'success');
                
                // Redirection après succès
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    // Redirection par défaut vers le dashboard ou login
                    setTimeout(() => {
                        window.location.href = '/vendor/dashboard';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('❌ Erreur:', error);
                
                showAlert(error.message || 'Une erreur est survenue lors de la création du mot de passe.', 'error');
                
                // Réactiver le bouton
                submitBtn.disabled = false;
                if (btnText) btnText.textContent = 'Créer mon mot de passe';
                if (btnLoading) btnLoading.classList.add('hidden');
            });
        });
    }
    
    // Fonction d'affichage des alertes
    function showAlert(message, type = 'success') {
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
    }
    
    console.log('✅ Page création mot de passe initialisée');
});