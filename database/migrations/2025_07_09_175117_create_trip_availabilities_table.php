<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            
            // Dates de disponibilité
            $table->date('start_date');
            $table->date('end_date');
            
            // Places et participants
            $table->integer('total_spots'); // Nombre total de places
            $table->integer('booked_spots')->default(0); // Places réservées
            $table->integer('min_participants')->default(1); // Minimum requis
            
            // Prix
            $table->decimal('adult_price', 10, 2); // Prix par adulte
            $table->decimal('child_price', 10, 2)->nullable(); // Prix enfant (optionnel)
            
            // Promotions
            $table->integer('discount_percentage')->default(0);
            $table->date('discount_ends_at')->nullable();
            
            // Statut
            $table->enum('status', ['available', 'guaranteed', 'full', 'cancelled'])->default('available');
            $table->boolean('is_guaranteed')->default(false); // Départ garanti
            
            // Notes internes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['trip_id', 'start_date']);
            $table->index(['start_date', 'end_date']);
            $table->index('status');
        });

        // Migration pour supprimer les anciens champs de la table trips
        Schema::table('trips', function (Blueprint $table) {
            // On vérifie si les colonnes existent avant de les supprimer
            if (Schema::hasColumn('trips', 'departure_date')) {
                $table->dropColumn('departure_date');
            }
            if (Schema::hasColumn('trips', 'return_date')) {
                $table->dropColumn('return_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restaurer les colonnes dans trips
        Schema::table('trips', function (Blueprint $table) {
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
        });
        
        // Supprimer la table des disponibilités
        Schema::dropIfExists('trip_availabilities');
    }
}