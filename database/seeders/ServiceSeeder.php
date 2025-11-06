<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'Guide local' => 'Accompagnement par un guide francophone local',
            'Transport privé' => 'Service de transport dédié pendant votre séjour',
            'Hébergement de luxe' => 'Séjour dans des hôtels 4 et 5 étoiles',
            'Excursions incluses' => 'Activités et visites incluses dans le forfait',
            'Gastronomie locale' => 'Découverte des spécialités culinaires locales',
            'Photographe professionnel' => 'Photos de qualité de votre voyage',
            'Visites exclusives' => 'Accès à des lieux normalement fermés au public',
            'Interprète' => 'Service de traduction pendant vos déplacements'
        ];

        foreach ($services as $name => $description) {
            Service::updateOrCreate(
                ['slug' => Str::slug($name)], // Clé unique pour identifier
                [
                    'name' => $name,
                    'description' => $description,
                    'active' => true
                ]
            );
        }
    }
}