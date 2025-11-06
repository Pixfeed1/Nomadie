<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_criteria', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['content', 'technical', 'images', 'engagement', 'authenticity']);
            $table->string('code')->unique(); // ex: title_length, keyword_density
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_score')->default(10);
            $table->boolean('is_active')->default(true);
            $table->json('validation_rules')->nullable(); // Règles de validation spécifiques
            $table->timestamps();
            
            $table->index('category');
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_criteria');
    }
};
