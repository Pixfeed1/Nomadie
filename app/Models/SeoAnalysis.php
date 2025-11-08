<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoAnalysis extends Model
{
    use HasFactory;

    protected $table = 'seo_analyses';

    protected $fillable = [
        'article_id',
        'user_id',
        'writer_type',
        'mode',
        'global_score',
        'content_score',
        'technical_score',
        'engagement_score',
        'authenticity_score',
        'images_score',
        'status',
        'is_dofollow',
        'has_auto_promo',
        'auto_promo_percentage',
        'word_count',
        'reading_time',
        'images_count',
        'internal_links_count',
        'external_links_count',
        'keyword_data',
        'schema_markup',
        'open_graph'
    ];

    protected $casts = [
        'global_score' => 'decimal:2',
        'content_score' => 'decimal:2',
        'technical_score' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'authenticity_score' => 'decimal:2',
        'images_score' => 'decimal:2',
        'auto_promo_percentage' => 'decimal:2',
        'is_dofollow' => 'boolean',
        'has_auto_promo' => 'boolean',
        'keyword_data' => 'array',
        'schema_markup' => 'array',
        'open_graph' => 'array',
        'word_count' => 'integer',
        'reading_time' => 'integer',
        'images_count' => 'integer',
        'internal_links_count' => 'integer',
        'external_links_count' => 'integer'
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($analysis) {
            // Vérifier automatiquement si l'article passe en dofollow
            $analysis->checkDoFollowEligibility();
        });

        static::updated(function ($analysis) {
            // Re-vérifier le statut dofollow après mise à jour
            if ($analysis->isDirty('global_score')) {
                $analysis->checkDoFollowEligibility();
            }
        });
    }

    /**
     * Relations
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SeoAnalysisDetail::class);
    }

    public function suggestions()
    {
        return $this->hasMany(SeoSuggestion::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDoFollow($query)
    {
        return $query->where('is_dofollow', true);
    }

    public function scopeByWriterType($query, $type)
    {
        return $query->where('writer_type', $type);
    }

    public function scopeAboveScore($query, $score)
    {
        return $query->where('global_score', '>=', $score);
    }

    /**
     * Methods
     */
    public function checkDoFollowEligibility()
    {
        // Déléguer la vérification au job CheckDoFollowStatus
        // qui contient toute la logique stricte (score, commentaires, partages, etc.)
        \App\Jobs\CheckDoFollowStatus::dispatch($this->user);

        // Mettre à jour le statut dofollow de cette analyse basé sur le statut utilisateur
        if ($this->user->hasDoFollowLinks()) {
            $this->is_dofollow = true;
            $this->saveQuietly();
        }
    }

    public function calculateGlobalScore()
    {
        // Calculer le score global basé sur les détails
        $categoryScores = [
            'content' => $this->content_score,
            'technical' => $this->technical_score,
            'engagement' => $this->engagement_score,
            'authenticity' => $this->authenticity_score,
            'images' => $this->images_score
        ];

        // Pondération par défaut (peut être ajustée selon writer_type et mode)
        $weights = $this->getCategoryWeights();
        
        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($categoryScores as $category => $score) {
            $weight = $weights[$category] ?? 1;
            $weightedSum += $score * $weight;
            $totalWeight += $weight;
        }

        $this->global_score = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        
        return $this->global_score;
    }

    protected function getCategoryWeights()
    {
        // Pondérations par défaut (peuvent varier selon writer_type et mode)
        $defaultWeights = [
            'content' => 0.30,
            'technical' => 0.25,
            'engagement' => 0.15,
            'authenticity' => 0.20,
            'images' => 0.10
        ];

        // Ajustements selon le type de rédacteur
        if ($this->writer_type === 'partner') {
            // Plus de poids sur l'authenticité pour les partenaires
            $defaultWeights['authenticity'] = 0.30;
            $defaultWeights['content'] = 0.25;
        }

        if ($this->mode === 'commande_interne') {
            // Mode commande peut avoir des pondérations personnalisées
            // À récupérer depuis une config ou une table
        }

        return $defaultWeights;
    }

    /**
     * Accessors
     */
    public function getIsPassingAttribute()
    {
        return $this->global_score >= 78;
    }

    public function getNeedsImprovementAttribute()
    {
        return $this->global_score >= 60 && $this->global_score < 78;
    }

    public function getIsFailingAttribute()
    {
        return $this->global_score < 60;
    }
}