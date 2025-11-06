<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe déjà avant de tenter de la créer
        if (!Schema::hasTable('service_vendor')) {
            Schema::create('service_vendor', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                // Index unique pour éviter les doublons
                $table->unique(['service_id', 'vendor_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_vendor');
    }
};