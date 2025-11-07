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
}