@extends('layouts.app')

@section('title', 'Candidature en attente - Nomadie')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Candidature en cours de traitement</h1>
            <p class="text-gray-600">Merci pour votre intérêt à rejoindre la communauté Nomadie !</p>
        </div>

        <!-- Status selon le type de rédacteur -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-l-4 border-blue-500">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-3xl">
                        @if($user->isCommunityWriter())
                            🌱
                        @elseif($user->isClientContributor())
                            ✈️
                        @elseif($user->isPartner())
                            🤝
                        @endif
                    </span>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Statut : {{ $user->getWriterTypeLabel() }}
                    </h3>

                    @if($user->isCommunityWriter())
                        @if($canSubmitTestArticle)
                            <p class="text-gray-700 mb-4">
                                <strong>Prochaine étape :</strong> Écrivez votre article test pour démontrer votre talent !
                            </p>
                            <div class="bg-white p-4 rounded-lg border border-blue-200 mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">📝 Conseils pour votre article test</h4>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <span class="text-blue-600 mr-2">•</span>
                                        <span>Choisissez une destination ou expérience que vous connaissez vraiment</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-blue-600 mr-2">•</span>
                                        <span>Visez un score NomadSEO ≥ 78/100 (utilisez l'outil d'analyse)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-blue-600 mr-2">•</span>
                                        <span>Minimum 1500 mots, photos authentiques, ton personnel</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-blue-600 mr-2">•</span>
                                        <span>Structurez avec H2/H3, ajoutez des anecdotes, soyez authentique</span>
                                    </li>
                                </ul>
                            </div>
                            <a href="{{ route('writer.articles.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Écrire mon article test →
                            </a>
                        @elseif($hasSubmittedTestArticle)
                            <p class="text-gray-700 mb-3">
                                <strong>✅ Article test soumis !</strong> Notre équipe l'examine actuellement.
                            </p>
                            <p class="text-sm text-gray-600">
                                Nous évaluons la qualité du contenu, le score SEO, l'authenticité et le style rédactionnel. Délai moyen : 2-3 jours ouvrés.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('writer.articles.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Voir mon article →
                                </a>
                            </div>
                        @endif

                    @elseif($user->isClientContributor())
                        <p class="text-gray-700 mb-3">
                            <strong>Vérification en cours</strong> de votre réservation (#{{ $user->verified_booking_id }}).
                        </p>
                        <p class="text-sm text-gray-600">
                            Notre équipe vérifie que votre voyage a bien été effectué et que votre profil correspond aux critères de qualité. Délai : 24-48h.
                        </p>

                    @elseif($user->isPartner())
                        <p class="text-gray-700 mb-3">
                            <strong>Examen de votre offre commerciale</strong> en cours.
                        </p>
                        <p class="text-sm text-gray-600">
                            Nous vérifions la qualité de votre offre, la cohérence avec Nomadie, et les conditions de partenariat. Vous serez contacté sous 48h.
                        </p>
                        @if($user->partner_offer_url)
                            <div class="mt-3 text-sm">
                                <span class="text-gray-500">Offre soumise :</span>
                                <a href="{{ $user->partner_offer_url }}" target="_blank" class="text-blue-600 hover:underline ml-2">
                                    {{ $user->partner_offer_url }}
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Processus de validation</h3>
            <div class="space-y-4">
                <!-- Étape 1 : Soumission -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-900">Candidature soumise</h4>
                        <p class="text-sm text-gray-600">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <!-- Étape 2 : Article test (community seulement) -->
                @if($user->isCommunityWriter())
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $hasSubmittedTestArticle ? 'bg-green-500' : 'bg-yellow-400' }} rounded-full flex items-center justify-center">
                                @if($hasSubmittedTestArticle)
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="text-white font-bold">2</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Article test</h4>
                            <p class="text-sm text-gray-600">
                                @if($hasSubmittedTestArticle)
                                    Soumis - En cours d'examen
                                @else
                                    En attente de votre article
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Étape 3 : Validation équipe -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-bold">{{ $user->isCommunityWriter() ? '3' : '2' }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-900">Validation par l'équipe</h4>
                        <p class="text-sm text-gray-600">En attente</p>
                    </div>
                </div>

                <!-- Étape 4 : Accès -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-bold">{{ $user->isCommunityWriter() ? '4' : '3' }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-900">Accès complet</h4>
                        <p class="text-sm text-gray-600">Dashboard + publications illimitées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infos complémentaires -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-3">💡 En attendant</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <span class="text-green-600 mr-2">✓</span>
                    <span>Explorez les articles de la communauté pour vous inspirer</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-600 mr-2">✓</span>
                    <span>Lisez notre <a href="#" class="text-blue-600 hover:underline">guide du rédacteur parfait</a></span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-600 mr-2">✓</span>
                    <span>Préparez vos photos de voyage (haute qualité, format paysage recommandé)</span>
                </li>
                @if($user->isCommunityWriter())
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Testez l'outil NomadSEO sur votre brouillon avant publication</span>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Support -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600 mb-2">Une question sur votre candidature ?</p>
            <a href="mailto:hello@nomadie.fr" class="text-blue-600 hover:underline font-medium">
                Contactez notre équipe éditoriale →
            </a>
        </div>
    </div>
</div>
@endsection
