<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ABMStorage extends Model
{
    use HasFactory;

    protected $table = 'abm_storage';

    protected $fillable = [
        'region_code',
        'region_name',
        'helmet_stock',
        'tshirt_stock',
        'helmet_received',
        'tshirt_received',
        'helmet_distributed',
        'tshirt_distributed',
        'notes',
        'last_updated_at',
        'last_updated_by',
    ];

    protected $casts = [
        'helmet_stock' => 'integer',
        'tshirt_stock' => 'integer',
        'helmet_received' => 'integer',
        'tshirt_received' => 'integer',
        'helmet_distributed' => 'integer',
        'tshirt_distributed' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get or create ABM storage for a specific region
     */
    public static function getOrCreateForRegion($regionCode)
    {
        // Get the real region name from the regions table
        $region = \App\Models\Region::where('code', $regionCode)->first();
        $regionName = $region ? $region->name : ucfirst($regionCode);

        return self::firstOrCreate(
            ['region_code' => $regionCode],
            [
                'region_name' => $regionName,
                'helmet_stock' => 0,
                'tshirt_stock' => 0,
                'helmet_received' => 0,
                'tshirt_received' => 0,
                'helmet_distributed' => 0,
                'tshirt_distributed' => 0,
            ]
        );
    }

    /**
     * Check if ABM centre has sufficient stock for distribution
     */
    public function checkAvailability($helmetQuantity, $tshirtQuantity)
    {
        return [
            'helmet_available' => $this->helmet_stock >= $helmetQuantity,
            'tshirt_available' => $this->tshirt_stock >= $tshirtQuantity,
            'helmet_stock' => $this->helmet_stock,
            'tshirt_stock' => $this->tshirt_stock,
            'all_available' => ($this->helmet_stock >= $helmetQuantity) && ($this->tshirt_stock >= $tshirtQuantity),
        ];
    }

    /**
     * Add stock to ABM centre (transfer from main inventory)
     */
    public function addStock($helmetQuantity, $tshirtQuantity, $updatedBy = null)
    {
        $this->helmet_stock += $helmetQuantity;
        $this->tshirt_stock += $tshirtQuantity;
        $this->helmet_received += $helmetQuantity;
        $this->tshirt_received += $tshirtQuantity;
        $this->last_updated_at = now();
        $this->last_updated_by = $updatedBy;
        $this->save();

        return $this;
    }

    /**
     * Deduct stock from ABM centre (for distribution to staff)
     */
    public function deductStock($helmetQuantity, $tshirtQuantity, $updatedBy = null)
    {
        if (!$this->checkAvailability($helmetQuantity, $tshirtQuantity)['all_available']) {
            throw new \Exception('Insufficient stock in ABM centre storage');
        }

        $this->helmet_stock -= $helmetQuantity;
        $this->tshirt_stock -= $tshirtQuantity;
        $this->helmet_distributed += $helmetQuantity;
        $this->tshirt_distributed += $tshirtQuantity;
        $this->last_updated_at = now();
        $this->last_updated_by = $updatedBy;
        $this->save();

        return $this;
    }

    /**
     * Get current stock summary
     */
    public function getStockSummary()
    {
        return [
            'region' => $this->region_name,
            'helmet_stock' => $this->helmet_stock,
            'tshirt_stock' => $this->tshirt_stock,
            'total_received' => $this->helmet_received + $this->tshirt_received,
            'total_distributed' => $this->helmet_distributed + $this->tshirt_distributed,
            'last_updated' => $this->last_updated_at,
        ];
    }

    /**
     * Relationship with Region model
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_code', 'code');
    }
}