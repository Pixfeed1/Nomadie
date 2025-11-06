@props([
    'sortable' => false,
    'direction' => null,  // 'asc' | 'desc'
])

<th {{ $attributes->merge(['class' => 'px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider']) }}>
    @if($sortable)
        <button class="group inline-flex items-center space-x-1 hover:text-gray-700">
            <span>{{ $slot }}</span>
            @if($direction === 'asc')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 10l5-5 5 5H5z"/>
                </svg>
            @elseif($direction === 'desc')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M15 10l-5 5-5-5h10z"/>
                </svg>
            @else
                <svg class="h-4 w-4 opacity-0 group-hover:opacity-50" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 10l5-5 5 5H5z"/>
                </svg>
            @endif
        </button>
    @else
        {{ $slot }}
    @endif
</th>
