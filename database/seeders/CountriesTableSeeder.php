<?php

namespace Database\Seeders;

use App\Models\Continent;
use App\Models\Country;
use App\Models\TravelType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les continents et types de voyage
        $continents = Continent::all()->keyBy('slug');
        $travelTypes = TravelType::all()->keyBy('slug');
        
        // Données des pays
        $countriesData = [
            // Europe
            [
                'name' => 'France',
                'continent_slug' => 'europe',
                'slug' => 'france',
                'description' => 'De Paris aux champs de lavande, en passant par les vignobles et les plages méditerranéennes.',
                'image' => '/images/countries/france.jpg',
                'popular' => true,
                'rating' => 4.8,
                'best_time' => 'Mai à Septembre',
                'position' => json_encode(['x' => 46, 'y' => 20]),
                'tags' => json_encode(['culture', 'gastronomie', 'romantique', 'ville']),
                'travel_types' => ['cultural'],
            ],
            [
                'name' => 'Italie',
                'continent_slug' => 'europe',
                'slug' => 'italie',
                'description' => 'Art, histoire, gastronomie et paysages à couper le souffle, l\'Italie a tout pour vous séduire.',
                'image' => '/images/countries/italy.jpg',
                'popular' => true,
                'rating' => 4.9,
                'best_time' => 'Avril à Octobre',
                'position' => json_encode(['x' => 48, 'y' => 22]),
                'tags' => json_encode(['histoire', 'gastronomie', 'art', 'ville']),
                'travel_types' => ['cultural'],
            ],
            [
                'name' => 'Espagne',
                'continent_slug' => 'europe',
                'slug' => 'espagne',
                'description' => 'Des plages ensoleillées de la Costa del Sol à l\'architecture audacieuse de Barcelone.',
                'image' => '/images/countries/spain.jpg',
                'popular' => true,
                'rating' => 4.7,
                'best_time' => 'Mai à Octobre',
                'position' => json_encode(['x' => 44, 'y' => 23]),
                'tags' => json_encode(['plage', 'fiesta', 'culture', 'soleil']),
                'travel_types' => ['beach', 'cultural'],
            ],
            
            // Asie
            [
                'name' => 'Japon',
                'continent_slug' => 'asie',
                'slug' => 'japon',
                'description' => 'Des métropoles ultramodernes aux temples traditionnels, découvrez la richesse culturelle du Japon.',
                'image' => '/images/countries/japan.jpg',
                'popular' => true,
                'rating' => 4.9,
                'best_time' => 'Mars à Mai, Septembre à Novembre',
                'position' => json_encode(['x' => 75, 'y' => 22]),
                'tags' => json_encode(['tradition', 'technologie', 'temples', 'gastronomie']),
                'travel_types' => ['cultural'],
            ],
            [
                'name' => 'Thaïlande',
                'continent_slug' => 'asie',
                'slug' => 'thailande',
                'description' => 'Entre temples bouddhistes, plages paradisiaques et cuisine savoureuse, la Thaïlande vous attend.',
                'image' => '/images/countries/thailand.jpg',
                'popular' => true,
                'rating' => 4.7,
                'best_time' => 'Novembre à Mars',
                'position' => json_encode(['x' => 68, 'y' => 30]),
                'tags' => json_encode(['plage', 'temples', 'îles', 'cuisine']),
                'travel_types' => ['beach', 'cultural'],
            ],
            
            // Afrique
            [
                'name' => 'Maroc',
                'continent_slug' => 'afrique',
                'slug' => 'maroc',
                'description' => 'Des médinas animées aux dunes du désert, en passant par les montagnes de l\'Atlas.',
                'image' => '/images/countries/morocco.jpg',
                'popular' => true,
                'rating' => 4.7,
                'best_time' => 'Mars à Mai, Septembre à Novembre',
                'position' => json_encode(['x' => 43, 'y' => 28]),
                'tags' => json_encode(['médina', 'désert', 'culture', 'gastronomie']),
                'travel_types' => ['cultural', 'adventure'],
            ],
            
            // Amérique
            [
                'name' => 'États-Unis',
                'continent_slug' => 'amerique',
                'slug' => 'etats-unis',
                'description' => 'Des métropoles vibrantes aux parcs nationaux époustouflants.',
                'image' => '/images/countries/usa.jpg',
                'popular' => true,
                'rating' => 4.8,
                'best_time' => 'Varie selon la région',
                'position' => json_encode(['x' => 20, 'y' => 20]),
                'tags' => json_encode(['nature', 'ville', 'parcs nationaux', 'road trip']),
                'travel_types' => ['adventure', 'cultural'],
            ],
            
            // Océanie
            [
                'name' => 'Australie',
                'continent_slug' => 'oceanie',
                'slug' => 'australie',
                'description' => 'Des villes modernes aux déserts rouges, en passant par la Grande Barrière de corail.',
                'image' => '/images/countries/australia.jpg',
                'popular' => true,
                'rating' => 4.9,
                'best_time' => 'Septembre à Novembre, Mars à Mai',
                'position' => json_encode(['x' => 80, 'y' => 50]),
                'tags' => json_encode(['nature', 'plage', 'ville', 'faune']),
                'travel_types' => ['adventure', 'beach'],
            ],
        ];
        
        foreach ($countriesData as $countryData) {
            // Récupérer le continent
            $continent = $continents[$countryData['continent_slug']] ?? null;
            if (!$continent) continue;
            
            // Enlever les données qui ne sont pas dans le modèle Country
            $travelTypeSlugs = $countryData['travel_types'] ?? [];
            $continentSlug = $countryData['continent_slug'];
            unset($countryData['travel_types'], $countryData['continent_slug']);
            
            // Ajouter l'ID du continent
            $countryData['continent_id'] = $continent->id;
            
            // Créer ou mettre à jour le pays
            $country = Country::updateOrCreate(
                ['slug' => $countryData['slug']],
                $countryData
            );
            
            // Synchroniser les types de voyage
            $travelTypeIds = [];
            foreach ($travelTypeSlugs as $slug) {
                if (isset($travelTypes[$slug])) {
                    $travelTypeIds[] = $travelTypes[$slug]->id;
                }
            }
            
            $country->travelTypes()->sync($travelTypeIds);
        }
    }
}