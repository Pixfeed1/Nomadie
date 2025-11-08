<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $query = Article::with(['user', 'destination']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('meta_description', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrage par auteur
        if ($request->filled('author')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('id', $request->author);
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'draft' => Article::where('status', 'draft')->count(),
            'pending' => Article::where('status', 'pending')->count(),
        ];

        return view('admin.articles.index', compact('articles', 'stats'));
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        $article->load(['user', 'destination', 'seoAnalysis']);

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Publish an article.
     */
    public function publish(Article $article)
    {
        $article->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return back()->with('success', 'Article publié avec succès.');
    }

    /**
     * Unpublish an article.
     */
    public function unpublish(Article $article)
    {
        $article->update([
            'status' => 'draft',
            'published_at' => null
        ]);

        return back()->with('success', 'Article dépublié avec succès.');
    }

    /**
     * Remove the specified article.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article supprimé avec succès.');
    }
}
