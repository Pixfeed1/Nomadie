<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class AddImageRatioCriterion extends Migration
{
    public function up()
    {
        SeoCriterion::updateOrCreate(
            ['code' => 'image_ratio'],
            [
                'name' => 'Ratio images / contenu',
                'description' => 'Vérifier qu\'il y a au moins 1 image tous les 300 mots',
                'category' => 'images',
                'max_score' => 15,
                'validation_rules' => [
                    'words_per_image' => 300,  // 1 image tous les 300 mots
                    'min_ratio' => 0.8,        // Tolérance : 80% du ratio optimal
                    'optimal_ratio' => 1.0     // Ratio parfait = 1 image / 300 mots
                ],
                'is_active' => true
            ]
        );
    }

    public function down()
    {
        SeoCriterion::where('code', 'image_ratio')->delete();
    }
}
