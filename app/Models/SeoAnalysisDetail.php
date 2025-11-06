<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoAnalysisDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_analysis_id',
        'criterion_id',
        'score',
        'passed',
        'feedback',
        'data'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'passed' => 'boolean',
        'feedback' => 'array',
        'data' => 'array'
    ];

    /**
     * Relations
     */
    public function analysis()
    {
        return $this->belongsTo(SeoAnalysis::class, 'seo_analysis_id');
    }

    public function criterion()
    {
        return $this->belongsTo(SeoCriterion::class, 'criterion_id');
    }

    /**
     * Scopes
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('criterion', function ($q) use ($category) {
            $q->where('category', $category);
        });
    }

    /**
     * Methods
     */
    public function generateFeedback()
    {
        $feedback = [];
        
        if (!$this->passed) {
            $feedback['status'] = 'error';
            $feedback['message'] = $this->getErrorMessage();
            $feedback['suggestions'] = $this->getSuggestions();
        } else {
            $feedback['status'] = 'success';
            $feedback['message'] = $this->getSuccessMessage();
        }
        
        $feedback['score_details'] = [
            'obtained' => $this->score,
            'maximum' => $this->criterion->max_score,
            'percentage' => $this->criterion->max_score > 0 
                ? round(($this->score / $this->criterion->max_score) * 100, 2) 
                : 0
        ];
        
        $this->feedback = $feedback;
        return $feedback;
    }

    protected function getErrorMessage()
    {
        $messages = [
            'title_length' => 'Le titre doit contenir entre 50 et 60 caractères',
            'word_count' => 'L\'article doit contenir au moins 1500 mots',
            'meta_description' => 'La meta description doit contenir entre 150 et 160 caractères',
            'image_count' => 'L\'article doit contenir au moins 3 images',
            'image_alt_text' => 'Toutes les images doivent avoir un texte alternatif',
            'internal_links' => 'L\'article doit contenir au moins 3 liens internes',
            'readability' => 'Le score de lisibilité est trop faible',
            'keyword_density' => 'La densité de mots-clés n\'est pas optimale',
            'plagiarism_check' => 'Du contenu dupliqué a été détecté',
            'cta_presence' => 'L\'article doit contenir au moins un call-to-action',
        ];
        
        return $messages[$this->criterion->code] ?? 'Ce critère n\'est pas respecté';
    }

    protected function getSuccessMessage()
    {
        $messages = [
            'title_length' => 'La longueur du titre est optimale',
            'word_count' => 'Le nombre de mots est suffisant',
            'meta_description' => 'La meta description est bien optimisée',
            'image_count' => 'Le nombre d\'images est suffisant',
            'image_alt_text' => 'Toutes les images ont un texte alternatif',
            'internal_links' => 'Le maillage interne est bien fait',
            'readability' => 'L\'article est facile à lire',
            'keyword_density' => 'La densité de mots-clés est optimale',
            'plagiarism_check' => 'Le contenu est original',
            'cta_presence' => 'Les call-to-action sont bien présents',
        ];
        
        return $messages[$this->criterion->code] ?? 'Ce critère est bien respecté';
    }

    protected function getSuggestions()
    {
        $suggestions = [
            'title_length' => [
                'Ajustez la longueur du titre pour qu\'il soit entre 50 et 60 caractères',
                'Incluez votre mot-clé principal dans le titre'
            ],
            'word_count' => [
                'Développez davantage votre contenu',
                'Ajoutez des sections supplémentaires pour enrichir l\'article',
                'Incluez des exemples concrets et des anecdotes personnelles'
            ],
            'meta_description' => [
                'Rédigez une meta description entre 150 et 160 caractères',
                'Incluez votre mot-clé principal',
                'Ajoutez un call-to-action dans la description'
            ],
            'image_count' => [
                'Ajoutez plus d\'images pour illustrer votre propos',
                'Privilégiez des photos authentiques de vos expériences',
                'Variez les types d\'images (paysages, détails, cartes)'
            ],
            'image_alt_text' => [
                'Ajoutez un texte alternatif descriptif à toutes les images',
                'Incluez vos mots-clés dans les alt text de manière naturelle'
            ],
            'internal_links' => [
                'Ajoutez des liens vers d\'autres articles pertinents',
                'Créez un maillage interne cohérent',
                'Utilisez des ancres de liens descriptives'
            ],
            'readability' => [
                'Utilisez des phrases plus courtes',
                'Ajoutez des sous-titres pour structurer le contenu',
                'Utilisez des listes à puces pour améliorer la lisibilité'
            ],
            'keyword_density' => [
                'Ajustez l\'utilisation de vos mots-clés principaux',
                'Utilisez des synonymes et variations',
                'Évitez la sur-optimisation'
            ],
            'plagiarism_check' => [
                'Reformulez les passages similaires à d\'autres contenus',
                'Ajoutez votre touche personnelle et vos expériences',
                'Citez vos sources si nécessaire'
            ],
            'cta_presence' => [
                'Ajoutez un call-to-action clair',
                'Encouragez les lecteurs à commenter ou partager',
                'Proposez des articles connexes à consulter'
            ],
        ];
        
        return $suggestions[$this->criterion->code] ?? ['Améliorez ce critère pour augmenter votre score'];
    }

    /**
     * Accessors
     */
    public function getScorePercentageAttribute()
    {
        if ($this->criterion->max_score == 0) {
            return 0;
        }
        
        return round(($this->score / $this->criterion->max_score) * 100, 2);
    }
}