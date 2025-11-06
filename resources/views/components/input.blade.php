@props([
    'type' => 'text',           // text, email, password, number, tel, url, date
    'name' => '',
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,            // Message d'erreur (optionnel)
    'hint' => null,             // Texte d'aide (optionnel)
])

@php
$inputId = $attributes->get('id') ?? $name;
$hasError = $error || $errors->has($name);
$errorMessage = $error ?? $errors->first($name);

$inputClasses = 'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors'
    . ($hasError ? ' border-error' : ' border-border');

$labelClasses = 'block text-sm font-medium text-text-primary mb-1';
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $inputId }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="{{ $inputClasses }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'id']) }}
    >

    @if($hint && !$hasError)
        <p class="text-xs text-text-secondary mt-1">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="text-xs text-error mt-1">{{ $errorMessage }}</p>
    @endif
</div>
