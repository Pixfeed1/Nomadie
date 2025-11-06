<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trip_id',
        'trip_availability_id',
        'user_id',
        'vendor_id',
        'booking_number',
        'number_of_adults',
        'number_of_children',
        'number_of_travelers', // Total (adults + children)
        'adult_price',
        'child_price',
        'subtotal',
        'discount_amount',
        'total_amount',
        'total_price', // Ajouté pour compatibilité avec CustomerDashboardController
        'status',
        'payment_status',
        'payment_method',
        'payment_id',
        'payment_intent_id',
        'notes',
        'special_requests',
        'traveler_details',
        'cancelled_at',
        'cancelled_reason',
        'confirmed_at',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2', // Ajouté
        'number_of_adults' => 'integer',
        'number_of_children' => 'integer',
        'number_of_travelers' => 'integer',
        'traveler_details' => 'array',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'payment_intent_id',
    ];

    /**
     * Statuts possibles pour une réservation
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Statuts de paiement possibles
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_PARTIAL_REFUND = 'partial_refund';

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Générer un numéro de réservation unique
            if (empty($booking->booking_number)) {
                $prefix = 'BK';
                $year = date('Y');
                $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $booking->booking_number = $prefix . $year . $random;
                
                // Vérifier l'unicité
                while (self::where('booking_number', $booking->booking_number)->exists()) {
                    $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                    $booking->booking_number = $prefix . $year . $random;
                }
            }

            // Calculer le nombre total de voyageurs
            if (!isset($booking->number_of_travelers)) {
                $booking->number_of_travelers = ($booking->number_of_adults ?? 0) + ($booking->number_of_children ?? 0);
            }

            // Synchroniser total_price avec total_amount pour compatibilité
            if (isset($booking->total_amount) && !isset($booking->total_price)) {
                $booking->total_price = $booking->total_amount;
            }

            // Définir le vendor_id depuis la disponibilité si pas défini
            if (!isset($booking->vendor_id) && $booking->availability) {
                $booking->vendor_id = $booking->availability->trip->vendor_id;
            }
        });

        static::updating(function ($booking) {
            // Recalculer le nombre total de voyageurs si adultes ou enfants changent
            if ($booking->isDirty(['number_of_adults', 'number_of_children'])) {
                $booking->number_of_travelers = ($booking->number_of_adults ?? 0) + ($booking->number_of_children ?? 0);
            }

            // Synchroniser total_price avec total_amount
            if ($booking->isDirty('total_amount')) {
                $booking->total_price = $booking->total_amount;
            }
        });

        static::saved(function ($booking) {
            // Mettre à jour les places réservées dans la disponibilité
            if ($booking->trip_availability_id) {
                $booking->updateAvailabilityBookedSpots();
            }
        });

        static::deleted(function ($booking) {
            // Mettre à jour les places réservées dans la disponibilité lors de la suppression
            if ($booking->trip_availability_id) {
                $booking->updateAvailabilityBookedSpots();
            }
        });
    }

    /**
     * Relation avec le voyage
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relation avec la disponibilité
     */
    public function availability()
    {
        return $this->belongsTo(TripAvailability::class, 'trip_availability_id');
    }

    /**
     * Alias pour la relation availability (pour compatibilité)
     */
    public function tripAvailability()
    {
        return $this->availability();
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le vendeur
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relation avec l'avis (un booking peut avoir un avis)
     */
    public function review()
    {
        // Cherche un avis basé sur booking_id si la colonne existe,
        // sinon basé sur user_id et trip_id
        if (\Schema::hasColumn('reviews', 'booking_id')) {
            return $this->hasOne(Review::class, 'booking_id');
        } else {
            // Fallback : cherche un avis du même utilisateur pour le même trip
            return $this->hasOne(Review::class, 'trip_id', 'trip_id')
                        ->where('user_id', $this->user_id);
        }
    }

    /**
     * Scope pour les réservations confirmées
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope pour les réservations en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope pour les réservations payées
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    /**
     * Scope pour les réservations annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope pour filtrer par vendor
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Vérifier si la réservation peut être annulée
     */
    public function canBeCancelled()
    {
        // Ne peut pas annuler si déjà annulée ou remboursée
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REFUNDED])) {
            return false;
        }

        // Ne peut pas annuler si le voyage a déjà commencé
        if ($this->availability && $this->availability->start_date < now()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si la réservation peut être modifiée
     */
    public function canBeModified()
    {
        // Ne peut pas modifier si annulée ou remboursée
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REFUNDED])) {
            return false;
        }

        // Ne peut pas modifier si le voyage commence dans moins de 48h
        if ($this->availability && $this->availability->start_date < now()->addHours(48)) {
            return false;
        }

        return true;
    }

    /**
     * Confirmer la réservation
     */
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Annuler la réservation
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);
    }

    /**
     * Marquer comme payé
     */
    public function markAsPaid($paymentId = null, $paymentIntentId = null)
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'paid_at' => now(),
            'payment_id' => $paymentId ?: $this->payment_id,
            'payment_intent_id' => $paymentIntentId ?: $this->payment_intent_id,
        ]);
    }

    /**
     * Calculer le montant du remboursement selon la politique d'annulation
     */
    public function calculateRefundAmount()
    {
        if (!$this->availability) {
            return 0;
        }

        $daysUntilStart = now()->diffInDays($this->availability->start_date, false);
        
        // Politique de remboursement (à adapter selon vos besoins)
        if ($daysUntilStart >= 30) {
            return $this->total_amount; // 100% de remboursement
        } elseif ($daysUntilStart >= 14) {
            return $this->total_amount * 0.5; // 50% de remboursement
        } elseif ($daysUntilStart >= 7) {
            return $this->total_amount * 0.25; // 25% de remboursement
        }
        
        return 0; // Pas de remboursement
    }

    /**
     * Obtenir le statut formaté
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_CANCELLED => 'Annulée',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_REFUNDED => 'Remboursée',
            default => $this->status
        };
    }

    /**
     * Obtenir le statut de paiement formaté
     */
    public function getFormattedPaymentStatusAttribute()
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'En attente',
            self::PAYMENT_PAID => 'Payé',
            self::PAYMENT_FAILED => 'Échec',
            self::PAYMENT_REFUNDED => 'Remboursé',
            self::PAYMENT_PARTIAL_REFUND => 'Remboursement partiel',
            default => $this->payment_status
        };
    }

    /**
     * Obtenir les dates du voyage
     */
    public function getTripDatesAttribute()
    {
        if (!$this->availability) {
            return null;
        }

        return [
            'start' => $this->availability->start_date,
            'end' => $this->availability->end_date,
            'duration' => $this->availability->start_date->diffInDays($this->availability->end_date) + 1,
        ];
    }

    /**
     * Mettre à jour les places réservées dans la disponibilité
     */
    protected function updateAvailabilityBookedSpots()
    {
        if ($this->availability) {
            $bookedSpots = $this->availability->bookings()
                ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_COMPLETED])
                ->sum('number_of_travelers');
            
            $this->availability->update(['booked_spots' => $bookedSpots]);
        }
    }

    /**
     * Obtenir le code QR pour la réservation
     */
    public function getQrCodeAttribute()
    {
        return route('bookings.verify', ['booking' => $this->booking_number]);
    }

    /**
     * Vérifier si la réservation est pour bientôt
     */
    public function isUpcoming()
    {
        return $this->availability && 
               $this->availability->start_date > now() && 
               $this->availability->start_date <= now()->addDays(7);
    }

    /**
     * Vérifier si le voyage est passé
     */
    public function isPast()
    {
        return $this->availability && $this->availability->end_date < now();
    }

    /**
     * Vérifier si le voyage est en cours
     */
    public function isOngoing()
    {
        return $this->availability && 
               $this->availability->start_date <= now() && 
               $this->availability->end_date >= now();
    }

    /**
     * Vérifier si un avis peut être laissé pour cette réservation
     */
    public function canBeReviewed()
    {
        // Doit être terminé et pas encore d'avis
        return $this->status === self::STATUS_COMPLETED && 
               !$this->review()->exists() &&
               $this->isPast();
    }
}