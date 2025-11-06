<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after('firstname');
            $table->string('pseudo')->nullable()->unique()->after('lastname');
            $table->string('avatar')->nullable()->after('email');
            $table->string('role')->default('customer')->after('avatar');
            $table->boolean('newsletter')->default(false)->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname', 'pseudo', 'avatar', 'role', 'newsletter']);
        });
    }
};