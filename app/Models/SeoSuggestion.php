<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_analysis_id',
        'suggested_article_id',
        'relevance_score',
        'reason',
        'accepted',
        'match_data'
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'accepted' => 'boolean',
        'match_data' => 'array'
    ];

    /**
     * Relations
     */
    public function analysis()
    {
        return $this->belongsTo(SeoAnalysis::class, 'seo_analysis_id');
    }

    public function suggestedArticle()
    {
        return $this->belongsTo(Article::class, 'suggested_article_id');
    }

    /**
     * Scopes
     */
    public function scopeAccepted($query)
    {
        return $query->where('accepted', true);
    }

    public function scopeRejected($query)
    {
        return $query->where('accepted', false);
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted');
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeHighRelevance($query, $minScore = 70)
    {
        return $query->where('relevance_score', '>=', $minScore);
    }

    /**
     * Methods
     */
    public function accept()
    {
        $this->accepted = true;
        $this->save();
        
        return $this;
    }

    public function reject()
    {
        $this->accepted = false;
        $this->save();
        
        return $this;
    }

    public function calculateRelevanceScore($currentArticle, $suggestedArticle)
    {
        $score = 0;
        $matchData = [];
        
        // 1. Analyse des mots-clés communs
        $currentKeywords = $this->extractKeywords($currentArticle->content);
        $suggestedKeywords = $this->extractKeywords($suggestedArticle->content);
        
        $commonKeywords = array_intersect($currentKeywords, $suggestedKeywords);
        $keywordScore = count($commonKeywords) > 0 
            ? (count($commonKeywords) / max(count($currentKeywords), count($suggestedKeywords))) * 30
            : 0;
        
        $score += $keywordScore;
        $matchData['common_keywords'] = $commonKeywords;
        
        // 2. Analyse de la destination (si applicable)
        if ($this->haveSameLocation($currentArticle, $suggestedArticle)) {
            $score += 25;
            $matchData['same_location'] = true;
        }
        
        // 3. Analyse de la thématique
        $themeMatch = $this->calculateThemeMatch($currentArticle, $suggestedArticle);
        $score += $themeMatch * 20;
        $matchData['theme_match'] = $themeMatch;
        
        // 4. Complémentarité du contenu
        if ($this->areComplementary($currentArticle, $suggestedArticle)) {
            $score += 15;
            $matchData['complementary'] = true;
        }
        
        // 5. Popularité de l'article suggéré
        $popularityScore = $this->calculatePopularityScore($suggestedArticle);
        $score += $popularityScore * 10;
        $matchData['popularity_score'] = $popularityScore;
        
        $this->relevance_score = min(100, $score);
        $this->match_data = $matchData;
        
        return $this->relevance_score;
    }

    protected function extractKeywords($content)
    {
        // Extraction simple des mots-clés (peut être améliorée avec un package NLP)
        $content = strip_tags($content);
        $content = strtolower($content);
        
        // Enlever les mots vides (stop words)
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'de', 'du', 'des', 'et', 'ou', 'mais', 
                      'donc', 'car', 'ni', 'que', 'qui', 'quoi', 'dont', 'où', 'si', 'ce', 
                      'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses',
                      'notre', 'votre', 'leur', 'nos', 'vos', 'leurs'];
        
        $words = str_word_count($content, 1);
        $words = array_diff($words, $stopWords);
        
        // Garder seulement les mots de plus de 3 caractères
        $keywords = array_filter($words, function($word) {
            return strlen($word) > 3;
        });
        
        // Compter la fréquence et garder les plus fréquents
        $frequency = array_count_values($keywords);
        arsort($frequency);
        
        return array_keys(array_slice($frequency, 0, 20)); // Top 20 mots-clés
    }

    protected function haveSameLocation($article1, $article2)
    {
        // Vérifier si les articles parlent de la même destination
        // Peut être basé sur des tags, catégories, ou analyse du contenu
        $meta1 = $article1->meta_data ?? [];
        $meta2 = $article2->meta_data ?? [];
        
        if (isset($meta1['location']) && isset($meta2['location'])) {
            return $meta1['location'] === $meta2['location'];
        }
        
        // Analyse basique du titre et contenu pour détecter des lieux
        $locations = ['paris', 'tokyo', 'new york', 'londres', 'rome', 'barcelone', 
                      'bali', 'thailande', 'japon', 'italie', 'espagne', 'france'];
        
        foreach ($locations as $location) {
            $inArticle1 = stripos($article1->title . ' ' . $article1->content, $location) !== false;
            $inArticle2 = stripos($article2->title . ' ' . $article2->content, $location) !== false;
            
            if ($inArticle1 && $inArticle2) {
                return true;
            }
        }
        
        return false;
    }

    protected function calculateThemeMatch($article1, $article2)
    {
        // Calcul simple de similarité thématique
        $themes = [
            'aventure' => ['randonnée', 'trek', 'escalade', 'montagne', 'sport'],
            'culture' => ['musée', 'histoire', 'art', 'tradition', 'patrimoine'],
            'gastronomie' => ['restaurant', 'cuisine', 'plat', 'gastronomie', 'food'],
            'plage' => ['plage', 'mer', 'océan', 'sable', 'baignade'],
            'nature' => ['nature', 'parc', 'forêt', 'lac', 'paysage'],
            'urbain' => ['ville', 'urbain', 'métropole', 'quartier', 'shopping']
        ];
        
        $article1Themes = [];
        $article2Themes = [];
        
        foreach ($themes as $theme => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($article1->content, $keyword) !== false) {
                    $article1Themes[$theme] = true;
                }
                if (stripos($article2->content, $keyword) !== false) {
                    $article2Themes[$theme] = true;
                }
            }
        }
        
        $commonThemes = array_intersect_key($article1Themes, $article2Themes);
        $totalThemes = count(array_merge($article1Themes, $article2Themes));
        
        return $totalThemes > 0 ? count($commonThemes) / $totalThemes : 0;
    }

    protected function areComplementary($article1, $article2)
    {
        // Détecter si les articles sont complémentaires
        // Par exemple : "Que faire à Paris" et "Où manger à Paris"
        $complementaryPairs = [
            ['que faire', 'où manger'],
            ['où dormir', 'que visiter'],
            ['budget', 'itinéraire'],
            ['transport', 'hébergement'],
            ['préparation', 'sur place']
        ];
        
        $title1 = strtolower($article1->title);
        $title2 = strtolower($article2->title);
        
        foreach ($complementaryPairs as $pair) {
            if ((stripos($title1, $pair[0]) !== false && stripos($title2, $pair[1]) !== false) ||
                (stripos($title1, $pair[1]) !== false && stripos($title2, $pair[0]) !== false)) {
                return true;
            }
        }
        
        return false;
    }

    protected function calculatePopularityScore($article)
    {
        // Score basé sur les vues, partages et commentaires
        $maxViews = 10000;
        $maxShares = 100;
        $maxComments = 50;
        
        $viewScore = min($article->views_count / $maxViews, 1);
        $shareScore = min($article->shares_count / $maxShares, 1);
        $commentScore = min($article->comments_count / $maxComments, 1);
        
        return ($viewScore * 0.5 + $shareScore * 0.3 + $commentScore * 0.2);
    }

    /**
     * Static method pour générer des suggestions
     */
    public static function generateSuggestionsFor(SeoAnalysis $analysis)
    {
        $currentArticle = $analysis->article;
        
        // Récupérer les articles candidats (publiés, pas du même auteur)
        $candidateArticles = Article::published()
            ->where('id', '!=', $currentArticle->id)
            ->where('user_id', '!=', $currentArticle->user_id)
            ->limit(50)
            ->get();
        
        $suggestions = [];
        
        foreach ($candidateArticles as $candidate) {
            $suggestion = new static([
                'seo_analysis_id' => $analysis->id,
                'suggested_article_id' => $candidate->id
            ]);
            
            $relevanceScore = $suggestion->calculateRelevanceScore($currentArticle, $candidate);
            
            // Déterminer la raison principale
            if (isset($suggestion->match_data['same_location']) && $suggestion->match_data['same_location']) {
                $suggestion->reason = 'same_location';
            } elseif ($suggestion->match_data['theme_match'] > 0.5) {
                $suggestion->reason = 'same_theme';
            } elseif (isset($suggestion->match_data['complementary']) && $suggestion->match_data['complementary']) {
                $suggestion->reason = 'complementary';
            } elseif (count($suggestion->match_data['common_keywords']) > 5) {
                $suggestion->reason = 'keyword_match';
            } else {
                $suggestion->reason = 'trending';
            }
            
            // Garder seulement les suggestions avec un score > 40
            if ($relevanceScore > 40) {
                $suggestions[] = $suggestion;
            }
        }
        
        // Trier par relevance et garder le top 10
        usort($suggestions, function($a, $b) {
            return $b->relevance_score <=> $a->relevance_score;
        });
        
        $topSuggestions = array_slice($suggestions, 0, 10);
        
        // Sauvegarder les suggestions
        foreach ($topSuggestions as $suggestion) {
            $suggestion->save();
        }
        
        return $topSuggestions;
    }
}