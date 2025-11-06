<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Données du commentaire
            $table->string('author_name', 100)->nullable();
            $table->string('author_email', 150)->nullable();
            $table->text('content');
            
            // Modération
            $table->enum('status', ['pending', 'approved', 'spam', 'trash'])->default('pending');
            $table->integer('spam_score')->default(0);
            $table->json('spam_flags')->nullable();
            
            // Sécurité
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['article_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('spam_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};