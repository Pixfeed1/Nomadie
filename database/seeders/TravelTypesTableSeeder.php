<?php

namespace Database\Seeders;

use App\Models\TravelType;
use Illuminate\Database\Seeder;

class TravelTypesTableSeeder extends Seeder
{
    public function run()
    {
        $travelTypes = [
            [
                'name' => 'Culturel',
                'slug' => 'cultural',
                'icon' => 'museum',
                'image' => '/images/cultural.jpg',
                'bg_class' => 'bg-primary/20',
                'description' => 'Explorez l\'histoire, l\'art et la culture locales lors de voyages enrichissants.',
            ],
            [
                'name' => 'Aventure',
                'slug' => 'adventure',
                'icon' => 'mountain',
                'image' => '/images/adventure.jpg',
                'bg_class' => 'bg-accent/20',
                'description' => 'Randonnées, sports extrêmes et activités en plein air pour les amateurs de sensations.',
            ],
            [
                'name' => 'Plage',
                'slug' => 'beach',
                'icon' => 'beach',
                'image' => '/images/beach.jpg',
                'bg_class' => 'bg-success/20',
                'description' => 'Découvrez les plus belles plages du monde pour des vacances relaxantes au bord de l\'eau.',
            ],
        ];

        foreach ($travelTypes as $type) {
            TravelType::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}