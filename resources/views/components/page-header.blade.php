@props([
    'title' => '',
    'subtitle' => null,
    'gradient' => true,         // true pour gradient primary, false pour simple
    'textColor' => 'white',     // white ou dark
])

@php
$headerClasses = $gradient
    ? 'bg-gradient-to-r from-primary to-primary-dark text-white'
    : 'bg-bg-alt text-text-primary';

$titleColor = $textColor === 'white' ? 'text-white' : 'text-text-primary';
$subtitleColor = $textColor === 'white' ? 'text-white/80' : 'text-text-secondary';
@endphp

<div class="{{ $headerClasses }} p-6" {{ $attributes }}>
    <h1 class="text-2xl font-bold {{ $titleColor }}">{{ $title }}</h1>

    @if($subtitle)
        <p class="{{ $subtitleColor }} mt-2">{{ $subtitle }}</p>
    @endif

    {{ $slot }}
</div>
