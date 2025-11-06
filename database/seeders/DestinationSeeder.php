<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'France',
                'continent' => 'Europe',
                'country' => 'France',
                'city' => 'Paris',
                'description' => 'Découvrez la beauté de la France',
            ],
            [
                'name' => 'Italie',
                'continent' => 'Europe',
                'country' => 'Italie',
                'city' => 'Rome',
                'description' => 'Explorez la riche histoire italienne',
            ],
            [
                'name' => 'Espagne',
                'continent' => 'Europe',
                'country' => 'Espagne',
                'city' => 'Madrid',
                'description' => 'Profitez du soleil espagnol',
            ],
            [
                'name' => 'Grèce',
                'continent' => 'Europe',
                'country' => 'Grèce',
                'city' => 'Athènes',
                'description' => 'Admirez les îles grecques',
            ],
            [
                'name' => 'Portugal',
                'continent' => 'Europe',
                'country' => 'Portugal',
                'city' => 'Lisbonne',
                'description' => 'Savourez la culture portugaise',
            ],
            [
                'name' => 'Thaïlande',
                'continent' => 'Asie',
                'country' => 'Thaïlande',
                'city' => 'Bangkok',
                'description' => 'Découvrez les temples et plages thaïlandaises',
            ],
            [
                'name' => 'Japon',
                'continent' => 'Asie',
                'country' => 'Japon',
                'city' => 'Tokyo',
                'description' => 'Immergez-vous dans la culture japonaise',
            ],
            [
                'name' => 'États-Unis',
                'continent' => 'Amérique du Nord',
                'country' => 'États-Unis',
                'city' => 'New York',
                'description' => 'Explorez la diversité des États-Unis',
            ],
            [
                'name' => 'Canada',
                'continent' => 'Amérique du Nord',
                'country' => 'Canada',
                'city' => 'Toronto',
                'description' => 'Admirez les paysages canadiens',
            ],
            [
                'name' => 'Australie',
                'continent' => 'Océanie',
                'country' => 'Australie',
                'city' => 'Sydney',
                'description' => 'Découvrez la faune australienne unique',
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::updateOrCreate(
                ['slug' => Str::slug($destination['name'])], // Clé unique
                [
                    'name' => $destination['name'],
                    'continent' => $destination['continent'],
                    'country' => $destination['country'],
                    'city' => $destination['city'],
                    'description' => $destination['description'],
                    'image_path' => null, // Pas d'image pour l'instant
                    'active' => true
                ]
            );
        }
    }
}