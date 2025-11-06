<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TripAvailability extends Model
{
    use HasFactory;
    
    // ===== CONSTANTES =====
    
    const STATUS_AVAILABLE = 'available';
    const STATUS_GUARANTEED = 'guaranteed';
    const STATUS_FULL = 'full';
    const STATUS_CANCELLED = 'cancelled';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id',
        'start_date',
        'end_date',
        'total_spots',
        'booked_spots',
        'min_participants',
        
        // Prix standard
        'adult_price',
        'child_price',
        
        // Nouveaux prix pour la flexibilité
        'property_price',      // Prix par nuit pour location entière
        'group_price',         // Prix forfaitaire pour un groupe
        'group_size',          // Taille du groupe pour le prix groupe
        'extra_person_price',  // Supplément par personne additionnelle
        
        // Promotions
        'discount_percentage',
        'discount_ends_at',
        
        // Statut et options
        'status',
        'is_guaranteed',
        'notes'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_ends_at' => 'datetime',
        'total_spots' => 'integer',
        'booked_spots' => 'integer',
        'min_participants' => 'integer',
        'group_size' => 'integer',
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'property_price' => 'decimal:2',
        'group_price' => 'decimal:2',
        'extra_person_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'is_guaranteed' => 'boolean'
    ];
    
    /**
     * Les valeurs par défaut
     */
    protected $attributes = [
        'booked_spots' => 0,
        'status' => 'available',
        'is_guaranteed' => false,
        'discount_percentage' => 0
    ];
    
    // ===== RELATIONS =====
    
    /**
     * Relation avec le voyage
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    
    /**
     * Relation avec les réservations
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trip_availability_id');
    }
    
    // ===== ACCESSEURS =====
    
    /**
     * Obtenir le nombre de places disponibles
     */
    public function getAvailableSpotsAttribute()
    {
        return max(0, $this->total_spots - $this->booked_spots);
    }
    
    /**
     * Vérifier si la disponibilité est complète
     */
    public function getIsFullAttribute()
    {
        return $this->available_spots === 0;
    }
    
    /**
     * Vérifier si la disponibilité est dans le futur
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_date->isFuture();
    }
    
    /**
     * Vérifier si la disponibilité est passée
     */
    public function getIsPastAttribute()
    {
        return $this->end_date->isPast();
    }
    
    /**
     * Vérifier si la disponibilité est en cours
     */
    public function getIsOngoingAttribute()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }
    
    /**
     * Obtenir le prix principal selon le mode de tarification du voyage
     */
    public function getPrimaryPriceAttribute()
    {
        if (!$this->trip) {
            return $this->adult_price;
        }
        
        return match($this->trip->pricing_mode) {
            Trip::PRICING_MODE_PER_NIGHT_PROPERTY => $this->property_price ?? $this->adult_price,
            Trip::PRICING_MODE_PER_GROUP => $this->group_price ?? $this->adult_price,
            default => $this->adult_price
        };
    }
    
    /**
     * Obtenir le prix avec réduction
     */
    public function getDiscountedPriceAttribute()
    {
        $price = $this->primary_price;
        
        if ($this->has_active_discount) {
            return $price * (1 - $this->discount_percentage / 100);
        }
        
        return $price;
    }
    
    /**
     * Obtenir le prix adulte avec réduction
     */
    public function getDiscountedAdultPriceAttribute()
    {
        if ($this->has_active_discount) {
            return $this->adult_price * (1 - $this->discount_percentage / 100);
        }
        return $this->adult_price;
    }
    
    /**
     * Obtenir le prix enfant avec réduction
     */
    public function getDiscountedChildPriceAttribute()
    {
        if (!$this->child_price) {
            return null;
        }
        
        if ($this->has_active_discount) {
            return $this->child_price * (1 - $this->discount_percentage / 100);
        }
        return $this->child_price;
    }
    
    /**
     * Obtenir le prix propriété avec réduction
     */
    public function getDiscountedPropertyPriceAttribute()
    {
        if (!$this->property_price) {
            return null;
        }
        
        if ($this->has_active_discount) {
            return $this->property_price * (1 - $this->discount_percentage / 100);
        }
        return $this->property_price;
    }
    
    /**
     * Obtenir le prix groupe avec réduction
     */
    public function getDiscountedGroupPriceAttribute()
    {
        if (!$this->group_price) {
            return null;
        }
        
        if ($this->has_active_discount) {
            return $this->group_price * (1 - $this->discount_percentage / 100);
        }
        return $this->group_price;
    }
    
    /**
     * Vérifier si la réduction est active
     */
    public function getHasActiveDiscountAttribute()
    {
        return $this->discount_percentage > 0 && 
               (!$this->discount_ends_at || $this->discount_ends_at->isFuture());
    }
    
    /**
     * Obtenir le pourcentage de remplissage
     */
    public function getFillRateAttribute()
    {
        if ($this->total_spots === 0) {
            return 0;
        }
        return round(($this->booked_spots / $this->total_spots) * 100);
    }
    
    /**
     * Obtenir le nombre de jours du voyage
     */
    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
    
    /**
     * Obtenir le statut formaté
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'Disponible',
            self::STATUS_GUARANTEED => 'Départ garanti',
            self::STATUS_FULL => 'Complet',
            self::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }
    
    /**
     * Obtenir la couleur du statut pour l'interface
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'green',
            self::STATUS_GUARANTEED => 'blue',
            self::STATUS_FULL => 'red',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray'
        };
    }
    
    /**
     * Obtenir l'affichage du prix selon le mode
     */
    public function getPriceDisplayAttribute()
    {
        if (!$this->trip) {
            return number_format($this->adult_price, 0, ',', ' ') . ' €';
        }
        
        $price = $this->has_active_discount ? $this->discounted_price : $this->primary_price;
        $formatted = number_format($price, 0, ',', ' ') . ' €';
        
        // Ajouter l'unité selon le type
        if ($this->trip->pricing_mode === Trip::PRICING_MODE_PER_NIGHT_PROPERTY) {
            $formatted .= '/nuit';
        } elseif ($this->trip->pricing_mode === Trip::PRICING_MODE_PER_GROUP) {
            $formatted .= '/groupe';
        } elseif ($this->trip->pricing_mode === Trip::PRICING_MODE_PER_PERSON_ACTIVITY) {
            $formatted .= '/pers';
        }
        
        return $formatted;
    }
    
    /**
     * Obtenir les dates formatées
     */
    public function getFormattedDatesAttribute()
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }
        
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }
    
    // ===== MÉTHODES =====
    
    /**
     * Vérifier si on peut encore réserver
     */
    public function canBook($numberOfTravelers = 1)
    {
        // Pour les locations, on vérifie juste la disponibilité
        if ($this->trip && $this->trip->isPropertyRental()) {
            return $this->status !== self::STATUS_CANCELLED && 
                   $this->booked_spots === 0 && // Pas déjà réservé
                   $this->is_upcoming;
        }
        
        // Pour les autres, on vérifie le nombre de places
        return $this->status !== self::STATUS_CANCELLED && 
               $this->available_spots >= $numberOfTravelers &&
               $this->is_upcoming;
    }
    
    /**
     * Calculer le prix pour une réservation
     */
    public function calculatePrice($options = [])
    {
        if (!$this->trip) {
            return 0;
        }
        
        return $this->trip->calculateTotalPrice($this, $options);
    }
    
    /**
     * Mettre à jour les places réservées
     */
    public function incrementBookedSpots($count = 1)
    {
        // Pour les locations, on marque comme réservé (0 ou 1)
        if ($this->trip && $this->trip->isPropertyRental()) {
            $this->update([
                'booked_spots' => $this->total_spots,
                'status' => self::STATUS_FULL
            ]);
            return;
        }
        
        // Pour les autres types
        $this->increment('booked_spots', $count);
        
        // Mettre à jour le statut si complet
        if ($this->available_spots === 0) {
            $this->update(['status' => self::STATUS_FULL]);
        }
    }
    
    /**
     * Libérer des places
     */
    public function decrementBookedSpots($count = 1)
    {
        // Pour les locations, on libère complètement
        if ($this->trip && $this->trip->isPropertyRental()) {
            $this->update([
                'booked_spots' => 0,
                'status' => self::STATUS_AVAILABLE
            ]);
            return;
        }
        
        // Pour les autres types
        $this->decrement('booked_spots', $count);
        
        // Remettre disponible si c'était complet
        if ($this->status === self::STATUS_FULL && $this->available_spots > 0) {
            $this->update(['status' => self::STATUS_AVAILABLE]);
        }
    }
    
    /**
     * Vérifier si le minimum de participants est atteint
     */
    public function hasMinimumParticipants()
    {
        return $this->booked_spots >= $this->min_participants;
    }
    
    /**
     * Marquer comme garanti
     */
    public function markAsGuaranteed()
    {
        $this->update([
            'is_guaranteed' => true,
            'status' => self::STATUS_GUARANTEED
        ]);
    }
    
    /**
     * Annuler la disponibilité
     */
    public function cancel($refundBookings = true)
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
        
        if ($refundBookings) {
            // Logique pour rembourser les réservations
            $this->bookings()->where('status', 'confirmed')->each(function($booking) {
                $booking->cancel('Disponibilité annulée');
            });
        }
    }
    
    // ===== SCOPES =====
    
    /**
     * Scope pour les disponibilités futures
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }
    
    /**
     * Scope pour les disponibilités passées
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }
    
    /**
     * Scope pour les disponibilités en cours
     */
    public function scopeOngoing($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }
    
    /**
     * Scope pour les disponibilités disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED)
                     ->where('start_date', '>', now())
                     ->where(function($q) {
                         $q->whereColumn('booked_spots', '<', 'total_spots')
                           ->orWhereHas('trip', function($tripQuery) {
                               $tripQuery->where('pricing_mode', Trip::PRICING_MODE_PER_NIGHT_PROPERTY)
                                        ->where('booked_spots', 0);
                           });
                     });
    }
    
    /**
     * Scope pour les disponibilités avec places
     */
    public function scopeWithAvailableSpots($query, $minSpots = 1)
    {
        return $query->whereRaw('(total_spots - booked_spots) >= ?', [$minSpots]);
    }
    
    /**
     * Scope pour les disponibilités dans une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        });
    }
    
    /**
     * Scope pour les disponibilités garanties
     */
    public function scopeGuaranteed($query)
    {
        return $query->where('is_guaranteed', true);
    }
    
    /**
     * Scope pour les disponibilités avec promotions
     */
    public function scopeWithDiscount($query)
    {
        return $query->where('discount_percentage', '>', 0)
                     ->where(function($q) {
                         $q->whereNull('discount_ends_at')
                           ->orWhere('discount_ends_at', '>', now());
                     });
    }
}