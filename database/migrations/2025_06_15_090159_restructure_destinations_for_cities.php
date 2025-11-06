<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Ajouter country_id à la table destinations si elle n'existe pas déjà
        Schema::table('destinations', function (Blueprint $table) {
            if (!Schema::hasColumn('destinations', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('countries');
            }
            
            if (!Schema::hasColumn('destinations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('active');
            }
            
            if (!Schema::hasColumn('destinations', 'type')) {
                $table->string('type', 20)->default('city')->after('name');
            }
        });

        // 2. Migrer les données existantes (si nécessaire)
        // Cette requête va essayer de matcher le nom du pays dans destinations avec celui dans countries
        DB::statement("UPDATE destinations SET country_id = 
            (SELECT id FROM countries WHERE countries.name = destinations.country LIMIT 1)
            WHERE country_id IS NULL");

        // 3. Copier les données de destination_id vers country_id pour les trips existants
        // Seulement si country_id est NULL et destination_id a une valeur
        DB::statement("UPDATE trips SET country_id = destination_id 
                      WHERE country_id IS NULL AND destination_id IS NOT NULL");
    }

    public function down()
    {
        // Annuler les modifications sur la table destinations
        Schema::table('destinations', function (Blueprint $table) {
            if (Schema::hasColumn('destinations', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
            
            if (Schema::hasColumn('destinations', 'is_active')) {
                $table->dropColumn('is_active');
            }
            
            if (Schema::hasColumn('destinations', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};