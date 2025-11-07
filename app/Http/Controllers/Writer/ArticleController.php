<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\Seo\SeoAnalyzer;
use App\Jobs\CheckUserBadges;
use App\Jobs\CheckDoFollowStatus;
use App\Notifications\ExceptionalScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    protected $seoAnalyzer;

    public function __construct(SeoAnalyzer $seoAnalyzer)
    {
        $this->seoAnalyzer = $seoAnalyzer;
    }

    /**
     * Display a listing of articles
     */
    public function index()
    {
        $articles = Article::where('user_id', Auth::id())
            ->with('latestSeoAnalysis')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('writer.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        return view('writer.articles.create');
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:500',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:' . config('uploads.max_sizes.image'),
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|array',
            'slug' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'nullable|in:draft,pending,published' // Ajout des statuts
        ]);

        $article = new Article();
        $article->user_id = Auth::id();
        $article->title = $validated['title'];
        $article->slug = $validated['slug'] ?? Str::slug($validated['title']);
        $article->content = $validated['content'];
        $article->excerpt = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160);
        $article->status = $request->input('status', 'draft');
        
        // Si publié, définir la date de publication
        if ($validated['status'] === 'published' && !$article->published_at) {
            $article->published_at = now();
        }
        
        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $article->featured_image = $path;
        }

        // Meta data
        $article->meta_data = [
            'description' => $validated['meta_description'] ?? '',
            'keywords' => $validated['keywords'] ?? [],
            'category' => $validated['category'] ?? '',
            'tags' => $validated['tags'] ?? ''
        ];

        $article->save();

        // Run SEO analysis - Utilisation directe au lieu de l'injection
        $analyzer = new \App\Services\Seo\SeoAnalyzer();
        $analysis = $analyzer->analyzeArticle($article, Auth::user());

        // Vérifier si le score est exceptionnel et envoyer une notification
        if ($analysis->global_score >= 90) {
            try {
                Auth::user()->notify(new ExceptionalScore($article, $analysis->global_score));
                Log::info('Notification de score exceptionnel envoyée', [
                    'user_id' => Auth::id(),
                    'article_id' => $article->id,
                    'score' => $analysis->global_score
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de la notification de score exceptionnel: ' . $e->getMessage());
            }
        }

        // Vérifier le statut dofollow
        CheckDoFollowStatus::dispatch(Auth::user());

        // Dispatch job to check user badges
        CheckUserBadges::dispatch(Auth::user());

        // Message de succès différencié selon le score
        $successMessage = $this->getSuccessMessage($analysis->global_score, 'créé');

        return redirect()->route('writer.articles.edit', $article->id)
            ->with('success', $successMessage);
    }

    /**
     * Show the form for editing an article
     */
    public function edit($id)
    {
        $article = Article::where('user_id', Auth::id())->findOrFail($id);
        $analysis = $article->latestSeoAnalysis;

        return view('writer.articles.edit', compact('article', 'analysis'));
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, $id)
    {
        $article = Article::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:500',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:' . config('uploads.max_sizes.image'),
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|array',
            'slug' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'nullable|in:draft,pending,published'
        ]);

        // Stocker l'ancien score pour comparaison
        $oldAnalysis = $article->latestSeoAnalysis;
        $oldScore = $oldAnalysis ? $oldAnalysis->global_score : 0;

        $article->title = $validated['title'];
        $article->slug = $validated['slug'] ?? Str::slug($validated['title']);
        $article->content = $validated['content'];
        $article->excerpt = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160);
        
        if ($request->hasFile('featured_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $article->featured_image = $path;
        }

        $article->meta_data = [
            'description' => $validated['meta_description'] ?? '',
            'keywords' => $validated['keywords'] ?? [],
            'category' => $validated['category'] ?? '',
            'tags' => $validated['tags'] ?? ''
        ];

        if (isset($validated['status'])) {
            $article->status = $validated['status'];
            if ($validated['status'] === 'published' && !$article->published_at) {
                $article->published_at = now();
            }
        }

        $article->save();

        // Re-run SEO analysis
        $analysis = $this->seoAnalyzer->analyzeArticle($article, Auth::user());

        // Vérifier si le score est exceptionnel et envoyer une notification
        // Ne notifier que si le nouveau score est >= 90 ET si c'est une amélioration significative
        if ($analysis->global_score >= 90 && $analysis->global_score > $oldScore) {
            try {
                Auth::user()->notify(new ExceptionalScore($article, $analysis->global_score));
                Log::info('Notification de score exceptionnel envoyée (mise à jour)', [
                    'user_id' => Auth::id(),
                    'article_id' => $article->id,
                    'old_score' => $oldScore,
                    'new_score' => $analysis->global_score
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de la notification de score exceptionnel: ' . $e->getMessage());
            }
        }

        // Vérifier le statut dofollow
        CheckDoFollowStatus::dispatch(Auth::user());

        // Dispatch job to check user badges
        CheckUserBadges::dispatch(Auth::user());

        // Message de succès différencié selon le score et l'évolution
        $successMessage = $this->getUpdateSuccessMessage($analysis->global_score, $oldScore);

        return redirect()->route('writer.articles.edit', $article->id)
            ->with('success', $successMessage);
    }

    /**
     * Upload image for TinyMCE editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:' . config('uploads.max_sizes.image')
        ]);
        
        $path = $request->file('image')->store('articles/content', 'public');
        
        return response()->json([
            'location' => Storage::url($path)
        ]);
    }

    /**
     * Analyze article SEO (AJAX)
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:160'
        ]);

        // Create temporary article for analysis
        $article = new Article([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'meta_data' => [
                'description' => $validated['meta_description'] ?? ''
            ]
        ]);

        $analysis = $this->seoAnalyzer->analyzeArticle($article, Auth::user());

        // Vérifier le statut DoFollow en temps réel
        $user = Auth::user();
        $isDoFollow = false;
        
        // Vérifier si la méthode existe avant de l'appeler
        if (method_exists($user, 'hasDoFollowLinks')) {
            if ($user->hasDoFollowLinks()) {
                $isDoFollow = $analysis->global_score >= 75;
            }
        } else {
            // Alternative : vérifier directement l'attribut is_dofollow si il existe
            if (isset($user->is_dofollow) && $user->is_dofollow) {
                $isDoFollow = $analysis->global_score >= 75;
            }
        }

        // Ajouter l'appel à analyzeRaw pour des données supplémentaires si nécessaire
        $analyzer = new \App\Services\Seo\SeoAnalyzer();
        $rawAnalysis = $analyzer->analyzeRaw(
            $request->title,
            $request->content,
            $request->meta_description
        );

        return response()->json([
            'success' => true,
            'analysis' => [
                'global_score' => $analysis->global_score,
                'content_score' => $analysis->content_score,
                'technical_score' => $analysis->technical_score,
                'images_score' => $analysis->images_score,
                'engagement_score' => $analysis->engagement_score,
                'authenticity_score' => $analysis->authenticity_score,
                'word_count' => $analysis->word_count,
                'reading_time' => $analysis->reading_time,
                'is_dofollow' => $isDoFollow,
                'will_be_dofollow' => $isDoFollow, // Indication en temps réel
                'details' => $analysis->details->map(function($detail) {
                    return [
                        'criterion' => $detail->criterion->name,
                        'category' => $detail->criterion->category,
                        'score' => $detail->score,
                        'max_score' => $detail->criterion->max_score,
                        'passed' => $detail->passed,
                        'feedback' => $detail->feedback
                    ];
                }),
                // Ajouter les données de analyzeRaw
                'raw_score' => $rawAnalysis['score'] ?? null,
                'suggestions' => $rawAnalysis['suggestions'] ?? []
            ]
        ]);
    }

    /**
     * Delete an article
     */
    public function destroy($id)
    {
        $article = Article::where('user_id', Auth::id())->findOrFail($id);
        
        // Supprimer l'image si elle existe
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        
        $article->delete();

        // Vérifier le statut DoFollow après suppression
        CheckDoFollowStatus::dispatch(Auth::user());

        return redirect()->route('writer.articles.index')
            ->with('success', 'Article supprimé avec succès');
    }

    /**
     * Generate success message based on score
     */
    protected function getSuccessMessage($score, $action = 'créé')
    {
        if ($score >= 95) {
            return "Exceptionnel ! Article {$action} avec un score SEO parfait de {$score}/100 !";
        } elseif ($score >= 90) {
            return "Excellent ! Article {$action} avec un score SEO de {$score}/100 !";
        } elseif ($score >= 80) {
            return "Très bien ! Article {$action} avec un bon score SEO de {$score}/100.";
        } elseif ($score >= 70) {
            return "Article {$action} avec succès. Score SEO : {$score}/100.";
        } else {
            return "Article {$action}. Score SEO : {$score}/100. Consultez les recommandations pour l'améliorer.";
        }
    }

    /**
     * Generate update success message based on score evolution
     */
    protected function getUpdateSuccessMessage($newScore, $oldScore)
    {
        $difference = $newScore - $oldScore;
        
        if ($difference > 10) {
            return "Amélioration spectaculaire ! Score SEO : {$newScore}/100 (+" . $difference . " points)";
        } elseif ($difference > 0) {
            return "Article amélioré ! Score SEO : {$newScore}/100 (+" . $difference . " points)";
        } elseif ($difference == 0) {
            return "Article mis à jour. Score SEO maintenu : {$newScore}/100";
        } else {
            return "Article mis à jour. Score SEO : {$newScore}/100 (" . $difference . " points)";
        }
    }
}