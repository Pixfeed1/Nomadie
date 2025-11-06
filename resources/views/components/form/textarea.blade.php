@props([
    'name',
    'label' => null,
    'value' => '',
    'rows' => 4,
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'error' => null,
    'disabled' => false,
    'readonly' => false,
    'maxlength' => null,
])

@php
$textareaClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm sm:text-base p-2 sm:p-2.5 transition-colors resize-y';

if($errors->has($name) || $error) {
    $textareaClasses .= ' border-red-500 focus:border-red-500 focus:ring-red-500';
}

if($disabled) {
    $textareaClasses .= ' bg-gray-100 cursor-not-allowed';
}
@endphp

<div class="mb-4">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
        @if($maxlength)
            <span class="text-xs text-gray-500 ml-1">(max {{ $maxlength }} caractères)</span>
        @endif
    </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $textareaClasses]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
    >{{ old($name, $value) }}</textarea>

    @if($errors->has($name))
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($help)
        <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>
