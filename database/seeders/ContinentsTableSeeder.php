<?php

namespace Database\Seeders;

use App\Models\Continent;
use Illuminate\Database\Seeder;

class ContinentsTableSeeder extends Seeder
{
    public function run()
    {
        $continents = [
            [
                'name' => 'Europe',
                'slug' => 'europe',
                'color' => '#38B2AC',
                'path' => 'M46 15 L53 16 L58 20 L56 25 L50 28 L45 26 L42 24 L45 20 L46 15',
                'position' => json_encode(['x' => 48, 'y' => 20]),
            ],
            [
                'name' => 'Asie',
                'slug' => 'asie',
                'color' => '#F6AD55',
                'path' => 'M56 18 L74 17 L80 25 L77 35 L65 38 L60 35 L55 32 L54 25 L56 18',
                'position' => json_encode(['x' => 68, 'y' => 28]),
            ],
            [
                'name' => 'Afrique',
                'slug' => 'afrique',
                'color' => '#68D391',
                'path' => 'M45 28 L54 27 L57 32 L54 42 L50 48 L45 44 L40 40 L43 33 L45 28',
                'position' => json_encode(['x' => 49, 'y' => 38]),
            ],
            [
                'name' => 'Amérique',
                'slug' => 'amerique',
                'color' => '#FC8181',
                'path' => 'M15 15 L28 14 L30 19 L26 25 L20 30 L15 28 L10 23 L13 18 L15 15',
                'position' => json_encode(['x' => 20, 'y' => 20]),
            ],
            [
                'name' => 'Océanie',
                'slug' => 'oceanie',
                'color' => '#4299E1',
                'path' => 'M75 45 L85 44 L87 52 L82 56 L78 55 L75 52 L75 45',
                'position' => json_encode(['x' => 80, 'y' => 50]),
            ],
        ];

        foreach ($continents as $continent) {
            Continent::updateOrCreate(['slug' => $continent['slug']], $continent);
        }
    }
}