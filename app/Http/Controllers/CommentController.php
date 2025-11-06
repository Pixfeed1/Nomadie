<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use App\Services\ImprovedSpamDetector;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $spamDetector;

    public function __construct(ImprovedSpamDetector $spamDetector)
    {
        $this->spamDetector = $spamDetector;
    }

    /**
     * Stocker un nouveau commentaire
     */
    public function store(Request $request, Article $article)
    {
        // Validation avec messages personnalisés en français
        $request->validate([
            'content' => 'required|string|min:5|max:2000',
            'author_name' => auth()->guest() ? 'required|string|max:50' : 'nullable',
            'author_email' => auth()->guest() ? 'required|email|max:150' : 'nullable',
        ], [
            'content.required' => 'Veuillez saisir votre commentaire.',
            'content.min' => 'Votre commentaire doit contenir au moins 5 caractères.',
            'content.max' => 'Votre commentaire ne peut pas dépasser 2000 caractères.',
            'author_name.required' => 'Veuillez indiquer votre nom.',
            'author_name.max' => 'Votre nom ne peut pas dépasser 50 caractères.',
            'author_email.required' => 'Votre adresse email est requise.',
            'author_email.email' => 'Veuillez fournir une adresse email valide.',
            'author_email.max' => 'L\'adresse email ne peut pas dépasser 150 caractères.'
        ]);

        // Honeypot anti-bot (champ caché "company" dans le formulaire)
        if ($request->filled('company')) {
            // Ne pas alerter le bot, faire semblant que ça marche
            return back()->with('info', 'Votre commentaire est en cours de traitement.');
        }

        $ip = $request->ip();

        // Vérification du rate limiting
        $recentComments = Comment::where('ip_address', $ip)
            ->where('created_at', '>', now()->subMinutes(10))
            ->count();

        if ($recentComments >= 3) {
            return back()->with('warning', 
                'Merci de patienter quelques minutes entre vos commentaires pour éviter le spam.'
            );
        }

        // Préparer l'historique utilisateur pour l'analyse
        $userHistory = null;
        if (auth()->check()) {
            $user = auth()->user();
            $userHistory = [
                'approved_articles' => $user->articles()->where('status', 'published')->count(),
                'approved_comments' => $user->comments()->where('status', 'approved')->count(),
                'account_age_days' => $user->created_at->diffInDays(now())
            ];
        }

        // Analyse anti-spam du commentaire
        $analysis = $this->spamDetector->analyzeComment(
            $request->content,
            $request->author_email ?? auth()->user()?->email,
            $ip,
            $userHistory
        );

        // Déterminer le statut basé sur l'analyse
        $status = match($analysis['recommendation']) {
            'approve' => 'approved',
            'moderate' => 'pending',
            'reject' => 'spam'
        };

        // Créer le commentaire
        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->check() ? auth()->user()->name : $request->author_name,
            'author_email' => auth()->check() ? auth()->user()->email : $request->author_email,
            'content' => $this->sanitizeContent($request->content),
            'status' => $status,
            'spam_score' => $analysis['score'],
            'spam_flags' => $analysis['flags'],
            'ip_address' => $ip,
            'user_agent' => $request->userAgent()
        ]);

        // Si approuvé, incrémenter le compteur de l'article
        if ($status === 'approved') {
            $article->increment('comments_count');
        }

        // Loguer pour debug (optionnel)
        if (config('app.debug') && $analysis['score'] > 0) {
            \Log::info('Comment spam analysis', [
                'comment_id' => $comment->id,
                'score' => $analysis['score'],
                'recommendation' => $analysis['recommendation'],
                'flags' => $analysis['flags']
            ]);
        }

        // Messages utilisateur diplomatiques
        return back()->with(...$this->getResponseMessage($comment, $analysis));
    }

    /**
     * Nettoyer le contenu du commentaire
     */
    protected function sanitizeContent(string $content): string
    {
        // Nettoyer les balises HTML dangereuses mais garder la mise en forme basique
        $content = strip_tags($content, '<br><p><strong><em><u>');
        
        // Convertir les retours à la ligne en <br>
        $content = nl2br($content);
        
        // Limiter les liens à 2 maximum
        $linkCount = substr_count($content, 'http');
        if ($linkCount > 2) {
            // Supprimer les liens en excès (garder les 2 premiers)
            $content = preg_replace('/https?:\/\/[^\s]+/', '', $content, $linkCount - 2);
        }

        return trim($content);
    }

    /**
     * Générer le message de réponse selon le statut
     */
    protected function getResponseMessage(Comment $comment, array $analysis): array
    {
        return match($comment->status) {
            'approved' => [
                'success', 
                'Merci pour votre commentaire ! Il a été publié avec succès.'
            ],
            'pending' => [
                'info', 
                'Merci pour votre commentaire ! Il sera visible après validation par notre équipe.'
            ],
            'spam' => [
                'warning', 
                'Votre commentaire n\'a pas pu être publié. Veuillez vérifier qu\'il respecte nos conditions d\'utilisation.'
            ],
            default => [
                'error', 
                'Une erreur est survenue lors de la publication de votre commentaire.'
            ]
        };
    }
}