<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'continent_id',
        'name',
        'slug',
        'image',
        'description',
        'popular',
        'rating',
        'best_time',
        'position',
        'tags'
    ];

    protected $casts = [
        'popular' => 'boolean',
        'position' => 'array',
        'tags' => 'array',
    ];

    public function continent()
    {
        return $this->belongsTo(Continent::class);
    }

    public function travelTypes()
    {
        return $this->belongsToMany(TravelType::class);
    }

    public function trips()
    {
        // Si vous avez un modèle Trip existant
        // return $this->hasMany(Trip::class);
        return [];
    }
    
    public function getTripsCountAttribute()
    {
        // Si vous avez un modèle Trip existant
        // return $this->trips()->count();
        return rand(5, 50); // Temporaire pour le développement
    }
}