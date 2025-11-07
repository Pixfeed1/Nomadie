<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Créer la table article_shares pour tracker les partages sociaux obligatoires
     * des rédacteurs (critère de réciprocité pour obtenir le DoFollow)
     */
    public function up(): void
    {
        Schema::create('article_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['facebook', 'twitter', 'linkedin', 'whatsapp', 'other'])
                  ->comment('Plateforme de partage');
            $table->string('share_url', 500)->nullable()->comment('URL du post partagé (optionnel)');
            $table->string('proof_screenshot', 500)->nullable()->comment('Capture preuve de partage (optionnel)');
            $table->timestamp('shared_at')->comment('Date du partage');
            $table->enum('status', ['pending', 'verified', 'rejected'])
                  ->default('pending')
                  ->comment('Statut validation : pending, verified, rejected');
            $table->text('admin_notes')->nullable()->comment('Notes admin sur la vérification');
            $table->timestamps();

            // Index pour performances
            $table->index(['article_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index('shared_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_shares');
    }
};
