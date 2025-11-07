@extends('layouts.admin')

@section('title', 'Validation Rédacteur - ' . $writer->name)

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.writers.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Retour à la liste
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                @if($writer->avatar)
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ asset('storage/' . $writer->avatar) }}" alt="{{ $writer->name }}">
                @else
                    <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-semibold">
                        {{ substr($writer->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $writer->name }}</h1>
                    <p class="text-gray-600">{{ $writer->email }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium
                            {{ $writer->isCommunityWriter() ? 'bg-green-100 text-green-800' : '' }}
                            {{ $writer->isClientContributor() ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $writer->isPartner() ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ $writer->getWriterTypeLabel() }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $writer->writer_status === 'pending_validation' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $writer->writer_status === 'validated' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $writer->writer_status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $writer->writer_status === 'suspended' ? 'bg-orange-100 text-orange-800' : '' }}">
                            {{ $writer->getWriterStatusLabel() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-2">
                @if($writer->isPendingWriter() || $writer->isRejectedWriter() || $writer->isSuspendedWriter())
                    <form method="POST" action="{{ route('admin.writers.validate', $writer->id) }}" onsubmit="return confirm('Confirmer la validation de ce rédacteur ?');">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                            ✅ Valider
                        </button>
                    </form>
                @endif

                @if($writer->isPendingWriter() || $writer->isValidatedWriter())
                    <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                        ❌ Refuser
                    </button>
                @endif

                @if($writer->isValidatedWriter())
                    <button onclick="document.getElementById('suspendModal').classList.remove('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700 transition">
                        🚫 Suspendre
                    </button>
                @endif

                @if($writer->isRejectedWriter() || $writer->isSuspendedWriter())
                    <form method="POST" action="{{ route('admin.writers.restore', $writer->id) }}" onsubmit="return confirm('Restaurer ce compte en statut Pending ?');">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            🔄 Restaurer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Motivation -->
            @if($writer->writer_notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💬 Motivation / Notes</h3>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap">{{ $writer->writer_notes }}</div>
                </div>
            @endif

            <!-- Article Test (Community Writer) -->
            @if($writer->isCommunityWriter())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Article Test</h3>
                    @if($testArticle)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $testArticle->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">Publié le {{ $testArticle->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                @if($testArticle->latestSeoAnalysis)
                                    <div class="text-right">
                                        <div class="text-2xl font-bold {{ $testArticle->latestSeoAnalysis->global_score >= 78 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $testArticle->latestSeoAnalysis->global_score }}/100
                                        </div>
                                        <div class="text-xs text-gray-500">Score SEO</div>
                                    </div>
                                @endif
                            </div>
                            <p class="text-gray-700 text-sm mb-4">{{ $testArticle->excerpt }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>{{ $testArticle->reading_time }} min de lecture</span>
                                <a href="{{ route('articles.show', $testArticle->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                                    Lire l'article →
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun article test soumis pour le moment.</p>
                    @endif
                </div>
            @endif

            <!-- Verified Booking (Client-Contributor) -->
            @if($writer->isClientContributor() && $verifiedBooking)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">✈️ Réservation Vérifiée</h3>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $verifiedBooking->trip->title ?? 'Voyage' }}</h4>
                                <p class="text-sm text-gray-500">Réservation #{{ $verifiedBooking->id }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $verifiedBooking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($verifiedBooking->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Date réservation:</strong> {{ $verifiedBooking->created_at->format('d/m/Y') }}</p>
                            <p><strong>Montant:</strong> {{ number_format($verifiedBooking->total_amount, 2) }} €</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Partner Offer -->
            @if($writer->isPartner() && $writer->partner_offer_url)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🤝 Offre Commerciale</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <a href="{{ $writer->partner_offer_url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                            {{ $writer->partner_offer_url }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- All Articles -->
            @if($writer->articles()->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📚 Articles publiés ({{ $writer->articles()->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($writer->articles()->with('latestSeoAnalysis')->latest()->limit(5)->get() as $article)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900">{{ $article->title }}</h5>
                                        <p class="text-xs text-gray-500 mt-1">{{ $article->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if($article->latestSeoAnalysis)
                                        <span class="ml-3 text-sm font-semibold {{ $article->latestSeoAnalysis->global_score >= 78 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $article->latestSeoAnalysis->global_score }}/100
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Stats & Notes -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Articles publiés</span>
                            <span class="font-semibold">{{ $writer->articles()->count() }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Badges débloqués</span>
                            <span class="font-semibold">{{ $writer->badges()->count() }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Statut DoFollow</span>
                            <span class="font-semibold {{ $writer->is_dofollow ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $writer->is_dofollow ? '✅ Oui' : '❌ Non' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Membre depuis</span>
                            <span class="font-semibold">{{ $writer->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes Admin</h3>
                <form method="POST" action="{{ route('admin.writers.update-notes', $writer->id) }}">
                    @csrf
                    <textarea name="notes" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Notes internes (visibles uniquement par les admins)...">{{ $writer->writer_notes }}</textarea>
                    <button type="submit" class="mt-3 w-full bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-700 transition">
                        Sauvegarder les notes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Refuser la candidature</h3>
            <form method="POST" action="{{ route('admin.writers.reject', $writer->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du refus (sera communiquée au rédacteur)
                    </label>
                    <textarea name="reason" id="reject_reason" rows="4" class="w-full border-gray-300 rounded-lg" placeholder="Ex: Article test ne respecte pas nos critères de qualité..." required></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700">
                        Confirmer le refus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Suspendre le compte rédacteur</h3>
            <form method="POST" action="{{ route('admin.writers.suspend', $writer->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="suspend_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de la suspension (sera communiquée au rédacteur)
                    </label>
                    <textarea name="reason" id="suspend_reason" rows="4" class="w-full border-gray-300 rounded-lg" placeholder="Ex: Contenu promotionnel excessif détecté..." required></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('suspendModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700">
                        Confirmer la suspension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
