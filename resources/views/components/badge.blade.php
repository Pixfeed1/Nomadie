@props([
    'variant' => 'primary',  // primary, success, warning, danger, info, gray, accent, error
    'size' => 'md',          // sm, md, lg
    'rounded' => 'full',     // full, md, sm, none
    'style' => 'soft',       // soft (light background), solid (full color)
])

@php
$variantClasses = [
    'soft' => [
        'primary' => 'bg-primary/10 text-primary',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'error' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-gray-100 text-gray-800',
        'accent' => 'bg-accent/10 text-accent',
    ],
    'solid' => [
        'primary' => 'bg-primary text-white',
        'success' => 'bg-success text-white',
        'warning' => 'bg-warning text-white',
        'danger' => 'bg-error text-white',
        'error' => 'bg-error text-white',
        'info' => 'bg-blue-500 text-white',
        'gray' => 'bg-gray-600 text-white',
        'accent' => 'bg-accent/90 text-white',
    ],
];

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 sm:px-3 py-0.5 sm:py-1 text-xs sm:text-sm',
    'lg' => 'px-3 sm:px-4 py-1 sm:py-1.5 text-sm sm:text-base',
];

$roundedClasses = [
    'full' => 'rounded-full',
    'md' => 'rounded-md',
    'sm' => 'rounded-sm',
    'none' => 'rounded-none',
];

$classes = 'inline-flex items-center justify-center font-medium whitespace-nowrap'
    . ' ' . ($variantClasses[$style][$variant] ?? $variantClasses['soft']['primary'])
    . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md'])
    . ' ' . ($roundedClasses[$rounded] ?? $roundedClasses['full']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
