<?php

namespace App\Livewire;

use App\Models\StockInventory;
use App\Models\StockManagement;
use App\Models\QrScan;
use Livewire\Component;

class Dashboard extends Component
{
    public function getTotalDistributedProperty()
    {
        return StockInventory::sum('quantity');
    }

    public function getTotalRecordsProperty()
    {
        return StockInventory::count();
    }

    public function getTotalStockProperty()
    {
        $stock = StockManagement::first();
        return $stock ? $stock->total_stock : 0;
    }

    public function getAvailableStockProperty()
    {
        $stock = StockManagement::first();
        return $stock ? $stock->available_stock : 0;
    }

    public function getQrScansProperty()
    {
        return QrScan::with('distribution')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTotalQrScansProperty()
    {
        return QrScan::count();
    }

    public function getCompletedQrScansProperty()
    {
        return QrScan::where('completed_distribution', true)->count();
    }

    public function getPendingQrScansProperty()
    {
        return QrScan::whereNull('scanned_at')->count();
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app.sidebar');
    }
}