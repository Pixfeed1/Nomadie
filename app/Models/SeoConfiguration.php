<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'writer_type',
        'mode',
        'criterion_id',
        'weight',
        'threshold',
        'is_required',
        'custom_rules'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'threshold' => 'decimal:2',
        'is_required' => 'boolean',
        'custom_rules' => 'array'
    ];

    /**
     * Relations
     */
    public function criterion()
    {
        return $this->belongsTo(SeoCriterion::class, 'criterion_id');
    }

    /**
     * Scopes
     */
    public function scopeForWriter($query, $writerType, $mode = 'libre')
    {
        return $query->where('writer_type', $writerType)
                     ->where('mode', $mode);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Methods
     */
    public static function getConfigurationsFor($writerType, $mode = 'libre')
    {
        return static::with('criterion')
                     ->forWriter($writerType, $mode)
                     ->get();
    }

    public static function seedDefaultConfigurations()
    {
        // Configuration par défaut pour chaque type de rédacteur
        $configurations = [
            // Communauté - Mode Libre
            [
                'writer_type' => 'communaute',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'word_count' => ['weight' => 1.5, 'threshold' => 8, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 6, 'is_required' => false],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'keyword_density' => ['weight' => 0.8, 'threshold' => 5, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 6, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.8, 'threshold' => 7, 'is_required' => false],
                    'internal_links' => ['weight' => 1.2, 'threshold' => 6, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 2.0, 'threshold' => 8, 'is_required' => true],
                    'cta_presence' => ['weight' => 0.8, 'threshold' => 5, 'is_required' => false],
                ]
            ],
            
            // Client-Contributeur - Mode Libre  
            [
                'writer_type' => 'client',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'word_count' => ['weight' => 1.2, 'threshold' => 7, 'is_required' => true],
                    'readability' => ['weight' => 0.9, 'threshold' => 5, 'is_required' => false],
                    'meta_description' => ['weight' => 0.9, 'threshold' => 6, 'is_required' => false],
                    'keyword_density' => ['weight' => 0.7, 'threshold' => 5, 'is_required' => false],
                    'image_count' => ['weight' => 1.2, 'threshold' => 7, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.7, 'threshold' => 6, 'is_required' => false],
                    'internal_links' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 1.8, 'threshold' => 7, 'is_required' => true],
                    'personal_experience' => ['weight' => 1.5, 'threshold' => 8, 'is_required' => true],
                ]
            ],
            
            // Partenaire-Rédacteur - Mode Libre avec contrôle renforcé
            [
                'writer_type' => 'partenaire',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 8, 'is_required' => true],
                    'word_count' => ['weight' => 1.3, 'threshold' => 8, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'keyword_density' => ['weight' => 1.0, 'threshold' => 6, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.9, 'threshold' => 7, 'is_required' => false],
                    'internal_links' => ['weight' => 1.3, 'threshold' => 7, 'is_required' => true],
                    'plagiarism_check' => ['weight' => 2.5, 'threshold' => 9, 'is_required' => true],
                    'cta_presence' => ['weight' => 0.5, 'threshold' => 4, 'is_required' => false],
                    // Critère spécial pour auto-promo (sera géré différemment)
                    'auto_promo_limit' => [
                        'weight' => 2.0, 
                        'threshold' => 20, // Max 20% d'auto-promo
                        'is_required' => true,
                        'custom_rules' => ['max_percentage' => 20]
                    ],
                ]
            ],
            
            // Équipe Nomadie - Mode Libre
            [
                'writer_type' => 'equipe',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'word_count' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'readability' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'keyword_density' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'image_alt_text' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'internal_links' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                    'cta_presence' => ['weight' => 1.0, 'threshold' => 5, 'is_required' => false],
                ]
            ],
            
            // Équipe Nomadie - Mode Commande Interne (exemple)
            [
                'writer_type' => 'equipe',
                'mode' => 'commande_interne',
                'configs' => [
                    // Configuration personnalisable par commande
                    // Les valeurs peuvent être overridées dynamiquement
                    'title_length' => ['weight' => 1.0, 'threshold' => 8, 'is_required' => true],
                    'word_count' => ['weight' => 1.0, 'threshold' => 8, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 8, 'is_required' => true],
                    'keyword_density' => ['weight' => 1.2, 'threshold' => 7, 'is_required' => true],
                    'image_count' => ['weight' => 1.0, 'threshold' => 7, 'is_required' => true],
                    'image_alt_text' => ['weight' => 1.0, 'threshold' => 8, 'is_required' => true],
                    'internal_links' => ['weight' => 1.5, 'threshold' => 8, 'is_required' => true],
                    'schema_markup' => ['weight' => 1.2, 'threshold' => 8, 'is_required' => true],
                    'plagiarism_check' => ['weight' => 1.5, 'threshold' => 9, 'is_required' => true],
                ]
            ],
        ];

        foreach ($configurations as $config) {
            foreach ($config['configs'] as $criterionCode => $settings) {
                $criterion = SeoCriterion::where('code', $criterionCode)->first();
                
                if ($criterion) {
                    static::firstOrCreate(
                        [
                            'writer_type' => $config['writer_type'],
                            'mode' => $config['mode'],
                            'criterion_id' => $criterion->id,
                        ],
                        [
                            'weight' => $settings['weight'],
                            'threshold' => $settings['threshold'],
                            'is_required' => $settings['is_required'],
                            'custom_rules' => $settings['custom_rules'] ?? null
                        ]
                    );
                }
            }
        }
    }

    /**
     * Accessors
     */
    public function getWeightedScoreAttribute($score)
    {
        return $score * $this->weight;
    }
}