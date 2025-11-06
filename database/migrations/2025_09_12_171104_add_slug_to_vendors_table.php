<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Vendor;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('company_name');
            $table->index('slug');
        });
        
        // Générer les slugs pour les vendors existants
        Vendor::all()->each(function ($vendor) {
            $baseSlug = Str::slug($vendor->company_name ?: 'vendor-' . $vendor->id);
            $slug = $baseSlug;
            $count = 1;
            
            // Vérifier l'unicité et ajouter un suffixe si nécessaire
            while (Vendor::where('slug', $slug)->where('id', '!=', $vendor->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            
            $vendor->slug = $slug;
            $vendor->save();
        });
        
        // Rendre la colonne non-nullable après avoir généré tous les slugs
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};