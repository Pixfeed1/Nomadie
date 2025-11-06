<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Vérifier et ajouter chaque colonne individuellement
            
            // Informations de base
            if (!Schema::hasColumn('trips', 'title')) {
                $table->string('title')->after('id');
            }
            
            if (!Schema::hasColumn('trips', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }
            
            if (!Schema::hasColumn('trips', 'short_description')) {
                $table->text('short_description')->after('slug');
            }
            
            if (!Schema::hasColumn('trips', 'description')) {
                $table->text('description')->after('short_description');
            }
            
            if (!Schema::hasColumn('trips', 'type')) {
                $table->enum('type', ['fixed', 'circuit'])->default('fixed')->after('description');
            }
            
            // Relations - Vérifier si elles existent déjà
            if (!Schema::hasColumn('trips', 'vendor_id')) {
                $table->foreignId('vendor_id')->after('type')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('trips', 'destination_id')) {
                $table->foreignId('destination_id')->after('vendor_id')->constrained('countries');
            }
            
            if (!Schema::hasColumn('trips', 'travel_type_id')) {
                $table->foreignId('travel_type_id')->after('destination_id')->constrained();
            }
            
            // Prix et capacité - certains existent peut-être déjà
            if (!Schema::hasColumn('trips', 'price')) {
                $table->decimal('price', 10, 2)->after('travel_type_id');
            }
            
            if (!Schema::hasColumn('trips', 'duration')) {
                $table->integer('duration')->after('price');
            }
            
            if (!Schema::hasColumn('trips', 'max_travelers')) {
                $table->integer('max_travelers')->after('duration');
            }
            
            if (!Schema::hasColumn('trips', 'min_travelers')) {
                $table->integer('min_travelers')->nullable()->default(1)->after('max_travelers');
            }
            
            // Dates
            if (!Schema::hasColumn('trips', 'departure_date')) {
                $table->date('departure_date')->after('min_travelers');
            }
            
            if (!Schema::hasColumn('trips', 'return_date')) {
                $table->date('return_date')->after('departure_date');
            }
            
            // Détails physiques et lieu
            if (!Schema::hasColumn('trips', 'physical_level')) {
                $table->enum('physical_level', ['easy', 'moderate', 'difficult', 'expert'])->after('return_date');
            }
            
            if (!Schema::hasColumn('trips', 'meeting_point')) {
                $table->string('meeting_point')->nullable()->after('physical_level');
            }
            
            if (!Schema::hasColumn('trips', 'meeting_time')) {
                $table->time('meeting_time')->nullable()->after('meeting_point');
            }
            
            if (!Schema::hasColumn('trips', 'meeting_address')) {
                $table->text('meeting_address')->nullable()->after('meeting_time');
            }
            
            if (!Schema::hasColumn('trips', 'meeting_instructions')) {
                $table->text('meeting_instructions')->nullable()->after('meeting_address');
            }
            
            // Services et conditions
            if (!Schema::hasColumn('trips', 'included')) {
                $table->json('included')->nullable()->after('meeting_instructions');
            }
            
            if (!Schema::hasColumn('trips', 'requirements')) {
                $table->text('requirements')->nullable()->after('included');
            }
            
            if (!Schema::hasColumn('trips', 'meal_plan')) {
                $table->enum('meal_plan', ['none', 'breakfast', 'half_board', 'full_board', 'all_inclusive'])
                      ->default('none')->after('requirements');
            }
            
            // Programme
            if (!Schema::hasColumn('trips', 'itinerary')) {
                $table->json('itinerary')->nullable()->after('meal_plan');
            }
            
            // Images
            if (!Schema::hasColumn('trips', 'images')) {
                $table->json('images')->nullable()->after('itinerary');
            }
            
            // Statut et méta
            if (!Schema::hasColumn('trips', 'status')) {
                $table->enum('status', ['draft', 'active', 'inactive'])->default('draft')->after('images');
            }
            
            if (!Schema::hasColumn('trips', 'featured')) {
                $table->boolean('featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('trips', 'views_count')) {
                $table->integer('views_count')->default(0)->after('featured');
            }
            
            // SEO
            if (!Schema::hasColumn('trips', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('views_count');
            }
            
            if (!Schema::hasColumn('trips', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
        
        // Ajouter les index après avoir ajouté les colonnes
        Schema::table('trips', function (Blueprint $table) {
            // Vérifier si les index existent avant de les ajouter
            $indexes = collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('trips'));
            
            if (!$indexes->has('trips_vendor_id_status_index')) {
                $table->index(['vendor_id', 'status']);
            }
            
            if (!$indexes->has('trips_destination_id_status_index')) {
                $table->index(['destination_id', 'status']);
            }
            
            if (!$indexes->has('trips_departure_date_index')) {
                $table->index('departure_date');
            }
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Supprimer les index d'abord
            $table->dropIndex(['vendor_id', 'status']);
            $table->dropIndex(['destination_id', 'status']);
            $table->dropIndex(['departure_date']);
            
            // Supprimer les foreign keys
            if (Schema::hasColumn('trips', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
            }
            if (Schema::hasColumn('trips', 'destination_id')) {
                $table->dropForeign(['destination_id']);
            }
            if (Schema::hasColumn('trips', 'travel_type_id')) {
                $table->dropForeign(['travel_type_id']);
            }
        });
        
        Schema::table('trips', function (Blueprint $table) {
            // Supprimer les colonnes qui ont été ajoutées
            $columnsToRemove = [
                'title', 'slug', 'short_description', 'description', 'type',
                'vendor_id', 'destination_id', 'travel_type_id',
                'price', 'duration', 'max_travelers', 'min_travelers',
                'departure_date', 'return_date',
                'physical_level', 'meeting_point', 'meeting_time', 
                'meeting_address', 'meeting_instructions',
                'included', 'requirements', 'meal_plan',
                'itinerary', 'images',
                'status', 'featured', 'views_count',
                'meta_title', 'meta_description'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('trips', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};