<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // D'abord, changer temporairement en VARCHAR pour éviter les erreurs
        DB::statement("ALTER TABLE trips MODIFY COLUMN offer_type VARCHAR(50) DEFAULT 'sejour'");
        
        // Mettre à jour les valeurs existantes selon le pricing_mode
        DB::statement("UPDATE trips SET offer_type = 'activity' WHERE pricing_mode = 'per_person_activity'");
        DB::statement("UPDATE trips SET offer_type = 'accommodation' WHERE pricing_mode = 'per_night_property'");
        DB::statement("UPDATE trips SET offer_type = 'organized_trip' WHERE pricing_mode IN ('per_person_per_day', 'per_group')");
        DB::statement("UPDATE trips SET offer_type = 'custom' WHERE pricing_mode = 'custom'");
        
        // Maintenant changer en ENUM avec les bonnes valeurs
        DB::statement("ALTER TABLE trips MODIFY COLUMN offer_type ENUM('accommodation', 'organized_trip', 'activity', 'custom') DEFAULT 'organized_trip'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE trips MODIFY COLUMN offer_type VARCHAR(191) DEFAULT 'sejour'");
    }
};