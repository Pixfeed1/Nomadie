@props([
    'variant' => 'full',      // full | compact
    'action' => null,
    'destinations' => [],
    'showAdvanced' => true,
])

@php
$formAction = $action ?? route('search');
@endphp

<div class="bg-white shadow-md relative z-10 {{ $variant === 'full' ? '-mt-8 mb-12' : '' }} rounded-lg max-w-6xl mx-auto">
    <div class="p-4 sm:p-6">
        <form action="{{ $formAction }}" method="GET" class="grid grid-cols-1 {{ $variant === 'full' ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-3 sm:gap-4">
            {{-- Destination --}}
            <div>
                <label for="destination" class="block text-xs sm:text-sm font-medium text-text-secondary mb-1">
                    Destination
                </label>
                <select id="destination"
                        name="destination"
                        class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary text-sm p-2 sm:p-2.5">
                    <option value="">Toutes les destinations</option>
                    @foreach($destinations as $destination)
                        @if(is_array($destination))
                            <option value="{{ $destination['slug'] ?? $destination['id'] }}" {{ request('destination') == ($destination['slug'] ?? $destination['id']) ? 'selected' : '' }}>
                                {{ $destination['name'] }}
                            </option>
                        @else
                            <option value="{{ $destination->slug ?? $destination->id }}" {{ request('destination') == ($destination->slug ?? $destination->id) ? 'selected' : '' }}>
                                {{ $destination->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            @if($variant === 'full')
            {{-- Date de départ --}}
            <div>
                <label for="date" class="block text-xs sm:text-sm font-medium text-text-secondary mb-1">
                    Date de départ
                </label>
                <input type="date"
                       id="date"
                       name="date"
                       value="{{ request('date') }}"
                       class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary text-sm p-2 sm:p-2.5">
            </div>
            @endif

            {{-- Voyageurs --}}
            <div>
                <label for="travelers" class="block text-xs sm:text-sm font-medium text-text-secondary mb-1">
                    Voyageurs
                </label>
                <select id="travelers"
                        name="travelers"
                        class="block w-full rounded-md border-border shadow-sm focus:border-primary focus:ring-primary text-sm p-2 sm:p-2.5">
                    <option value="1" {{ request('travelers') == '1' ? 'selected' : '' }}>1 voyageur</option>
                    <option value="2" {{ request('travelers') == '2' ? 'selected' : '' }}>2 voyageurs</option>
                    <option value="3" {{ request('travelers') == '3' ? 'selected' : '' }}>3 voyageurs</option>
                    <option value="4" {{ request('travelers') == '4' ? 'selected' : '' }}>4 voyageurs</option>
                    <option value="5+" {{ request('travelers') == '5+' ? 'selected' : '' }}>5+ voyageurs</option>
                </select>
            </div>

            {{-- Bouton de recherche --}}
            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 sm:py-2.5 px-3 sm:px-4 rounded-md transition-colors">
                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4 sm:h-5 sm:w-5 mr-2"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-sm sm:text-base">Rechercher</span>
                    </div>
                </button>
            </div>
        </form>

        {{-- Lien recherche avancée --}}
        @if($showAdvanced)
        <div class="mt-3 text-right">
            <a href="{{ route('search.advanced') }}"
               class="text-xs sm:text-sm text-primary hover:text-primary-dark font-medium flex items-center justify-end">
                Recherche avancée
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-3 w-3 sm:h-4 sm:w-4 ml-1"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        @endif
    </div>
</div>
