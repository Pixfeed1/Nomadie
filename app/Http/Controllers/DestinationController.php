<?php

namespace App\Http\Controllers;

use App\Models\Continent;
use App\Models\Country;
use App\Models\Trip;
use App\Models\Destination;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Affiche la page principale des destinations
     */
    public function index()
    {
        // Obtenir les continents
        $continents = Continent::all();
        
        // Récupérer les pays qui ont de vraies offres
        $featuredDestinations = Country::select('countries.*')
            ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.country_id = countries.id) as trips_count')
            ->having('trips_count', '>', 0)
            ->with('continent')
            ->orderBy('trips_count', 'desc')
            ->orderByDesc('popular')
            ->get();
            
        // Si aucun pays n'a d'offres, prendre tous les pays
        if ($featuredDestinations->isEmpty()) {
            $featuredDestinations = Country::with('continent')->get();
        }
        
        return view('destinations.index', compact('continents', 'featuredDestinations'));
    }
    
    /**
     * Affiche la page détaillée d'une destination
     */
    public function show($slug)
    {
        // Récupérer le pays
        $destination = Country::where('slug', $slug)
            ->with('continent')
            ->firstOrFail();
            
        // Récupérer les VRAIES offres pour ce pays
        $trips = Trip::where('country_id', $destination->id)
            ->orWhereHas('destination', function($q) use ($destination) {
                $q->where('country_id', $destination->id);
            })
            ->with(['vendor', 'destination'])
            ->where('status', 'active')
            ->get();
            
        // Récupérer les vendeurs qui ont des offres pour ce pays
        $organizers = Vendor::whereHas('trips', function($q) use ($destination) {
            $q->where('country_id', $destination->id);
        })->get();
        
        // Récupérer des pays similaires (même continent)
        $similarDestinations = Country::where('continent_id', $destination->continent_id)
            ->where('id', '!=', $destination->id)
            ->select('countries.*')
            ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.country_id = countries.id) as trips_count')
            ->take(4)
            ->get();
            
        // Points forts (on pourrait les stocker en base)
        $highlights = [
            'Une culture riche et fascinante à découvrir',
            'Des paysages naturels époustouflants',
            'Une gastronomie locale authentique',
            'Des hébergements de qualité',
            'Des activités pour tous les goûts'
        ];
        
        // Avis - pour l'instant vide car pas de table reviews
        $reviews = collect([]);
        
        return view('destinations.show', compact(
            'destination', 
            'organizers',
            'trips', 
            'reviews', 
            'similarDestinations',
            'highlights'
        ));
    }
    
    /**
     * Récupère les pays par continent (pour les onglets)
     */
    public function getCountriesByContinent($continentSlug)
    {
        if ($continentSlug === 'all') {
            $countries = Country::select('countries.*')
                ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.country_id = countries.id) as trips_count')
                ->with('continent')
                ->get();
        } else {
            $continent = Continent::where('slug', $continentSlug)->first();
            
            if (!$continent) {
                return response()->json([
                    'success' => false,
                    'countries' => []
                ]);
            }
            
            $countries = Country::select('countries.*')
                ->selectRaw('(SELECT COUNT(*) FROM trips WHERE trips.country_id = countries.id) as trips_count')
                ->where('continent_id', $continent->id)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'countries' => $countries
        ]);
    }
}