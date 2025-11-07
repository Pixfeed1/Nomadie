@extends('layouts.admin')

@section('title', 'Gestion des Rédacteurs')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Rédacteurs</h1>
            <p class="text-gray-600 mt-1">Valider, refuser ou gérer les candidatures rédacteurs</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-yellow-700 font-medium">En attente</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
                </div>
                <div class="text-3xl">⏳</div>
            </div>
        </div>

        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-green-700 font-medium">Validés</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['validated'] }}</p>
                </div>
                <div class="text-3xl">✅</div>
            </div>
        </div>

        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-red-700 font-medium">Refusés</p>
                    <p class="text-2xl font-bold text-red-900">{{ $stats['rejected'] }}</p>
                </div>
                <div class="text-3xl">❌</div>
            </div>
        </div>

        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-orange-700 font-medium">Suspendus</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $stats['suspended'] }}</p>
                </div>
                <div class="text-3xl">🚫</div>
            </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Total</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Filtrer par statut :</span>
            <div class="flex gap-2">
                <a href="{{ route('admin.writers.index', ['status' => 'pending']) }}"
                   class="px-4 py-2 rounded-lg {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    En attente ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('admin.writers.index', ['status' => 'validated']) }}"
                   class="px-4 py-2 rounded-lg {{ $status === 'validated' ? 'bg-green-100 text-green-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Validés ({{ $stats['validated'] }})
                </a>
                <a href="{{ route('admin.writers.index', ['status' => 'rejected']) }}"
                   class="px-4 py-2 rounded-lg {{ $status === 'rejected' ? 'bg-red-100 text-red-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Refusés ({{ $stats['rejected'] }})
                </a>
                <a href="{{ route('admin.writers.index', ['status' => 'suspended']) }}"
                   class="px-4 py-2 rounded-lg {{ $status === 'suspended' ? 'bg-orange-100 text-orange-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Suspendus ({{ $stats['suspended'] }})
                </a>
                <a href="{{ route('admin.writers.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-lg {{ $status === 'all' ? 'bg-blue-100 text-blue-800 font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Tous ({{ $stats['total'] }})
                </a>
            </div>
        </div>
    </div>

    <!-- Writers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rédacteur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Articles
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date candidature
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($writers as $writer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($writer->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $writer->avatar) }}" alt="{{ $writer->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($writer->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $writer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $writer->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $writer->isCommunityWriter() ? 'bg-green-100 text-green-800' : '' }}
                                {{ $writer->isClientContributor() ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $writer->isPartner() ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $writer->isTeamMember() ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                @if($writer->isCommunityWriter())
                                    🌱
                                @elseif($writer->isClientContributor())
                                    ✈️
                                @elseif($writer->isPartner())
                                    🤝
                                @elseif($writer->isTeamMember())
                                    👑
                                @endif
                                {{ $writer->getWriterTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $writer->writer_status === 'pending_validation' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $writer->writer_status === 'validated' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $writer->writer_status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $writer->writer_status === 'suspended' ? 'bg-orange-100 text-orange-800' : '' }}">
                                {{ $writer->getWriterStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $writer->articles()->count() }} article(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $writer->created_at->format('d/m/Y') }}
                            <div class="text-xs text-gray-400">{{ $writer->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.writers.show', $writer->id) }}" class="text-blue-600 hover:text-blue-900">
                                Examiner →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-4xl mb-2">📝</div>
                            <p>Aucun rédacteur avec ce statut</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($writers->hasPages())
        <div class="mt-6">
            {{ $writers->links() }}
        </div>
    @endif
</div>
@endsection
