<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('subscriptions', 'metadata')) {
                $table->json('metadata')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'current_period_start')) {
                $table->timestamp('current_period_start')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'current_period_end')) {
                $table->timestamp('current_period_end')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['metadata', 'current_period_start', 'current_period_end']);
        });
    }
};