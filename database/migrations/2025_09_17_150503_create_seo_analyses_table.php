<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('writer_type', ['communaute', 'client', 'partenaire', 'equipe']);
            $table->enum('mode', ['libre', 'commande_interne']);
            
            // Scores
            $table->decimal('global_score', 5, 2)->default(0);
            $table->decimal('content_score', 5, 2)->default(0);
            $table->decimal('technical_score', 5, 2)->default(0);
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->decimal('authenticity_score', 5, 2)->default(0);
            $table->decimal('images_score', 5, 2)->default(0);
            
            // Statut et paramètres
            $table->enum('status', ['draft', 'analyzing', 'completed', 'archived'])->default('draft');
            $table->boolean('is_dofollow')->default(false);
            $table->boolean('has_auto_promo')->default(false); // Pour partenaires
            $table->decimal('auto_promo_percentage', 5, 2)->nullable(); // % auto-promo détecté
            
            // Métriques
            $table->integer('word_count')->default(0);
            $table->integer('reading_time')->default(0); // en minutes
            $table->integer('images_count')->default(0);
            $table->integer('internal_links_count')->default(0);
            $table->integer('external_links_count')->default(0);
            
            // Données JSON pour détails
            $table->json('keyword_data')->nullable(); // mots-clés principaux, LSI, densité
            $table->json('schema_markup')->nullable(); // schemas détectés
            $table->json('open_graph')->nullable(); // meta OG générées
            
            $table->timestamps();
            
            $table->index(['article_id', 'status']);
            $table->index(['user_id', 'writer_type']);
            $table->index('is_dofollow');
            $table->index('global_score');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_analyses');
    }
};