<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceCategoriesAndAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Catégories principales
        $categories = [
            [
                'name' => 'Aventure & Nature',
                'description' => 'Trekking, randonnée, safari, activités outdoor',
                'display_order' => 1,
            ],
            [
                'name' => 'Culture & Découverte',
                'description' => 'Circuits, visites guidées, patrimoine, histoire',
                'display_order' => 2,
            ],
            [
                'name' => 'Détente & Bien-être',
                'description' => 'Spa, yoga, retraite, séjours balnéaires',
                'display_order' => 3,
            ],
            [
                'name' => 'Gastronomie & Terroir',
                'description' => 'Cuisine locale, œnologie, marchés, agritourisme',
                'display_order' => 4,
            ],
            [
                'name' => 'Événementiel',
                'description' => 'Festivals, mariages, événements sportifs, concerts',
                'display_order' => 5,
            ],
            [
                'name' => 'Luxe & Premium',
                'description' => 'Expériences exclusives, services haut de gamme',
                'display_order' => 6,
            ],
            [
                'name' => 'Famille & Groupes',
                'description' => 'Voyages multi-générationnels, activités familiales',
                'display_order' => 7,
            ],
            [
                'name' => 'Voyages thématiques',
                'description' => 'Photo, spirituel, sportif, linguistique',
                'display_order' => 8,
            ],
            [
                'name' => 'Urbain & City-breaks',
                'description' => 'Séjours courts en ville, shopping, vie nocturne',
                'display_order' => 9,
            ],
            [
                'name' => 'Croisières & Navigation',
                'description' => 'Voyages en bateau, yacht, voile, fleuves',
                'display_order' => 10,
            ],
        ];

        // Insérer les catégories avec gestion des doublons
        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            
            // Vérifier si la catégorie existe déjà
            $exists = DB::table('service_categories')->where('slug', $slug)->exists();
            
            if (!$exists) {
                DB::table('service_categories')->insert([
                    'name' => $category['name'],
                    'slug' => $slug,
                    'description' => $category['description'],
                    'display_order' => $category['display_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Attributs secondaires regroupés par type
        $attributeGroups = [
            'format' => [
                'Sur mesure',
                'En groupe',
                'Solo accompagné',
                'Circuit organisé',
                'Séjour libre',
            ],
            'style' => [
                'Écoresponsable/Durable',
                'Solidaire/Humanitaire',
                'Authentique/Traditionnel',
                'Luxe',
                'Aventure',
                'Éducatif',
                'Spirituel',
            ],
            'service' => [
                'Conciergerie 24/7',
                'Guide personnel/Interprète',
                'Transport privé',
                'Services VIP',
                'Adaptations spéciales',
            ],
            'duration' => [
                'Week-end/Court séjour',
                'Semaine',
                'Long séjour',
                'Expédition',
            ],
        ];

        // Insérer tous les attributs avec gestion des doublons
        $order = 1;
        foreach ($attributeGroups as $type => $attributes) {
            foreach ($attributes as $attribute) {
                $slug = Str::slug($attribute);
                
                // Vérifier si l'attribut existe déjà
                $exists = DB::table('service_attributes')->where('slug', $slug)->exists();
                
                if (!$exists) {
                    DB::table('service_attributes')->insert([
                        'name' => $attribute,
                        'slug' => $slug,
                        'type' => $type,
                        'display_order' => $order,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $order++;
            }
        }
    }
}