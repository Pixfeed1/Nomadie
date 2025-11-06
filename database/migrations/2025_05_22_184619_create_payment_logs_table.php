<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // vendor_subscription_webhook, trip_booking_webhook, etc.
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->json('metadata')->nullable(); // Stocker les métadonnées Stripe
            $table->timestamp('processed_at');
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['type', 'processed_at']);
            $table->index('stripe_session_id');
            $table->index('stripe_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};