@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,              // slot nommé possible
    'iconColor' => 'primary',    // primary, green, purple, yellow, red, blue
    'valueColor' => null,        // Si null, utilise text-text-primary
])

@php
$iconBgClasses = [
    'primary' => 'bg-primary/10',
    'green' => 'bg-green-100',
    'purple' => 'bg-purple-100',
    'yellow' => 'bg-yellow-100',
    'red' => 'bg-red-100',
    'blue' => 'bg-blue-100',
];

$iconTextClasses = [
    'primary' => 'text-primary',
    'green' => 'text-green-600',
    'purple' => 'text-purple-600',
    'yellow' => 'text-yellow-600',
    'red' => 'text-red-600',
    'blue' => 'text-blue-600',
];

$iconBg = $iconBgClasses[$iconColor] ?? $iconBgClasses['primary'];
$iconText = $iconTextClasses[$iconColor] ?? $iconTextClasses['primary'];
$valueColorClass = $valueColor ?? 'text-text-primary';
@endphp

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs sm:text-sm text-text-secondary">{{ $title }}</p>
            <p class="text-xl sm:text-2xl font-bold {{ $valueColorClass }} mt-1 truncate">
                {{ $value }}
            </p>
            @if($subtitle)
            <p class="text-xs text-text-secondary mt-1">
                {{ $subtitle }}
            </p>
            @endif
        </div>

        @if($icon || isset($iconSlot))
        <div class="flex-shrink-0 ml-3 sm:ml-4">
            <div class="h-10 w-10 sm:h-12 sm:w-12 {{ $iconBg }} rounded-full flex items-center justify-center">
                @if(isset($iconSlot))
                    {{ $iconSlot }}
                @else
                    <div class="{{ $iconText }}">
                        {{ $icon }}
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
