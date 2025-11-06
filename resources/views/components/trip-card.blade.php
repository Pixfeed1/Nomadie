@props([
    'trip',
    'showVendor' => false,
    'showDiscount' => true,
    'featured' => false,
])

@php
$cardClass = $featured ? 'shadow-lg hover:shadow-2xl' : 'shadow-md hover:shadow-lg';
@endphp

<div class="bg-white rounded-lg {{ $cardClass }} overflow-hidden relative card transition-shadow duration-300">
    {{-- Badge de promotion --}}
    @if($showDiscount && isset($trip->best_discount) && $trip->best_discount > 0)
    <div class="absolute top-2 left-2 z-10 bg-red-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold">
        -{{ $trip->best_discount }}%
    </div>
    @endif

    {{-- Image --}}
    <div class="relative h-40 sm:h-48 overflow-hidden">
        @if($trip->main_image)
            <img src="{{ asset($trip->main_image) }}"
                 alt="{{ $trip->title }}"
                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                 loading="lazy">
        @else
            <img src="/api/placeholder/600/400?text={{ urlencode($trip->title) }}"
                 alt="{{ $trip->title }}"
                 class="w-full h-full object-cover">
        @endif

        {{-- Badge type d'offre --}}
        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-semibold text-primary">
            {{ $trip->offer_type_label ?? 'Offre' }}
        </div>
    </div>

    {{-- Contenu --}}
    <div class="p-3 sm:p-4">
        {{-- Titre --}}
        <h4 class="text-base sm:text-lg font-bold text-text-primary mb-1 line-clamp-2">
            {{ $trip->title }}
        </h4>

        {{-- Destination --}}
        @if(isset($trip->destination))
        <p class="text-xs sm:text-sm text-text-secondary mb-2 flex items-center">
            <svg class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ $trip->destination->name ?? 'Destination' }}
        </p>
        @endif

        {{-- Description courte --}}
        @if(isset($trip->short_description))
        <p class="text-xs text-gray-600 mb-3 line-clamp-2 hidden sm:block">
            {{ $trip->short_description }}
        </p>
        @endif

        {{-- Vendor (optionnel) --}}
        @if($showVendor && isset($trip->vendor))
        <p class="text-xs text-gray-500 mb-2 flex items-center">
            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ $trip->vendor->company_name ?? 'Organisateur' }}
        </p>
        @endif

        {{-- Footer : Prix + CTA --}}
        <div class="flex flex-wrap sm:flex-nowrap justify-between items-center gap-2 mt-3">
            <div class="flex-1 min-w-0">
                @if(isset($trip->best_discount) && $trip->best_discount > 0)
                    <div class="flex items-baseline gap-1 sm:gap-2">
                        <span class="text-base sm:text-lg font-bold text-primary">{{ $trip->price_display ?? number_format($trip->price, 0, ',', ' ') . ' €' }}</span>
                        <span class="text-xs text-gray-500 line-through">{{ number_format($trip->price * 1.2, 0, ',', ' ') }} €</span>
                    </div>
                @else
                    <span class="text-base sm:text-lg font-bold text-primary block">
                        {{ $trip->price_display ?? number_format($trip->price, 0, ',', ' ') . ' €' }}
                    </span>
                @endif

                @if(isset($trip->price_unit))
                <span class="text-xs text-gray-500">/{{ $trip->price_unit }}</span>
                @endif
            </div>

            <a href="{{ route('trips.show', $trip->slug) }}"
               class="text-primary hover:text-primary-dark text-xs sm:text-sm font-medium flex items-center whitespace-nowrap">
                Voir
                <svg class="h-3 w-3 sm:h-4 sm:w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</div>
