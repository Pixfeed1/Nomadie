<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BriefTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'content_requirements',
        'keywords',
        'min_words',
        'target_score',
        'seo_requirements',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'content_requirements' => 'array',
        'keywords' => 'array',
        'seo_requirements' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Relations
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePopular($query)
    {
        return $query->where('usage_count', '>', 0)->orderBy('usage_count', 'desc');
    }

    /**
     * Créer un brief à partir de ce template
     */
    public function createBrief(array $overrides = [])
    {
        $this->increment('usage_count');

        return ContentBrief::create(array_merge([
            'title' => $this->name,
            'type' => $this->type,
            'content_requirements' => $this->content_requirements,
            'keywords' => $this->keywords,
            'min_words' => $this->min_words,
            'target_score' => $this->target_score,
            'seo_requirements' => $this->seo_requirements,
            'status' => ContentBrief::STATUS_DRAFT,
        ], $overrides));
    }
}
