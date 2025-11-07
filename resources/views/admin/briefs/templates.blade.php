@extends('layouts.admin')

@section('title', 'Templates de Briefs')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Templates de Briefs</h1>
            <p class="text-gray-600 mt-1">Modèles prédéfinis pour créer rapidement des briefs</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.briefs.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                ← Retour aux briefs
            </a>
            <a href="{{ route('admin.briefs.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <span>➕</span>
                <span>Nouveau Brief</span>
            </a>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="text-2xl">💡</span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Comment utiliser les templates ?</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Les templates permettent de créer rapidement des briefs en pré-remplissant la structure, les exigences de contenu et les objectifs SEO. Cliquez sur "Utiliser ce template" pour créer un nouveau brief basé sur le modèle.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $template->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $template->getTypeLabel() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            @if($template->is_active)
                                <span class="w-2 h-2 bg-green-400 rounded-full" title="Actif"></span>
                            @else
                                <span class="w-2 h-2 bg-gray-400 rounded-full" title="Inactif"></span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">{{ $template->description }}</p>
                </div>

                <!-- Stats -->
                <div class="p-6 bg-gray-50">
                    <div class="grid grid-cols-3 gap-4 text-center mb-4">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $template->min_words }}</div>
                            <div class="text-xs text-gray-500">Mots min</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ $template->target_score }}</div>
                            <div class="text-xs text-gray-500">Score cible</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $template->usage_count }}</div>
                            <div class="text-xs text-gray-500">Utilisations</div>
                        </div>
                    </div>

                    <!-- Content Preview -->
                    @if($template->content_requirements)
                        <div class="mb-4">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase mb-2">Sections incluses:</h4>
                            @if(isset($template->content_requirements['sections']))
                                <div class="text-xs text-gray-600 space-y-1">
                                    @foreach(array_slice(array_keys($template->content_requirements['sections']), 0, 3) as $section)
                                        <div class="flex items-center gap-1">
                                            <span class="text-blue-500">•</span>
                                            <span>{{ $section }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($template->content_requirements['sections']) > 3)
                                        <div class="text-gray-400 italic">
                                            + {{ count($template->content_requirements['sections']) - 3 }} autres...
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Keywords Preview -->
                    @if($template->keywords && count($template->keywords) > 0)
                        <div class="mb-4">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase mb-2">Mots-clés:</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($template->keywords, 0, 3) as $keyword)
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">
                                        {{ $keyword }}
                                    </span>
                                @endforeach
                                @if(count($template->keywords) > 3)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                        +{{ count($template->keywords) - 3 }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Meta Info -->
                    <div class="text-xs text-gray-500 mb-4">
                        Créé par {{ $template->createdBy->name ?? 'Système' }}
                        <br>
                        Le {{ $template->created_at->format('d/m/Y') }}
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.briefs.create-from-template', $template) }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center justify-center gap-2">
                                <span>📝</span>
                                <span>Utiliser ce template</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Full Details Expandable -->
                <div class="border-t border-gray-200">
                    <button onclick="toggleDetails{{ $template->id }}()"
                            class="w-full px-6 py-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition">
                        <span id="toggle-text-{{ $template->id }}">Voir les détails ↓</span>
                    </button>
                    <div id="details-{{ $template->id }}" class="hidden px-6 pb-6">
                        <!-- Full Sections List -->
                        @if(isset($template->content_requirements['sections']))
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Toutes les sections:</h4>
                                <div class="space-y-2 text-sm">
                                    @foreach($template->content_requirements['sections'] as $section => $desc)
                                        <div class="bg-gray-50 p-2 rounded">
                                            <div class="font-medium text-gray-900">{{ $section }}</div>
                                            <div class="text-xs text-gray-600">{{ $desc }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tone -->
                        @if(isset($template->content_requirements['tone']) || isset($template->content_requirements['ton']))
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Ton & Style:</h4>
                                <div class="bg-blue-50 p-2 rounded text-sm text-gray-700">
                                    {{ $template->content_requirements['tone'] ?? $template->content_requirements['ton'] }}
                                </div>
                            </div>
                        @endif

                        <!-- SEO Requirements -->
                        @if($template->seo_requirements)
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Exigences SEO:</h4>
                                <div class="bg-purple-50 p-2 rounded text-sm">
                                    <pre class="text-xs text-gray-700">{{ json_encode($template->seo_requirements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif

                        <!-- All Keywords -->
                        @if($template->keywords && count($template->keywords) > 0)
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Tous les mots-clés:</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($template->keywords as $keyword)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">
                                            {{ $keyword }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <script>
                function toggleDetails{{ $template->id }}() {
                    const details = document.getElementById('details-{{ $template->id }}');
                    const toggleText = document.getElementById('toggle-text-{{ $template->id }}');
                    if (details.classList.contains('hidden')) {
                        details.classList.remove('hidden');
                        toggleText.textContent = 'Masquer les détails ↑';
                    } else {
                        details.classList.add('hidden');
                        toggleText.textContent = 'Voir les détails ↓';
                    }
                }
            </script>
        @empty
            <div class="col-span-3 text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun template disponible</h3>
                <p class="text-gray-600 mb-6">Les templates seront créés automatiquement lors du premier seed de la base de données.</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-2xl mx-auto">
                    <p class="text-sm text-yellow-800">
                        <strong>Pour créer les templates par défaut, exécutez:</strong>
                    </p>
                    <code class="block mt-2 bg-yellow-100 p-2 rounded text-sm text-yellow-900">
                        php artisan db:seed --class=BriefTemplateSeeder
                    </code>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection
