<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'max_selections',
        'display_order',
        'is_active'
    ];

    /**
     * Les vendeurs qui proposent cette catégorie de service
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_service_category');
    }
}