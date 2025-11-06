<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Afficher la liste des commentaires à modérer
     */
    public function index(Request $request)
    {
        $query = Comment::with(['article', 'user']);
        
        // Filtres
        $status = $request->get('status', 'pending');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        // Filtre par article
        if ($request->filled('article_id')) {
            $query->where('article_id', $request->article_id);
        }
        
        // Filtre par score spam
        if ($request->filled('spam_level')) {
            switch ($request->spam_level) {
                case 'high':
                    $query->where('spam_score', '>=', 10);
                    break;
                case 'medium':
                    $query->whereBetween('spam_score', [5, 9]);
                    break;
                case 'low':
                    $query->whereBetween('spam_score', [1, 4]);
                    break;
                case 'none':
                    $query->where('spam_score', 0);
                    break;
            }
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('author_email', 'like', "%{$search}%");
            });
        }
        
        // Ordre par défaut : plus récents en premier
        $query->orderBy('created_at', 'desc');
        
        $comments = $query->paginate(20);
        
        // Statistiques pour les badges
        $stats = [
            'total' => Comment::count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
            'high_spam' => Comment::where('spam_score', '>=', 10)->count()
        ];
        
        // Articles pour le filtre
        $articles = Article::orderBy('title')->get(['id', 'title']);
        
        Log::info('Admin accessed comments moderation', [
            'user_id' => auth()->id(),
            'status_filter' => $status,
            'comments_count' => $comments->total()
        ]);
        
        return view('admin.comments.index', compact(
            'comments', 
            'stats', 
            'articles',
            'status'
        ));
    }
    
    /**
     * Afficher les détails d'un commentaire
     */
    public function show(Comment $comment)
    {
        $comment->load(['article', 'user']);
        
        // Autres commentaires du même auteur
        $otherComments = Comment::where('author_email', $comment->author_email)
            ->where('id', '!=', $comment->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Historique IP
        $ipHistory = Comment::where('ip_address', $comment->ip_address)
            ->where('id', '!=', $comment->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        return view('admin.comments.show', compact(
            'comment', 
            'otherComments', 
            'ipHistory'
        ));
    }
    
    /**
     * Approuver un commentaire
     */
    public function approve(Comment $comment)
    {
        $comment->approve();
        
        Log::info('Comment approved', [
            'comment_id' => $comment->id,
            'admin_id' => auth()->id(),
            'article_id' => $comment->article_id,
            'author_email' => $comment->author_email
        ]);
        
        return back()->with('success', 'Commentaire approuvé avec succès.');
    }
    
    /**
     * Rejeter un commentaire
     */
    public function reject(Comment $comment)
    {
        $comment->reject();
        
        Log::info('Comment rejected', [
            'comment_id' => $comment->id,
            'admin_id' => auth()->id(),
            'spam_score' => $comment->spam_score,
            'spam_flags' => $comment->spam_flags
        ]);
        
        return back()->with('success', 'Commentaire rejeté et marqué comme spam.');
    }
    
    /**
     * Marquer comme spam
     */
    public function markAsSpam(Comment $comment)
    {
        $oldStatus = $comment->status;
        $comment->update(['status' => 'spam']);
        
        // Décrémenter le compteur si il était approuvé
        if ($oldStatus === 'approved') {
            $comment->article->decrement('comments_count');
        }
        
        Log::warning('Comment marked as spam', [
            'comment_id' => $comment->id,
            'admin_id' => auth()->id(),
            'old_status' => $oldStatus,
            'ip_address' => $comment->ip_address
        ]);
        
        return back()->with('success', 'Commentaire marqué comme spam.');
    }
    
    /**
     * Supprimer un commentaire
     */
    public function destroy(Comment $comment)
    {
        $articleId = $comment->article_id;
        $wasApproved = $comment->status === 'approved';
        
        $comment->delete();
        
        // Décrémenter le compteur si nécessaire
        if ($wasApproved) {
            Article::find($articleId)?->decrement('comments_count');
        }
        
        Log::info('Comment deleted', [
            'comment_id' => $comment->id,
            'admin_id' => auth()->id(),
            'article_id' => $articleId
        ]);
        
        return back()->with('success', 'Commentaire supprimé définitivement.');
    }
    
    /**
     * Actions en lot sur plusieurs commentaires
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id'
        ]);
        
        $commentIds = $request->comment_ids;
        $action = $request->action;
        $count = 0;
        
        foreach ($commentIds as $commentId) {
            $comment = Comment::find($commentId);
            if (!$comment) continue;
            
            switch ($action) {
                case 'approve':
                    if ($comment->status !== 'approved') {
                        $comment->approve();
                        $count++;
                    }
                    break;
                    
                case 'reject':
                    if ($comment->status !== 'spam') {
                        $comment->reject();
                        $count++;
                    }
                    break;
                    
                case 'spam':
                    if ($comment->status !== 'spam') {
                        $oldStatus = $comment->status;
                        $comment->update(['status' => 'spam']);
                        
                        // Décrémenter le compteur si il était approuvé
                        if ($oldStatus === 'approved') {
                            $comment->article->decrement('comments_count');
                        }
                        $count++;
                    }
                    break;
                    
                case 'delete':
                    $wasApproved = $comment->status === 'approved';
                    $articleId = $comment->article_id;
                    
                    $comment->delete();
                    
                    // Décrémenter le compteur si nécessaire
                    if ($wasApproved) {
                        Article::find($articleId)?->decrement('comments_count');
                    }
                    $count++;
                    break;
            }
        }
        
        Log::info('Bulk action performed on comments', [
            'action' => $action,
            'comment_ids' => $commentIds,
            'affected_count' => $count,
            'admin_id' => auth()->id()
        ]);
        
        $actionMessages = [
            'approve' => 'approuvés',
            'reject' => 'rejetés',
            'spam' => 'marqués comme spam',
            'delete' => 'supprimés'
        ];
        
        $message = "{$count} commentaire(s) {$actionMessages[$action]} avec succès.";
        
        return back()->with('success', $message);
    }
}
