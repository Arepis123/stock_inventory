<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockManagement extends Model
{
    protected $table = 'stock_management';

    protected $fillable = [
        'item_type',
        'total_stock',
        'allocated_stock',
        'available_stock',
        'notes',
    ];

    protected $casts = [
        'total_stock' => 'integer',
        'allocated_stock' => 'integer',
        'available_stock' => 'integer',
    ];

    public static function getHelmetShirtSetStock()
    {
        return self::firstOrCreate(
            ['item_type' => 'helmet_shirt_set'],
            [
                'total_stock' => 0,
                'allocated_stock' => 0,
                'available_stock' => 0,
            ]
        );
    }

    public function updateAvailableStock()
    {
        $this->available_stock = $this->total_stock - $this->allocated_stock;
        $this->save();
    }
}
