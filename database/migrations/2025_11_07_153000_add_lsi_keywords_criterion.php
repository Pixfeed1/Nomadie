<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class AddLsiKeywordsCriterion extends Migration
{
    public function up()
    {
        SeoCriterion::updateOrCreate(
            ['code' => 'lsi_keywords'],
            [
                'name' => 'Richesse sémantique (LSI)',
                'description' => 'Analyser la diversité du vocabulaire et la richesse sémantique du contenu',
                'category' => 'content',
                'max_score' => 15,
                'validation_rules' => [
                    'min_lexical_diversity' => 0.45,    // Min 45% de mots uniques
                    'optimal_lexical_diversity' => 0.60, // Optimal 60%+ de mots uniques
                    'max_word_repetition' => 3.0,        // Max 3% pour un seul mot
                    'min_avg_word_length' => 5.0,        // Longueur moyenne min 5 caractères
                    'min_long_words_ratio' => 0.25,      // Min 25% de mots de 7+ caractères
                    'check_top_keywords_balance' => true // Vérifier distribution des top keywords
                ],
                'is_active' => true
            ]
        );
    }

    public function down()
    {
        SeoCriterion::where('code', 'lsi_keywords')->delete();
    }
}
