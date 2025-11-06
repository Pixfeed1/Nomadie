<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Destination extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'country_id',
        'is_active',
        'active' // Pour la rétrocompatibilité
    ];
    
    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'active' => 'boolean',
    ];
    
    /**
     * Relation avec le pays
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Relation avec le continent (via le pays)
     * Cette relation permet d'accéder directement au continent
     */
    public function continent()
    {
        return $this->hasOneThrough(
            Continent::class,
            Country::class,
            'id', // Foreign key on countries table
            'id', // Foreign key on continents table
            'country_id', // Local key on destinations table
            'continent_id' // Local key on countries table
        );
    }
    
    /**
     * Relation avec les voyages associés à cette destination
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
    
    /**
     * Relation avec les vendeurs qui proposent cette destination
     */
    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'destination_vendor');
    }
    
    /**
     * Accesseur pour obtenir le continent_id directement
     * Utilisé pour la rétrocompatibilité avec le code existant
     */
    public function getContinentIdAttribute()
    {
        return $this->country ? $this->country->continent_id : null;
    }
    
    /**
     * Scope pour les destinations actives
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', true)
              ->orWhere('active', true);
        });
    }
}
