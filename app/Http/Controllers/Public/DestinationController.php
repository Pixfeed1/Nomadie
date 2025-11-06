<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Country;

class DestinationController extends Controller
{
    /**
     * Affiche la page d'index des destinations
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer tous les continents et les pays associés
        $continentsWithCountries = [];
        
        // Supposons que vous avez une structure où les continents ont des pays associés
        // Ici, on simule cette structure pour l'exemple
        $continents = ['Europe', 'Asie', 'Amérique', 'Afrique', 'Océanie'];
        
        foreach ($continents as $continent) {
            // Dans une application réelle, vous récupéreriez les pays associés à ce continent depuis la base de données
            $countries = Country::where('continent', $continent)->get();
            
            // Ajouter des pays factices pour la démo si nécessaire
            if ($continent == 'Europe' && $countries->isEmpty()) {
                $countries = collect([
                    (object)['id' => 1, 'name' => 'France', 'trips_count' => 42, 'image' => 'france.jpg'],
                    (object)['id' => 2, 'name' => 'Italie', 'trips_count' => 36, 'image' => 'italy.jpg'],
                    (object)['id' => 3, 'name' => 'Espagne', 'trips_count' => 28, 'image' => 'spain.jpg'],
                ]);
            } elseif ($continent == 'Asie' && $countries->isEmpty()) {
                $countries = collect([
                    (object)['id' => 4, 'name' => 'Japon', 'trips_count' => 24, 'image' => 'japan.jpg'],
                    (object)['id' => 5, 'name' => 'Thaïlande', 'trips_count' => 22, 'image' => 'thailand.jpg'],
                    (object)['id' => 6, 'name' => 'Vietnam', 'trips_count' => 18, 'image' => 'vietnam.jpg'],
                ]);
            } elseif ($continent == 'Amérique' && $countries->isEmpty()) {
                $countries = collect([
                    (object)['id' => 7, 'name' => 'États-Unis', 'trips_count' => 20, 'image' => 'usa.jpg'],
                    (object)['id' => 8, 'name' => 'Canada', 'trips_count' => 15, 'image' => 'canada.jpg'],
                    (object)['id' => 9, 'name' => 'Brésil', 'trips_count' => 12, 'image' => 'brazil.jpg'],
                ]);
            } elseif ($continent == 'Afrique' && $countries->isEmpty()) {
                $countries = collect([
                    (object)['id' => 10, 'name' => 'Maroc', 'trips_count' => 16, 'image' => 'morocco.jpg'],
                    (object)['id' => 11, 'name' => 'Afrique du Sud', 'trips_count' => 14, 'image' => 'southafrica.jpg'],
                    (object)['id' => 12, 'name' => 'Kenya', 'trips_count' => 10, 'image' => 'kenya.jpg'],
                ]);
            } elseif ($continent == 'Océanie' && $countries->isEmpty()) {
                $countries = collect([
                    (object)['id' => 13, 'name' => 'Australie', 'trips_count' => 18, 'image' => 'australia.jpg'],
                    (object)['id' => 14, 'name' => 'Nouvelle-Zélande', 'trips_count' => 12, 'image' => 'newzealand.jpg'],
                ]);
            }
            
            $continentsWithCountries[$continent] = $countries;
        }
        
        // Récupérer les destinations en vedette
        $featuredDestinations = [
            (object)['name' => 'Japon', 'trips_count' => 24, 'image' => 'japan.jpg', 'description' => 'Découvrez la culture fascinante et les paysages contrastés du Japon'],
            (object)['name' => 'Italie', 'trips_count' => 36, 'image' => 'italy.jpg', 'description' => 'Art, histoire et gastronomie dans un pays à la beauté intemporelle'],
            (object)['name' => 'Thaïlande', 'trips_count' => 22, 'image' => 'thailand.jpg', 'description' => 'Des plages paradisiaques et des temples bouddhistes étonnants']
        ];
        
        return view('destinations.index', compact('continentsWithCountries', 'featuredDestinations'));
    }
    
    /**
     * Affiche la page de détail d'un pays
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Dans une application réelle, vous récupéreriez le pays depuis la base de données
        // Ici, on simule des données pour l'exemple
        $countries = [
            1 => ['name' => 'France', 'image' => 'france.jpg', 'description' => 'De Paris aux villages de Provence, la France offre un mélange unique d\'histoire, de culture et de gastronomie.'],
            4 => ['name' => 'Japon', 'image' => 'japan.jpg', 'description' => 'Un pays où la tradition et la modernité se rencontrent, des temples ancestraux aux métropoles futuristes.']
        ];
        
        // Si le pays n'existe pas, rediriger vers la liste des destinations
        if (!isset($countries[$id])) {
            return redirect()->route('destinations');
        }
        
        $country = (object)$countries[$id];
        
        // Simuler des voyages associés à ce pays
        $trips = [
            ['id' => 1, 'title' => 'Week-end à Paris', 'image' => 'paris.jpg', 'price' => 450, 'duration' => 3, 'rating' => 4.8],
            ['id' => 2, 'title' => 'La Côte d\'Azur en été', 'image' => 'cotedazur.jpg', 'price' => 780, 'duration' => 7, 'rating' => 4.7],
            ['id' => 3, 'title' => 'Tour de Normandie', 'image' => 'normandie.jpg', 'price' => 520, 'duration' => 5, 'rating' => 4.6],
        ];
        
        // Convertir en collection pour la pagination
        $trips = collect($trips)->map(function($trip) {
            return (object)$trip;
        });
        
        return view('destinations.show', compact('country', 'trips'));
    }
}