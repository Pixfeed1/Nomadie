<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Ajouter stripe_subscription_id s'il n'existe pas
            if (!Schema::hasColumn('vendors', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            }
            
            // Ajouter subscription_status s'il n'existe pas
            if (!Schema::hasColumn('vendors', 'subscription_status')) {
                $table->enum('subscription_status', ['pending', 'active', 'cancelled', 'expired'])->default('pending')->after('payment_status');
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['stripe_subscription_id', 'subscription_status']);
        });
    }
};