<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'slug')) {
                $table->string('slug')->nullable();
            }
            if (!Schema::hasColumn('trips', 'short_description')) {
                $table->text('short_description')->nullable();
            }
            if (!Schema::hasColumn('trips', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('trips', 'max_travelers')) {
                $table->integer('max_travelers')->default(10);
            }
            if (!Schema::hasColumn('trips', 'min_travelers')) {
                $table->integer('min_travelers')->default(1);
            }
            if (!Schema::hasColumn('trips', 'travel_type_id')) {
                $table->foreignId('travel_type_id')->nullable();
            }
            if (!Schema::hasColumn('trips', 'rating')) {
                $table->float('rating', 2, 1)->default(0);
            }
            if (!Schema::hasColumn('trips', 'reviews_count')) {
                $table->integer('reviews_count')->default(0);
            }
            if (!Schema::hasColumn('trips', 'featured')) {
                $table->boolean('featured')->default(false);
            }
            if (!Schema::hasColumn('trips', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('trips', 'cover_image')) {
                $table->string('cover_image')->nullable();
            }
            if (!Schema::hasColumn('trips', 'physical_level')) {
                $table->string('physical_level')->nullable();
            }
            if (!Schema::hasColumn('trips', 'included')) {
                $table->json('included')->nullable();
            }
            if (!Schema::hasColumn('trips', 'not_included')) {
                $table->json('not_included')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'short_description', 'duration', 'max_travelers', 
                'min_travelers', 'travel_type_id', 'rating', 'reviews_count',
                'featured', 'image', 'cover_image', 'physical_level',
                'included', 'not_included'
            ]);
        });
    }
};