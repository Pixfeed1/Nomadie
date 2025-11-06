<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncWorldCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:world-cities 
                            {--country= : ISO2 code du pays à synchroniser}
                            {--min-population=0 : Population minimum pour filtrer les villes}
                            {--limit= : Limiter le nombre de villes}
                            {--dry-run : Afficher ce qui sera fait sans l\'exécuter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les villes depuis world_cities vers destinations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $countryFilter = $this->option('country');
        $minPopulation = $this->option('min-population');
        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info('Démarrage de la synchronisation des villes...');

        // Mapper les pays existants avec ceux de world
        $countryMapping = $this->getCountryMapping();

        if ($dryRun) {
            $this->warn('Mode DRY RUN activé - Aucune modification ne sera effectuée');
        }

        $totalSynced = 0;

        foreach ($countryMapping as $mapping) {
            // Si un pays spécifique est demandé
            if ($countryFilter && strtolower($mapping['iso2']) !== strtolower($countryFilter)) {
                continue;
            }

            $this->info("\nSynchronisation pour : {$mapping['local_name']}");

            // Récupérer les villes du pays depuis world_cities
            $query = DB::table('world_cities')
                ->where('country_id', $mapping['world_id'])
                ->orderBy('name');

            if ($limit) {
                $query->limit($limit);
            }

            $cities = $query->get();

            $this->info("Nombre de villes trouvées : " . $cities->count());

            $synced = 0;
            $skipped = 0;

            foreach ($cities as $city) {
                // Vérifier si la ville existe déjà dans destinations
                $exists = DB::table('destinations')
                    ->where('name', $city->name)
                    ->where('country_id', $mapping['local_id'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Préparer les données pour l'insertion
                $destinationData = [
                    'name' => $city->name,
                    'slug' => Str::slug($city->name . '-' . $mapping['local_name']),
                    'country' => $mapping['local_name'],
                    'country_id' => $mapping['local_id'],
                    'city' => $city->name,
                    'type' => 'city',
                    'description' => "Découvrez {$city->name} en {$mapping['local_name']}",
                    'active' => true,
                    'is_active' => true,
                    'continent' => $mapping['continent'] ?? 'Afrique',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (!$dryRun) {
                    DB::table('destinations')->insert($destinationData);
                } else {
                    $this->line("  - Ajouterait : {$city->name}");
                }

                $synced++;
            }

            $this->info("  ✓ Synchronisées : {$synced}");
            $this->info("  → Ignorées (existent déjà) : {$skipped}");
            
            $totalSynced += $synced;
        }

        $this->info("\n✅ Synchronisation terminée !");
        $this->info("Total de villes synchronisées : {$totalSynced}");

        return Command::SUCCESS;
    }

    /**
     * Obtenir le mapping entre les pays locaux et world
     */
    private function getCountryMapping()
    {
        // Mapping manuel des pays
        // Tu peux l'ajuster selon tes besoins
        return [
            [
                'local_id' => 105,
                'local_name' => 'République du Congo',
                'world_id' => 50,
                'iso2' => 'CG',
                'continent' => 'Afrique'
            ],
            [
                'local_id' => 106,
                'local_name' => 'République démocratique du Congo',
                'world_id' => 59,
                'iso2' => 'CD',
                'continent' => 'Afrique'
            ],
            [
                'local_id' => 75,
                'local_name' => 'France',
                'world_id' => 83, // À vérifier
                'iso2' => 'FR',
                'continent' => 'Europe'
            ],
            // Ajoute d'autres pays selon tes besoins
        ];
    }
}
