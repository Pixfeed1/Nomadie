<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Espace Rédacteur | {{ config('app.name') }}</title>

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
    @stack('head')
</head>

<body x-data="{
    sidebarOpen: false,
    sidebarHidden: {{ request()->routeIs('writer.articles.create') || request()->routeIs('writer.articles.edit') ? 'true' : 'false' }}
}" class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar desktop -->
        <div x-show="!sidebarHidden" class="hidden lg:flex lg:flex-shrink-0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-full">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-4">
                        <a href="{{ route('writer.dashboard') }}" class="flex items-center">
                            <svg class="h-8 w-8 text-primary mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <div>
                                <span class="text-lg font-bold text-text-primary">{{ config('app.name') }}</span>
                                <span class="block text-xs text-text-secondary">Espace Rédacteur</span>
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
                                        {{ Auth::user()->name ?? Auth::user()->pseudo }}
                                    </p>
                                    <p class="text-xs text-text-secondary">
                                        Rédacteur
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <a href="{{ route('writer.dashboard') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.dashboard') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>

                        <a href="{{ route('writer.articles.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.articles.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mes articles
                        </a>

                        <a href="{{ route('writer.briefs.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.briefs.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Briefs disponibles
                        </a>

                        <a href="{{ route('writer.badges.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.badges.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Mes badges
                        </a>

                        <a href="{{ route('writer.notifications.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.notifications.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Notifications
                            @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $unreadNotifications }}
                                </span>
                            @endif
                        </a>

                        <hr class="my-3 border-gray-200">

                        <a href="{{ route('home') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-primary hover:bg-primary/5 transition-colors">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            Retour au site
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-error hover:bg-error/5 transition-colors">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Sidebar Mobile -->
        <div x-show="sidebarOpen"
             x-cloak
             @click.away="sidebarOpen = false"
             class="fixed inset-0 z-40 flex lg:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                    <!-- Logo Mobile -->
                    <div class="flex items-center flex-shrink-0 px-4">
                        <a href="{{ route('writer.dashboard') }}" class="flex items-center">
                            <svg class="h-8 w-8 text-primary mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <div>
                                <span class="text-lg font-bold text-text-primary">{{ config('app.name') }}</span>
                                <span class="block text-xs text-text-secondary">Espace Rédacteur</span>
                            </div>
                        </a>
                    </div>

                    <!-- Info utilisateur Mobile -->
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
                                        {{ Auth::user()->name ?? Auth::user()->pseudo }}
                                    </p>
                                    <p class="text-xs text-text-secondary">
                                        Rédacteur
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Mobile (same as desktop) -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <!-- Same menu items as desktop -->
                        <a href="{{ route('writer.dashboard') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.dashboard') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                        <a href="{{ route('writer.articles.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.articles.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mes articles
                        </a>
                        <a href="{{ route('writer.briefs.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.briefs.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Briefs disponibles
                        </a>
                        <a href="{{ route('writer.badges.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.badges.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Mes badges
                        </a>
                        <a href="{{ route('writer.notifications.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors
                                  {{ request()->routeIs('writer.notifications.*') ? 'nav-active' : 'text-text-secondary hover:text-primary hover:bg-primary/5' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Notifications
                        </a>
                        <hr class="my-3 border-gray-200">
                        <a href="{{ route('home') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-primary hover:bg-primary/5 transition-colors">
                            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            Retour au site
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-text-secondary hover:text-error hover:bg-error/5 transition-colors">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 overflow-hidden">
            <!-- Header - Masqué sur pages création/édition article -->
            @unless(request()->routeIs('writer.articles.create') || request()->routeIs('writer.articles.edit'))
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Menu burger mobile -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="flex-1 lg:flex-none">
                            <h1 class="text-2xl font-bold text-text-primary">@yield('page-title', 'Dashboard')</h1>
                            @hasSection('page-description')
                                <p class="text-sm text-text-secondary mt-1">@yield('page-description')</p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Notifications badge -->
                            <a href="{{ route('writer.notifications.index') }}" class="relative p-2 text-gray-400 hover:text-primary transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                                @endif
                            </a>

                            <!-- Profile -->
                            <div class="hidden sm:flex items-center">
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
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            @endunless

            <!-- Bouton toggle sidebar pour pages création/édition -->
            @if(request()->routeIs('writer.articles.create') || request()->routeIs('writer.articles.edit'))
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                <button @click="sidebarHidden = !sidebarHidden"
                        class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <div class="hidden lg:block">
                        <span class="text-sm font-bold text-text-primary">{{ config('app.name') }}</span>
                        <span class="block text-xs text-text-secondary">Cliquer pour toggle le menu</span>
                    </div>
                </button>

                <!-- Menu burger mobile pour ouvrir sidebar -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            @endif

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-6 bg-success/10 border-l-4 border-success rounded-r-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="ml-3 text-sm text-success">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-error/10 border-l-4 border-error rounded-r-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-error" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="ml-3 text-sm text-error">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
