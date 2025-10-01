<?php

namespace App\Livewire;

use App\Models\StockInventory;
use App\Models\StockManagement;
use App\Models\QrScan;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    // Stock Statistics
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

    // Equipment-specific Statistics
    public function getTotalHelmetsDistributedProperty()
    {
        return StockInventory::sum('helmet_quantity');
    }

    public function getTotalShirtsDistributedProperty()
    {
        return StockInventory::sum('tshirt_quantity');
    }

    // Time-based Statistics
    public function getDistributionsTodayProperty()
    {
        return StockInventory::whereDate('created_at', today())->count();
    }

    public function getDistributionsThisWeekProperty()
    {
        return StockInventory::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
    }

    public function getDistributionsThisMonthProperty()
    {
        return StockInventory::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function getQuantityDistributedTodayProperty()
    {
        return StockInventory::whereDate('created_at', today())->sum('quantity');
    }

    public function getQuantityDistributedThisWeekProperty()
    {
        return StockInventory::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('quantity');
    }

    public function getQuantityDistributedThisMonthProperty()
    {
        return StockInventory::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');
    }

    // Regional Statistics
    public function getRegionalBreakdownProperty()
    {
        return StockInventory::select('region', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('region')
            ->get()
            ->map(function ($item) {
                return [
                    'region' => $item->region,
                    'region_display' => match($item->region) {
                        'east' => 'East Coast',
                        'west' => 'West Coast',
                        'north' => 'North',
                        'south' => 'South',
                        default => $item->region,
                    },
                    'count' => $item->count,
                    'total_quantity' => $item->total_quantity,
                ];
            });
    }

    // Warehouse Statistics
    public function getWarehouseBreakdownProperty()
    {
        return StockInventory::select('warehouse', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('warehouse')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    // QR Code Statistics
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

    public function getActiveQrCodesProperty()
    {
        return QrScan::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();
    }

    public function getQrScansTodayProperty()
    {
        return QrScan::whereNotNull('scanned_at')
            ->whereDate('scanned_at', today())
            ->count();
    }

    // Recent Activity
    public function getRecentDistributionsProperty()
    {
        return StockInventory::with(['regionModel'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getLastDistributionProperty()
    {
        return StockInventory::latest()->first();
    }

    // Usage Tracking Statistics
    public function getTotalForUseProperty()
    {
        return StockInventory::sum('for_use_stock');
    }

    public function getTotalForStoringProperty()
    {
        return StockInventory::sum('for_storing');
    }

    // Stock Health Metrics
    public function getStockUtilizationRateProperty()
    {
        if ($this->totalStock == 0) return 0;
        return round(($this->totalDistributed / $this->totalStock) * 100, 2);
    }

    public function getAverageDailyDistributionProperty()
    {
        $firstDistribution = StockInventory::oldest()->first();
        if (!$firstDistribution) return 0;

        $daysSinceFirst = max(1, Carbon::parse($firstDistribution->created_at)->diffInDays(now()));
        return round($this->totalRecords / $daysSinceFirst, 2);
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app.sidebar');
    }
}