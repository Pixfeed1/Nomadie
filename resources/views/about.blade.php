@extends('layouts.public')

@section('title', 'À propos de nous')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-text-primary">À propos de Marketplace Voyages</h1>
            <p class="mt-4 text-lg text-text-secondary">Découvrez qui nous sommes et notre vision pour transformer l'expérience du voyage</p>
        </div>
        
        <!-- Section Notre Histoire -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Notre Histoire</h2>
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="md:w-1/2">
                    <p class="text-text-secondary mb-4">Marketplace Voyages est née d'une passion pour l'exploration et d'une vision d'un monde où voyager est accessible à tous.</p>
                    <p class="text-text-secondary mb-4">Notre fondatrice, Priscilla, voyageuse passionnée et entrepreneure dans l'âme, a constaté un manque sur le marché : une plateforme qui connecte directement les voyageurs avec des organisateurs locaux et spécialisés.</p>
                    <p class="text-text-secondary">En 2025, elle a lancé cette marketplace pour créer une communauté où les amoureux du voyage peuvent découvrir des expériences authentiques proposées par des experts passionnés.</p>
                </div>
                <div class="md:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="/images/about/our-story.jpg" alt="Notre histoire" class="w-full h-auto" onerror="this.src='/api/placeholder/600/400';this.onerror=null;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Notre Mission -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Notre Mission</h2>
            <div class="flex flex-col md:flex-row-reverse gap-8 items-center">
                <div class="md:w-1/2">
                    <p class="text-text-secondary mb-4">Notre mission est de créer un écosystème où les voyageurs peuvent découvrir des expériences uniques et authentiques, pendant que les organisateurs de voyages indépendants peuvent développer leur activité et atteindre une audience mondiale.</p>
                    <p class="text-text-secondary">Nous croyons fermement que les meilleures expériences de voyage viennent des experts locaux et des passionnés du secteur, et nous sommes déterminés à leur donner une plateforme pour briller.</p>
                </div>
                <div class="md:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="/images/about/our-mission.jpg" alt="Notre mission" class="w-full h-auto" onerror="this.src='/api/placeholder/600/400';this.onerror=null;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Nos Valeurs -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Nos Valeurs</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4">
                    <div class="h-16 w-16 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Authenticité</h3>
                    <p class="text-text-secondary">Nous valorisons les expériences authentiques et les relations sincères entre voyageurs et organisateurs.</p>
                </div>
                <div class="text-center p-4">
                    <div class="h-16 w-16 mx-auto bg-accent/10 text-accent rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Communauté</h3>
                    <p class="text-text-secondary">Nous construisons une communauté mondiale de passionnés qui partagent leur amour du voyage.</p>
                </div>
                <div class="text-center p-4">
                    <div class="h-16 w-16 mx-auto bg-success/10 text-success rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Innovation</h3>
                    <p class="text-text-secondary">Nous explorons constamment de nouvelles façons d'améliorer l'expérience de voyage pour tous.</p>
                </div>
            </div>
        </div>
        
        <!-- Section Pourquoi Nous Choisir -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Pourquoi Nous Choisir</h2>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-text-primary mb-1">Expériences uniques</h3>
                        <p class="text-text-secondary">Accédez à des offres uniques proposées par des organisateurs passionnés et locaux qui connaissent leurs destinations comme personne.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-text-primary mb-1">Transparence totale</h3>
                        <p class="text-text-secondary">Notre système de commission est clair et transparent pour tous. Pas de frais cachés, que ce soit pour les voyageurs ou les organisateurs.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-text-primary mb-1">Sécurité garantie</h3>
                        <p class="text-text-secondary">Notre plateforme sécurisée protège vos paiements et vos données personnelles, pour que vous puissiez voyager l'esprit tranquille.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-text-primary mb-1">Support réactif</h3>
                        <p class="text-text-secondary">Notre équipe de support est toujours prête à vous aider, que vous soyez voyageur ou organisateur.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Pour les Organisateurs -->
        <div class="bg-primary/5 rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-text-primary mb-6">Pour les Organisateurs de Voyages</h2>
            <p class="text-text-secondary mb-4">Nous offrons aux organisateurs de voyages une plateforme pour atteindre une audience mondiale, avec différentes formules adaptées à leurs besoins :</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-text-primary mb-2">Gratuit</h3>
                    <p class="text-text-secondary mb-2">Commission par vente: <span class="font-bold text-error">20%</span></p>
                    <p class="text-text-secondary">Parfait pour débuter et tester le potentiel de la plateforme.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border-2 border-accent relative">
                    <div class="absolute -top-3 right-4 bg-accent text-white text-xs px-2 py-1 rounded-full">Populaire</div>
                    <h3 class="text-lg font-bold text-text-primary mb-2">Essentiel</h3>
                    <p class="text-text-secondary mb-2">Commission par vente: <span class="font-bold text-accent">10%</span></p>
                    <p class="text-text-secondary">Idéal pour les organisateurs qui veulent développer leur activité.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-text-primary mb-2">Pro</h3>
                    <p class="text-text-secondary mb-2">Commission par vente: <span class="font-bold text-success">5%</span></p>
                    <p class="text-text-secondary">Pour les professionnels qui souhaitent maximiser leur présence et revenus.</p>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('vendor.register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Devenir Organisateur
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Appel à l'action -->
        <div class="text-center mt-12">
            <h2 class="text-2xl font-semibold text-text-primary mb-4">Rejoignez notre aventure</h2>
            <p class="text-text-secondary mb-8">Que vous soyez voyageur en quête d'expériences authentiques ou organisateur passionné, nous vous invitons à faire partie de notre communauté.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Explorer les destinations
                </a>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-6 py-3 border border-primary text-base font-medium rounded-md shadow-sm text-primary bg-white hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Contactez-nous
                </a>
            </div>
        </div>
    </div>
</div>
@endsection