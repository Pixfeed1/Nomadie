<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Étape 1: Modifier temporairement en VARCHAR pour permettre la mise à jour
        DB::statement("ALTER TABLE seo_configurations MODIFY writer_type VARCHAR(50)");

        // Étape 2: Mettre à jour les données existantes
        DB::table('seo_configurations')->where('writer_type', 'communaute')->update(['writer_type' => 'community']);
        DB::table('seo_configurations')->where('writer_type', 'client')->update(['writer_type' => 'client_contributor']);
        DB::table('seo_configurations')->where('writer_type', 'partenaire')->update(['writer_type' => 'partner']);
        DB::table('seo_configurations')->where('writer_type', 'equipe')->update(['writer_type' => 'team']);

        // Étape 3: Reconvertir en ENUM avec les nouvelles valeurs
        DB::statement("ALTER TABLE seo_configurations MODIFY writer_type ENUM('community', 'client_contributor', 'partner', 'team')");
    }

    public function down()
    {
        // Retour aux anciennes valeurs
        DB::statement("ALTER TABLE seo_configurations MODIFY writer_type VARCHAR(50)");

        DB::table('seo_configurations')->where('writer_type', 'community')->update(['writer_type' => 'communaute']);
        DB::table('seo_configurations')->where('writer_type', 'client_contributor')->update(['writer_type' => 'client']);
        DB::table('seo_configurations')->where('writer_type', 'partner')->update(['writer_type' => 'partenaire']);
        DB::table('seo_configurations')->where('writer_type', 'team')->update(['writer_type' => 'equipe']);

        DB::statement("ALTER TABLE seo_configurations MODIFY writer_type ENUM('communaute', 'client', 'partenaire', 'equipe')");
    }
};
