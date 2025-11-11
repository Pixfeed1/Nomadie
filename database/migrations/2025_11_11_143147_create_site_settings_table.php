<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        DB::table('site_settings')->insert([
            [
                'key' => 'hero_banner_image',
                'value' => 'images/hero-bg.jpg',
                'type' => 'image',
                'description' => 'Image d\'arrière-plan du bandeau principal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_banner_title',
                'value' => 'Organisez et vivez des expériences authentiques',
                'type' => 'text',
                'description' => 'Titre du bandeau principal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_banner_subtitle',
                'value' => 'Voyages, circuits, séjours, hébergements et activités uniques dans le monde entier. Réservez directement auprès d\'organisateurs locaux experts.',
                'type' => 'text',
                'description' => 'Sous-titre du bandeau principal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_settings');
    }
};
