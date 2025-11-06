<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // premier_pas, contributeur_confirme, etc.
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable(); // Icône ou image du badge
            $table->string('color')->default('primary'); // Couleur du badge
            $table->integer('order')->default(0); // Ordre d'affichage
            
            // Conditions d'obtention
            $table->json('conditions'); // Stockage des conditions en JSON
            $table->json('rewards')->nullable(); // Avantages du badge
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('badges');
    }
};