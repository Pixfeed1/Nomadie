<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute une contrainte unique sur (user_id, trip_id) pour empêcher
     * qu'un utilisateur ne puisse laisser plusieurs avis pour le même voyage.
     * Cela prévient les race conditions au niveau DB.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Supprimer d'abord les doublons s'il y en a
            \DB::statement('
                DELETE r1 FROM reviews r1
                INNER JOIN reviews r2
                WHERE r1.id > r2.id
                AND r1.user_id = r2.user_id
                AND r1.trip_id = r2.trip_id
            ');

            // Ajouter la contrainte unique
            $table->unique(['user_id', 'trip_id'], 'reviews_user_trip_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_user_trip_unique');
        });
    }
};
