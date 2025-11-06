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
        Schema::table('vendors', function (Blueprint $table) {
            // Vérifier et supprimer les anciennes colonnes si elles existent
            if (Schema::hasColumn('vendors', 'name')) {
                $table->dropColumn('name');
            }
            
            // Ajouter les nouvelles colonnes s'ils n'existent pas déjà
            if (!Schema::hasColumn('vendors', 'company_name')) {
                $table->string('company_name');
            }
            
            if (!Schema::hasColumn('vendors', 'legal_status')) {
                $table->string('legal_status', 50);
            }
            
            if (!Schema::hasColumn('vendors', 'siret')) {
                $table->string('siret', 14);
            }
            
            if (!Schema::hasColumn('vendors', 'vat')) {
                $table->string('vat', 50)->nullable();
            }
            
            if (!Schema::hasColumn('vendors', 'website')) {
                $table->string('website')->nullable();
            }
            
            if (!Schema::hasColumn('vendors', 'postal_code')) {
                $table->string('postal_code', 10);
            }
            
            if (!Schema::hasColumn('vendors', 'city')) {
                $table->string('city', 100);
            }
            
            if (!Schema::hasColumn('vendors', 'country')) {
                $table->string('country', 2);
            }
            
            if (!Schema::hasColumn('vendors', 'rep_firstname')) {
                $table->string('rep_firstname', 100);
            }
            
            if (!Schema::hasColumn('vendors', 'rep_lastname')) {
                $table->string('rep_lastname', 100);
            }
            
            if (!Schema::hasColumn('vendors', 'rep_position')) {
                $table->string('rep_position', 100);
            }
            
            if (!Schema::hasColumn('vendors', 'rep_email')) {
                $table->string('rep_email');
            }
            
            if (!Schema::hasColumn('vendors', 'experience')) {
                $table->string('experience', 10)->nullable();
            }
            
            if (!Schema::hasColumn('vendors', 'confirmation_token')) {
                $table->string('confirmation_token', 64)->nullable();
            }
            
            if (!Schema::hasColumn('vendors', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            
            if (!Schema::hasColumn('vendors', 'subscription_plan')) {
                $table->string('subscription_plan', 20)->default('free');
            }
            
            if (!Schema::hasColumn('vendors', 'newsletter')) {
                $table->boolean('newsletter')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $columns = [
                'company_name', 'legal_status', 'siret', 'vat', 'website',
                'postal_code', 'city', 'country', 'rep_firstname', 'rep_lastname',
                'rep_position', 'rep_email', 'experience', 'confirmation_token',
                'email_verified_at', 'subscription_plan', 'newsletter'
            ];
            
            $table->dropColumn($columns);
            
            // Rétablir les colonnes supprimées si nécessaire
            $table->string('name')->nullable();
        });
    }
};