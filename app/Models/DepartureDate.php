<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DepartureDate extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id',
        'date',
        'price',
        'available_seats',
        'is_confirmed',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'price' => 'float',
        'available_seats' => 'integer',
        'is_confirmed' => 'boolean',
    ];

    /**
     * Relation avec le voyage
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Accesseur pour récupérer la date formatée
     */
    public function getFormattedAttribute()
    {
        return $this->date->format('d F Y');
    }

    /**
     * Accesseur pour récupérer la valeur (date) au format ISO 8601
     */
    public function getValueAttribute()
    {
        return $this->date->format('Y-m-d');
    }
}