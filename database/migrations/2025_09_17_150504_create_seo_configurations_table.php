<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_configurations', function (Blueprint $table) {
            $table->id();
            $table->enum('writer_type', ['community', 'client_contributor', 'partner', 'team']);
            $table->enum('mode', ['libre', 'commande_interne']);
            $table->foreignId('criterion_id')->constrained('seo_criteria')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->default(1.0); // Pondération (0.5 = 50%, 2.0 = 200%)
            $table->decimal('threshold', 5, 2)->nullable(); // Seuil minimum pour ce critère
            $table->boolean('is_required')->default(false); // Critère obligatoire ou non
            $table->json('custom_rules')->nullable(); // Règles spécifiques par type/mode
            $table->timestamps();
            
            $table->unique(['writer_type', 'mode', 'criterion_id']);
            $table->index(['writer_type', 'mode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_configurations');
    }
};
