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
        Schema::table('users', function (Blueprint $table) {
            // Colonnes pour le système de streak quotidien
            $table->integer('daily_streak')->default(0)->comment('Nombre de jours consécutifs de connexion');
            $table->integer('longest_streak')->default(0)->comment('Record du plus long streak de connexion');
            $table->date('last_streak_date')->nullable()->comment('Date du dernier streak calculé');
            
            // Index pour optimiser les requêtes de streak
            $table->index(['daily_streak']);
            $table->index(['longest_streak']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les index d'abord
            $table->dropIndex(['daily_streak']);
            $table->dropIndex(['longest_streak']);
            
            // Supprimer les colonnes
            $table->dropColumn([
                'daily_streak',
                'longest_streak',
                'last_streak_date'
            ]);
        });
    }
};