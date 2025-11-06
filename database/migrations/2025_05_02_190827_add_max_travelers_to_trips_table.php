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
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'max_travelers')) {
                $table->integer('max_travelers')->default(1);
            }
            if (!Schema::hasColumn('trips', 'duration')) {
                $table->integer('duration')->nullable();
            }
            if (!Schema::hasColumn('trips', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('trips', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('trips', 'featured')) {
                $table->boolean('featured')->default(false);
            }
            // Relations
            if (!Schema::hasColumn('trips', 'destination_id')) {
                $table->foreignId('destination_id')->nullable();
            }
            if (!Schema::hasColumn('trips', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable();
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
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'max_travelers',
                'duration',
                'type',
                'price',
                'featured',
                'destination_id',
                'vendor_id'
            ]);
        });
    }
};