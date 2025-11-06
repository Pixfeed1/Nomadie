<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'cancelled', 'completed'])
                  ->default('active')
                  ->after('id');
            
            // Ajouter un index pour optimiser les requêtes
            $table->index('status');
            $table->index('departure_date');
        });
        
        // Mettre à jour les voyages existants selon leur date
        DB::table('trips')
            ->where('departure_date', '<=', now())
            ->update(['status' => 'completed']);
            
        DB::table('trips')
            ->where('departure_date', '>', now())
            ->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['departure_date']);
            $table->dropColumn('status');
        });
    }
};