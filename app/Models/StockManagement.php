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

    public static function getSafetyHelmetStock()
    {
        return self::firstOrCreate(
            ['item_type' => 'safety_helmet'],
            [
                'total_stock' => 0,
                'allocated_stock' => 0,
                'available_stock' => 0,
            ]
        );
    }

    public static function getTshirtStock()
    {
        return self::firstOrCreate(
            ['item_type' => 'tshirt'],
            [
                'total_stock' => 0,
                'allocated_stock' => 0,
                'available_stock' => 0,
            ]
        );
    }

    public static function checkStockAvailability($helmetQuantity, $tshirtQuantity)
    {
        $helmetStock = self::getSafetyHelmetStock();
        $tshirtStock = self::getTshirtStock();

        $result = [
            'helmet_available' => $helmetStock->available_stock >= $helmetQuantity,
            'tshirt_available' => $tshirtStock->available_stock >= $tshirtQuantity,
            'helmet_stock' => $helmetStock->available_stock,
            'tshirt_stock' => $tshirtStock->available_stock,
        ];

        $result['all_available'] = $result['helmet_available'] && $result['tshirt_available'];

        return $result;
    }

    public static function deductStock($helmetQuantity, $tshirtQuantity)
    {
        $helmetStock = self::getSafetyHelmetStock();
        $tshirtStock = self::getTshirtStock();

        if ($helmetQuantity > 0) {
            $helmetStock->allocated_stock += $helmetQuantity;
            $helmetStock->updateAvailableStock();
            $helmetStock->save();
        }

        if ($tshirtQuantity > 0) {
            $tshirtStock->allocated_stock += $tshirtQuantity;
            $tshirtStock->updateAvailableStock();
            $tshirtStock->save();
        }

        return [
            'helmet_stock' => $helmetStock,
            'tshirt_stock' => $tshirtStock,
        ];
    }

    public function updateAvailableStock()
    {
        $this->available_stock = $this->total_stock - $this->allocated_stock;
        $this->save();
    }
}
