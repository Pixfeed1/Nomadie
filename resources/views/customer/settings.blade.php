@extends('customer.layouts.app')

@section('title', 'Paramètres')

@section('page-title', 'Paramètres')

@section('content')
<div class="space-y-6">
    {{-- En-tête de page --}}
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Paramètres du compte
        </h2>
        <p class="text-text-secondary mt-1">
            Gérez vos préférences, notifications et la sécurité de votre compte.
        </p>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Menu latéral --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <nav class="space-y-1 p-4">
                    <button onclick="showSection('notifications')" id="btn-notifications"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-primary/10 text-primary">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </button>

                    <button onclick="showSection('security')" id="btn-security"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Sécurité
                    </button>

                    <button onclick="showSection('privacy')" id="btn-privacy"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Confidentialité
                    </button>

                    <button onclick="showSection('account')" id="btn-account"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Gestion du compte
                    </button>
                </nav>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:col-span-2">
            {{-- Section Notifications --}}
            <div id="section-notifications" class="settings-section">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                        <p class="text-sm text-gray-600 mt-1">Gérez vos préférences de notifications</p>
                    </div>
                    
                    <form action="{{ url('/mon-compte/parametres') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            {{-- Newsletter --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           id="newsletter" 
                                           name="newsletter" 
                                           value="1"
                                           {{ $user->newsletter ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label for="newsletter" class="text-sm font-medium text-gray-700">
                                        Newsletter
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Recevez nos dernières offres et actualités par email
                                    </p>
                                </div>
                            </div>

                            {{-- Notifications email --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           id="notifications_email" 
                                           name="notifications_email" 
                                           value="1"
                                           {{ $user->notifications_email ?? true ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label for="notifications_email" class="text-sm font-medium text-gray-700">
                                        Notifications par email
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Recevez des emails pour vos réservations et messages importants
                                    </p>
                                </div>
                            </div>

                            {{-- Notifications SMS --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           id="notifications_sms" 
                                           name="notifications_sms" 
                                           value="1"
                                           {{ $user->notifications_sms ?? false ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label for="notifications_sms" class="text-sm font-medium text-gray-700">
                                        Notifications SMS
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Recevez des SMS pour les rappels de réservation (nécessite un numéro de téléphone)
                                    </p>
                                </div>
                            </div>

                            {{-- Rappels --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           id="reminders" 
                                           name="reminders" 
                                           value="1"
                                           {{ $user->reminders ?? true ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label for="reminders" class="text-sm font-medium text-gray-700">
                                        Rappels automatiques
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Recevez des rappels avant vos expériences
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg hover:from-primary-dark hover:to-primary transition-all shadow-md hover:shadow-lg">
                                Enregistrer les préférences
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Section Sécurité --}}
            <div id="section-security" class="settings-section hidden">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Sécurité</h3>
                        <p class="text-sm text-gray-600 mt-1">Changez votre mot de passe et gérez la sécurité</p>
                    </div>
                    
                    <form action="{{ url('/mon-compte/parametres/password') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mot de passe actuel
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input type="password" 
                                           name="current_password" 
                                           id="current_password" 
                                           required
                                           class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nouveau mot de passe
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </div>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           required
                                           minlength="8"
                                           class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmer le nouveau mot de passe
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </div>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation" 
                                           required
                                           class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg hover:from-primary-dark hover:to-primary transition-all shadow-md hover:shadow-lg">
                                Changer le mot de passe
                            </button>
                        </div>
                    </form>

                    {{-- Sessions actives --}}
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Sessions actives</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Session actuelle</p>
                                        <p class="text-xs text-gray-500">{{ request()->ip() }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-green-600 font-medium">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section Confidentialité --}}
            <div id="section-privacy" class="settings-section hidden">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Confidentialité et données</h3>
                        <p class="text-sm text-gray-600 mt-1">Gérez vos données personnelles</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Export de données</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Téléchargez une copie de toutes vos données personnelles (profil, réservations, avis, messages).
                            </p>
                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Télécharger mes données
                            </button>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Cookies et suivi</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Cookies essentiels</p>
                                        <p class="text-sm text-gray-500">Nécessaires au fonctionnement du site</p>
                                    </div>
                                    <span class="text-xs text-gray-500">Toujours actifs</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Cookies d'analyse</p>
                                        <p class="text-sm text-gray-500">Nous aident à améliorer le service</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Politique de confidentialité</h4>
                            <div class="space-y-2">
                                <a href="#" class="text-sm text-primary hover:text-primary-dark">
                                    → Lire notre politique de confidentialité
                                </a>
                                <a href="#" class="text-sm text-primary hover:text-primary-dark">
                                    → Conditions générales d'utilisation
                                </a>
                                <a href="#" class="text-sm text-primary hover:text-primary-dark">
                                    → Gestion des cookies
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section Gestion du compte --}}
            <div id="section-account" class="settings-section hidden">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-accent/10 p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Gestion du compte</h3>
                        <p class="text-sm text-gray-600 mt-1">Options de désactivation et suppression du compte</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Désactiver le compte --}}
                        <div class="pb-6 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Désactiver temporairement mon compte</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Votre compte sera masqué et vous ne recevrez plus de notifications. Vous pourrez le réactiver à tout moment en vous reconnectant.
                            </p>
                            <button onclick="confirmDeactivate()" 
                                    class="px-4 py-2 border border-yellow-500 text-sm font-medium rounded-lg text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                                <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Désactiver temporairement
                            </button>
                        </div>

                        {{-- Supprimer le compte --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Supprimer définitivement mon compte</h4>
                            <p class="text-sm text-gray-600 mb-2">
                                Cette action est <strong class="text-red-600">irréversible</strong>. En supprimant votre compte :
                            </p>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 mb-4">
                                <li>Toutes vos données personnelles seront supprimées</li>
                                <li>Vos réservations passées seront anonymisées</li>
                                <li>Vos avis resteront mais seront anonymes</li>
                                <li>Vous ne pourrez plus accéder à votre historique</li>
                            </ul>
                            <button onclick="confirmDelete()" 
                                    class="px-4 py-2 border border-red-500 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700">
                                <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer définitivement mon compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Supprimer votre compte ?</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Cette action est irréversible. Toutes vos données seront supprimées définitivement.
                </p>
                
                                    <form action="{{ url('/mon-compte/supprimer') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4">
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Entrez votre mot de passe pour confirmer
                        </label>
                        <input type="password" 
                               name="password" 
                               id="delete_password" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Tapez "DELETE" pour confirmer
                        </label>
                        <input type="text" 
                               name="confirmation" 
                               id="confirmation" 
                               required
                               pattern="DELETE"
                               placeholder="DELETE"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeDeleteModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700">
                            Supprimer définitivement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showSection(section) {
    // Cacher toutes les sections
    document.querySelectorAll('.settings-section').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Retirer la classe active de tous les boutons
    document.querySelectorAll('[id^="btn-"]').forEach(btn => {
        btn.classList.remove('bg-primary/10', 'text-primary');
        btn.classList.add('text-gray-600', 'hover:bg-gray-50');
    });
    
    // Afficher la section sélectionnée
    document.getElementById('section-' + section).classList.remove('hidden');
    
    // Activer le bouton sélectionné
    const activeBtn = document.getElementById('btn-' + section);
    activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');
    activeBtn.classList.add('bg-primary/10', 'text-primary');
}

function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDeactivate() {
    if (confirm('Êtes-vous sûr de vouloir désactiver votre compte ?')) {
        // Logique de désactivation
        console.log('Compte désactivé');
    }
}

// Fermer la modal si on clique en dehors
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush