@props([
    'type' => 'info',        // success, error, warning, info
    'dismissible' => true,
    'icon' => true,
])

@php
$config = [
    'success' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-200',
        'text' => 'text-green-800',
        'icon_color' => 'text-green-400',
        'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    'error' => [
        'bg' => 'bg-red-50',
        'border' => 'border-red-200',
        'text' => 'text-red-800',
        'icon_color' => 'text-red-400',
        'icon_path' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    'warning' => [
        'bg' => 'bg-yellow-50',
        'border' => 'border-yellow-200',
        'text' => 'text-yellow-800',
        'icon_color' => 'text-yellow-400',
        'icon_path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    ],
    'info' => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'text' => 'text-blue-800',
        'icon_color' => 'text-blue-400',
        'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
];

$current = $config[$type] ?? $config['info'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-md border {$current['bg']} {$current['border']} p-3 sm:p-4 mb-4"]) }}
     x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">
    <div class="flex flex-wrap sm:flex-nowrap items-start">
        @if($icon)
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $current['icon_color'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $current['icon_path'] }}" />
            </svg>
        </div>
        @endif

        <div class="ml-0 sm:ml-3 mt-2 sm:mt-0 flex-1 w-full sm:w-auto">
            <div class="text-sm sm:text-base font-medium {{ $current['text'] }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
        <div class="ml-auto pl-3">
            <button @click="show = false"
                    type="button"
                    class="inline-flex rounded-md p-1.5 {{ $current['text'] }} hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent">
                <span class="sr-only">Fermer</span>
                <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        @endif
    </div>
</div>
