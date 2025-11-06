<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajoute ici les colonnes manquantes pour users
            $table->timestamp('dofollow_achieved_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notifications_enabled')->default(true);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dofollow_achieved_at', 'is_active', 'notifications_enabled']);
        });
    }
};