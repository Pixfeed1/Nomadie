@extends('layouts.admin')

@section('title', 'Gestion des Clients')
@section('page-title', 'Clients')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Total Clients</p>
                    <p class="text-2xl font-bold text-text-primary">{{ $stats['total'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Actifs</p>
                    <p class="text-2xl font-bold text-success">{{ $stats['active'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-success/10 flex items-center justify-center text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-secondary">Suspendus</p>
                    <p class="text-2xl font-bold text-error">{{ $stats['suspended'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-error/10 flex items-center justify-center text-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" placeholder="Rechercher par nom ou email..."
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">

            <select name="status" class="px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendus</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors btn">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-border">
            <thead class="bg-bg-alt">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Inscription</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-border">
                @forelse($users as $user)
                <tr class="hover:bg-bg-alt transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-text-primary">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-text-primary">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-text-secondary">{{ $user->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->suspended_at)
                            <span class="badge badge-error">Suspendu</span>
                        @else
                            <span class="badge badge-success">Actif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-primary hover:text-primary-dark mr-3">Voir</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-text-secondary">Aucun client trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
