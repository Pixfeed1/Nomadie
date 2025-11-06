<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            
            // Données de progression
            $table->json('progress_data')->nullable(); // Données de progression vers le badge
            $table->integer('progress_percentage')->default(0); // % de progression
            
            // Dates importantes
            $table->timestamp('unlocked_at'); // Date d'obtention
            $table->timestamp('notified_at')->nullable(); // Date de notification
            $table->boolean('is_featured')->default(false); // Badge mis en avant sur le profil
            
            $table->timestamps();
            
            // Un utilisateur ne peut avoir qu'une fois le même badge
            $table->unique(['user_id', 'badge_id']);
            
            $table->index('unlocked_at');
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_badges');
    }
};