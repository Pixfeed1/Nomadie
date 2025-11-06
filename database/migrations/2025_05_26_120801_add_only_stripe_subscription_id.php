<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendors', 'stripe_subscription_id')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            });
        }
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'stripe_subscription_id')) {
                $table->dropColumn('stripe_subscription_id');
            }
        });
    }
};