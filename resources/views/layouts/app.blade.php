<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('writer.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('writer.articles.index') }}">Mes Articles</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('writer.badges.index') }}">Mes Badges</a>
                            </li>
                        @endauth
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- Notifications -->
                            <li class="nav-item" x-data="{ 
                                open: false,
                                count: {{ auth()->user()->unreadNotifications()->count() }}
                            }">
                                <div class="position-relative" @click.away="open = false">
                                    <a href="#" 
                                       @click.prevent="open = !open"
                                       class="nav-link position-relative px-3">
                                        <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <span x-show="count > 0" 
                                              x-text="count"
                                              class="notification-badge">
                                        </span>
                                    </a>
                                    
                                    <!-- Dropdown notifications -->
                                    <div x-show="open"
                                         x-transition
                                         class="position-absolute end-0 bg-white border rounded shadow-lg"
                                         style="width: 320px; max-height: 400px; overflow-y: auto; z-index: 1000; margin-top: 8px;">
                                        
                                        <div class="border-bottom p-3 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Notifications</h6>
                                            <a href="{{ route('writer.notifications.index') }}" class="text-decoration-none small">
                                                Voir tout
                                            </a>
                                        </div>
                                        
                                        @forelse(auth()->user()->unreadNotifications()->latest()->limit(5)->get() as $notification)
                                        <div class="border-bottom p-3">
                                            <div class="d-flex align-items-start">
                                                @if($notification->data['type'] === 'badge_unlocked')
                                                    <span class="me-2">🏆</span>
                                                @elseif($notification->data['type'] === 'dofollow_achieved')
                                                    <span class="me-2">🚀</span>
                                                @elseif($notification->data['type'] === 'exceptional_score')
                                                    <span class="me-2">⭐</span>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small">{{ $notification->data['message'] }}</p>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            @if(isset($notification->data['url']))
                                            <a href="{{ $notification->data['url'] }}" class="text-decoration-none small">
                                                Voir →
                                            </a>
                                            @endif
                                        </div>
                                        @empty
                                        <div class="p-4 text-center">
                                            <p class="text-muted mb-0">Aucune notification</p>
                                        </div>
                                        @endforelse
                                        
                                        @if(auth()->user()->unreadNotifications()->count() > 0)
                                        <div class="p-2 text-center">
                                            <form method="POST" action="{{ route('writer.notifications.markAllAsRead') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link text-decoration-none">
                                                    Tout marquer comme lu
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            
                            <!-- User dropdown -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('writer.dashboard') }}">
                                        Dashboard
                                    </a>
                                    <a class="dropdown-item" href="{{ route('writer.articles.create') }}">
                                        Nouvel article
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>