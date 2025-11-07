<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('brief_templates', function (Blueprint $table) {
            $table->id();

            // Informations du template
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Type prédéfini
            $table->enum('type', ['destination', 'guide_pratique', 'culture', 'gastronomie', 'hebergement', 'transport', 'budget', 'custom']);

            // Template content
            $table->json('content_requirements'); // Structure réutilisable
            $table->json('keywords')->nullable();
            $table->integer('min_words')->default(1500);
            $table->integer('target_score')->default(85);
            $table->json('seo_requirements')->nullable();

            // Métadonnées
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            // Index
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('brief_templates');
    }
};
