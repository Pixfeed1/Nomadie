<?php

namespace App\Observers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class CountryVendorObserver
{
    /**
     * Handle the created event.
     * Quand un vendor ajoute un nouveau pays
     */
    public function created($countryVendor)
    {
        // Récupérer le pays ajouté
        $country = DB::table('countries')->find($countryVendor->country_id);
        
        if (!$country) {
            return;
        }

        // Chercher le pays correspondant dans world_countries
        $worldCountry = DB::table('world_countries')
            ->where('name', 'like', '%' . explode(' ', $country->name)[0] . '%')
            ->first();

        if (!$worldCountry) {
            Log::warning("Pays non trouvé dans world_countries : " . $country->name);
            return;
        }

        // Vérifier si des villes existent déjà pour ce pays
        $existingCities = DB::table('destinations')
            ->where('country_id', $country->id)
            ->count();

        if ($existingCities > 0) {
            Log::info("Des villes existent déjà pour : " . $country->name);
            return;
        }

        // Synchroniser les villes en arrière-plan
        $this->syncCitiesForCountry($country, $worldCountry);
        
        Log::info("Synchronisation automatique lancée pour : " . $country->name);
    }

    /**
     * Synchroniser les villes d'un pays
     */
    private function syncCitiesForCountry($country, $worldCountry)
    {
        // Option 1 : Synchronisation immédiate
        $cities = DB::table('world_cities')
            ->where('country_id', $worldCountry->id)
            ->get();

        $inserted = 0;
        
        foreach ($cities as $city) {
            try {
                DB::table('destinations')->insert([
                    'name' => $city->name,
                    'slug' => Str::slug($city->name . '-' . $country->name),
                    'country' => $country->name,
                    'country_id' => $country->id,
                    'city' => $city->name,
                    'type' => 'city',
                    'description' => "Découvrez {$city->name} en {$country->name}",
                    'active' => true,
                    'is_active' => true,
                    'continent' => $country->continent->name ?? 'Afrique',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $inserted++;
            } catch (\Exception $e) {
                // Ignorer les doublons
                continue;
            }
        }

        Log::info("Synchronisation terminée : {$inserted} villes ajoutées pour {$country->name}");

        // Option 2 : Utiliser la commande artisan (plus lent mais réutilise la logique)
        // Artisan::call('sync:world-cities', [
        //     '--country' => $worldCountry->iso2
        // ]);
    }
}
