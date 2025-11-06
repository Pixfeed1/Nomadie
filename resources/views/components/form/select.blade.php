@props([
    'name',
    'label' => null,
    'options' => [],         // ['value' => 'label'] ou [[value, label]]
    'selected' => null,
    'placeholder' => 'Sélectionnez...',
    'required' => false,
    'disabled' => false,
    'help' => null,
    'error' => null,
])

@php
$selectClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm sm:text-base p-2 sm:p-2.5 transition-colors';

if($errors->has($name) || $error) {
    $selectClasses .= ' border-red-500 focus:border-red-500 focus:ring-red-500';
}

if($disabled) {
    $selectClasses .= ' bg-gray-100 cursor-not-allowed';
}

$selectedValue = old($name, $selected);
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => $selectClasses]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $label)
            @if(is_array($label))
                {{-- Format: [['value' => 1, 'label' => 'Option 1']] --}}
                <option value="{{ $label['value'] ?? $value }}"
                        {{ ($selectedValue == ($label['value'] ?? $value)) ? 'selected' : '' }}>
                    {{ $label['label'] ?? $label['value'] ?? $value }}
                </option>
            @else
                {{-- Format: ['value' => 'label'] --}}
                <option value="{{ $value }}" {{ ($selectedValue == $value) ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endif
        @endforeach
    </select>

    @if($errors->has($name))
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $errors->first($name) }}</p>
    @elseif($error)
        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($help)
        <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>
