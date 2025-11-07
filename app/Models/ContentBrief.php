<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ContentBrief extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'category',
        'content_requirements',
        'keywords',
        'references',
        'min_words',
        'target_score',
        'seo_requirements',
        'assigned_to',
        'assigned_at',
        'created_by',
        'deadline',
        'priority',
        'status',
        'article_id',
        'admin_notes',
        'writer_notes',
        'started_at',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'content_requirements' => 'array',
        'keywords' => 'array',
        'references' => 'array',
        'seo_requirements' => 'array',
        'assigned_at' => 'datetime',
        'deadline' => 'date',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Constants pour les statuts
    const STATUS_DRAFT = 'draft';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Constants pour les priorités
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Constants pour les types
    const TYPE_DESTINATION = 'destination';
    const TYPE_GUIDE_PRATIQUE = 'guide_pratique';
    const TYPE_CULTURE = 'culture';
    const TYPE_GASTRONOMIE = 'gastronomie';
    const TYPE_HEBERGEMENT = 'hebergement';
    const TYPE_TRANSPORT = 'transport';
    const TYPE_BUDGET = 'budget';
    const TYPE_CUSTOM = 'custom';

    /**
     * Boot method pour générer automatiquement le slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brief) {
            if (empty($brief->slug)) {
                $brief->slug = Str::slug($brief->title);
            }
        });

        static::updating(function ($brief) {
            if ($brief->isDirty('title') && empty($brief->slug)) {
                $brief->slug = Str::slug($brief->title);
            }
        });
    }

    /**
     * Relations
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Scopes
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Helpers
     */
    public function isOverdue()
    {
        return $this->deadline && $this->deadline->isPast() && !$this->isCompleted();
    }

    public function isCompleted()
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isAssigned()
    {
        return !empty($this->assigned_to);
    }

    public function canBeAssigned()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_ASSIGNED]);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'red',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_LOW => 'gray',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'green',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_PENDING_REVIEW => 'purple',
            self::STATUS_REVISION_REQUESTED => 'orange',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            self::TYPE_DESTINATION => 'Destination',
            self::TYPE_GUIDE_PRATIQUE => 'Guide Pratique',
            self::TYPE_CULTURE => 'Culture',
            self::TYPE_GASTRONOMIE => 'Gastronomie',
            self::TYPE_HEBERGEMENT => 'Hébergement',
            self::TYPE_TRANSPORT => 'Transport',
            self::TYPE_BUDGET => 'Budget',
            self::TYPE_CUSTOM => 'Personnalisé',
            default => 'Autre',
        };
    }

    /**
     * Assigner le brief à un rédacteur
     */
    public function assignTo(User $user)
    {
        $this->update([
            'assigned_to' => $user->id,
            'assigned_at' => now(),
            'status' => self::STATUS_ASSIGNED,
        ]);
    }

    /**
     * Marquer comme commencé
     */
    public function markAsStarted()
    {
        if ($this->status === self::STATUS_ASSIGNED) {
            $this->update([
                'status' => self::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]);
        }
    }

    /**
     * Soumettre pour review
     */
    public function submitForReview(Article $article)
    {
        $this->update([
            'article_id' => $article->id,
            'status' => self::STATUS_PENDING_REVIEW,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Marquer comme complété
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Demander une révision
     */
    public function requestRevision($notes = null)
    {
        $update = ['status' => self::STATUS_REVISION_REQUESTED];

        if ($notes) {
            $update['admin_notes'] = $notes;
        }

        $this->update($update);
    }
}
