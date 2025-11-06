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
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('destination_vendor')) {
            Schema::create('destination_vendor', function (Blueprint $table) {
                $table->id();
                $table->foreignId('destination_id')->constrained()->onDelete('cascade');
                $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                // Index unique pour éviter les doublons
                $table->unique(['destination_id', 'vendor_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_vendor');
    }
};