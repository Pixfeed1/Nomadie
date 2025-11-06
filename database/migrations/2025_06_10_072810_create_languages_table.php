<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // fr, en, zh-CN, zh-HK
            $table->string('name'); // Français, English, 中文(简体)
            $table->string('native_name')->nullable(); // Nom dans la langue native
            $table->string('region')->nullable(); // France, China, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false); // Pour les langues populaires
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Index pour les performances
            $table->index('is_active');
            $table->index('is_popular');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}