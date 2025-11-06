<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'display_order',
        'is_active'
    ];

    /**
     * Les vendeurs qui utilisent cet attribut de service
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_service_attribute');
    }
}