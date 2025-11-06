@props([
    'name' => '',
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
    'maxlength' => null,
])

@php
$textareaId = $attributes->get('id') ?? $name;
$hasError = $error || $errors->has($name);
$errorMessage = $error ?? $errors->first($name);

$textareaClasses = 'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-vertical'
    . ($hasError ? ' border-error' : ' border-border');

$labelClasses = 'block text-sm font-medium text-text-primary mb-1';
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $textareaId }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="{{ $textareaClasses }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $maxlength ? 'maxlength=' . $maxlength : '' }}
        {{ $attributes->except(['class', 'id']) }}
    >{{ old($name, $value) }}</textarea>

    @if($hint && !$hasError)
        <p class="text-xs text-text-secondary mt-1">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="text-xs text-error mt-1">{{ $errorMessage }}</p>
    @endif
</div>
