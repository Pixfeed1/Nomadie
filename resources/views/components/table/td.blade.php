@props([])

<td {{ $attributes->merge(['class' => 'px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900']) }}>
    {{ $slot }}
</td>
