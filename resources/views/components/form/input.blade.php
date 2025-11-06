@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'error' => null,
    'disabled' => false,
    'readonly' => false,
])

@php
$inputClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm sm:text-base p-2 sm:p-2.5 transition-colors';

if($errors->has($name) || $error) {
    $inputClasses .= ' border-red-500 focus:border-red-500 focus:ring-red-500';
}

if($disabled) {
    $inputClasses .= ' bg-gray-100 cursor-not-allowed';
}
@endphp

<div class="mb-4">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => $inputClasses]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
    >

    @if($errors->has($name))
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($help)
        <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>
