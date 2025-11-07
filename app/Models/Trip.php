<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Trip extends Model
{
    use HasFactory;
    
    // ===== CONSTANTES =====
    
    // Types d'offres principaux
    const OFFER_TYPE_ACCOMMODATION = 'accommodation';
    const OFFER_TYPE_ORGANIZED_TRIP = 'organized_trip';
    const OFFER_TYPE_ACTIVITY = 'activity';
    const OFFER_TYPE_CUSTOM = 'custom';
    
    // Types de séjours (ancien système, conservé pour compatibilité)
    const TYPE_FIXED = 'fixed';
    const TYPE_CIRCUIT = 'circuit';
    
    // Modes de tarification
    const PRICING_MODE_PER_PERSON_PER_DAY = 'per_person_per_day';
    const PRICING_MODE_PER_NIGHT_PROPERTY = 'per_night_property';
    const PRICING_MODE_PER_PERSON_ACTIVITY = 'per_person_activity';
    const PRICING_MODE_PER_GROUP = 'per_group';
    const PRICING_MODE_CUSTOM = 'custom';
    
    // Statuts
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    
    // Configuration des types d'offres
    const OFFER_TYPES_CONFIG = [
        'accommodation' => [
            'label' => 'Location d\'hébergement',
            'description' => 'Louez votre gîte, villa, appartement ou maison entière',
            'examples' => ['Gîte rural', 'Villa avec piscine', 'Chalet montagne', 'Appartement en ville'],
            'pricing_mode' => 'per_night_property',
            'duration_type' => 'nights',
            'booking_label' => 'Réserver cet hébergement',
            'cta_text' => 'Envie de séjourner ici ?'
        ],
        'organized_trip' => [
            'label' => 'Séjour organisé',
            'description' => 'Proposez un séjour tout compris avec activités et services',
            'examples' => ['Retraite yoga', 'Stage de surf', 'Circuit culturel', 'Séjour bien-être'],
            'pricing_mode' => 'per_person_per_day',
            'duration_type' => 'days',
            'booking_label' => 'Réserver ce séjour',
            'cta_text' => 'Prêt à partir à l\'aventure ?'
        ],
        'activity' => [
            'label' => 'Activité ou expérience',
            'description' => 'Offrez une activité ponctuelle de quelques heures',
            'examples' => ['Randonnée guidée', 'Cours de cuisine', 'Visite culturelle', 'Activité nautique', 'Cours de danse', 'Cours de yoga'],
            'pricing_mode' => 'per_person_activity',
            'duration_type' => 'hours',
            'booking_label' => 'Réserver cette activité',
            'cta_text' => 'Prêt à vivre cette expérience ?'
        ],
        'custom' => [
            'label' => 'Offre sur mesure',
            'description' => 'Créez une offre personnalisée qui ne rentre pas dans les catégories standard',
            'examples' => ['Package complexe', 'Tarification spéciale', 'Offre hybride'],
            'pricing_mode' => 'custom',
            'duration_type' => 'custom',
            'booking_label' => 'Demander un devis',
            'cta_text' => 'Envie d\'une expérience sur mesure ?'
        ]
    ];
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        // Informations de base
        'title',
        'slug',
        'short_description',
        'description',
        'type', // 'fixed' ou 'circuit'
        'offer_type', // 'accommodation', 'organized_trip', 'activity', 'custom'
        
        // Mode de tarification
        'pricing_mode',
        'pricing_description',
        
        // Relations
        'vendor_id',
        'destination_id',
        'country_id',
        'travel_type_id',
        
        // Prix et capacité
        'price', // Prix de référence
        'duration', // Durée en jours
        'duration_hours', // Durée en heures pour les activités
        'max_travelers', // Capacité max par défaut
        'min_travelers', // Minimum de participants par défaut
        'property_capacity', // Capacité pour les locations
        'min_nights', // Minimum de nuits pour les locations
        
        // Détails physiques et lieu
        'physical_level',
        'meeting_point',
        'meeting_time',
        'meeting_address',
        'meeting_instructions',
        
        // Services et conditions
        'included',
        'not_included',
        'requirements',
        'meal_plan', // none, breakfast, half_board, full_board, all_inclusive
        
        // Programme
        'itinerary', // pour les circuits
        
        // Images
        'image',
        'cover_image',
        'images', // array avec légendes
        
        // Statut et méta
        'status',
        'featured',
        'views_count',
        'rating',
        'reviews_count',
        
        // Paramètres de réservation et paiement
        'payment_online_required',
        'free_booking_allowed',
        
        // SEO
        'meta_title',
        'meta_description'
    ];
    
    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'max_travelers' => 'integer',
        'min_travelers' => 'integer',
        'property_capacity' => 'integer',
        'min_nights' => 'integer',
        'duration' => 'integer',
        'duration_hours' => 'float',
        'featured' => 'boolean',
        'included' => 'array',
        'not_included' => 'array',
        'itinerary' => 'array',
        'images' => 'array',
        'meeting_time' => 'datetime:H:i',
        'views_count' => 'integer',
        'payment_online_required' => 'boolean',
        'free_booking_allowed' => 'boolean',
    ];
    
    /**
     * Les valeurs par défaut des attributs
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'fixed',
        'offer_type' => 'organized_trip',
        'pricing_mode' => 'per_person_per_day',
        'status' => 'draft',
        'meal_plan' => 'none',
        'views_count' => 0,
        'min_travelers' => 1,
        'max_travelers' => 10,
        'rating' => 0.0,
        'reviews_count' => 0,
        'featured' => false,
        'payment_online_required' => false,
        'free_booking_allowed' => false
    ];
    
    /**
     * Boot method pour générer automatiquement le slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($trip) {
            // Générer le slug si non fourni
            if (empty($trip->slug)) {
                $baseSlug = Str::slug($trip->title);
                $slug = $baseSlug . '-' . uniqid();
                $trip->slug = $slug;
            }
            
            // Auto-détection du type d'offre selon le pricing_mode si non défini
            if (empty($trip->offer_type)) {
                $trip->offer_type = match($trip->pricing_mode) {
                    self::PRICING_MODE_PER_NIGHT_PROPERTY => self::OFFER_TYPE_ACCOMMODATION,
                    self::PRICING_MODE_PER_PERSON_ACTIVITY => self::OFFER_TYPE_ACTIVITY,
                    self::PRICING_MODE_CUSTOM => self::OFFER_TYPE_CUSTOM,
                    default => self::OFFER_TYPE_ORGANIZED_TRIP
                };
            }
            
            // Générer les méta si non fournis
            if (empty($trip->meta_title)) {
                $trip->meta_title = $trip->title;
            }
            
            if (empty($trip->meta_description)) {
                $trip->meta_description = Str::limit($trip->short_description ?: $trip->description, 160);
            }
        });
        
        static::updating(function ($trip) {
            // Synchroniser offer_type avec pricing_mode
            if ($trip->isDirty('pricing_mode')) {
                $trip->offer_type = match($trip->pricing_mode) {
                    self::PRICING_MODE_PER_NIGHT_PROPERTY => self::OFFER_TYPE_ACCOMMODATION,
                    self::PRICING_MODE_PER_PERSON_ACTIVITY => self::OFFER_TYPE_ACTIVITY,
                    self::PRICING_MODE_CUSTOM => self::OFFER_TYPE_CUSTOM,
                    default => self::OFFER_TYPE_ORGANIZED_TRIP
                };
            }
        });
    }
    
    // ===== RELATIONS =====
    
    /**
     * Relation avec les disponibilités
     */
    public function availabilities()
    {
        return $this->hasMany(TripAvailability::class);
    }
    
    /**
     * Relation avec la destination du voyage
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
    
    /**
     * Relation avec le pays
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Relation avec le vendeur du voyage
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    
    /**
     * Relation avec le type de voyage
     */
    public function travelType()
    {
        return $this->belongsTo(TravelType::class);
    }
    
    /**
     * Relation avec les avis
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * Relation avec les langues disponibles pour ce voyage
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'trip_languages')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    
    /**
     * Relation avec les réservations
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    // ===== MÉTHODES DE VÉRIFICATION DE TYPE =====
    
    /**
     * Vérifier si c'est une location d'hébergement
     */
    public function isAccommodation()
    {
        return $this->offer_type === self::OFFER_TYPE_ACCOMMODATION || 
               $this->pricing_mode === self::PRICING_MODE_PER_NIGHT_PROPERTY;
    }
    
    /**
     * Vérifier si c'est une activité
     */
    public function isActivity()
    {
        return $this->offer_type === self::OFFER_TYPE_ACTIVITY || 
               $this->pricing_mode === self::PRICING_MODE_PER_PERSON_ACTIVITY;
    }
    
    /**
     * Vérifier si c'est un séjour organisé
     */
    public function isOrganizedTrip()
    {
        return $this->offer_type === self::OFFER_TYPE_ORGANIZED_TRIP || 
               $this->pricing_mode === self::PRICING_MODE_PER_PERSON_PER_DAY;
    }
    
    /**
     * Vérifier si c'est une offre personnalisée
     */
    public function isCustomOffer()
    {
        return $this->offer_type === self::OFFER_TYPE_CUSTOM ||
               $this->pricing_mode === self::PRICING_MODE_CUSTOM;
    }

    /**
     * Vérifier si c'est une location de propriété (alias de isAccommodation)
     * Utilisé par TripAvailability pour gérer les réservations exclusives
     */
    public function isPropertyRental()
    {
        return $this->isAccommodation();
    }

    /**
     * Vérifier si c'est un tarif de groupe
     */
    public function isGroupPricing()
    {
        return $this->pricing_mode === self::PRICING_MODE_PER_GROUP;
    }
    
    // ===== ACCESSEURS POUR L'AFFICHAGE =====
    
    /**
     * Obtenir le label du type d'offre
     */
    public function getOfferTypeLabelAttribute()
    {
        return self::OFFER_TYPES_CONFIG[$this->offer_type]['label'] ?? 'Offre';
    }
    
    /**
     * Obtenir le texte du bouton de réservation
     */
    public function getBookingButtonTextAttribute()
    {
        return self::OFFER_TYPES_CONFIG[$this->offer_type]['booking_label'] ?? 'Réserver';
    }
    
    /**
     * Obtenir le texte CTA (Call To Action)
     */
    public function getCtaTextAttribute()
    {
        return self::OFFER_TYPES_CONFIG[$this->offer_type]['cta_text'] ?? 'Prêt pour l\'aventure ?';
    }
    
    /**
     * Obtenir le texte descriptif CTA
     */
    public function getCtaDescriptionAttribute()
    {
        return match($this->offer_type) {
            self::OFFER_TYPE_ACTIVITY => 'Réservez cette activité dès maintenant et vivez une expérience unique.',
            self::OFFER_TYPE_ACCOMMODATION => 'Réservez votre hébergement dès maintenant pour un séjour inoubliable.',
            self::OFFER_TYPE_CUSTOM => 'Contactez-nous pour créer votre expérience sur mesure.',
            default => 'Réservez ce voyage dès maintenant et laissez-vous guider par nos experts pour une expérience inoubliable.'
        };
    }
    
    /**
     * Obtenir l'unité de prix pour l'affichage
     */
    public function getPriceUnitAttribute()
    {
        return match($this->pricing_mode) {
            self::PRICING_MODE_PER_NIGHT_PROPERTY => 'par nuit',
            self::PRICING_MODE_PER_PERSON_PER_DAY => 'par personne/jour',
            self::PRICING_MODE_PER_PERSON_ACTIVITY => 'par personne',
            self::PRICING_MODE_PER_GROUP => 'pour le groupe',
            self::PRICING_MODE_CUSTOM => '',
            default => 'par personne/jour'
        };
    }
    
    /**
     * Obtenir le label du prix pour l'affichage
     */
    public function getPriceLabelAttribute()
    {
        return match($this->pricing_mode) {
            self::PRICING_MODE_PER_NIGHT_PROPERTY => 'Prix par nuit',
            self::PRICING_MODE_PER_PERSON_PER_DAY => 'Prix par personne/jour',
            self::PRICING_MODE_PER_PERSON_ACTIVITY => 'Prix par personne',
            self::PRICING_MODE_PER_GROUP => 'Prix pour le groupe',
            self::PRICING_MODE_CUSTOM => 'Prix',
            default => 'Prix par personne'
        };
    }
    
    /**
     * Obtenir la durée formatée selon le type
     */
    public function getDurationFormattedAttribute()
    {
        if ($this->isActivity() && $this->duration_hours) {
            if ($this->duration_hours < 1) {
                return round($this->duration_hours * 60) . ' minutes';
            }
            return $this->duration_hours . ' ' . Str::plural('heure', $this->duration_hours);
        }
        
        if ($this->isAccommodation() && $this->min_nights) {
            return 'Minimum ' . $this->min_nights . ' ' . Str::plural('nuit', $this->min_nights);
        }
        
        if ($this->duration) {
            return $this->duration . ' ' . Str::plural('jour', $this->duration);
        }
        
        return '';
    }
    
    /**
     * Obtenir le label de durée
     */
    public function getDurationLabelAttribute()
    {
        if ($this->isActivity()) {
            return 'Durée';
        }
        if ($this->isAccommodation()) {
            return 'Séjour minimum';
        }
        return 'Durée du séjour';
    }
    
    /**
     * Obtenir la capacité selon le type
     */
    public function getCapacityAttribute()
    {
        if ($this->isAccommodation() && $this->property_capacity) {
            return $this->property_capacity;
        }
        return $this->max_travelers;
    }
    
    /**
     * Obtenir le texte de capacité formaté
     */
    public function getCapacityTextAttribute()
    {
        if ($this->isAccommodation() && $this->property_capacity) {
            return "Jusqu'à {$this->property_capacity} personnes";
        }
        
        if ($this->min_travelers > 1) {
            return "{$this->min_travelers} à {$this->max_travelers} personnes";
        }
        
        return "Maximum {$this->max_travelers} personnes";
    }
    
    /**
     * Obtenir le texte du type de séjour
     */
    public function getTypeTextAttribute()
    {
        if ($this->isActivity()) return 'Activité';
        if ($this->isAccommodation()) return 'Hébergement';
        if ($this->isCustomOffer()) return 'Sur mesure';
        
        return match($this->type) {
            'fixed' => 'Séjour fixe',
            'circuit' => 'Circuit itinérant',
            default => 'Séjour'
        };
    }
    
    /**
     * Obtenir le texte du niveau physique
     */
    public function getPhysicalLevelTextAttribute()
    {
        return match($this->physical_level) {
            'easy' => 'Facile',
            'moderate' => 'Modéré',
            'difficult' => 'Difficile',
            'expert' => 'Expert',
            default => 'Non spécifié'
        };
    }
    
    /**
     * Obtenir le texte de la formule repas
     */
    public function getMealPlanTextAttribute()
    {
        return match($this->meal_plan) {
            'none' => 'Repas non inclus',
            'breakfast' => 'Petit-déjeuner inclus',
            'half_board' => 'Demi-pension',
            'full_board' => 'Pension complète',
            'all_inclusive' => 'Tout inclus',
            default => 'Non spécifié'
        };
    }
    
    /**
     * Obtenir l'image principale
     */
    public function getMainImageAttribute()
    {
        // Si on a un array d'images, prendre la première
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            return $this->images[0]['path'] ?? null;
        }
        
        // Sinon utiliser l'ancienne image ou cover_image
        return $this->cover_image ?: $this->image;
    }
    
    /**
     * Vérifie si le séjour est actif
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }
    
    /**
     * Vérifie si le voyage est réservable
     */
    public function getIsBookableAttribute()
    {
        return $this->status === 'active' && $this->has_availabilities;
    }
    
    // ===== MÉTHODES DE CALCUL =====
    
    /**
     * Calculer le prix total selon le mode de tarification
     */
    public function calculateTotalPrice($availability, $options = [])
    {
        $numberOfPersons = $options['persons'] ?? 1;
        $numberOfAdults = $options['adults'] ?? $numberOfPersons;
        $numberOfChildren = $options['children'] ?? 0;
        $numberOfNights = $options['nights'] ?? null;
        
        switch ($this->pricing_mode) {
            case self::PRICING_MODE_PER_NIGHT_PROPERTY:
                $nights = $numberOfNights ?? $this->duration ?? 1;
                return ($availability->property_price ?? $availability->adult_price ?? $this->price) * $nights;
                
            case self::PRICING_MODE_PER_PERSON_PER_DAY:
                $days = $this->duration ?? 1;
                $adultTotal = ($availability->adult_price ?? $this->price) * $numberOfAdults * $days;
                $childTotal = ($availability->child_price ?? 0) * $numberOfChildren * $days;
                return $adultTotal + $childTotal;
                
            case self::PRICING_MODE_PER_PERSON_ACTIVITY:
                $adultTotal = ($availability->adult_price ?? $this->price) * $numberOfAdults;
                $childTotal = ($availability->child_price ?? 0) * $numberOfChildren;
                return $adultTotal + $childTotal;
                
            case self::PRICING_MODE_PER_GROUP:
                return $availability->group_price ?? $availability->adult_price ?? $this->price;
                
            case self::PRICING_MODE_CUSTOM:
                // Pour les cas personnalisés, utiliser le prix de base
                return $availability->adult_price ?? $this->price;
                
            default:
                // Fallback sur le mode par défaut
                return ($availability->adult_price ?? $this->price) * $numberOfPersons;
        }
    }
    
    // ===== MÉTHODES POUR LES DISPONIBILITÉS =====
    
    /**
     * Obtenir les prochaines disponibilités
     */
    public function getUpcomingAvailabilities($limit = 10)
    {
        return $this->availabilities()
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Obtenir la prochaine date disponible
     */
    public function getNextAvailableDateAttribute()
    {
        return $this->availabilities()
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'full')
            ->orderBy('start_date')
            ->first();
    }
    
    /**
     * Vérifier si le voyage a des disponibilités
     */
    public function getHasAvailabilitiesAttribute()
    {
        return $this->availabilities()
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->exists();
    }
    
    /**
     * Obtenir le prix minimum parmi toutes les disponibilités
     */
    public function getMinPriceAttribute()
    {
        $query = $this->availabilities();
        
        if ($this->isAccommodation()) {
            return $query->min('property_price') ?? $query->min('adult_price') ?? $this->price;
        }
        
        return $query->min('adult_price') ?? $this->price;
    }
    
    /**
     * Obtenir le prix maximum parmi toutes les disponibilités
     */
    public function getMaxPriceAttribute()
    {
        $query = $this->availabilities();
        
        if ($this->isAccommodation()) {
            return $query->max('property_price') ?? $query->max('adult_price') ?? $this->price;
        }
        
        return $query->max('adult_price') ?? $this->price;
    }
    
    /**
     * Obtenir la fourchette de prix
     */
    public function getPriceRangeAttribute()
    {
        $min = $this->min_price;
        $max = $this->max_price;
        
        if ($min == $max) {
            return number_format($min, 0, ',', ' ') . ' €';
        }
        
        return number_format($min, 0, ',', ' ') . ' - ' . number_format($max, 0, ',', ' ') . ' €';
    }
    
    /**
     * Obtenir le prix formaté avec l'unité
     */
    public function getPriceDisplayAttribute()
    {
        $price = $this->min_price;
        $formatted = number_format($price, 0, ',', ' ') . ' €';
        
        if ($this->price_unit) {
            $formatted .= ' ' . $this->price_unit;
        }
        
        return $formatted;
    }
    
    /**
     * Obtenir le nombre total de places restantes toutes dates confondues
     */
    public function getTotalAvailableSpotsAttribute()
    {
        return $this->availabilities()
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'full')
            ->sum(\DB::raw('total_spots - booked_spots'));
    }
    
    // ===== SCOPES =====
    
    /**
     * Scope pour les offres actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope pour filtrer par type d'offre
     */
    public function scopeOfferType($query, $type)
    {
        return $query->where('offer_type', $type);
    }
    
    /**
     * Scope pour les hébergements
     */
    public function scopeAccommodations($query)
    {
        return $query->where('offer_type', self::OFFER_TYPE_ACCOMMODATION);
    }
    
    /**
     * Scope pour les séjours organisés
     */
    public function scopeOrganizedTrips($query)
    {
        return $query->where('offer_type', self::OFFER_TYPE_ORGANIZED_TRIP);
    }
    
    /**
     * Scope pour les activités
     */
    public function scopeActivities($query)
    {
        return $query->where('offer_type', self::OFFER_TYPE_ACTIVITY);
    }
    
    /**
     * Scope pour les offres personnalisées
     */
    public function scopeCustomOffers($query)
    {
        return $query->where('offer_type', self::OFFER_TYPE_CUSTOM);
    }
    
    /**
     * Scope pour les séjours fixes
     */
    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }
    
    /**
     * Scope pour les circuits
     */
    public function scopeCircuit($query)
    {
        return $query->where('type', 'circuit');
    }
    
    /**
     * Scope pour filtrer par mode de tarification
     */
    public function scopePricingMode($query, $mode)
    {
        return $query->where('pricing_mode', $mode);
    }
    
    /**
     * Scope pour les voyages avec disponibilités futures
     */
    public function scopeWithUpcomingAvailabilities($query)
    {
        return $query->whereHas('availabilities', function($q) {
            $q->where('start_date', '>=', now())
              ->where('status', '!=', 'cancelled');
        });
    }
    
    /**
     * Scope pour les offres populaires
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc')
                     ->orderBy('rating', 'desc')
                     ->orderBy('reviews_count', 'desc');
    }
    
    /**
     * Scope pour les offres en vedette
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    
    /**
     * Scope pour filtrer par destination
     */
    public function scopeInDestination($query, $destinationId)
    {
        return $query->where('destination_id', $destinationId);
    }
    
    /**
     * Scope pour filtrer par vendeur
     */
    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
}