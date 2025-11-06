<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_analysis_id')->constrained('seo_analyses')->cascadeOnDelete();
            $table->foreignId('suggested_article_id')->constrained('articles')->cascadeOnDelete();
            $table->decimal('relevance_score', 5, 2)->default(0); // 0 à 100
            $table->enum('reason', [
                'keyword_match',     // Mots-clés similaires
                'same_location',     // Même destination
                'same_theme',        // Même thématique
                'complementary',     // Contenu complémentaire
                'trending'          // Article tendance
            ]);
            $table->boolean('accepted')->nullable(); // null = pas encore décidé
            $table->json('match_data')->nullable(); // Détails sur pourquoi c'est suggéré
            $table->timestamps();
            
            $table->index(['seo_analysis_id', 'relevance_score']);
            $table->index('accepted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_suggestions');
    }
};