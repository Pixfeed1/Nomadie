@extends('customer.layouts.app')

@section('title', 'Tableau de bord')

@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Message de bienvenue -->
    <div class="bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 rounded-lg p-6 border border-primary/20">
        <h2 class="text-xl font-bold text-text-primary">
            Bonjour {{ $user->pseudo ?? $user->firstname }} !
        </h2>
        <p class="text-text-secondary mt-1">
            Bienvenue dans votre espace personnel. Gérez vos réservations et découvrez de nouvelles expériences.
        </p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Réservations</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['total_bookings'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">À venir</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['upcoming_bookings'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-accent/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Favoris</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['favorites_count'] }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21.721 12.752a9.711 9.711 0 00-.945-5.003 12.754 12.754 0 01-4.339 2.708 18.991 18.991 0 01-.214 4.772 17.165 17.165 0 005.498-2.477zM14.634 15.55a17.324 17.324 0 00.332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 00.332 4.647 17.385 17.385 0 005.268 0zM9.772 17.119a18.963 18.963 0 004.456 0A17.182 17.182 0 0112 21.724a17.18 17.18 0 01-2.228-4.605zM7.777 15.23a18.87 18.87 0 01-.214-4.774 12.753 12.753 0 01-4.34-2.708 9.711 9.711 0 00-.944 5.004 17.165 17.165 0 005.498 2.477zM21.356 14.752a9.765 9.765 0 01-7.478 6.817 18.64 18.64 0 001.988-4.718 18.627 18.627 0 005.49-2.098zM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 001.988 4.718 9.765 9.765 0 01-7.478-6.816zM13.878 2.43a9.755 9.755 0 016.116 3.986 11.267 11.267 0 01-3.746 2.504 18.63 18.63 0 00-2.37-6.49zM12 2.276a17.152 17.152 0 012.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0112 2.276zM10.122 2.43a18.629 18.629 0 00-2.37 6.49 11.266 11.266 0 01-3.746-2.504 9.754 9.754 0 016.116-3.985z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-text-secondary uppercase">Messages</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $stats['unread_messages'] }}</p>
                    <p class="text-xs text-text-secondary">non lus</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Réservations à venir -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-text-primary">Prochaines expériences</h3>
                        <a href="{{ route('customer.bookings') }}" class="text-sm text-primary hover:text-primary-dark">
                            Voir tout
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($upcomingBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBookings as $booking)
                                <div class="flex items-start space-x-4 p-4 rounded-lg border border-border hover:bg-gray-50 transition-colors">
                                    @if($booking['trip_image'])
                                        <img src="{{ asset('storage/' . $booking['trip_image']) }}" 
                                             alt="{{ $booking['trip_title'] }}"
                                             class="h-20 w-20 rounded-lg object-cover">
                                    @else
                                        <div class="h-20 w-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-text-primary">{{ $booking['trip_title'] }}</h4>
                                        <p class="text-sm text-text-secondary">{{ $booking['vendor_name'] }}</p>
                                        <div class="flex items-center mt-2 text-sm text-text-secondary">
                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            @if($booking['start_date'])
                                                {{ \Carbon\Carbon::parse($booking['start_date'])->format('d/m/Y') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-text-primary">{{ number_format($booking['total_price'], 0, ',', ' ') }} €</p>
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-success/10 text-success">
                                            Confirmé
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-text-secondary py-8">
                            Aucune réservation à venir
                        </p>
                    @endif
                </div>
            </div>

            <!-- Recommandations -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-text-primary">Recommandations pour vous</h3>
                </div>
                <div class="p-6">
                    @if($recommendations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($recommendations as $trip)
                                <div class="group cursor-pointer">
                                    <div class="relative overflow-hidden rounded-lg">
                                        @if($trip['image'])
                                            <img src="{{ asset('storage/' . $trip['image']) }}" 
                                                 alt="{{ $trip['title'] }}"
                                                 class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="h-48 w-full bg-gray-200"></div>
                                        @endif
                                        <div class="absolute top-2 right-2">
                                            <button class="p-2 rounded-full bg-white/80 hover:bg-white">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h4 class="font-medium text-text-primary group-hover:text-primary transition-colors">
                                            {{ $trip['title'] }}
                                        </h4>
                                        <p class="text-sm text-text-secondary">{{ $trip['destination'] }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="font-semibold text-primary">
                                                {{ number_format($trip['price'], 0, ',', ' ') }} €
                                            </span>
                                            @if($trip['rating'] > 0)
                                                <div class="flex items-center text-sm">
                                                    <svg class="h-4 w-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    {{ $trip['rating'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-text-secondary py-8">
                            Aucune recommandation disponible
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Profil -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Mon profil</h3>
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-text-secondary">Complété à</span>
                        <span class="font-medium text-text-primary">{{ $profileCompletion['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $profileCompletion['percentage'] }}%"></div>
                    </div>
                </div>
                @if($profileCompletion['percentage'] < 100)
                    <a href="{{ route('customer.profile') }}" 
                       class="w-full px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors text-center block">
                        Compléter mon profil
                    </a>
                @endif
            </div>

            <!-- Avis à donner -->
            @if($pendingReviews->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Avis en attente</h3>
                    <div class="space-y-3">
                        @foreach($pendingReviews as $review)
                            <div class="p-3 rounded-lg border border-border">
                                <h4 class="font-medium text-sm text-text-primary">{{ $review['trip_title'] }}</h4>
                                <p class="text-xs text-text-secondary mt-1">
                                    Terminé il y a {{ $review['days_ago'] }} jours
                                </p>
                                <a href="{{ route('customer.reviews.create', $review['booking_id']) }}" 
                                   class="mt-2 inline-flex items-center text-xs text-primary hover:text-primary-dark">
                                    Donner mon avis
                                    <svg class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Activité récente -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-text-primary mb-4">Activité récente</h3>
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}/10 flex items-center justify-center">
                                    <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-text-primary">{{ $activity['title'] }}</p>
                                <p class="text-xs text-text-secondary">{{ $activity['description'] }}</p>
                                <p class="text-xs text-primary mt-1">{{ $activity['date']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection