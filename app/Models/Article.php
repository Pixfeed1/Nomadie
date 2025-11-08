<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category',
        'tags',
        'status',
        'views_count',
        'shares_count',
        'comments_count',
        'meta_data',
        'published_at',
        'scheduled_at'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
                
                // Assure l'unicité du slug
                $count = static::where('slug', 'like', $article->slug . '%')->count();
                if ($count > 0) {
                    $article->slug = $article->slug . '-' . ($count + 1);
                }
            }
            
            // Génère l'excerpt si non fourni
            if (empty($article->excerpt) && !empty($article->content)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 160);
            }

            // Définir une catégorie par défaut si non fournie
            if (empty($article->category)) {
                $article->category = 'Destinations';
            }
        });

        static::updating(function ($article) {
            // Regénérer l'excerpt si le contenu change et qu'il n'y a pas d'excerpt personnalisé
            if ($article->isDirty('content') && empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 160);
            }
        });
    }

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seoAnalyses()
    {
        return $this->hasMany(SeoAnalysis::class);
    }

    public function seoAnalysis()
    {
        return $this->hasOne(SeoAnalysis::class)->latestOfMany();
    }

    public function latestSeoAnalysis()
    {
        return $this->hasOne(SeoAnalysis::class)->latestOfMany();
    }

    public function suggestedIn()
    {
        return $this->hasMany(SeoSuggestion::class, 'suggested_article_id');
    }

    /**
     * Relations avec les commentaires
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Commentaires approuvés seulement
     */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Commentaires en attente de modération
     */
    public function pendingComments()
    {
        return $this->hasMany(Comment::class)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relations avec les partages sociaux
     */
    public function shares()
    {
        return $this->hasMany(ArticleShare::class)->orderBy('shared_at', 'desc');
    }

    /**
     * Partages vérifiés seulement
     */
    public function verifiedShares()
    {
        return $this->hasMany(ArticleShare::class)
                    ->where('status', 'verified')
                    ->orderBy('shared_at', 'desc');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    public function getReadingTimeAttribute()
    {
        $wordsPerMinute = 200;
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    public function getImageUrlAttribute()
    {
        if ($this->featured_image) {
            // Si c'est une URL complète
            if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
                return $this->featured_image;
            }
            // Si c'est un chemin relatif
            return asset('storage/' . $this->featured_image);
        }
        // Image par défaut
        return asset('images/blog/placeholder.jpg');
    }

    public function getAuthorNameAttribute()
    {
        return $this->user ? $this->user->display_name : 'Anonyme';
    }

    public function getFormattedDateAttribute()
    {
        $date = $this->published_at ?? $this->created_at;
        return $date->locale('fr')->isoFormat('LL');
    }

    public function getCategoryNameAttribute()
    {
        return $this->category ?? 'Non catégorisé';
    }

    public function getIsDoFollowAttribute()
    {
        return $this->latestSeoAnalysis ? $this->latestSeoAnalysis->is_dofollow : false;
    }

    public function getMetaTitleAttribute()
    {
        return $this->meta_data['title'] ?? $this->title;
    }

    public function getMetaDescriptionAttribute()
    {
        return $this->meta_data['description'] ?? $this->excerpt;
    }

    public function getMetaKeywordsAttribute()
    {
        return $this->meta_data['keywords'] ?? [];
    }

    /**
     * Accesseur pour le nombre de commentaires approuvés
     */
    public function getApprovedCommentsCountAttribute()
    {
        return $this->approvedComments()->count();
    }

    /**
     * Open Graph Tags Generation
     */
    public function getOpenGraphTags()
    {
        $tags = [
            'og:title' => $this->title,
            'og:type' => 'article',
            'og:url' => $this->getPublicUrl(),
            'og:site_name' => config('app.name', 'Nomadie'),
            'og:locale' => 'fr_FR'
        ];
        
        // Description
        if ($this->excerpt) {
            $tags['og:description'] = $this->excerpt;
        } elseif (!empty($this->meta_data['description'])) {
            $tags['og:description'] = $this->meta_data['description'];
        } else {
            $tags['og:description'] = Str::limit(strip_tags($this->content), 160);
        }
        
        // Image
        if ($this->featured_image) {
            $imageUrl = Str::startsWith($this->featured_image, 'http') 
                ? $this->featured_image 
                : asset('storage/' . $this->featured_image);
            
            $tags['og:image'] = $imageUrl;
            $tags['og:image:alt'] = $this->title;
            $tags['og:image:width'] = '1200';
            $tags['og:image:height'] = '630';
        }
        
        // Dates
        if ($this->published_at) {
            $tags['article:published_time'] = $this->published_at->toISOString();
        }
        $tags['article:modified_time'] = $this->updated_at->toISOString();
        
        // Auteur
        if ($this->user) {
            $tags['article:author'] = $this->user->name;
        }
        
        // Catégorie
        if ($this->category) {
            $tags['article:section'] = $this->category;
        }
        
        // Tags
        if ($this->tags && is_array($this->tags)) {
            foreach (array_slice($this->tags, 0, 5) as $tag) {
                $tags['article:tag'] = $tag;
            }
        }
        
        return $tags;
    }

    /**
     * Twitter Card Tags Generation
     */
    public function getTwitterCardTags()
    {
        $ogTags = $this->getOpenGraphTags();
        
        return [
            'twitter:card' => $this->featured_image ? 'summary_large_image' : 'summary',
            'twitter:title' => $this->title,
            'twitter:description' => $ogTags['og:description'],
            'twitter:image' => $this->featured_image ? $ogTags['og:image'] : null,
            'twitter:image:alt' => $this->title
        ];
    }

    /**
     * Get public URL for the article
     */
    public function getPublicUrl()
    {
        // Adaptez selon votre structure de routes
        if (function_exists('route')) {
            try {
                return route('articles.show', $this->slug);
            } catch (\Exception $e) {
                // Fallback si la route n'existe pas
                return url('/blog/' . $this->slug);
            }
        }
        
        return url('/blog/' . $this->slug);
    }

    /**
     * Generate structured data (JSON-LD)
     */
    public function getStructuredData()
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->title,
            'description' => $this->excerpt ?: Str::limit(strip_tags($this->content), 160),
            'url' => $this->getPublicUrl(),
            'datePublished' => $this->published_at ? $this->published_at->toISOString() : $this->created_at->toISOString(),
            'dateModified' => $this->updated_at->toISOString(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->user ? $this->user->name : 'Anonyme'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'Nomadie'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png')
                ]
            ]
        ];

        // Image principale
        if ($this->featured_image) {
            $imageUrl = Str::startsWith($this->featured_image, 'http') 
                ? $this->featured_image 
                : asset('storage/' . $this->featured_image);
                
            $data['image'] = [
                '@type' => 'ImageObject',
                'url' => $imageUrl,
                'width' => 1200,
                'height' => 630
            ];
        }

        // Catégorie
        if ($this->category) {
            $data['articleSection'] = $this->category;
        }

        // Tags/Keywords
        if ($this->tags && is_array($this->tags)) {
            $data['keywords'] = implode(', ', $this->tags);
        }

        // Estimation du temps de lecture
        $data['timeRequired'] = 'PT' . $this->reading_time . 'M';

        return $data;
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    public function canBeAnalyzed()
    {
        return in_array($this->status, ['draft', 'pending', 'published']);
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => $this->published_at ?? now()
        ]);
    }

    public function unpublish()
    {
        $this->update(['status' => 'draft']);
    }

    public function getRelatedArticles($limit = 3)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->when($this->category, function($query) {
                return $query->where('category', $this->category);
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if article has good SEO for social sharing
     */
    public function hasGoodSocialSeo()
    {
        return !empty($this->title) && 
               (!empty($this->excerpt) || !empty($this->meta_data['description'])) && 
               !empty($this->featured_image);
    }

    /**
     * Get social sharing URL
     */
    public function getSocialSharingUrl($platform = 'facebook')
    {
        $url = urlencode($this->getPublicUrl());
        $title = urlencode($this->title);
        
        switch ($platform) {
            case 'facebook':
                return "https://www.facebook.com/sharer/sharer.php?u={$url}";
            case 'twitter':
                return "https://twitter.com/intent/tweet?url={$url}&text={$title}";
            case 'linkedin':
                return "https://www.linkedin.com/sharing/share-offsite/?url={$url}";
            case 'whatsapp':
                return "https://wa.me/?text={$title} {$url}";
            default:
                return $this->getPublicUrl();
        }
    }

    /**
     * Vérifier si les commentaires sont activés pour cet article
     */
    public function commentsEnabled()
    {
        // Vous pouvez ajouter une logique ici si vous voulez désactiver 
        // les commentaires sur certains articles
        return $this->status === 'published';
    }
}