<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_analysis_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_analysis_id')->constrained('seo_analyses')->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('seo_criteria')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->json('feedback')->nullable(); // Suggestions et détails spécifiques
            $table->json('data')->nullable(); // Données brutes de l'analyse
            $table->timestamps();
            
            $table->index(['seo_analysis_id', 'criterion_id']);
            $table->index('passed');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_analysis_details');
    }
};