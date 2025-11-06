@props([
    'name' => '',
    'label' => null,
    'options' => [],            // Array d'options : ['value' => 'label'] ou [['value' => 'x', 'label' => 'y']]
    'selected' => null,
    'placeholder' => 'Sélectionnez...',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
])

@php
$selectId = $attributes->get('id') ?? $name;
$hasError = $error || $errors->has($name);
$errorMessage = $error ?? $errors->first($name);

$selectClasses = 'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors'
    . ($hasError ? ' border-error' : ' border-border');

$labelClasses = 'block text-sm font-medium text-text-primary mb-1';
$selectedValue = old($name, $selected);
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $selectId }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        class="{{ $selectClasses }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'id']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $label)
            @if(is_array($label))
                {{-- Format: [['value' => 'x', 'label' => 'y']] --}}
                <option value="{{ $label['value'] }}" {{ $selectedValue == $label['value'] ? 'selected' : '' }}>
                    {{ $label['label'] }}
                </option>
            @else
                {{-- Format: ['value' => 'label'] --}}
                <option value="{{ $value }}" {{ $selectedValue == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endif
        @endforeach
    </select>

    @if($hint && !$hasError)
        <p class="text-xs text-text-secondary mt-1">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="text-xs text-error mt-1">{{ $errorMessage }}</p>
    @endif
</div>
