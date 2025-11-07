<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('content_briefs', function (Blueprint $table) {
            $table->id();

            // Informations de base
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Type et catégorie
            $table->enum('type', ['destination', 'guide_pratique', 'culture', 'gastronomie', 'hebergement', 'transport', 'budget', 'custom'])->default('custom');
            $table->string('category')->nullable(); // Sous-catégorie libre

            // Contenu du brief
            $table->json('content_requirements')->nullable(); // Instructions détaillées
            $table->json('keywords')->nullable(); // Mots-clés à inclure
            $table->json('references')->nullable(); // URLs de référence

            // SEO Requirements
            $table->integer('min_words')->default(1500);
            $table->integer('target_score')->default(85); // Score NomadSEO visé
            $table->json('seo_requirements')->nullable(); // Critères SEO spécifiques

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Deadline et priorité
            $table->date('deadline')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Statut et progression
            $table->enum('status', ['draft', 'assigned', 'in_progress', 'pending_review', 'revision_requested', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('article_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->text('writer_notes')->nullable();

            // Dates de suivi
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_to');
            $table->index('deadline');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('content_briefs');
    }
};
