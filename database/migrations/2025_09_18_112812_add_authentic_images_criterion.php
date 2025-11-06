<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCriterion;

class AddAuthenticImagesCriterion extends Migration
{
    public function up()
    {
        SeoCriterion::create([
            'code' => 'authentic_images',
            'name' => 'Images authentiques',
            'description' => 'Privilégier les photos personnelles vs stock',
            'category' => 'images',
            'max_score' => 10,
            'validation_rules' => [
                'min_authentic_ratio' => 0.5,
                'preferred_ratio' => 0.8
            ],
            'is_active' => true
        ]);
    }

    public function down()
    {
        SeoCriterion::where('code', 'authentic_images')->delete();
    }
}