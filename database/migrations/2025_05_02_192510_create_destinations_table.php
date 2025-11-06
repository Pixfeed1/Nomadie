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
        if (Schema::hasTable('destinations')) {
            Schema::table('destinations', function (Blueprint $table) {
                if (!Schema::hasColumn('destinations', 'continent')) {
                    $table->string('continent')->nullable();
                }
                if (!Schema::hasColumn('destinations', 'country')) {
                    $table->string('country')->nullable();
                }
                if (!Schema::hasColumn('destinations', 'city')) {
                    $table->string('city')->nullable();
                }
            });
        } else {
            Schema::create('destinations', function (Blueprint $table) {
                $table->id();
                $table->string('continent')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destinations');
    }
};