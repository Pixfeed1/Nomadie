<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryPoint extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id',
        'title',
        'description',
        'duration',
        'accommodation',
        'activities',
        'latitude',
        'longitude',
        'order',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'activities' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'order' => 'integer',
    ];

    /**
     * Relation avec le voyage
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}