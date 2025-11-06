@props([
    'rating' => 0,           // 0-5 (peut être décimal: 4.5)
    'size' => 'md',          // sm, md, lg
    'showValue' => true,
    'count' => null,         // Nombre d'avis (optionnel)
    'interactive' => false,  // Si true, permet la sélection
])

@php
$sizeClasses = [
    'sm' => 'h-3 w-3 sm:h-4 sm:w-4',
    'md' => 'h-4 w-4 sm:h-5 sm:w-5',
    'lg' => 'h-5 w-5 sm:h-6 sm:w-6',
];

$textSizeClasses = [
    'sm' => 'text-xs sm:text-sm',
    'md' => 'text-sm sm:text-base',
    'lg' => 'text-base sm:text-lg',
];

$starSize = $sizeClasses[$size] ?? $sizeClasses['md'];
$textSize = $textSizeClasses[$size] ?? $textSizeClasses['md'];

$fullStars = floor($rating);
$hasHalfStar = ($rating - $fullStars) >= 0.5;
$emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
@endphp

<div class="flex items-center gap-1 sm:gap-2" {{ $attributes }}>
    {{-- Stars --}}
    <div class="flex items-center gap-0.5">
        {{-- Full stars --}}
        @for($i = 0; $i < $fullStars; $i++)
        <svg class="{{ $starSize }} text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        @endfor

        {{-- Half star --}}
        @if($hasHalfStar)
        <svg class="{{ $starSize }} text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <defs>
                <linearGradient id="half-{{ $size }}">
                    <stop offset="50%" stop-color="currentColor"/>
                    <stop offset="50%" stop-color="#D1D5DB" stop-opacity="1"/>
                </linearGradient>
            </defs>
            <path fill="url(#half-{{ $size }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        @endif

        {{-- Empty stars --}}
        @for($i = 0; $i < $emptyStars; $i++)
        <svg class="{{ $starSize }} text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        @endfor
    </div>

    {{-- Rating value --}}
    @if($showValue)
    <span class="{{ $textSize }} font-medium text-gray-700">
        {{ number_format($rating, 1) }}
    </span>
    @endif

    {{-- Review count --}}
    @if($count !== null)
    <span class="{{ $textSize }} text-gray-500">
        ({{ $count }})
    </span>
    @endif
</div>
