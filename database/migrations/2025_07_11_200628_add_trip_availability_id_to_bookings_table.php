<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTripAvailabilityIdToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Ajouter la colonne trip_availability_id
            $table->unsignedBigInteger('trip_availability_id')->nullable()->after('trip_id');
            
            // Ajouter l'index pour les performances
            $table->index('trip_availability_id');
        });

        // Migration des données existantes si nécessaire
        // Cette partie essaie de lier les réservations existantes à des disponibilités
        $bookings = DB::table('bookings')->whereNull('trip_availability_id')->get();
        
        foreach ($bookings as $booking) {
            // Essayer de trouver une disponibilité correspondante pour cette réservation
            // Basé sur le trip_id et potentiellement les dates
            $availability = DB::table('trip_availabilities')
                ->where('trip_id', $booking->trip_id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('start_date', 'asc')
                ->first();
            
            if ($availability) {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['trip_availability_id' => $availability->id]);
            }
        }

        // Maintenant ajouter la contrainte de clé étrangère
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('trip_availability_id')
                  ->references('id')
                  ->on('trip_availabilities')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign(['trip_availability_id']);
            
            // Supprimer l'index
            $table->dropIndex(['trip_availability_id']);
            
            // Supprimer la colonne
            $table->dropColumn('trip_availability_id');
        });
    }
}