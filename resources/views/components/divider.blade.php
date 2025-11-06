@props([
    'text' => null,             // Texte au milieu du divider (optionnel)
    'color' => 'border',        // Couleur de la ligne
])

@php
$lineColor = $color === 'border' ? 'border-border' : "border-{$color}";
@endphp

<div class="relative flex py-3 items-center" {{ $attributes }}>
    <div class="flex-grow border-t {{ $lineColor }}"></div>

    @if($text)
        <span class="flex-shrink mx-4 text-text-secondary text-sm">{{ $text }}</span>
        <div class="flex-grow border-t {{ $lineColor }}"></div>
    @endif
</div>
