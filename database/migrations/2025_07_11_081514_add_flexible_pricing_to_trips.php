<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlexiblePricingToTrips extends Migration
{
    public function up()
    {
        // Ajouter les nouveaux champs à la table trips
        Schema::table('trips', function (Blueprint $table) {
            // Type de tarification
            $table->enum('pricing_mode', [
                'per_person_per_day',    // Mode actuel (par défaut)
                'per_night_property',    // Pour les locations
                'per_person_activity',   // Pour les activités
                'per_group',            // Pour les forfaits groupe
                'custom'                // Pour les cas spéciaux
            ])->default('per_person_per_day')->after('type');
            
            // Description libre de la tarification
            $table->text('pricing_description')->nullable()->after('pricing_mode');
            
            // Pour les locations d'hébergement
            $table->integer('property_capacity')->nullable()->after('max_travelers');
            $table->integer('min_nights')->nullable()->after('property_capacity');
            
            // Durée en heures pour les activités
            $table->decimal('duration_hours', 4, 2)->nullable()->after('duration');
        });
        
        // Modifier la table trip_availabilities
        Schema::table('trip_availabilities', function (Blueprint $table) {
            // Prix pour location complète (par nuit)
            $table->decimal('property_price', 10, 2)->nullable()->after('child_price');
            
            // Prix pour un groupe
            $table->decimal('group_price', 10, 2)->nullable()->after('property_price');
            $table->integer('group_size')->nullable()->after('group_price');
            
            // Supplément par personne additionnelle
            $table->decimal('extra_person_price', 10, 2)->nullable()->after('group_size');
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_mode',
                'pricing_description',
                'property_capacity',
                'min_nights',
                'duration_hours'
            ]);
        });
        
        Schema::table('trip_availabilities', function (Blueprint $table) {
            $table->dropColumn([
                'property_price',
                'group_price',
                'group_size',
                'extra_person_price'
            ]);
        });
    }
}