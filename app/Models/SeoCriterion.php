<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoCriterion extends Model
{
    use HasFactory;

    protected $table = 'seo_criteria';

    protected $fillable = [
        'category',
        'code',
        'name',
        'description',
        'max_score',
        'is_active',
        'validation_rules'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'max_score' => 'integer'
    ];

    /**
     * Relations
     */
    public function configurations()
    {
        return $this->hasMany(SeoConfiguration::class, 'criterion_id');
    }

    public function analysisDetails()
    {
        return $this->hasMany(SeoAnalysisDetail::class, 'criterion_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeContent($query)
    {
        return $query->where('category', 'content');
    }

    public function scopeTechnical($query)
    {
        return $query->where('category', 'technical');
    }

    public function scopeImages($query)
    {
        return $query->where('category', 'images');
    }

    public function scopeEngagement($query)
    {
        return $query->where('category', 'engagement');
    }

    public function scopeAuthenticity($query)
    {
        return $query->where('category', 'authenticity');
    }

    /**
     * Methods
     */
    public function getConfigurationFor($writerType, $mode = 'libre')
    {
        return $this->configurations()
                    ->where('writer_type', $writerType)
                    ->where('mode', $mode)
                    ->first();
    }

    public function getWeightFor($writerType, $mode = 'libre')
    {
        $config = $this->getConfigurationFor($writerType, $mode);
        return $config ? $config->weight : 1.0;
    }

    public function isRequiredFor($writerType, $mode = 'libre')
    {
        $config = $this->getConfigurationFor($writerType, $mode);
        return $config ? $config->is_required : false;
    }

    /**
     * Static methods pour créer les critères par défaut
     */
    public static function seedDefaultCriteria()
    {
        $criteria = [
            // Content
            ['category' => 'content', 'code' => 'title_length', 'name' => 'Longueur du titre', 'max_score' => 10],
            ['category' => 'content', 'code' => 'word_count', 'name' => 'Nombre de mots', 'max_score' => 15],
            ['category' => 'content', 'code' => 'readability', 'name' => 'Score de lisibilité', 'max_score' => 10],
            ['category' => 'content', 'code' => 'paragraph_structure', 'name' => 'Structure des paragraphes', 'max_score' => 5],
            
            // Technical
            ['category' => 'technical', 'code' => 'meta_description', 'name' => 'Meta description', 'max_score' => 10],
            ['category' => 'technical', 'code' => 'url_structure', 'name' => 'Structure URL', 'max_score' => 5],
            ['category' => 'technical', 'code' => 'keyword_density', 'name' => 'Densité mots-clés', 'max_score' => 10],
            ['category' => 'technical', 'code' => 'headings_hierarchy', 'name' => 'Hiérarchie des titres', 'max_score' => 10],
            ['category' => 'technical', 'code' => 'internal_links', 'name' => 'Liens internes', 'max_score' => 10],
            ['category' => 'technical', 'code' => 'schema_markup', 'name' => 'Schema markup', 'max_score' => 10],
            
            // Images
            ['category' => 'images', 'code' => 'image_count', 'name' => 'Nombre d\'images', 'max_score' => 10],
            ['category' => 'images', 'code' => 'image_alt_text', 'name' => 'Alt text des images', 'max_score' => 10],
            ['category' => 'images', 'code' => 'image_quality', 'name' => 'Qualité des images', 'max_score' => 5],
            
            // Engagement
            ['category' => 'engagement', 'code' => 'cta_presence', 'name' => 'Call-to-action', 'max_score' => 10],
            ['category' => 'engagement', 'code' => 'questions_to_reader', 'name' => 'Questions au lecteur', 'max_score' => 5],
            ['category' => 'engagement', 'code' => 'scannable_structure', 'name' => 'Structure scannable', 'max_score' => 10],
            
            // Authenticité
            ['category' => 'authenticity', 'code' => 'plagiarism_check', 'name' => 'Originalité', 'max_score' => 15],
            ['category' => 'authenticity', 'code' => 'emotional_words', 'name' => 'Mots émotionnels', 'max_score' => 5],
            ['category' => 'authenticity', 'code' => 'personal_experience', 'name' => 'Expérience personnelle', 'max_score' => 10],
        ];

        foreach ($criteria as $criterion) {
            static::firstOrCreate(
                ['code' => $criterion['code']],
                $criterion
            );
        }
    }
}