<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 5: Article Test Obligatoire
     *
     * Ajoute le champ is_test_article pour identifier les articles de test
     * soumis par les community writers en attente de validation.
     *
     * Workflow :
     * 1. Community writer s'inscrit → writer_status = pending_validation
     * 2. Writer crée un article et coche "Article test"
     * 3. Article sauvegardé avec is_test_article = true
     * 4. Admin examine l'article test dans /admin/writers/{id}
     * 5. Admin valide → writer_status = validated → Accès complet
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Marquer si c'est un article test (pour community writers)
            $table->boolean('is_test_article')->default(false)->after('status');

            // Index pour requêtes rapides (admin doit trouver articles test rapidement)
            $table->index('is_test_article');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['is_test_article']);
            $table->dropColumn('is_test_article');
        });
    }
};
