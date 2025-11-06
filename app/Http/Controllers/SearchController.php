<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Country;
use App\Models\Trip;

class SearchController extends Controller
{
    /**
     * Traiter la recherche depuis la page d'accueil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // Récupération des paramètres de recherche
        $destination = $request->input('destination');
        $departureDate = $request->input('date');
        $travelers = $request->input('travelers');
        
        // Filtres supplémentaires (seront null s'ils ne sont pas fournis)
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $duration = $request->input('duration');
        $travelType = $request->input('travel_type');
        
        // Initialisation de la requête
        $query = Trip::query();
        
        // Application des filtres
        if ($destination) {
            $query->whereHas('destination', function($q) use ($destination) {
                // Si la destination est un continent
                $q->where('continent', $destination)
                  // Ou si la destination est un pays spécifique
                  ->orWhere('country', $destination);
            });
        }
        
        // Utilisation de la colonne departure_date (ajoutée par migration)
        if ($departureDate) {
            $query->where('departure_date', '>=', $departureDate);
        }
        
        if ($travelers) {
            $query->where('max_travelers', '>=', $travelers);
        }
        
        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }
        
        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }
        
        if ($duration) {
            // Si duration est un intervalle (ex: "7-10")
            if (strpos($duration, '-') !== false) {
                list($minDuration, $maxDuration) = explode('-', $duration);
                $query->whereBetween('duration', [$minDuration, $maxDuration]);
            } else {
                // Si c'est une valeur unique
                $query->where('duration', $duration);
            }
        }
        
        if ($travelType) {
            $query->where('type', $travelType);
        }
        
        // Récupération des résultats paginés
        $trips = $query->with(['destination', 'vendor'])
                      ->orderBy('featured', 'desc')
                      ->orderBy('price')
                      ->paginate(12);
        
        // Récupération des données pour les filtres de la page de résultats
        $continents = Destination::distinct('continent')->pluck('continent');
        $countries = Country::orderBy('name')->get();
        $durations = ['1-3', '4-7', '7-10', '10-14', '14+'];
        $travelTypes = ['Culture', 'Aventure', 'Relaxation', 'Gastronomie', 'Écotourisme'];
        
        // Renvoi vers la vue de résultats avec toutes les données nécessaires
        return view('search.results', compact(
            'trips', 
            'continents', 
            'countries', 
            'durations', 
            'travelTypes',
            'destination',
            'departureDate',
            'travelers',
            'priceMin',
            'priceMax',
            'duration',
            'travelType'
        ));
    }
    
    /**
     * Affiche la page de recherche avancée
     *
     * @return \Illuminate\Http\Response
     */
    public function advancedSearch()
    {
        // Récupération des données pour les filtres
        $continents = Destination::distinct('continent')->pluck('continent');
        $countries = Country::orderBy('name')->get();
        $durations = ['1-3', '4-7', '7-10', '10-14', '14+'];
        $travelTypes = ['Culture', 'Aventure', 'Relaxation', 'Gastronomie', 'Écotourisme'];
        
        return view('search.advanced', compact(
            'continents', 
            'countries', 
            'durations', 
            'travelTypes'
        ));
    }
}