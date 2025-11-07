<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Review extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'trip_id',
        'booking_id',
        'rating',
        'content',
        'travel_date',
        'user_name',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'float',
        'travel_date' => 'date',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le voyage
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relation avec la réservation
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Obtenir la date formatée pour l'affichage
     */
    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }
}