<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // Liste des continents à ajouter
        $continents = [
            ['name' => 'Amérique du Nord', 'slug' => 'amerique-du-nord', 'color' => '#FF6961', 'position' => 2],
            ['name' => 'Amérique du Sud', 'slug' => 'amerique-du-sud', 'color' => '#CB99C9', 'position' => 3],
            ['name' => 'Antarctique', 'slug' => 'antarctique', 'color' => '#FDFD96', 'position' => 4],
            ['name' => 'Océanie', 'slug' => 'oceanie', 'color' => '#B39EB5', 'position' => 7],
        ];
        
        // Nombre de continents ajoutés
        $added = 0;
        
        foreach ($continents as $continent) {
            // Vérifier si ce continent existe déjà
            $exists = DB::table('continents')
                ->where('slug', $continent['slug'])
                ->exists();
                
            if (!$exists) {
                DB::table('continents')->insert([
                    'name' => $continent['name'],
                    'slug' => $continent['slug'],
                    'color' => $continent['color'],
                    'position' => $continent['position'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $added++;
            }
        }
        
        echo "Migration terminée : {$added} continents ajoutés.\n";
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Rien à faire, nous ne voulons pas supprimer les continents
    }
};