<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Article;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Writer type constants
     */
    const WRITER_TYPE_COMMUNITY = 'community';
    const WRITER_TYPE_CLIENT_CONTRIBUTOR = 'client_contributor';
    const WRITER_TYPE_PARTNER = 'partner';
    const WRITER_TYPE_TEAM = 'team';

    /**
     * Writer status constants
     */
    const WRITER_STATUS_PENDING = 'pending_validation';
    const WRITER_STATUS_VALIDATED = 'validated';
    const WRITER_STATUS_REJECTED = 'rejected';
    const WRITER_STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'lastname',
        'pseudo',
        'email',
        'password',
        'avatar',
        'role',
        'newsletter',
        'email_verification_token',
        'email_verified_at',
        'google_id',
        'writer_type',
        'writer_status',
        'writer_validated_at',
        'writer_notes',
        'verified_booking_id',
        'partner_offer_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token', // Ajout� pour ne pas exposer le token
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'newsletter' => 'boolean',
        'is_dofollow' => 'boolean',
        'writer_validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the vendor associated with the user.
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Check if the user is a vendor.
     */
    public function isVendor()
    {
        return $this->vendor()->exists();
    }

    /**
     * Check if the user is an active vendor.
     */
    public function isActiveVendor()
    {
        return $this->vendor()->where('status', 'active')->exists();
    }

    /**
     * Get the user's vendor status.
     */
    public function getVendorStatusAttribute()
    {
        if ($this->vendor) {
            return $this->vendor->status;
        }
        return null;
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        // V�rification bas�e sur le champ 'role' de la table users
        if ($this->role === $role) {
            return true;
        }
        
        // V�rification sp�ciale pour les vendors
        if ($role === 'vendor') {
            return $this->isVendor();
        }
        
        return false;
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if email is verified.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_verification_token' => null,
        ])->save();
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
        }
        return $this->name;
    }

    /**
     * Get the user's display name (pseudo or firstname).
     */
    public function getDisplayNameAttribute()
    {
        return $this->pseudo ?? $this->firstname ?? explode(' ', $this->name)[0] ?? 'Utilisateur';
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Avatar par d�faut si aucun n'est d�fini
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->display_name) . '&background=38B2AC&color=fff';
    }

    /**
     * Relation avec les articles
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Relations avec les badges
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('progress_data', 'progress_percentage', 'unlocked_at', 'notified_at', 'is_featured')
                    ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function unlockedBadges()
    {
        return $this->badges()->wherePivotNotNull('unlocked_at');
    }

    public function featuredBadge()
    {
        return $this->badges()->wherePivot('is_featured', true)->first();
    }

    /**
     * Relation avec les analyses SEO
     */
    public function seoAnalyses()
    {
        return $this->hasMany(SeoAnalysis::class);
    }

    /**
     * V�rifier si l'utilisateur a un badge
     */
    public function hasBadge($badgeCode)
    {
        return $this->badges()->where('code', $badgeCode)->exists();
    }

    /**
     * D�bloquer un badge
     */
    public function unlockBadge($badge)
    {
        if ($badge instanceof Badge) {
            $badgeId = $badge->id;
        } else {
            $badge = Badge::where('code', $badge)->first();
            $badgeId = $badge->id;
        }
        
        if (!$this->badges()->where('badge_id', $badgeId)->exists()) {
            $this->badges()->attach($badgeId, [
                'unlocked_at' => now(),
                'progress_percentage' => 100
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Scope to filter only verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to filter only unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope to filter by role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to filter newsletter subscribers.
     */
    public function scopeNewsletterSubscribers($query)
    {
        return $query->where('newsletter', true);
    }

    // ==========================================
    // WRITER TYPE METHODS
    // ==========================================

    /**
     * Check if user is a writer (has any writer_type)
     */
    public function isWriter()
    {
        return !is_null($this->writer_type);
    }

    /**
     * Check if user is a community writer
     */
    public function isCommunityWriter()
    {
        return $this->writer_type === self::WRITER_TYPE_COMMUNITY;
    }

    /**
     * Check if user is a client-contributor
     */
    public function isClientContributor()
    {
        return $this->writer_type === self::WRITER_TYPE_CLIENT_CONTRIBUTOR;
    }

    /**
     * Check if user is a partner writer
     */
    public function isPartner()
    {
        return $this->writer_type === self::WRITER_TYPE_PARTNER;
    }

    /**
     * Check if user is a team member
     */
    public function isTeamMember()
    {
        return $this->writer_type === self::WRITER_TYPE_TEAM;
    }

    /**
     * Check if writer is validated
     */
    public function isValidatedWriter()
    {
        return $this->writer_status === self::WRITER_STATUS_VALIDATED;
    }

    /**
     * Check if writer is pending validation
     */
    public function isPendingWriter()
    {
        return $this->writer_status === self::WRITER_STATUS_PENDING;
    }

    /**
     * Check if writer is rejected
     */
    public function isRejectedWriter()
    {
        return $this->writer_status === self::WRITER_STATUS_REJECTED;
    }

    /**
     * Check if writer is suspended
     */
    public function isSuspendedWriter()
    {
        return $this->writer_status === self::WRITER_STATUS_SUSPENDED;
    }

    /**
     * Check if user can write articles (validated or team)
     */
    public function canWriteArticles()
    {
        // Team members can always write
        if ($this->isTeamMember()) {
            return true;
        }

        // Other writers need to be validated
        return $this->isWriter() && $this->isValidatedWriter();
    }

    /**
     * Check if user needs to submit a test article (community writers only)
     */
    public function needsTestArticle()
    {
        return $this->isCommunityWriter() &&
               $this->writer_status === self::WRITER_STATUS_PENDING &&
               $this->articles()->count() === 0;
    }

    /**
     * Get writer type label
     */
    public function getWriterTypeLabel()
    {
        return match($this->writer_type) {
            self::WRITER_TYPE_COMMUNITY => 'Rédacteur Communauté',
            self::WRITER_TYPE_CLIENT_CONTRIBUTOR => 'Client-Contributeur',
            self::WRITER_TYPE_PARTNER => 'Partenaire',
            self::WRITER_TYPE_TEAM => 'Équipe Nomadie',
            default => 'Non défini',
        };
    }

    /**
     * Get writer status label
     */
    public function getWriterStatusLabel()
    {
        return match($this->writer_status) {
            self::WRITER_STATUS_PENDING => 'En attente de validation',
            self::WRITER_STATUS_VALIDATED => 'Validé',
            self::WRITER_STATUS_REJECTED => 'Refusé',
            self::WRITER_STATUS_SUSPENDED => 'Suspendu',
            default => 'Non défini',
        };
    }

    /**
     * Validate writer (admin action)
     */
    public function validateWriter()
    {
        $this->update([
            'writer_status' => self::WRITER_STATUS_VALIDATED,
            'writer_validated_at' => now()
        ]);
    }

    /**
     * Reject writer (admin action)
     */
    public function rejectWriter($reason = null)
    {
        $this->update([
            'writer_status' => self::WRITER_STATUS_REJECTED,
            'writer_notes' => $reason
        ]);
    }

    /**
     * Suspend writer (admin action)
     */
    public function suspendWriter($reason = null)
    {
        $this->update([
            'writer_status' => self::WRITER_STATUS_SUSPENDED,
            'writer_notes' => $reason
        ]);
    }

    /**
     * Scope to filter by writer type
     */
    public function scopeWriterType($query, $type)
    {
        return $query->where('writer_type', $type);
    }

    /**
     * Scope to filter by writer status
     */
    public function scopeWriterStatus($query, $status)
    {
        return $query->where('writer_status', $status);
    }

    /**
     * Scope to get all validated writers
     */
    public function scopeValidatedWriters($query)
    {
        return $query->where('writer_status', self::WRITER_STATUS_VALIDATED);
    }

    /**
     * Scope to get all pending writers
     */
    public function scopePendingWriters($query)
    {
        return $query->where('writer_status', self::WRITER_STATUS_PENDING);
    }
}