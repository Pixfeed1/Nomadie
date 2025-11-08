<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Mon Compte | {{ config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .nav-active {
            background: linear-gradient(90deg, #38B2AC, #2C9A94);
            color: white !important;
        }
    </style>
    
    @stack('styles')
</head>

<body x-data="{ sidebarOpen: false }" class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar desktop -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-4">
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center">
                            <svg class="h-8 w-8 text-primary mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <div>
                                <span class="text-lg font-bold text-text-primary">{{ config('app.name') }}</span>
                                <span class="block text-xs text-text-secondary">Mon espace</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Info utilisateur -->
                    <div class="mt-5 px-4">
                        <div class="bg-primary/10 rounded-lg p-3">
                            <div class="flex items-center">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="Avatar" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-text-primary truncate">
                                        {{ Auth::user()->pseudo ?? Auth::user()->firstname }}
                                    </p>
                                    <p class="text-xs text-text-secondary">
                                        Membre depuis {{ Auth::user()->created_at->year }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <a href="{{ route('customer.dashboard') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.dashboard') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>

                        <a href="{{ route('customer.bookings') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.bookings*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Mes réservations
                        </a>

                        <a href="{{ route('customer.favorites') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.favorites*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Mes favoris
                        </a>

                        <a href="{{ route('customer.messages') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.messages*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Messages
                            @if(isset($stats['unread_messages']) && $stats['unread_messages'] > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $stats['unread_messages'] }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('customer.reviews') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.reviews*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Mes avis
                        </a>

                        <hr class="my-3 border-gray-200">

                        <a href="{{ route('customer.profile') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.profile*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mon profil
                        </a>

                        <a href="{{ route('customer.settings') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('customer.settings*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Paramètres
                        </a>

                        <hr class="my-3 border-gray-200">

                        @if(Auth::user()->isWriter())
                            <a href="{{ route('writer.dashboard') }}"
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                      {{ request()->routeIs('writer.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Espace rédacteur
                            </a>
                        @else
                            <a href="{{ route('writer.register') }}"
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-primary hover:bg-primary/5 transition-colors">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Devenir rédacteur
                            </a>
                        @endif

                        <a href="{{ route('home') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-primary hover:bg-primary/5 transition-colors">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            Explorer
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-text-primary">@yield('page-title', 'Mon compte')</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-400 hover:text-primary transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(isset($stats['unread_messages']) && $stats['unread_messages'] > 0)
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                                @endif
                            </button>

                            <!-- Profile Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                             alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="ml-2 text-text-primary font-medium">{{ Auth::user()->pseudo ?? Auth::user()->firstname }}</span>
                                    <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-cloak
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-text-secondary hover:bg-gray-100">
                                            Mon profil
                                        </a>
                                        <a href="{{ route('customer.settings') }}" class="block px-4 py-2 text-sm text-text-secondary hover:bg-gray-100">
                                            Paramètres
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        @if(Auth::user()->isWriter())
                                            <a href="{{ route('writer.dashboard') }}" class="block px-4 py-2 text-sm text-primary hover:bg-gray-100">
                                                Espace rédacteur
                                            </a>
                                        @else
                                            <a href="{{ route('writer.register') }}" class="block px-4 py-2 text-sm text-primary hover:bg-gray-100">
                                                Devenir rédacteur
                                            </a>
                                        @endif
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>