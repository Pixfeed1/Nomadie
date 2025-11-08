<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('published_at');
        });

        // Ajouter 'scheduled' au ENUM status
        DB::statement("ALTER TABLE articles MODIFY status ENUM('draft', 'pending', 'published', 'scheduled')");
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });

        // Revenir à l'ancien ENUM
        DB::statement("ALTER TABLE articles MODIFY status ENUM('draft', 'pending', 'published')");
    }
};
