<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'action_type',
        'equipment_type',
        'quantity',
        'total_stock_before',
        'total_stock_after',
        'notes',
        'admin_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActionDisplayAttribute(): string
    {
        return match($this->action_type) {
            'add' => 'Stock Added',
            'deduct' => 'Stock Deducted',
            default => $this->action_type,
        };
    }

    public function getQuantityDisplayAttribute(): string
    {
        $prefix = $this->action_type === 'add' ? '+' : '-';
        return $prefix . $this->quantity;
    }
}
