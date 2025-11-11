@extends('layouts.admin')

@section('title', 'Gestion des Pays')

@section('header-left')
    <h1 class="text-2xl font-bold text-text-primary">Pays et Destinations</h1>
    <p class="text-sm text-text-secondary mt-1">Gérez les images d'arrière-plan des pays</p>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats globales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Total Pays</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ number_format($stats['total_countries']) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Pays avec Expériences</p>
            <p class="text-3xl font-bold text-success mt-2">{{ number_format($stats['countries_with_trips']) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <p class="text-sm text-text-secondary">Pays avec Images</p>
            <p class="text-3xl font-bold text-accent mt-2">{{ number_format($stats['countries_with_images']) }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du pays..." class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Filtrer</label>
                <select name="with_trips" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Tous les pays</option>
                    <option value="1" {{ request('with_trips') ? 'selected' : '' }}>Avec expériences uniquement</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des pays -->
    <div class="bg-white rounded-lg shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Pays</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Expériences</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse($countries as $country)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-text-primary">{{ $country->name }}</p>
                            @if($country->description)
                            <p class="text-xs text-text-secondary mt-1">{{ Str::limit($country->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($country->image)
                            <div class="flex justify-center">
                                <img src="{{ Storage::url($country->image) }}" alt="{{ $country->name }}" class="h-16 w-24 rounded-lg object-cover">
                            </div>
                            @else
                            <span class="text-xs text-text-secondary">Aucune image</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-text-primary font-medium">
                            {{ $country->trips_count }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.countries.edit', $country) }}" class="text-primary hover:text-primary-dark font-medium text-sm">
                                Modifier
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-text-secondary">
                            Aucun pays trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border">
            {{ $countries->links() }}
        </div>
    </div>
</div>
@endsection
