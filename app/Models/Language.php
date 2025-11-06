<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'region',
        'is_active',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    /**
     * Scope pour les langues actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les langues populaires
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Les voyages qui utilisent cette langue
     */
    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_languages')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    /**
     * Obtenir le nom complet avec la région
     */
    public function getFullNameAttribute()
    {
        return $this->region ? "{$this->name} ({$this->region})" : $this->name;
    }
}