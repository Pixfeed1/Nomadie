<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'archived'])->default('draft');
            $table->integer('views_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->json('meta_data')->nullable(); // Pour stocker title, description, keywords
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index('slug');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};