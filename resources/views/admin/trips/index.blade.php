@extends('layouts.admin')

@section('title', 'Gestion des Expériences')

@section('header-left')
    <h1 class="text-2xl font-bold text-text-primary">Expériences</h1>
    <p class="text-sm text-text-secondary mt-1">Gérez toutes les expériences créées par les organisateurs</p>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats globales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Total Expériences</p>
                    <p class="text-3xl font-bold text-primary mt-2">{{ number_format($stats['total_trips']) }}</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Réservations</p>
                    <p class="text-3xl font-bold text-success mt-2">{{ number_format($stats['total_bookings']) }}</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Revenus Totaux</p>
                    <p class="text-3xl font-bold text-accent mt-2">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} €</p>
                </div>
                <div class="bg-accent/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Note Moyenne</p>
                    <p class="text-3xl font-bold text-text-primary mt-2">{{ number_format($stats['avg_rating'], 1) }} / 5</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, description..." class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Statut</label>
                <select name="status" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Type</label>
                <select name="offer_type" class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Tous</option>
                    <option value="organized_trip" {{ request('offer_type') === 'organized_trip' ? 'selected' : '' }}>Séjour</option>
                    <option value="activity" {{ request('offer_type') === 'activity' ? 'selected' : '' }}>Activité</option>
                    <option value="accommodation" {{ request('offer_type') === 'accommodation' ? 'selected' : '' }}>Hébergement</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des expériences -->
    <div class="bg-white rounded-lg shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Expérience</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Organisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Réservations</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Prix</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse($trips as $trip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($trip->main_image)
                                <img src="{{ Storage::url($trip->main_image) }}" alt="{{ $trip->title }}" class="h-12 w-12 rounded-lg object-cover mr-3">
                                @else
                                <div class="h-12 w-12 rounded-lg bg-gray-200 mr-3"></div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-text-primary">{{ Str::limit($trip->title, 40) }}</p>
                                    <p class="text-xs text-text-secondary">{{ $trip->destination->name ?? '-' }}, {{ $trip->country->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-text-primary">
                            {{ $trip->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $trip->offer_type === 'organized_trip' ? 'bg-blue-100 text-blue-800' : ($trip->offer_type === 'activity' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $trip->offer_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-text-primary font-medium">
                            {{ $trip->bookings_count }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-sm text-text-primary">{{ number_format($trip->rating, 1) }}</span>
                                <span class="text-xs text-text-secondary ml-1">({{ $trip->reviews_count }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-text-primary">
                            {{ number_format($trip->price, 0, ',', ' ') }} €
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $trip->status === 'active' ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($trip->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.trips.show', $trip) }}" class="text-primary hover:text-primary-dark font-medium text-sm">
                                Voir détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-text-secondary">
                            Aucune expérience trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border">
            {{ $trips->links() }}
        </div>
    </div>
</div>
@endsection
