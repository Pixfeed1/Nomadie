<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class ImproveMetaDescriptionCriterion extends Migration
{
    public function up()
    {
        $criterion = SeoCriterion::where('code', 'meta_description')->first();

        if ($criterion) {
            // Améliorer les règles pour inclure vérifications qualité
            $criterion->update([
                'validation_rules' => [
                    'min' => 150,
                    'max' => 160,
                    'check_keywords' => true,      // Vérifier présence mots-clés du titre
                    'check_cta' => true,           // Vérifier présence CTA
                    'check_uniqueness' => true,    // Vérifier différente de excerpt
                    'min_keywords_match' => 2,     // Min 2 mots-clés du titre
                    'cta_words' => [
                        'découvrez', 'explorez', 'consultez', 'lisez', 'apprenez',
                        'trouvez', 'visitez', 'réservez', 'planifiez', 'préparez'
                    ]
                ],
                'description' => 'Analyser la qualité de la meta description (longueur, mots-clés, CTA, unicité)',
                'max_score' => 12  // Augmenté car plus de validations
            ]);
        }
    }

    public function down()
    {
        $criterion = SeoCriterion::where('code', 'meta_description')->first();

        if ($criterion) {
            // Revenir aux anciennes règles
            $criterion->update([
                'validation_rules' => [
                    'min' => 150,
                    'max' => 160
                ],
                'description' => 'Vérifier la longueur de la meta description',
                'max_score' => 10
            ]);
        }
    }
}
