<?php

namespace App\Livewire;

use App\Models\StockInventory;
use App\Models\Region;
use App\Models\Warehouse;
use App\Models\Staff;
use App\Models\StockManagement;
use Livewire\Component;
use Flux\Flux;

class StockDistribution extends Component
{
    public $staff_name = '';
    public $region = '';
    public $warehouse = '';
    public $for_use_stock = '';
    public $for_storing = '';
    public $quantity = 0;
    public $remarks = '';
    public $distribution_date = '';
    public $scanned = false;

    public function mount()
    {
        $this->distribution_date = now()->format('Y-m-d');

        // Verify access via QR code scan
        $qrScanId = session('current_qr_scan_id');
        if (!$qrScanId) {
            // No valid QR scan session - redirect to home
            session()->flash('error', 'Access denied. Please scan a QR code to access the distribution form.');
            return redirect()->route('home');
        }

        // Verify the QR scan is still valid
        $qrScan = \App\Models\QrScan::find($qrScanId);
        if (!$qrScan || !$qrScan->scanned_at || $qrScan->completed_distribution) {
            session()->forget('current_qr_scan_id');
            session()->flash('error', 'Invalid or expired access. Please scan a QR code again.');
            return redirect()->route('home');
        }

        $this->scanned = true; // Mark as scanned since they accessed via QR code
    }

    public function getStaffNamesProperty()
    {
        return Staff::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function getRegionsProperty()
    {
        return Region::where('is_active', true)->get();
    }

    public function getAvailableWarehousesProperty()
    {
        if (!$this->region) {
            return [];
        }

        $selectedRegion = Region::where('code', $this->region)->first();
        if (!$selectedRegion) {
            return [];
        }

        return Warehouse::where('region_id', $selectedRegion->id)
            ->where('is_active', true)
            ->get();
    }

    public function updatedRegion()
    {
        $this->warehouse = '';
    }

    public function updatedForUseStock()
    {
        $this->updateQuantity();
    }

    public function updatedForStoring()
    {
        $this->updateQuantity();
    }

    protected function updateQuantity()
    {
        $for_use = $this->for_use_stock ?: 0;
        $for_storing = $this->for_storing ?: 0;
        $this->quantity = (int)$for_use + (int)$for_storing;
    }

    public function submit()
    {
        try {
            $this->validate([
                'staff_name' => 'required|string|max:255',
                'region' => 'required|in:east,west,north,south',
                'warehouse' => 'required|string|max:255',
                'for_use_stock' => 'nullable|integer|min:0',
                'for_storing' => 'nullable|integer|min:0',
                'distribution_date' => 'required|date',
                'remarks' => 'nullable|string|max:1000',
            ]);

            if ($this->quantity <= 0) {
                Flux::toast('Invalid Quantity', 'Please enter a quantity for either "For Use Stock" or "For Storing" (or both).');
                return;
            }

            $stockManagement = StockManagement::getHelmetShirtSetStock();

            if ($stockManagement->available_stock < $this->quantity) {
                Flux::toast('Insufficient Stock', "Only {$stockManagement->available_stock} helmet & shirt sets available in inventory.");
                return;
            }

            $stockInventory = StockInventory::create([
                'staff_name' => $this->staff_name,
                'region' => $this->region,
                'warehouse' => $this->warehouse,
                'item_type' => 'helmet_shirt_set',
                'for_use_stock' => $this->for_use_stock ?: 0,
                'for_storing' => $this->for_storing ?: 0,
                'quantity' => $this->quantity,
                'remarks' => $this->remarks,
                'distribution_date' => $this->distribution_date,
            ]);

            $stockManagement->allocated_stock += $this->quantity;
            $stockManagement->updateAvailableStock();
            $stockManagement->save();

            // Link with QR scan if accessed via QR code
            $qrScanId = session('current_qr_scan_id');
            if ($qrScanId) {
                $qrScan = \App\Models\QrScan::find($qrScanId);
                if ($qrScan) {
                    $qrScan->update([
                        'completed_distribution' => true,
                        'distribution_id' => $stockInventory->id,
                    ]);
                }
                session()->forget('current_qr_scan_id');
            }

            $quantity = $this->quantity;
            $warehouse = $this->warehouse;

            $this->reset(['staff_name', 'region', 'warehouse', 'for_use_stock', 'for_storing', 'quantity', 'remarks']);
            $this->distribution_date = now()->format('Y-m-d');

            Flux::toast("Successfully distributed {$quantity} helmet & shirt sets to {$warehouse}.", "Distribution Recorded", 5000, 'success');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Flux::toast('Validation Error', 'Please check all required fields and try again.', 5000, 'warning');
        } catch (\Exception $e) {
            Flux::toast('Error', 'An unexpected error occurred while saving the record. Please try again.', 5000, 'danger');
        }
    }

    public function render()
    {
        return view('livewire.stock-distribution')
            ->layout('components.layouts.app');
    }
}