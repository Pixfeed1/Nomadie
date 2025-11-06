<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCountriesTable extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà avant de les ajouter
            if (!Schema::hasColumn('countries', 'continent_id')) {
                $table->foreignId('continent_id')->nullable()->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('countries', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'image')) {
                $table->string('image')->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'popular')) {
                $table->boolean('popular')->default(false);
            }
            
            if (!Schema::hasColumn('countries', 'rating')) {
                $table->float('rating', 2, 1)->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'best_time')) {
                $table->string('best_time')->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'position')) {
                $table->json('position')->nullable();
            }
            
            if (!Schema::hasColumn('countries', 'tags')) {
                $table->json('tags')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Supprimer les colonnes dans le rollback
            $table->dropColumn([
                'continent_id', 
                'slug',
                'image', 
                'description', 
                'popular', 
                'rating', 
                'best_time', 
                'position', 
                'tags'
            ]);
        });
    }
}