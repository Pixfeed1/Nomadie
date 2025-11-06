<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'author_name',
        'author_email',
        'content',
        'status',
        'spam_score',
        'spam_flags',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'spam_flags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relations
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accesseurs
     */
    public function getAuthorDisplayNameAttribute()
    {
        return $this->user ? $this->user->name : $this->author_name;
    }

    public function getIsSpamAttribute()
    {
        return $this->status === 'spam' || $this->spam_score >= 10;
    }

    public function getSpamRiskLevelAttribute()
    {
        if ($this->spam_score >= 10) return 'high';
        if ($this->spam_score >= 6) return 'medium';
        if ($this->spam_score >= 3) return 'low';
        return 'none';
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->locale('fr')->diffForHumans();
    }

    /**
     * Méthodes d'action
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
        
        // Incrémenter le compteur de commentaires de l'article
        $this->article->increment('comments_count');
    }

    public function reject()
    {
        $this->update(['status' => 'spam']);
        
        // Décrémenter le compteur si il était approuvé avant
        if ($this->getOriginal('status') === 'approved') {
            $this->article->decrement('comments_count');
        }
    }

    public function markAsTrash()
    {
        $this->update(['status' => 'trash']);
        
        // Décrémenter le compteur si il était approuvé avant
        if ($this->getOriginal('status') === 'approved') {
            $this->article->decrement('comments_count');
        }
    }

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();
        
        // Quand un commentaire est créé directement avec le statut approved
        static::created(function ($comment) {
            if ($comment->status === 'approved') {
                $comment->article->increment('comments_count');
            }
        });
        
        // Quand un commentaire est supprimé, décrémenter le compteur
        static::deleting(function ($comment) {
            if ($comment->status === 'approved') {
                $comment->article->decrement('comments_count');
            }
        });
    }
}