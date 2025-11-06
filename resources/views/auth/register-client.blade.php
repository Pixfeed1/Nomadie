@extends('layouts.public')

@section('title', 'Inscription')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-primary to-primary-dark p-6 text-white">
                <h1 class="text-2xl font-bold">Créer mon compte</h1>
                <p class="text-white/80 mt-2">Rejoignez Nomadie pour réserver hébergements, séjours et activités uniques</p>
            </div>

            <!-- Formulaire avec ID et action correcte -->
            <form method="POST" action="{{ url('/inscription') }}" enctype="multipart/form-data" class="p-6 space-y-6" id="registerForm">
                @csrf

                <!-- Nom et Prénom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-text-primary mb-1">
                            Nom <span class="text-error">*</span>
                        </label>
                        <input type="text" id="lastname" name="lastname" 
                               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary @error('lastname') border-error @enderror" 
                               value="{{ old('lastname') }}" required>
                        @error('lastname')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="firstname" class="block text-sm font-medium text-text-primary mb-1">
                            Prénom <span class="text-error">*</span>
                        </label>
                        <input type="text" id="firstname" name="firstname" 
                               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary @error('firstname') border-error @enderror" 
                               value="{{ old('firstname') }}" required>
                        @error('firstname')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pseudo -->
                <div>
                    <label for="pseudo" class="block text-sm font-medium text-text-primary mb-1">
                        Pseudo <span class="text-text-secondary text-xs">(optionnel)</span>
                    </label>
                    <input type="text" id="pseudo" name="pseudo" 
                           class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary @error('pseudo') border-error @enderror" 
                           value="{{ old('pseudo') }}"
                           placeholder="Comment souhaitez-vous être affiché ?">
                    <p class="text-xs text-text-secondary mt-1">Si vide, nous utiliserons votre prénom</p>
                    @error('pseudo')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-text-primary mb-1">
                        Adresse email <span class="text-error">*</span>
                    </label>
                    <input type="email" id="email" name="email" 
                           class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary @error('email') border-error @enderror" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-text-primary mb-1">
                            Mot de passe <span class="text-error">*</span>
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary @error('password') border-error @enderror" 
                               required>
                        <p class="text-xs text-text-secondary mt-1">8 caractères minimum</p>
                        @error('password')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1">
                            Confirmer le mot de passe <span class="text-error">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                               required>
                    </div>
                </div>

                <!-- Photo de profil -->
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-1">
                        Photo de profil <span class="text-text-secondary text-xs">(optionnel)</span>
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div id="avatar-preview" class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                            <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Choisir une photo
                            </label>
                            <p class="text-xs text-text-secondary mt-1">JPG, PNG ou GIF. Max 2MB</p>
                        </div>
                    </div>
                    @error('avatar')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CGV -->
                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" required
                           class="mt-1 h-4 w-4 text-primary border-border rounded focus:ring-primary">
                    <label for="terms" class="ml-2 text-sm text-text-secondary">
                        J'accepte les <a href="#" class="text-primary hover:text-primary-dark underline">conditions générales</a> 
                        et la <a href="#" class="text-primary hover:text-primary-dark underline">politique de confidentialité</a>
                    </label>
                </div>
                @error('terms')
                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                @enderror

                <!-- Newsletter -->
                <div class="flex items-start">
                    <input type="checkbox" id="newsletter" name="newsletter"
                           class="mt-1 h-4 w-4 text-primary border-border rounded focus:ring-primary"
                           {{ old('newsletter') ? 'checked' : '' }}>
                    <label for="newsletter" class="ml-2 text-sm text-text-secondary">
                        Je souhaite recevoir les bons plans et nouveautés par email
                    </label>
                </div>

                <!-- Bouton submit -->
                <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Créer mon compte
                </button>

                <!-- Lien connexion -->
                <p class="text-center text-sm text-text-secondary">
                    Déjà inscrit ? 
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">
                        Connectez-vous
                    </a>
                </p>
            </form>
        </div>

        <!-- Lien vendor -->
        <div class="mt-6 text-center">
            <p class="text-text-secondary">
                Vous souhaitez proposer des expériences ?
                <a href="{{ route('vendor.register') }}" class="text-primary hover:text-primary-dark font-medium">
                    Devenez organisateur
                </a>
            </p>
        </div>
    </div>
</div>

<script>
// Preview de l'avatar
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').innerHTML = 
                '<img src="' + e.target.result + '" class="h-full w-full object-cover">';
        }
        reader.readAsDataURL(file);
    }
});

// Debug et validation de soumission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    console.log('Formulaire en cours de soumission...');
    
    // Vérifier que les champs requis sont remplis
    const required = ['firstname', 'lastname', 'email', 'password', 'password_confirmation'];
    let hasError = false;
    
    for(let fieldName of required) {
        const input = document.getElementById(fieldName);
        if(!input || !input.value.trim()) {
            console.error('Champ manquant ou vide:', fieldName);
            hasError = true;
        }
    }
    
    // Vérifier que les mots de passe correspondent
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    if(password !== passwordConfirmation) {
        console.error('Les mots de passe ne correspondent pas');
        alert('Les mots de passe ne correspondent pas');
        e.preventDefault();
        return false;
    }
    
    // Vérifier que les CGV sont acceptées
    const terms = document.getElementById('terms');
    if(!terms.checked) {
        console.error('Les CGV doivent être acceptées');
        alert('Veuillez accepter les conditions générales');
        e.preventDefault();
        return false;
    }
    
    if(hasError) {
        console.error('Formulaire incomplet, soumission annulée');
        e.preventDefault();
        return false;
    }
    
    console.log('Formulaire valide, soumission en cours...');
    console.log('Action:', this.action);
    console.log('Method:', this.method);
});

// Log des erreurs Laravel au chargement
document.addEventListener('DOMContentLoaded', function() {
    const errors = document.querySelectorAll('.text-error');
    if(errors.length > 0) {
        console.log('Erreurs de validation Laravel détectées:');
        errors.forEach(error => {
            if(error.textContent.trim()) {
                console.error('- ' + error.textContent.trim());
            }
        });
    }
});
</script>
@endsection