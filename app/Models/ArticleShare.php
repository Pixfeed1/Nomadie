<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleShare extends Model
{
    use HasFactory;

    /**
     * Platform constants
     */
    const PLATFORM_FACEBOOK = 'facebook';
    const PLATFORM_TWITTER = 'twitter';
    const PLATFORM_LINKEDIN = 'linkedin';
    const PLATFORM_WHATSAPP = 'whatsapp';
    const PLATFORM_OTHER = 'other';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'article_id',
        'user_id',
        'platform',
        'share_url',
        'proof_screenshot',
        'shared_at',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'shared_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Methods
     */
    public function verify()
    {
        $this->update(['status' => self::STATUS_VERIFIED]);
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_notes' => $reason
        ]);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get platform label
     */
    public function getPlatformLabel()
    {
        return match($this->platform) {
            self::PLATFORM_FACEBOOK => 'Facebook',
            self::PLATFORM_TWITTER => 'Twitter (X)',
            self::PLATFORM_LINKEDIN => 'LinkedIn',
            self::PLATFORM_WHATSAPP => 'WhatsApp',
            self::PLATFORM_OTHER => 'Autre',
            default => 'Inconnu',
        };
    }

    /**
     * Get platform icon emoji
     */
    public function getPlatformIcon()
    {
        return match($this->platform) {
            self::PLATFORM_FACEBOOK => '📘',
            self::PLATFORM_TWITTER => '🐦',
            self::PLATFORM_LINKEDIN => '💼',
            self::PLATFORM_WHATSAPP => '💬',
            self::PLATFORM_OTHER => '🔗',
            default => '📱',
        };
    }
}
