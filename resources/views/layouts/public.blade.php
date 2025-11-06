<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Nomadie</title>
    
    <!-- Intégration de Vite pour les assets compilés -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles personnalisés -->
    <style>
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(to right, rgba(237, 242, 247, 0.6), rgba(247, 250, 252, 0.9));
            background-attachment: fixed;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .travel-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2338B2AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        /* Corrections responsive pour les boutons */
        @media (max-width: 768px) {
            .user-menu-mobile {
                padding: 0.5rem 0;
            }
        }
    </style>
</head>

<body class="travel-pattern">
    <div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xl font-bold text-text-primary">Nomadie</span>
                            </a>
                        </div>
                        <nav class="hidden md:ml-8 md:flex md:space-x-8">
                            <a href="/" class="border-transparent text-text-primary hover:text-primary hover:border-primary px-3 py-2 inline-flex items-center text-sm font-medium border-b-2 transition-colors">
                                Accueil
                            </a>
                            <a href="{{ route('destinations.index') }}" class="border-transparent text-text-primary hover:text-primary hover:border-primary px-3 py-2 inline-flex items-center text-sm font-medium border-b-2 transition-colors">
                                Destinations
                            </a>
                            <a href="{{ route('about') }}" class="border-transparent text-text-primary hover:text-primary hover:border-primary px-3 py-2 inline-flex items-center text-sm font-medium border-b-2 transition-colors">
                                À propos
                            </a>
                            <a href="{{ route('blog') }}" class="border-transparent text-text-primary hover:text-primary hover:border-primary px-3 py-2 inline-flex items-center text-sm font-medium border-b-2 transition-colors">
                                Blog
                            </a>
                            <a href="{{ route('contact') }}" class="border-transparent text-text-primary hover:text-primary hover:border-primary px-3 py-2 inline-flex items-center text-sm font-medium border-b-2 transition-colors">
                                Contact
                            </a>
                        </nav>
                    </div>
                    
                    <!-- Desktop User Menu -->
                    <div class="hidden md:flex items-center space-x-3">
                        @auth
                            <!-- Utilisateur connecté avec dropdown -->
                            <div x-data="{ userMenuOpen: false }" class="relative">
                                <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center text-sm font-medium text-text-primary hover:text-primary transition-colors">
                                    <!-- Avatar ou initiales SANS LE NOM - Cercle plus grand -->
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="h-11 w-11 rounded-full object-cover border-2 border-gray-200 hover:border-primary transition-colors cursor-pointer">
                                    @else
                                        <div class="h-11 w-11 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-base border-2 border-transparent hover:border-primary transition-colors cursor-pointer">
                                            {{ substr(Auth::user()->name, 0, 1) }}{{ substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1) }}
                                        </div>
                                    @endif
                                </button>
                                
                                <!-- Dropdown menu ENCORE PLUS LARGE -->
                                <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-80 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100" style="display: none;">
                                    <!-- Header avec nom et email -->
                                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100">
                                        <p class="text-base font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    
                                    @if(Auth::user()->role === 'customer')
                                    <div class="py-2">
                                        <a href="{{ route('customer.dashboard') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                                Mon compte
                                            </div>
                                        </a>
                                        <a href="{{ route('customer.bookings') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Mes réservations
                                            </div>
                                        </a>
                                        <a href="{{ route('customer.favorites') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                Mes favoris
                                            </div>
                                        </a>
                                    </div>
                                    @endif
                                    
                                    @if(Auth::user()->vendor && Auth::user()->vendor->status === 'active')
                                    <div class="py-2">
                                        <a href="{{ route('vendor.dashboard.index') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                                Dashboard
                                            </div>
                                        </a>
                                        <a href="{{ route('vendor.trips.index') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                    Mes Offres
                                                </div>
                                                <span class="text-sm bg-primary/10 text-primary font-semibold px-2.5 py-1 rounded-full">{{ Auth::user()->vendor->trips()->count() ?? 0 }}</span>
                                            </div>
                                        </a>
                                        <a href="{{ route('vendor.bookings.index') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Réservations
                                            </div>
                                        </a>
                                        <a href="{{ route('vendor.dashboard.analytics') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                                Analytiques
                                            </div>
                                        </a>
                                        <a href="{{ route('vendor.payments.index') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Paiements
                                            </div>
                                        </a>
                                        <a href="{{ route('vendor.settings.index') }}" class="block px-6 py-3.5 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Paramètres
                                            </div>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="py-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-6 py-3.5 text-base text-red-600 hover:bg-red-50 transition-colors">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                    Se déconnecter
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Utilisateur non connecté - MODIFICATION ICI -->
                            <a href="{{ route('login') }}" class="flex items-center justify-center h-8 px-3 text-text-primary hover:text-primary font-medium text-xs md:text-sm transition-colors border border-transparent hover:border-primary/20 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Se connecter
                            </a>
                            <a href="{{ route('register.choose') }}" class="flex items-center justify-center h-8 px-3 bg-primary hover:bg-primary-dark text-white font-medium rounded text-xs md:text-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Créer un compte
                            </a>
                        @endauth
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-text-primary hover:text-primary hover:bg-bg-alt focus:outline-none transition-colors">
                            <span class="sr-only">Ouvrir le menu</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-border" x-cloak>
                <!-- Navigation Links -->
                <div class="pt-2 pb-3 space-y-1">
                    <a href="/" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                        Accueil
                    </a>
                    <a href="{{ route('destinations.index') }}" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                        Destinations
                    </a>
                    <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                        À propos
                    </a>
                    <a href="{{ route('blog') }}" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                        Blog
                    </a>
                    <a href="{{ route('contact') }}" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                        Contact
                    </a>
                </div>
                
                <!-- User Menu Mobile -->
                <div class="pt-4 pb-3 border-t border-border">
                    @auth
                        <!-- Utilisateur connecté - Mobile -->
                        <div class="px-3 pb-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-text-primary">{{ Auth::user()->name }}</div>
                                    <div class="text-sm text-text-secondary">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1 user-menu-mobile">
                            @if(Auth::user()->role === 'customer')
                                <a href="{{ route('customer.dashboard') }}" class="block pl-3 pr-4 py-2 text-primary hover:text-primary-dark hover:bg-bg-alt font-medium transition-colors">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Mon compte
                                    </div>
                                </a>
                            @endif
                            @if(Auth::user()->vendor && Auth::user()->vendor->status === 'active')
                                <a href="{{ route('vendor.dashboard.index') }}" class="block pl-3 pr-4 py-2 text-primary hover:text-primary-dark hover:bg-bg-alt font-medium transition-colors">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        Dashboard Vendeur
                                    </div>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left pl-3 pr-4 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 font-medium transition-colors">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Déconnexion
                                    </div>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Utilisateur non connecté - Mobile - MODIFICATION ICI -->
                        <div class="space-y-1">
                            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 text-text-primary hover:text-primary hover:bg-bg-alt font-medium transition-colors">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Se connecter
                                </div>
                            </a>
                            <a href="{{ route('register.choose') }}" class="block pl-3 pr-4 py-2 text-primary hover:text-primary-dark font-medium transition-colors">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Créer un compte
                                </div>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Contenu principal -->
        <main class="flex-grow">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-success/10 p-4 border border-success/30 fixed top-20 right-4 z-50 rounded-lg shadow-lg max-w-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-success">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" class="inline-flex text-success hover:text-success/80">
                                <span class="sr-only">Fermer</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-border mt-12">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-sm font-semibold text-text-primary tracking-wider uppercase">À propos</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('about') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Qui sommes-nous</a>
                            </li>
                            <li>
                                <a href="{{ route('about') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Nos valeurs</a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">Témoignages</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-text-primary tracking-wider uppercase">Destinations</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">Europe</a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">Asie</a>
                            </li>
                            <li>
                                <a href="{{ route('destinations.index') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Toutes les destinations</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-text-primary tracking-wider uppercase">Ressources</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('blog') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Blog</a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">Guide du voyageur</a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">FAQ</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-text-primary tracking-wider uppercase">Nous contacter</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('contact') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Contact</a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-text-secondary hover:text-primary transition-colors">Support</a>
                            </li>
                            <li>
                                <a href="{{ route('register.choose') }}" class="text-base text-text-secondary hover:text-primary transition-colors">Créer un compte</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-border">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-base text-text-secondary mb-4 md:mb-0">&copy; 2025 Nomadie. Tous droits réservés.</p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-text-secondary hover:text-primary transition-colors">
                                <span class="sr-only">X</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts JavaScript supplémentaires -->
    @stack('scripts')
</body>
</html>