<?php

namespace App\Livewire\Admin;

use App\Models\StockManagement as StockModel;
use App\Models\StockHistory;
use App\Models\StockInventory;
use App\Models\ABMStorage;
use Livewire\Component;
use Flux\Flux;

class StockManagement extends Component
{
    // Main Stock Modal Properties
    public $showMainStockModal = false;
    public $action_type = 'add';
    public $equipment_type = '';
    public $quantity = '';
    public $notes = '';

    // History Modal Properties
    public $showHistoryModal = false;

    // ABM Details Modal Properties
    public $showAbmDetailsModal = false;
    public $selectedAbmStorage = null;
    public $showAllTransactions = false;


    public function openMainStockModal($actionType)
    {
        $this->action_type = $actionType;
        $this->showMainStockModal = true;
    }

    public function closeMainStockModal()
    {
        $this->showMainStockModal = false;
        $this->resetMainStockForm();
    }

    public function openHistoryModal()
    {
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
    }


    public function saveMainStock()
    {
        $this->validate([
            'equipment_type' => 'required|in:safety_helmet,tshirt',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            if ($this->equipment_type === 'safety_helmet') {
                $stock = StockModel::getSafetyHelmetStock();
                $equipmentName = 'Safety Helmets';
            } else {
                $stock = StockModel::getTshirtStock();
                $equipmentName = 'T-shirts';
            }

            $stockBefore = $stock->total_stock;

            if ($this->action_type === 'add') {
                $stock->total_stock += (int)$this->quantity;
                $message = "Successfully added {$this->quantity} {$equipmentName} to main inventory!";
            } else {
                if ($stock->available_stock < (int)$this->quantity) {
                    Flux::toast('Insufficient Stock', "Cannot deduct more than available stock! Only {$stock->available_stock} {$equipmentName} available.", 5000, 'warning');
                    return;
                }
                $stock->total_stock -= (int)$this->quantity;
                $message = "Successfully deducted {$this->quantity} {$equipmentName} from main inventory!";
            }

            $stock->updateAvailableStock();
            $stock->notes = $this->notes ?: null;
            $stock->save();

            // Record history
            StockHistory::create([
                'action_type' => $this->action_type,
                'equipment_type' => $this->equipment_type,
                'quantity' => (int)$this->quantity,
                'total_stock_before' => $stockBefore,
                'total_stock_after' => $stock->total_stock,
                'notes' => $this->notes,
                'admin_name' => auth()->user()->name ?? 'System',
            ]);

            Flux::toast($message, 'Stock Updated', 5000, 'success');
            $this->closeMainStockModal();

        } catch (\Exception $e) {
            Flux::toast('Error', 'An error occurred while updating stock. Please try again.', 5000, 'danger');
        }
    }


    public function refreshAbmStorage()
    {
        // This will trigger a re-render and ensure all ABM storage records exist
        Flux::toast('ABM Storage data refreshed.', 'Refreshed', 3000, 'success');
    }

    public function viewAbmDetails($regionCode)
    {
        $this->selectedAbmStorage = ABMStorage::getOrCreateForRegion($regionCode);
        $this->showAbmDetailsModal = true;
    }

    public function closeAbmDetailsModal()
    {
        $this->showAbmDetailsModal = false;
        $this->selectedAbmStorage = null;
        $this->showAllTransactions = false;
    }

    public function toggleTransactionView()
    {
        $this->showAllTransactions = !$this->showAllTransactions;
    }

    private function resetMainStockForm()
    {
        $this->action_type = 'add';
        $this->equipment_type = '';
        $this->quantity = '';
        $this->notes = '';
    }


    public function render()
    {
        $helmetStock = StockModel::getSafetyHelmetStock();
        $tshirtStock = StockModel::getTshirtStock();

        // Get all active regions from database and ensure ABM storage records exist
        $regions = \App\Models\Region::where('is_active', true)->get();
        $abmStorages = collect();

        foreach ($regions as $region) {
            $storage = ABMStorage::getOrCreateForRegion($region->code);
            // Override the region_name with the real name from regions table
            $storage->region_name = $region->name;
            $storage->save();
            $abmStorages->push($storage);
        }

        // Sort by region name for consistent display
        $abmStorages = $abmStorages->sortBy('region_name');

        // Get stock history for the modal (latest 50 records)
        $stockHistory = StockHistory::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Get ABM transactions for the selected storage (if any)
        $abmTransactions = collect();
        if ($this->selectedAbmStorage) {
            $abmTransactions = StockInventory::where('region', $this->selectedAbmStorage->region_code)
                ->whereIn('deduction_source', ['total_stocks', 'abm_storage'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        return view('livewire.admin.stock-management', [
            'helmetStock' => $helmetStock,
            'tshirtStock' => $tshirtStock,
            'abmStorages' => $abmStorages,
            'stockHistory' => $stockHistory,
            'abmTransactions' => $abmTransactions,
        ]);
    }
}
