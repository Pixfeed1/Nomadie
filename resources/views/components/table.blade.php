@props([
    'striped' => true,
    'hoverable' => true,
    'responsive' => true,
])

@php
$tableClasses = 'min-w-full divide-y divide-gray-200';
$tbodyClasses = 'bg-white divide-y divide-gray-200';

if($striped) {
    $tbodyClasses = 'bg-white';
}
@endphp

<div class="{{ $responsive ? 'overflow-x-auto -mx-4 sm:mx-0' : '' }}">
    <div class="{{ $responsive ? 'inline-block min-w-full align-middle' : '' }}">
        <div class="{{ $responsive ? 'overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg' : 'shadow overflow-hidden border-b border-gray-200 sm:rounded-lg' }}">
            <table {{ $attributes->merge(['class' => $tableClasses]) }}>
                {{-- Header --}}
                @isset($head)
                <thead class="bg-gray-50">
                    {{ $head }}
                </thead>
                @endisset

                {{-- Body --}}
                <tbody class="{{ $tbodyClasses }}">
                    @if($striped)
                        @php $rowIndex = 0; @endphp
                        @foreach($slot->toHtml() ? [$slot] : [] as $row)
                            {{ $slot }}
                        @endforeach
                    @else
                        {{ $slot }}
                    @endif
                </tbody>

                {{-- Footer (optionnel) --}}
                @isset($footer)
                <tfoot class="bg-gray-50">
                    {{ $footer }}
                </tfoot>
                @endisset
            </table>
        </div>
    </div>
</div>

{{-- Styles pour striped et hoverable --}}
@if($striped || $hoverable)
<style>
    @if($striped)
    tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    @endif

    @if($hoverable)
    tbody tr:hover {
        background-color: #f3f4f6;
    }
    @endif
</style>
@endif
