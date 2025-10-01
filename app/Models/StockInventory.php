<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_name',
        'region',
        'warehouse',
        'item_type',
        'for_use_stock',
        'for_storing',
        'quantity',
        'remarks',
        'distribution_date',
        // New fields for separated equipment tracking
        'helmet_quantity',
        'tshirt_quantity',
        'deduction_source',
        // New fields for separated usage tracking
        'for_use_helmets',
        'for_use_tshirts',
        'for_storing_helmets',
        'for_storing_tshirts',
    ];

    protected $casts = [
        'distribution_date' => 'datetime',
    ];

    public function getItemTypeDisplayAttribute()
    {
        return match($this->item_type) {
            'safety_helmet' => 'Safety Helmet',
            'shirt' => 'Shirt',
            default => $this->item_type,
        };
    }

    public function getRegionDisplayAttribute()
    {
        return match($this->region) {
            'east' => 'East Coast',
            'west' => 'West Coast',
            'north' => 'North',
            'south' => 'South',
            default => $this->region,
        };
    }

    public function regionModel()
    {
        return $this->belongsTo(Region::class, 'region', 'code');
    }
}
