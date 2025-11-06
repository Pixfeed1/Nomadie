@props([
    'text' => null,           // Texte simple (optionnel si on utilise le slot)
    'icon' => 'check',        // check ou check-circle
    'color' => 'primary',     // primary, accent, success, etc.
])

@php
$colorClasses = [
    'primary' => 'text-primary bg-primary/10',
    'accent' => 'text-accent bg-accent/10',
    'success' => 'text-success bg-success/10',
    'error' => 'text-error bg-error/10',
    'warning' => 'text-warning bg-warning/10',
];

$colors = $colorClasses[$color] ?? $colorClasses['primary'];
list($textColor, $bgColor) = explode(' ', $colors);

// Map des icônes
$icons = [
    'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
    'check-circle' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
];

$iconPath = $icons[$icon] ?? $icons['check'];
$isFilled = $icon === 'check-circle';
@endphp

<div class="flex items-center" {{ $attributes }}>
    <div class="flex-shrink-0 w-8 h-8 {{ $bgColor }} rounded-full flex items-center justify-center mr-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $textColor }}" fill="{{ $isFilled ? 'currentColor' : 'none' }}" viewBox="0 0 20 20" stroke="currentColor">
            {!! $iconPath !!}
        </svg>
    </div>
    @if($text)
        <span class="text-sm text-text-secondary">{{ $text }}</span>
    @else
        <div class="text-sm text-text-secondary">{{ $slot }}</div>
    @endif
</div>
