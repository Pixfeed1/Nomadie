<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Espace Vendeur') - {{ config('app.name', 'Marketplace Voyages') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Mobile menu button -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Logo -->
                <a href="{{ route('vendor.dashboard.index') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    Espace Vendeur
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('vendor.dashboard.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors {{ request()->routeIs('vendor.dashboard.index') ? 'text-blue-600 border-b-2 border-blue-600 pb-4' : '' }}">
                        Tableau de bord
                    </a>
                    <a href="{{ route('vendor.trips.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors {{ request()->routeIs('vendor.trips.*') ? 'text-blue-600 border-b-2 border-blue-600 pb-4' : '' }}">
                        Mes voyages
                    </a>
                    <a href="{{ route('vendor.bookings.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors {{ request()->routeIs('vendor.bookings.*') ? 'text-blue-600 border-b-2 border-blue-600 pb-4' : '' }}">
                        Réservations
                    </a>
                </nav>

                <!-- User Menu -->
                <div class="relative">
                    <button onclick="toggleUserMenu()" class="flex items-center space-x-3 p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden sm:block font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    
                    <!-- Dropdown menu (hidden by default) -->
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="{{ route('vendor.settings.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-cog mr-2"></i> Mon profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-white shadow-md fixed lg:static lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out z-40 h-full">
            <nav class="py-6">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('vendor.dashboard.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.dashboard.index') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-home w-5"></i>
                            <span class="font-medium">Tableau de bord</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.trips.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.trips.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-route w-5"></i>
                            <span class="font-medium">Mes voyages</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.bookings.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.bookings.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span class="font-medium">Réservations</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.payments.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.payments.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-credit-card w-5"></i>
                            <span class="font-medium">Paiements</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.reviews.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.reviews.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-star w-5"></i>
                            <span class="font-medium">Avis clients</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.activity.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.activity.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-history w-5"></i>
                            <span class="font-medium">Historique</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.settings.profile') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.settings.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-user-cog w-5"></i>
                            <span class="font-medium">Mon profil</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendor.subscription.index') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('vendor.subscription.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : '' }}">
                            <i class="fas fa-crown w-5"></i>
                            <span class="font-medium">Abonnement</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8">
            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center space-x-3 animate-fade-in">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center space-x-3 animate-fade-in">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg flex items-center space-x-3 animate-fade-in">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Toggle User Menu
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (!userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.animate-fade-in');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    </script>
    @stack('scripts')
</body>
</html>
</html>