<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Affiche la liste des articles de blog
     */
    public function index()
    {
        $articles = Article::with(['user', 'seoAnalysis'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(9);
        
        // Ajouter les champs calculés pour chaque article
        $articles->through(function ($article) {
            $article->reading_time = $this->calculateReadingTime($article->content);
            $article->category = $article->category ?? 'Destinations';
            $article->author = $article->user->name;
            $article->date = $article->published_at ?? $article->created_at;
            $article->image = $article->featured_image;
            return $article;
        });
        
        return view('blog.index', compact('articles'));
    }
    
    /**
     * Affiche un article de blog spécifique
     */
    public function show($slug)
    {
        $article = Article::with(['user', 'seoAnalysis'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Incrémenter les vues
        $article->increment('views_count');
        
        // Préparer les données pour la vue
        $article->reading_time = $this->calculateReadingTime($article->content);
        $article->category = $article->category ?? 'Destinations';
        $article->author = $article->user->name;
        $article->date = $article->published_at ?? $article->created_at;
        $article->image = $article->featured_image;
        
        // Décoder les tags s'ils sont en JSON
        if ($article->tags && is_string($article->tags)) {
            $article->tags = json_decode($article->tags, true) ?? explode(',', $article->tags);
        }
        
        // Articles connexes (même catégorie ou random si pas de catégorie)
        $relatedArticles = Article::with('user')
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->when($article->category, function($query) use ($article) {
                return $query->where('category', $article->category);
            })
            ->limit(3)
            ->get()
            ->map(function ($related) {
                $related->reading_time = $this->calculateReadingTime($related->content);
                $related->category = $related->category ?? 'Destinations';
                $related->date = $related->published_at ?? $related->created_at;
                $related->image = $related->featured_image;
                return $related;
            });
        
        // Récupérer les commentaires approuvés
        $comments = Comment::where('article_id', $article->id)
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('blog.show', compact('article', 'relatedArticles', 'comments'));
    }
    
    /**
     * Affiche les articles filtrés par catégorie
     */
    public function category($category)
    {
        // Normaliser la catégorie
        $categoryNormalized = Str::lower($category);
        
        $articles = Article::with(['user', 'seoAnalysis'])
            ->where('status', 'published')
            ->whereRaw('LOWER(category) = ?', [$categoryNormalized])
            ->orderBy('published_at', 'desc')
            ->paginate(9);
        
        // Ajouter les champs calculés
        $articles->through(function ($article) {
            $article->reading_time = $this->calculateReadingTime($article->content);
            $article->author = $article->user->name;
            $article->date = $article->published_at ?? $article->created_at;
            $article->image = $article->featured_image;
            return $article;
        });
        
        return view('blog.category', compact('articles', 'category'));
    }
    
    /**
     * Calculer le temps de lecture estimé
     */
    private function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200); // 200 mots par minute
        return max(1, $readingTime); // Minimum 1 minute
    }
    
    /**
     * Recherche d'articles
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $articles = Article::with(['user', 'seoAnalysis'])
            ->where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate(9);
        
        // Ajouter les champs calculés
        $articles->through(function ($article) {
            $article->reading_time = $this->calculateReadingTime($article->content);
            $article->category = $article->category ?? 'Destinations';
            $article->author = $article->user->name;
            $article->date = $article->published_at ?? $article->created_at;
            $article->image = $article->featured_image;
            return $article;
        });
        
        return view('blog.search', compact('articles', 'query'));
    }
    
    /**
     * Compteur de partage (AJAX)
     */
    public function share(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->increment('shares_count');
        
        return response()->json(['success' => true, 'shares' => $article->shares_count]);
    }
}