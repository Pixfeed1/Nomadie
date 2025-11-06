<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type')->default('customer')->after('sender_id');
            $table->string('recipient_type')->default('vendor')->after('recipient_id');
            $table->string('conversation_id')->nullable()->after('id');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            
            // Index pour les performances
            $table->index('conversation_id');
            $table->index(['sender_id', 'recipient_id']);
            $table->index('is_read');
            $table->index('trip_id');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['sender_type', 'recipient_type', 'conversation_id', 'is_archived', 'archived_at']);
            $table->dropIndex(['conversation_id']);
            $table->dropIndex(['sender_id', 'recipient_id']);
            $table->dropIndex(['is_read']);
            $table->dropIndex(['trip_id']);
        });
    }
};