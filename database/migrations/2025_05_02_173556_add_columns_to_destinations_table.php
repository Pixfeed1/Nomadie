<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('destinations', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (!Schema::hasColumn('destinations', 'continent')) {
                $table->string('continent')->nullable()->after('id');
            }
            if (!Schema::hasColumn('destinations', 'country')) {
                $table->string('country')->nullable()->after('continent');
            }
            if (!Schema::hasColumn('destinations', 'city')) {
                $table->string('city')->nullable()->after('country');
            }
            if (!Schema::hasColumn('destinations', 'description')) {
                $table->text('description')->nullable()->after('city');
            }
            if (!Schema::hasColumn('destinations', 'image_path')) {
                $table->string('image_path')->nullable()->after('description');
            }
            if (!Schema::hasColumn('destinations', 'active')) {
                $table->boolean('active')->default(true)->after('image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'continent',
                'country',
                'city',
                'description',
                'image_path',
                'active'
            ]);
        });
    }
};