<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class AddLinksBalanceCriterion extends Migration
{
    public function up()
    {
        SeoCriterion::create([
            'code' => 'links_balance',
            'name' => 'Équilibre liens internes/externes',
            'description' => 'Ratio optimal entre liens internes et externes',
            'category' => 'technical',
            'max_score' => 5,
            'validation_rules' => [
                'optimal_internal_ratio' => 0.7,
                'min_internal_ratio' => 0.6,
                'max_internal_ratio' => 0.8,
                'min_total_links' => 3
            ],
            'is_active' => true
        ]);
    }

    public function down()
    {
        SeoCriterion::where('code', 'links_balance')->delete();
    }
}