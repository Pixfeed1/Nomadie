@extends('layouts.public')

@section('title', 'Contactez-nous')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-text-primary">Contactez-nous</h1>
            <p class="mt-4 text-lg text-text-secondary">Une question ou une suggestion ? N'hésitez pas à nous écrire !</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Informations de contact -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-border">
                        <h2 class="text-xl font-semibold text-text-primary">Nos coordonnées</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center 
text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 
0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-text-primary">Email</h3>
                                <p class="mt-1 text-sm text-text-secondary">contact@marketplace-voyages.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center 
text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 
01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 
01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-text-primary">Téléphone</h3>
                                <p class="mt-1 text-sm text-text-secondary">+33 (0)1 23 45 67 89</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center 
text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 
20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 
016 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-text-primary">Adresse</h3>
                                <p class="mt-1 text-sm text-text-secondary">
                                    123 Avenue des Voyageurs<br>
                                    75008 Paris<br>
                                    France
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-bg-alt border-t border-border">
                        <h3 class="text-base font-medium text-text-primary mb-4">Suivez-nous</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 
9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 
1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 
4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 
4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 
1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 
0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 
01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 
4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 
1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 
1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 
1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 
3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 
1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 
3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 
110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 
010-2.4z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 
5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 
01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 
4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de contact -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-border">
                        <h2 class="text-xl font-semibold text-text-primary">Envoyez-nous un message</h2>
                    </div>
                    
                    @if(session('success'))
                    <div class="bg-success/10 border-l-4 border-success p-4 m-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 
11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-success">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <form action="{{ route('contact.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-text-primary mb-1">Nom *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border 
@error('name') border-error @else border-border @enderror rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                                @error('name')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-text-primary mb-1">Email *</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border 
@error('email') border-error @else border-border @enderror rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                                @error('email')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-text-primary mb-1">Sujet *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 border 
@error('subject') border-error @else border-border @enderror rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
required>
                            @error('subject')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-text-primary mb-1">Message *</label>
                            <textarea id="message" name="message" rows="6" class="w-full px-4 py-2 border @error('message') border-error 
@else border-border @enderror rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none" required>{{ old('message') 
}}</textarea>
                            @error('message')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="privacy" name="privacy" type="checkbox" class="focus:ring-primary h-4 w-4 text-primary 
border-border rounded" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="privacy" class="font-medium text-text-primary">J'accepte que mes données soient traitées 
conformément à la <a href="#" class="text-primary hover:text-primary-dark">politique de confidentialité</a>.</label>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base 
font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 
focus:ring-primary transition-colors">
                                Envoyer le message
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" 
/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-semibold text-text-primary text-center mb-8">Questions fréquentes</h2>
            
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="divide-y divide-border">
                    <div class="p-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full justify-between items-center text-left focus:outline-none">
                            <h3 class="text-lg font-medium text-text-primary">Comment contacter un organisateur de voyage ?</h3>
                            <svg class="h-5 w-5 text-text-primary" :class="{'transform rotate-180': open}" 
xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="mt-3">
                            <p class="text-text-secondary">Vous pouvez contacter directement les organisateurs de voyage via leur page 
de profil. Chaque organisateur dispose d'un formulaire de contact dédié, ou vous pouvez également utiliser les informations de contact 
qu'ils ont partagées sur leur profil.</p>
                        </div>
                    </div>
                    
                    <div class="p-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full justify-between items-center text-left focus:outline-none">
                            <h3 class="text-lg font-medium text-text-primary">Comment devenir organisateur sur la plateforme ?</h3>
                            <svg class="h-5 w-5 text-text-primary" :class="{'transform rotate-180': open}" 
xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="mt-3">
                            <p class="text-text-secondary">Pour devenir organisateur, rendez-vous sur la page <a href="{{ 
route('vendor.register') }}" class="text-primary hover:text-primary-dark">Devenir Organisateur</a> et suivez les étapes d'inscription. 
Nous examinerons votre candidature et vous recevrez une réponse dans les 48 heures ouvrables.</p>
                        </div>
                    </div>
                    
                    <div class="p-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full justify-between items-center text-left focus:outline-none">
                            <h3 class="text-lg font-medium text-text-primary">Comment fonctionne le processus de réservation ?</h3>
                            <svg class="h-5 w-5 text-text-primary" :class="{'transform rotate-180': open}" 
xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="mt-3">
                            <p class="text-text-secondary">Le processus de réservation est simple : sélectionnez l'expérience qui vous 
intéresse, choisissez vos dates, le nombre de participants, puis procédez au paiement sécurisé. Vous recevrez une confirmation par email 
avec tous les détails de votre réservation.</p>
                        </div>
                    </div>
                    
                    <div class="p-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full justify-between items-center text-left focus:outline-none">
                            <h3 class="text-lg font-medium text-text-primary">Comment puis-je annuler ou modifier ma réservation ?</h3>
                            <svg class="h-5 w-5 text-text-primary" :class="{'transform rotate-180': open}" 
xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="mt-3">
                            <p class="text-text-secondary">Vous pouvez annuler ou modifier votre réservation depuis votre espace membre. 
Les conditions d'annulation varient selon les organisateurs - elles sont clairement indiquées sur chaque expérience avant la 
réservation. Pour toute assistance, n'hésitez pas à nous contacter directement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="mt-16">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 border-b border-border">
                    <h2 class="text-xl font-semibold text-text-primary">Où nous trouver</h2>
                </div>
                <div class="aspect-w-16 aspect-h-9 w-full">
                    <!-- Remplacer par un iframe Google Maps ou une image -->
                    <div class="w-full h-96 bg-bg-alt flex items-center justify-center">
                        <p class="text-text-secondary">Carte interactive à intégrer ici</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
