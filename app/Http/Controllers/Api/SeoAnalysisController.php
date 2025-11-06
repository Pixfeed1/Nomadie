<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\SeoAnalysis;
use App\Models\SeoCriterion;
use App\Models\SeoConfiguration;
use App\Services\Seo\SeoAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeoAnalysisController extends Controller
{
    protected $analyzer;

    public function __construct(SeoAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
        $this->middleware('auth:sanctum');
    }

    /**
     * Analyser un article en temps réel
     */
    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:100',
            'slug' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:200',
            'keywords' => 'nullable|array',
            'article_id' => 'nullable|exists:articles,id',
            'mode' => 'nullable|in:libre,commande_interne'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $mode = $request->input('mode', 'libre');

        // Créer ou récupérer l'article temporaire pour l'analyse
        if ($request->has('article_id')) {
            $article = Article::find($request->article_id);
            if ($article->user_id !== $user->id && !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }
        } else {
            // Article temporaire pour l'analyse en temps réel
            $article = new Article([
                'title' => $request->title,
                'content' => $request->content,
                'slug' => $request->slug ?? \Str::slug($request->title),
                'meta_data' => [
                    'description' => $request->meta_description,
                    'keywords' => $request->keywords ?? []
                ],
                'user_id' => $user->id,
                'status' => 'draft'
            ]);
        }

        // Effectuer l'analyse
        try {
            $analysis = $this->analyzer->analyzeArticle($article, $user, $mode);
            
            return response()->json([
                'success' => true,
                'data' => $this->formatAnalysisResponse($analysis)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtenir une analyse existante
     */
    public function getAnalysis($id)
    {
        $analysis = SeoAnalysis::with(['details.criterion', 'suggestions.suggestedArticle'])
            ->findOrFail($id);

        // Vérifier l'autorisation
        if ($analysis->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatAnalysisResponse($analysis)
        ]);
    }

    /**
     * Obtenir les critères pour un type de rédacteur
     */
    public function getCriteria(Request $request)
    {
        $user = Auth::user();
        $writerType = $this->determineWriterType($user);
        $mode = $request->input('mode', 'libre');

        $configurations = SeoConfiguration::with('criterion')
            ->where('writer_type', $writerType)
            ->where('mode', $mode)
            ->get();

        $criteria = [];
        foreach ($configurations as $config) {
            $criterion = $config->criterion;
            $criteria[] = [
                'id' => $criterion->id,
                'code' => $criterion->code,
                'name' => $criterion->name,
                'category' => $criterion->category,
                'description' => $criterion->description,
                'max_score' => $criterion->max_score,
                'weight' => $config->weight,
                'threshold' => $config->threshold,
                'is_required' => $config->is_required,
                'validation_rules' => $criterion->validation_rules
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'writer_type' => $writerType,
                'mode' => $mode,
                'criteria' => $criteria,
                'min_score' => 78,
                'categories' => [
                    'content' => 'Contenu',
                    'technical' => 'Technique',
                    'images' => 'Images',
                    'engagement' => 'Engagement',
                    'authenticity' => 'Authenticité'
                ]
            ]
        ]);
    }

    /**
     * Obtenir des suggestions de maillage interne
     */
    public function getSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'keywords' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Rechercher des articles similaires pour le maillage
        $suggestions = $this->findRelatedArticles(
            $request->title,
            $request->content,
            $request->keywords ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Accepter/Refuser une suggestion
     */
    public function updateSuggestion(Request $request, $id)
    {
        $suggestion = \App\Models\SeoSuggestion::findOrFail($id);
        
        // Vérifier l'autorisation
        if ($suggestion->analysis->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $suggestion->accepted = $request->input('accepted');
        $suggestion->save();

        return response()->json([
            'success' => true,
            'message' => $suggestion->accepted ? 'Suggestion acceptée' : 'Suggestion refusée'
        ]);
    }

    /**
     * Obtenir l'historique des analyses
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $analyses = SeoAnalysis::where('user_id', $user->id)
            ->with(['article', 'details'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $data = $analyses->map(function ($analysis) {
            return [
                'id' => $analysis->id,
                'article' => [
                    'id' => $analysis->article->id,
                    'title' => $analysis->article->title,
                    'slug' => $analysis->article->slug,
                    'status' => $analysis->article->status
                ],
                'scores' => [
                    'global' => $analysis->global_score,
                    'content' => $analysis->content_score,
                    'technical' => $analysis->technical_score,
                    'images' => $analysis->images_score,
                    'engagement' => $analysis->engagement_score,
                    'authenticity' => $analysis->authenticity_score
                ],
                'is_dofollow' => $analysis->is_dofollow,
                'status' => $analysis->status,
                'created_at' => $analysis->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $analyses->currentPage(),
                'last_page' => $analyses->lastPage(),
                'total' => $analyses->total()
            ]
        ]);
    }

    /**
     * Obtenir les statistiques de l'utilisateur
     */
    public function stats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_articles' => Article::where('user_id', $user->id)->count(),
            'published_articles' => Article::where('user_id', $user->id)
                ->where('status', 'published')
                ->count(),
            'average_score' => SeoAnalysis::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('global_score'),
            'dofollow_articles' => SeoAnalysis::where('user_id', $user->id)
                ->where('is_dofollow', true)
                ->count(),
            'recent_scores' => SeoAnalysis::where('user_id', $user->id)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('global_score')
        ];

        // Déterminer le statut dofollow
        $doFollowEligible = false;
        if ($stats['average_score'] >= 78 && $stats['published_articles'] >= 3) {
            $doFollowEligible = true;
        }

        $stats['dofollow_eligible'] = $doFollowEligible;
        $stats['writer_type'] = $this->determineWriterType($user);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Formater la réponse d'analyse
     */
    protected function formatAnalysisResponse($analysis)
    {
        $details = [];
        foreach ($analysis->details as $detail) {
            $criterion = $detail->criterion;
            $details[$criterion->category][] = [
                'code' => $criterion->code,
                'name' => $criterion->name,
                'score' => $detail->score,
                'max_score' => $criterion->max_score,
                'passed' => $detail->passed,
                'feedback' => $detail->feedback,
                'percentage' => round(($detail->score / $criterion->max_score) * 100, 2)
            ];
        }

        $suggestions = [];
        foreach ($analysis->suggestions as $suggestion) {
            $suggestions[] = [
                'id' => $suggestion->id,
                'article' => [
                    'id' => $suggestion->suggestedArticle->id,
                    'title' => $suggestion->suggestedArticle->title,
                    'slug' => $suggestion->suggestedArticle->slug,
                    'excerpt' => $suggestion->suggestedArticle->excerpt
                ],
                'relevance_score' => $suggestion->relevance_score,
                'reason' => $suggestion->reason,
                'accepted' => $suggestion->accepted
            ];
        }

        return [
            'analysis_id' => $analysis->id,
            'scores' => [
                'global' => $analysis->global_score,
                'content' => $analysis->content_score,
                'technical' => $analysis->technical_score,
                'images' => $analysis->images_score,
                'engagement' => $analysis->engagement_score,
                'authenticity' => $analysis->authenticity_score
            ],
            'metrics' => [
                'word_count' => $analysis->word_count,
                'reading_time' => $analysis->reading_time,
                'images_count' => $analysis->images_count,
                'internal_links' => $analysis->internal_links_count,
                'external_links' => $analysis->external_links_count
            ],
            'status' => [
                'is_passing' => $analysis->global_score >= 78,
                'is_dofollow' => $analysis->is_dofollow,
                'has_auto_promo' => $analysis->has_auto_promo,
                'auto_promo_percentage' => $analysis->auto_promo_percentage
            ],
            'details' => $details,
            'suggestions' => $suggestions,
            'keyword_data' => $analysis->keyword_data,
            'schema_markup' => $analysis->schema_markup,
            'created_at' => $analysis->created_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Déterminer le type de rédacteur
     */
    protected function determineWriterType($user)
    {
        // À adapter selon ta logique de rôles
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin') || $user->hasRole('editor')) {
                return 'equipe';
            }
            if ($user->hasRole('partner')) {
                return 'partenaire';
            }
            if ($user->hasRole('client')) {
                return 'client';
            }
        }
        
        // Vérifier via d'autres moyens (ex: champs dans la table users)
        if (isset($user->user_type)) {
            return $user->user_type;
        }
        
        return 'communaute';
    }

    /**
     * Trouver des articles liés
     */
    protected function findRelatedArticles($title, $content, $keywords)
    {
        $query = Article::published()
            ->where('user_id', '!=', Auth::id())
            ->limit(10);

        // Recherche par mots-clés
        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%");
                }
            });
        }

        // Recherche par similarité de titre
        $titleWords = explode(' ', $title);
        $query->orWhere(function ($q) use ($titleWords) {
            foreach ($titleWords as $word) {
                if (strlen($word) > 4) {
                    $q->orWhere('title', 'like', "%{$word}%");
                }
            }
        });

        $articles = $query->get();

        return $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'url' => route('articles.show', $article->slug)
            ];
        });
    }
}