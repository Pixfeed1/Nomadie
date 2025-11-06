<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'color',
        'order',
        'conditions',
        'rewards',
        'is_active'
    ];

    protected $casts = [
        'conditions' => 'array',
        'rewards' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Les 8 badges Nomadie selon tes documents
     */
    const BADGES = [
        'premier_pas' => [
            'name' => 'Premier Pas',
            'description' => 'Publication de votre 1er article validé par NomadSEO',
            'color' => 'green',
            'icon' => '🌱',
            'conditions' => [
                'type' => 'articles_published',
                'count' => 1,
                'min_score' => 78
            ],
            'rewards' => [
                'seo_tool_access' => true,
                'profile_visibility' => true
            ]
        ],
        'contributeur_confirme' => [
            'name' => 'Contributeur Confirmé',
            'description' => '5 articles publiés avec score NomadSEO ≥ 75/100',
            'color' => 'blue',
            'icon' => '✍️',
            'conditions' => [
                'type' => 'articles_published',
                'count' => 5,
                'min_score' => 75,
                'social_share' => true
            ],
            'rewards' => [
                'badge_kit' => true,
                'monthly_mention' => true,
                'priority_suggestions' => true
            ]
        ],
        'redacteur_expert' => [
            'name' => 'Rédacteur Expert',
            'description' => '20 articles publiés avec maintien de la qualité',
            'color' => 'purple',
            'icon' => '📖',
            'conditions' => [
                'type' => 'articles_published',
                'count' => 20,
                'min_score' => 75,
                'min_months' => 6
            ],
            'rewards' => [
                'expert_status' => true,
                'event_invitations' => true,
                'topic_proposals' => true
            ]
        ],
        'dofollow_debloquer' => [
            'name' => 'Dofollow Débloqué',
            'description' => 'Premier article qualifié pour liens dofollow',
            'color' => 'indigo',
            'icon' => '🎯',
            'conditions' => [
                'type' => 'dofollow_achieved',
                'min_score' => 78
            ],
            'rewards' => [
                'dofollow_links' => true,
                'seo_boost' => true,
                'certificate' => true
            ]
        ],
        'maitre_nomadseo' => [
            'name' => 'Maître NomadSEO',
            'description' => '3 articles consécutifs avec score ≥ 90/100',
            'color' => 'yellow',
            'icon' => '🏅',
            'conditions' => [
                'type' => 'consecutive_high_score',
                'count' => 3,
                'min_score' => 90
            ],
            'rewards' => [
                'expert_recognition' => true,
                'beta_features' => true,
                'early_access' => true
            ]
        ],
        'ambassadeur_social' => [
            'name' => 'Ambassadeur Social',
            'description' => '50 commentaires reçus et 100% partages respectés',
            'color' => 'pink',
            'icon' => '🤝',
            'conditions' => [
                'type' => 'social_engagement',
                'min_comments' => 50,
                'share_compliance' => 100
            ],
            'rewards' => [
                'co_promotion' => true,
                'content_highlight' => true,
                'advanced_stats' => true
            ]
        ],
        'favori_lecteurs' => [
            'name' => 'Favori des Lecteurs',
            'description' => '3 articles dans le top 10 mensuel',
            'color' => 'red',
            'icon' => '❤️',
            'conditions' => [
                'type' => 'top_articles',
                'count' => 3,
                'top_rank' => 10
            ],
            'rewards' => [
                'newsletter_mention' => true,
                'priority_placement' => true,
                'special_collaborations' => true
            ]
        ],
        'pionnier_nomadie' => [
            'name' => 'Pionnier Nomadie',
            'description' => 'Parmi les 100 premiers contributeurs actifs',
            'color' => 'gold',
            'icon' => '👑',
            'conditions' => [
                'type' => 'early_adopter',
                'max_rank' => 100,
                'min_articles' => 3
            ],
            'rewards' => [
                'vip_status' => true,
                'priority_access' => true,
                'platform_consultation' => true,
                'rare_badge' => true
            ]
        ]
    ];

    /**
     * Relations
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('progress_data', 'progress_percentage', 'unlocked_at', 'notified_at', 'is_featured')
                    ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Methods
     */
    public static function seedBadges()
    {
        $order = 0;
        foreach (self::BADGES as $code => $data) {
            self::updateOrCreate(
                ['code' => $code],
                array_merge($data, ['order' => $order++])
            );
        }
    }

    public function checkEligibility(User $user)
    {
        $conditions = $this->conditions;
        
        switch ($conditions['type']) {
            case 'articles_published':
                return $this->checkArticlesPublished($user, $conditions);
                
            case 'dofollow_achieved':
                return $this->checkDoFollowAchieved($user, $conditions);
                
            case 'consecutive_high_score':
                return $this->checkConsecutiveHighScore($user, $conditions);
                
            case 'social_engagement':
                return $this->checkSocialEngagement($user, $conditions);
                
            case 'top_articles':
                return $this->checkTopArticles($user, $conditions);
                
            case 'early_adopter':
                return $this->checkEarlyAdopter($user, $conditions);
                
            default:
                return false;
        }
    }

    private function checkArticlesPublished(User $user, $conditions)
    {
        $query = $user->articles()
                      ->where('status', 'published')
                      ->whereHas('latestSeoAnalysis', function($q) use ($conditions) {
                          $q->where('global_score', '>=', $conditions['min_score'] ?? 75);
                      });

        $count = $query->count();
        
        // Vérifier la durée minimum si spécifiée
        if (isset($conditions['min_months'])) {
            $firstArticleDate = $user->articles()
                                     ->where('status', 'published')
                                     ->orderBy('published_at', 'asc')
                                     ->first();
            
            if (!$firstArticleDate || $firstArticleDate->published_at->diffInMonths(now()) < $conditions['min_months']) {
                return false;
            }
        }
        
        return $count >= $conditions['count'];
    }

    private function checkDoFollowAchieved(User $user, $conditions)
    {
        return $user->articles()
                    ->whereHas('latestSeoAnalysis', function($q) use ($conditions) {
                        $q->where('is_dofollow', true)
                          ->where('global_score', '>=', $conditions['min_score'] ?? 78);
                    })
                    ->exists();
    }

    private function checkConsecutiveHighScore(User $user, $conditions)
    {
        $recentAnalyses = $user->seoAnalyses()
                               ->where('status', 'completed')
                               ->orderBy('created_at', 'desc')
                               ->limit($conditions['count'])
                               ->get();
        
        if ($recentAnalyses->count() < $conditions['count']) {
            return false;
        }
        
        return $recentAnalyses->every(function ($analysis) use ($conditions) {
            return $analysis->global_score >= $conditions['min_score'];
        });
    }

    private function checkSocialEngagement(User $user, $conditions)
    {
        $totalComments = $user->articles()->sum('comments_count');
        
        if ($totalComments < ($conditions['min_comments'] ?? 50)) {
            return false;
        }
        
        // Vérifier le taux de partage (à implémenter selon ta logique)
        // Pour l'instant on retourne true si les commentaires sont OK
        return true;
    }

    private function checkTopArticles(User $user, $conditions)
    {
        // Cette vérification nécessiterait un système de ranking mensuel
        // Pour l'instant, on vérifie juste les articles les plus vus
        $topArticles = $user->articles()
                            ->where('status', 'published')
                            ->orderBy('views_count', 'desc')
                            ->limit($conditions['count'])
                            ->get();
        
        return $topArticles->count() >= $conditions['count'];
    }

    private function checkEarlyAdopter(User $user, $conditions)
    {
        // Vérifier si l'utilisateur fait partie des 100 premiers
        $userRank = User::where('created_at', '<', $user->created_at)->count() + 1;
        
        if ($userRank > $conditions['max_rank']) {
            return false;
        }
        
        // Vérifier le nombre minimum d'articles
        $articleCount = $user->articles()->where('status', 'published')->count();
        
        return $articleCount >= ($conditions['min_articles'] ?? 3);
    }
}