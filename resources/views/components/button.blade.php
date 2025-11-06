@props([
    'variant' => 'primary',  // primary, secondary, danger, success, outline
    'size' => 'md',          // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'fullWidth' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = [
    'primary' => 'bg-primary text-white hover:bg-primary-dark focus:ring-primary',
    'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'outline' => 'bg-transparent border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary',
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs sm:text-sm',
    'md' => 'px-4 py-2 text-sm sm:text-base',
    'lg' => 'px-6 py-3 text-base sm:text-lg',
];

$widthClass = $fullWidth ? 'w-full' : '';

$classes = trim($baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . $widthClass);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 sm:h-5 sm:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif

        {{ $slot }}

        @if(!$loading && $icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </button>
@endif
