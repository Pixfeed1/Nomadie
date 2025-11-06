<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('offer_type')->default('sejour')->after('type');
            // Ajouter un index pour améliorer les performances des requêtes
            $table->index('offer_type');
        });
        
        // Optionnel : Migrer les données existantes
        // Les trips de type 'fixed' ou 'circuit' deviennent 'sejour'
        DB::table('trips')->update(['offer_type' => 'sejour']);
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['offer_type']);
            $table->dropColumn('offer_type');
        });
    }
};