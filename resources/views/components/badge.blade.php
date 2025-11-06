@props([
    'variant' => 'primary',  // primary, success, warning, danger, info, gray
    'size' => 'md',          // sm, md, lg
    'rounded' => 'full',     // full, md, sm, none
])

@php
$variantClasses = [
    'primary' => 'bg-primary/10 text-primary',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-blue-100 text-blue-800',
    'gray' => 'bg-gray-100 text-gray-800',
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
    . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary'])
    . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md'])
    . ' ' . ($roundedClasses[$rounded] ?? $roundedClasses['full']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
