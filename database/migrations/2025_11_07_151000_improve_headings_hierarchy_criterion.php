<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class ImproveHeadingsHierarchyCriterion extends Migration
{
    public function up()
    {
        $criterion = SeoCriterion::where('code', 'headings_hierarchy')->first();

        if ($criterion) {
            // Mettre à jour les règles pour inclure H4-H6 et validation hiérarchique
            $criterion->update([
                'validation_rules' => [
                    'h1_count' => 1,           // Exactement 1 H1
                    'min_h2' => 3,             // Minimum 3 H2 (structuration)
                    'check_hierarchy' => true,  // Vérifier l'ordre logique H1 > H2 > H3 > H4 > H5 > H6
                    'warn_deep_nesting' => 5,   // Avertir si profondeur > 5 niveaux
                    'max_skip_level' => 1       // Ne pas sauter de niveau (H2 -> H4 = interdit)
                ],
                'description' => 'Vérifier la hiérarchie complète des titres (H1-H6) et l\'ordre logique sans saut de niveau',
                'max_score' => 12  // Augmenté car plus de validations
            ]);
        }
    }

    public function down()
    {
        $criterion = SeoCriterion::where('code', 'headings_hierarchy')->first();

        if ($criterion) {
            // Revenir aux anciennes règles
            $criterion->update([
                'validation_rules' => [
                    'h1_count' => 1,
                    'min_h2' => 3
                ],
                'description' => 'Vérifier la structure des titres',
                'max_score' => 10
            ]);
        }
    }
}
