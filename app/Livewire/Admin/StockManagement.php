<?php

namespace App\Livewire\Admin;

use App\Models\StockManagement as StockModel;
use App\Models\StockHistory;
use Livewire\Component;
use Flux\Flux;

class StockManagement extends Component
{
    public $action_type = 'add';
    public $quantity = '';
    public $notes = '';
    public $showModal = false;

    public function addStock()
    {
        $this->action_type = 'add';
        $this->showModal = true;
    }

    public function deductStock()
    {
        $this->action_type = 'deduct';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $stock = StockModel::getHelmetShirtSetStock();
        $stockBefore = $stock->total_stock;

        if ($this->action_type === 'add') {
            $stock->total_stock += (int)$this->quantity;
            $message = "Successfully added {$this->quantity} helmet & shirt sets to stock!";
        } else {
            if ($stock->available_stock < (int)$this->quantity) {
                Flux::toast('Cannot deduct more than available stock!', 'error');
                return;
            }
            $stock->total_stock -= (int)$this->quantity;
            $message = "Successfully deducted {$this->quantity} helmet & shirt sets from stock!";
        }

        $stock->updateAvailableStock();
        $stock->notes = $this->notes ?: null;
        $stock->save();

        // Record history
        StockHistory::create([
            'action_type' => $this->action_type,
            'quantity' => (int)$this->quantity,
            'total_stock_before' => $stockBefore,
            'total_stock_after' => $stock->total_stock,
            'notes' => $this->notes,
            'admin_name' => auth()->user()->name ?? 'System',
        ]);

        Flux::toast($message);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->action_type = 'add';
        $this->quantity = '';
        $this->notes = '';
    }

    public function render()
    {
        $stock = StockModel::getHelmetShirtSetStock();
        $stockHistory = StockHistory::orderBy('created_at', 'desc')->limit(20)->get();

        return view('livewire.admin.stock-management', [
            'stock' => $stock,
            'stockHistory' => $stockHistory
        ]);
    }
}
