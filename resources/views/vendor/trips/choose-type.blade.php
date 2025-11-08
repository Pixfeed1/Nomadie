@extends('layouts.vendor')

@section('title', 'Choisir le type d\'offre')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-text-primary mb-4">Créer une nouvelle offre</h1>
        <p class="text-lg text-text-secondary">
            Choisissez le type d'offre que vous souhaitez proposer à vos voyageurs
        </p>
    </div>

    <!-- Grille de types d'offres -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Hébergement -->
        <a href="{{ route('vendor.trips.create', ['type' => 'accommodation']) }}"
           class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all p-8 border-2 border-transparent hover:border-primary group card">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-primary/10 group-hover:bg-primary flex items-center justify-center mb-4 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-text-primary mb-2">{{ $offerTypes['accommodation']['name'] }}</h2>
                <p class="text-text-secondary mb-4">
                    Location d'appartements, maisons, villas ou chambres d'hôtes. Tarification à la nuit ou au séjour.
                </p>
                <ul class="text-sm text-text-secondary space-y-2 mb-6 text-left">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Capacité, chambres, salles de bain</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Séjour minimum configurable</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Calendrier de disponibilité</span>
                    </li>
                </ul>
                <div class="btn bg-primary text-white group-hover:bg-primary-dark px-6 py-3 rounded-lg transition-colors">
                    Créer un hébergement
                </div>
            </div>
        </a>

        <!-- Séjour organisé -->
        <a href="{{ route('vendor.trips.create', ['type' => 'organized_trip']) }}"
           class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all p-8 border-2 border-transparent hover:border-primary group card">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-info/10 group-hover:bg-info flex items-center justify-center mb-4 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-text-primary mb-2">{{ $offerTypes['organized_trip']['name'] }}</h2>
                <p class="text-text-secondary mb-4">
                    Voyages organisés, circuits, tours guidés avec itinéraire défini. Tarification par personne.
                </p>
                <ul class="text-sm text-text-secondary space-y-2 mb-6 text-left">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Itinéraire jour par jour</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Nombre max de voyageurs</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Niveau physique et plan de repas</span>
                    </li>
                </ul>
                <div class="btn bg-info text-white group-hover:bg-info/80 px-6 py-3 rounded-lg transition-colors">
                    Créer un séjour organisé
                </div>
            </div>
        </a>

        <!-- Activité -->
        <a href="{{ route('vendor.trips.create', ['type' => 'activity']) }}"
           class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all p-8 border-2 border-transparent hover:border-primary group card">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-success/10 group-hover:bg-success flex items-center justify-center mb-4 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-text-primary mb-2">{{ $offerTypes['activity']['name'] }}</h2>
                <p class="text-text-secondary mb-4">
                    Activités ponctuelles, expériences uniques, ateliers, sports, visites guidées.
                </p>
                <ul class="text-sm text-text-secondary space-y-2 mb-6 text-left">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Durée en heures</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Équipement inclus ou non</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Nombre max de participants</span>
                    </li>
                </ul>
                <div class="btn bg-success text-white group-hover:bg-success/80 px-6 py-3 rounded-lg transition-colors">
                    Créer une activité
                </div>
            </div>
        </a>

        <!-- Sur mesure -->
        <a href="{{ route('vendor.trips.create', ['type' => 'custom']) }}"
           class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all p-8 border-2 border-transparent hover:border-primary group card">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-warning/10 group-hover:bg-warning flex items-center justify-center mb-4 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-text-primary mb-2">{{ $offerTypes['custom']['name'] }}</h2>
                <p class="text-text-secondary mb-4">
                    Offres personnalisées, services à la carte, voyages sur mesure avec tarification flexible.
                </p>
                <ul class="text-sm text-text-secondary space-y-2 mb-6 text-left">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Durée flexible</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Tarification personnalisée</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Descriptif libre du service</span>
                    </li>
                </ul>
                <div class="btn bg-warning text-white group-hover:bg-warning/80 px-6 py-3 rounded-lg transition-colors">
                    Créer une offre sur mesure
                </div>
            </div>
        </a>
    </div>

    <!-- Retour -->
    <div class="text-center pt-4">
        <a href="{{ route('vendor.trips.index') }}" class="text-text-secondary hover:text-primary transition-colors inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à mes offres
        </a>
    </div>
</div>
@endsection
