<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateServicesToServiceCategoriesAndAttributes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Attention: cette migration ne fonctionnera que si les tables service_categories et service_attributes existent
        if (!Schema::hasTable('service_categories') || !Schema::hasTable('service_attributes')) {
            return;
        }

        // 1. Récupérer tous les services existants
        if (Schema::hasTable('services')) {
            $services = DB::table('services')->get();
            
            // 2. Pour chaque service, créer une catégorie équivalente si nécessaire
            foreach ($services as $service) {
                // Vérifier si une catégorie similaire existe déjà
                $category = DB::table('service_categories')
                    ->where('name', 'like', '%' . $service->name . '%')
                    ->orWhere('slug', 'like', '%' . Str::slug($service->name) . '%')
                    ->first();
                    
                // Si aucune catégorie similaire n'existe, en créer une nouvelle
                if (!$category) {
                    $categoryId = DB::table('service_categories')->insertGetId([
                        'name' => $service->name,
                        'slug' => Str::slug($service->name),
                        'description' => $service->description ?? null,
                        'display_order' => 999, // Mettre à la fin
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $categoryId = $category->id;
                }
                
                // 3. Migrer les relations vendor-service vers vendor-service_category
                if (Schema::hasTable('service_vendor')) {
                    $vendorServices = DB::table('service_vendor')
                        ->where('service_id', $service->id)
                        ->get();
                        
                    foreach ($vendorServices as $vendorService) {
                        // Vérifier si la relation n'existe pas déjà
                        $exists = DB::table('vendor_service_category')
                            ->where('vendor_id', $vendorService->vendor_id)
                            ->where('service_category_id', $categoryId)
                            ->exists();
                            
                        if (!$exists) {
                            DB::table('vendor_service_category')->insert([
                                'vendor_id' => $vendorService->vendor_id,
                                'service_category_id' => $categoryId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Cette opération n'est pas réversible de manière simple
    }
}