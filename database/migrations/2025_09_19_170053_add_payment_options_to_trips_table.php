<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->boolean('payment_online_required')->default(true)->after('price');
            $table->boolean('free_booking_allowed')->default(false)->after('payment_online_required');
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'payment_online_required',
                'free_booking_allowed'
            ]);
        });
    }
};