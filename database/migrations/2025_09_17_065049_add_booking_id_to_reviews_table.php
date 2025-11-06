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
        Schema::table('reviews', function (Blueprint $table) {
            // Ajouter la colonne booking_id après trip_id
            $table->foreignId('booking_id')
                  ->nullable()
                  ->after('trip_id')
                  ->constrained('bookings')
                  ->onDelete('set null');
            
            // Ajouter un index pour améliorer les performances
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Supprimer l'index d'abord
            $table->dropIndex(['booking_id']);
            
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['booking_id']);
            
            // Supprimer la colonne
            $table->dropColumn('booking_id');
        });
    }
};