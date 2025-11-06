<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
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
        'icon',
        'active'
    ];

    /**
     * Relation avec les vendeurs qui proposent ce service
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'service_vendor');
    }
    
    /**
     * Relation avec les voyages qui incluent ce service
     */
    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'service_trip');
    }
}