<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Nombre maximum de destinations selon le forfait
            $table->integer('max_destinations')->default(3)->after('max_trips');
            
            // Compteur de modifications ce mois-ci  
            $table->integer('destinations_changes_this_month')->default(0)->after('max_destinations');
            
            // Date de la dernière modification
            $table->date('last_destinations_change')->nullable()->after('destinations_changes_this_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'max_destinations',
                'destinations_changes_this_month', 
                'last_destinations_change'
            ]);
        });
    }
};