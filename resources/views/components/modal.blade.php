@props([
    'name',
    'title' => null,
    'size' => 'md',          // sm, md, lg, xl, full
    'closeButton' => true,
    'show' => false,
])

@php
$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md sm:max-w-lg',
    'lg' => 'max-w-lg sm:max-w-2xl',
    'xl' => 'max-w-xl sm:max-w-4xl',
    'full' => 'max-w-full mx-4',
];

$maxWidth = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div x-data="{ show: @js($show) }"
     x-show="show"
     @{{ $name }}.window="show = true"
     @keydown.escape.window="show = false"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
    </div>

    {{-- Modal Container --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full {{ $maxWidth }}">

            {{-- Header --}}
            @if($title || $closeButton)
            <div class="bg-white px-4 sm:px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    @if($title)
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900" id="modal-title">
                        {{ $title }}
                    </h3>
                    @endif

                    @if($closeButton)
                    <button @click="show = false"
                            type="button"
                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 ml-auto">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Body --}}
            <div class="bg-white px-4 sm:px-6 py-4 sm:py-5">
                {{ $slot }}
            </div>

            {{-- Footer (optionnel via slot nommé) --}}
            @isset($footer)
            <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                {{ $footer }}
            </div>
            @endisset
        </div>
    </div>
</div>
