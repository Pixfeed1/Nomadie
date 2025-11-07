<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image',
        'bg_class',
        'description',
    ];

    /**
     * Get all trips of this travel type.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get all countries offering this travel type.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_travel_type');
    }
}
