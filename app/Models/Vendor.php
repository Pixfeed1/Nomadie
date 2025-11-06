<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'company_name',
        'slug',
        'legal_status',
        'siret',
        'vat',
        'email',
        'phone',
        'website',
        'address',
        'postal_code',
        'city',
        'country',
        'rep_firstname',
        'rep_lastname',
        'rep_position',
        'rep_email',
        'description',
        'experience',
        'logo',
        'status',
        'confirmation_token',
        'email_verified_at',
        'subscription_plan',
        'max_trips',
        'max_destinations',
        'destinations_changes_this_month',
        'last_destinations_change',
        'newsletter',
        'user_id'
    ];

    /**
     * Les attributs qui doivent être castés en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_destinations_change' => 'date',
        'newsletter' => 'boolean',
        'max_trips' => 'integer',
        'max_destinations' => 'integer',
        'destinations_changes_this_month' => 'integer'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vendor) {
            // Générer automatiquement le slug lors de la création
            if (empty($vendor->slug)) {
                $baseSlug = Str::slug($vendor->company_name ?: 'vendor-' . time());
                $vendor->slug = $baseSlug;
                
                // Vérifier l'unicité et ajouter un suffixe si nécessaire
                $count = 1;
                while (static::where('slug', $vendor->slug)->exists()) {
                    $vendor->slug = $baseSlug . '-' . $count;
                    $count++;
                }
            }
        });
        
        static::updating(function ($vendor) {
            // Mettre à jour le slug si le nom de l'entreprise change
            if ($vendor->isDirty('company_name') && !$vendor->isDirty('slug')) {
                $baseSlug = Str::slug($vendor->company_name);
                $vendor->slug = $baseSlug;
                
                // Vérifier l'unicité
                $count = 1;
                while (static::where('slug', $vendor->slug)->where('id', '!=', $vendor->id)->exists()) {
                    $vendor->slug = $baseSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    /**
     * Get the route key for the model.
     * Permet d'utiliser le slug dans les routes au lieu de l'ID
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relation avec les voyages/offres
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Obtient les offres groupées par type
     */
    public function getTripsByTypeAttribute()
    {
        return $this->trips()
            ->where('status', 'active')
            ->get()
            ->groupBy('offer_type');
    }

    /**
     * Obtient le nombre d'offres actives par type
     */
    public function getOfferCountsByTypeAttribute()
    {
        $trips = $this->trips()->where('status', 'active')->get();
        
        return [
            'all' => $trips->count(),
            'accommodation' => $trips->where('offer_type', 'accommodation')->count(),
            'organized_trip' => $trips->where('offer_type', 'organized_trip')->count(),
            'activity' => $trips->where('offer_type', 'activity')->count(),
            'custom' => $trips->where('offer_type', 'custom')->count(),
        ];
    }

    /**
     * Obtient le nombre de voyages actifs
     */
    public function getActiveTripsCountAttribute()
    {
        return $this->trips()->where('status', 'active')->count();
    }

    /**
     * Obtient le nombre total de voyages
     */
    public function getTotalTripsCountAttribute()
    {
        return $this->trips()->count();
    }

    /**
     * Vérifie si le vendor peut créer plus de voyages
     */
    public function canCreateMoreTrips()
    {
        // Pro = illimité
        if ($this->subscription_plan === 'pro') {
            return true;
        }

        return $this->trips()->count() < $this->max_trips;
    }

    /**
     * Obtient le nombre de voyages restants
     */
    public function getRemainingTripsAttribute()
    {
        if ($this->subscription_plan === 'pro') {
            return 9999; // Illimité
        }

        $remaining = $this->max_trips - $this->trips()->count();
        return max(0, $remaining);
    }
    
    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation avec les destinations
     */
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_vendor');
    }
    
    /**
     * Relation avec les pays
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_vendor');
    }
    
    /**
     * Relation avec les services (ancien système)
     * @deprecated Utiliser serviceCategories() et serviceAttributes() à la place
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_vendor');
    }
    
    /**
     * Relation avec les catégories de services (nouveau système)
     */
    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'vendor_service_category');
    }
    
    /**
     * Relation avec les attributs de services (nouveau système)
     */
    public function serviceAttributes()
    {
        return $this->belongsToMany(ServiceAttribute::class, 'vendor_service_attribute');
    }

    /**
     * Relation avec les bookings à travers les trips
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Trip::class);
    }

    /**
     * Relation avec les reviews à travers les trips
     */
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Trip::class);
    }

    /**
     * Obtient la note moyenne du vendor
     */
    public function getAverageRatingAttribute()
    {
        return $this->trips()
            ->where('rating', '>', 0)
            ->avg('rating') ?? 0;
    }

    /**
     * Obtient le nombre total d'avis
     */
    public function getTotalReviewsAttribute()
    {
        return $this->trips()->sum('reviews_count');
    }

    /**
     * Obtient le nombre total de réservations confirmées
     */
    public function getTotalBookingsAttribute()
    {
        return $this->bookings()
            ->where('status', 'confirmed')
            ->count();
    }
    
    /**
     * Vérifie si le vendeur est en attente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Vérifie si le vendeur est actif
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Vérifie si le vendeur est rejeté
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    
    /**
     * Vérifie si le vendeur est suspendu
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
    
    /**
     * Vérifie si l'email a été vérifié
     */
    public function isEmailVerified()
    {
        return $this->email_verified_at !== null;
    }
    
    /**
     * Obtient le statut de souscription du vendeur
     */
    public function getSubscriptionPlanAttribute($value)
    {
        return $value ?? 'free';
    }

    /**
     * Met à jour max_trips et max_destinations quand le plan change
     */
    public function setSubscriptionPlanAttribute($value)
    {
        $this->attributes['subscription_plan'] = $value;
        
        // Mettre à jour automatiquement max_trips
        $this->attributes['max_trips'] = match($value) {
            'pro' => 9999,
            'essential' => 50,
            'free' => 5,
            default => 5
        };

        // Mettre à jour automatiquement max_destinations
        $this->attributes['max_destinations'] = match($value) {
            'pro' => 999,
            'essential' => 10,
            'free' => 3,
            default => 3
        };
    }
    
    /**
     * Obtient l'URL complète du logo
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        
        // Si c'est une URL complète, la retourner telle quelle
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }
        
        // Sinon, construire l'URL depuis storage
        return asset('storage/' . $this->logo);
    }

    /**
     * Obtient les initiales pour l'avatar par défaut
     */
    public function getInitialsAttribute()
    {
        $name = $this->company_name ?: $this->rep_firstname . ' ' . $this->rep_lastname;
        $words = explode(' ', $name);
        $initials = '';
        
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }
        
        return strtoupper($initials);
    }
    
    /**
     * Obtient le taux de commission en fonction de l'abonnement
     */
    public function getCommissionRateAttribute()
    {
        switch ($this->subscription_plan) {
            case 'pro':
                return 5; // 5%
            case 'essential':
                return 10; // 10%
            case 'free':
            default:
                return 20; // 20%
        }
    }
    
    /**
     * Obtient le nombre maximum de destinations selon le forfait
     */
    public function getMaxDestinationsAttribute($value)
    {
        // Si la valeur est définie en BDD, l'utiliser
        if ($value !== null) {
            return $value;
        }

        // Sinon, calculer selon le plan
        return match($this->subscription_plan) {
            'pro' => 999,      // Illimité
            'essential' => 10, // 10 destinations max
            'free' => 3,       // 3 destinations max
            default => 3
        };
    }

    /**
     * Vérifie si le vendor peut ajouter plus de destinations
     */
    public function canAddMoreDestinations()
    {
        if ($this->subscription_plan === 'pro') {
            return true; // Illimité
        }
        
        return $this->countries()->count() < $this->max_destinations;
    }

    /**
     * Obtient le nombre de destinations restantes
     */
    public function getRemainingDestinationsAttribute()
    {
        if ($this->subscription_plan === 'pro') {
            return 999; // Illimité
        }

        $remaining = $this->max_destinations - $this->countries()->count();
        return max(0, $remaining);
    }

    /**
     * Vérifie si le vendor peut modifier ses destinations ce mois
     */
    public function canModifyDestinations()
    {
        // Pro = illimité
        if ($this->subscription_plan === 'pro') {
            return true;
        }

        // Réinitialiser le compteur si on est dans un nouveau mois
        $this->resetDestinationChangesIfNewMonth();

        // Limites par forfait
        $maxChanges = match($this->subscription_plan) {
            'essential' => 5,
            'free' => 2,
            default => 2
        };

        return $this->destinations_changes_this_month < $maxChanges;
    }

    /**
     * Incrémente le compteur de modifications destinations
     */
    public function incrementDestinationChanges()
    {
        $this->update([
            'destinations_changes_this_month' => $this->destinations_changes_this_month + 1,
            'last_destinations_change' => now()->toDateString()
        ]);
    }

    /**
     * Remet à zéro le compteur si on change de mois
     */
    private function resetDestinationChangesIfNewMonth()
    {
        if (!$this->last_destinations_change || 
            now()->format('Y-m') !== $this->last_destinations_change->format('Y-m')) {
            
            $this->update([
                'destinations_changes_this_month' => 0
            ]);
        }
    }

    /**
     * Obtient le nombre de modifications destinations restantes ce mois
     */
    public function getDestinationChangesRemaining()
    {
        if ($this->subscription_plan === 'pro') {
            return 999;
        }

        $this->resetDestinationChangesIfNewMonth();

        $maxChanges = match($this->subscription_plan) {
            'essential' => 5,
            'free' => 2,
            default => 2
        };

        return max(0, $maxChanges - $this->destinations_changes_this_month);
    }

    /**
     * Obtient les statistiques complètes du vendor
     */
    public function getStatsAttribute()
    {
        return [
            'total_trips' => $this->active_trips_count,
            'total_bookings' => $this->total_bookings,
            'average_rating' => round($this->average_rating, 1),
            'total_reviews' => $this->total_reviews,
            'member_since' => $this->created_at ? $this->created_at->year : date('Y'),
            'destinations_count' => $this->countries()->count(),
            'response_time' => '< 24h', // À implémenter avec un vrai calcul
            'completion_rate' => 98, // À calculer depuis les bookings
            'languages' => ['Français', 'Anglais'], // À récupérer depuis la BDD
        ];
    }

    /**
     * Obtient les limites du plan actuel (version enrichie)
     */
    public function getPlanLimitsAttribute()
    {
        return [
            // Voyages
            'max_trips' => $this->max_trips,
            'trips_used' => $this->trips()->count(),
            'trips_remaining' => $this->remaining_trips,
            'unlimited_trips' => $this->subscription_plan === 'pro',
            'can_create_trips' => $this->canCreateMoreTrips(),

            // Destinations
            'max_destinations' => $this->max_destinations,
            'destinations_used' => $this->countries()->count(),
            'destinations_remaining' => $this->remaining_destinations,
            'unlimited_destinations' => $this->subscription_plan === 'pro',
            'can_add_destinations' => $this->canAddMoreDestinations(),
            'can_modify_destinations' => $this->canModifyDestinations(),
            'destinations_changes_remaining' => $this->getDestinationChangesRemaining(),

            // Finances
            'commission_rate' => $this->commission_rate,

            // Plan
            'subscription_plan' => $this->subscription_plan,
            'plan_name' => match($this->subscription_plan) {
                'pro' => 'Professionnel',
                'essential' => 'Essentiel', 
                'free' => 'Gratuit',
                default => 'Gratuit'
            },
            'plan_badge_color' => match($this->subscription_plan) {
                'pro' => 'gold',
                'essential' => 'silver',
                'free' => 'bronze',
                default => 'gray'
            }
        ];
    }

    /**
     * Obtient l'URL publique du vendor
     */
    public function getPublicUrlAttribute()
    {
        return route('vendors.show', $this->slug ?: $this->id);
    }

    /**
     * Scope pour les vendors actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les vendors avec email vérifié
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}