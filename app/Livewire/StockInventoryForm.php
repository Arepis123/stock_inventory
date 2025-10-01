<?php

namespace App\Livewire;

use App\Models\StockInventory;
use App\Models\Region;
use App\Models\Warehouse;
use App\Models\Staff;
use App\Models\StockManagement;
use App\Models\ABMStorage;
use Livewire\Component;
use Flux\Flux;

class StockInventoryForm extends Component
{
    public $staff_name = '';
    public $region = '';
    public $warehouse = '';
    public $for_use_stock = '';
    public $for_storing = '';
    public $quantity = 0;
    public $remarks = '';
    public $distribution_date = '';

    // New properties for separated equipment tracking
    public $helmet_quantity = '';
    public $tshirt_quantity = '';
    public $deduction_source = '';

    // New properties for separated usage tracking
    public $for_use_helmets = '';
    public $for_use_tshirts = '';
    public $for_storing_helmets = '';
    public $for_storing_tshirts = '';

    public function mount()
    {
        $this->distribution_date = now()->format('Y-m-d');
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

    public function updatedHelmetQuantity()
    {
        $this->updateQuantity();
    }

    public function updatedTshirtQuantity()
    {
        $this->updateQuantity();
    }

    public function updatedForUseHelmets()
    {
        $this->updateUsageQuantities();
    }

    public function updatedForUseTshirts()
    {
        $this->updateUsageQuantities();
    }

    public function updatedForStoringHelmets()
    {
        $this->updateUsageQuantities();
    }

    public function updatedForStoringTshirts()
    {
        $this->updateUsageQuantities();
    }

    protected function updateQuantity()
    {
        $for_use = $this->for_use_stock ?: 0;
        $for_storing = $this->for_storing ?: 0;
        $helmet = $this->helmet_quantity ?: 0;
        $tshirt = $this->tshirt_quantity ?: 0;

        // For backward compatibility, use legacy calculation if new fields are empty
        if ($helmet == 0 && $tshirt == 0) {
            $this->quantity = (int)$for_use + (int)$for_storing;
        } else {
            // Use the total of helmet and tshirt quantities
            $this->quantity = (int)$helmet + (int)$tshirt;
        }
    }

    protected function updateUsageQuantities()
    {
        // When usage fields are updated, calculate total helmet and tshirt quantities
        $useHelmets = $this->for_use_helmets ?: 0;
        $useTshirts = $this->for_use_tshirts ?: 0;
        $storeHelmets = $this->for_storing_helmets ?: 0;
        $storeTshirts = $this->for_storing_tshirts ?: 0;

        // Update main equipment quantities
        $this->helmet_quantity = (int)$useHelmets + (int)$storeHelmets;
        $this->tshirt_quantity = (int)$useTshirts + (int)$storeTshirts;

        // Update legacy usage totals for backward compatibility
        $this->for_use_stock = (int)$useHelmets + (int)$useTshirts;
        $this->for_storing = (int)$storeHelmets + (int)$storeTshirts;

        // Update total quantity
        $this->updateQuantity();
    }

    public function resetForm()
    {
        $this->reset([
            'staff_name',
            'region',
            'warehouse',
            'for_use_stock',
            'for_storing',
            'quantity',
            'remarks',
            'helmet_quantity',
            'tshirt_quantity',
            'deduction_source',
            'for_use_helmets',
            'for_use_tshirts',
            'for_storing_helmets',
            'for_storing_tshirts'
        ]);

        $this->distribution_date = now()->format('Y-m-d');

        Flux::toast('Form reset successfully', 'Reset', 2000, 'success');
    }

    public function debugForm()
    {
        $data = [
            'staff_name' => $this->staff_name,
            'region' => $this->region,
            'warehouse' => $this->warehouse,
            'deduction_source' => $this->deduction_source,
            'helmet_quantity' => $this->helmet_quantity,
            'tshirt_quantity' => $this->tshirt_quantity,
            'for_use_helmets' => $this->for_use_helmets,
            'for_use_tshirts' => $this->for_use_tshirts,
            'for_storing_helmets' => $this->for_storing_helmets,
            'for_storing_tshirts' => $this->for_storing_tshirts,
            'distribution_date' => $this->distribution_date,
        ];

        \Log::info('Form Debug Data', $data);

        $debugMessage = "Debug Data:\n";
        foreach ($data as $key => $value) {
            $debugMessage .= "{$key}: " . ($value ?: 'empty') . "\n";
        }

        Flux::toast($debugMessage, 'Form Debug', 10000, 'info');
    }

    public function submit()
    {
        // Test if method is being called
        Flux::toast('Submit method called!', 'Debug', 3000, 'info');

        try {
            // Debug: Log form data
            \Log::info('Form submission attempt', [
                'staff_name' => $this->staff_name,
                'region' => $this->region,
                'warehouse' => $this->warehouse,
                'deduction_source' => $this->deduction_source,
                'helmet_quantity' => $this->helmet_quantity,
                'tshirt_quantity' => $this->tshirt_quantity,
                'for_use_helmets' => $this->for_use_helmets,
                'for_use_tshirts' => $this->for_use_tshirts,
                'for_storing_helmets' => $this->for_storing_helmets,
                'for_storing_tshirts' => $this->for_storing_tshirts,
            ]);

            $validationRules = [
                'staff_name' => 'required|string|max:255',
                'region' => 'required|in:east,west,north,south',
                'warehouse' => 'required|string|max:255',
                'distribution_date' => 'required|date',
                'remarks' => 'nullable|string|max:1000',
                'deduction_source' => 'required|in:total_stocks,abm_storage',
            ];

            // Conditional validation based on deduction source
            if ($this->deduction_source === 'total_stocks') {
                $validationRules['for_use_helmets'] = 'nullable|integer|min:0';
                $validationRules['for_use_tshirts'] = 'nullable|integer|min:0';
                $validationRules['for_storing_helmets'] = 'nullable|integer|min:0';
                $validationRules['for_storing_tshirts'] = 'nullable|integer|min:0';
                $validationRules['for_use_stock'] = 'nullable|integer|min:0';
                $validationRules['for_storing'] = 'nullable|integer|min:0';
                $validationRules['helmet_quantity'] = 'nullable|integer|min:0';
                $validationRules['tshirt_quantity'] = 'nullable|integer|min:0';
            } else {
                // For ABM storage deduction, equipment quantities must be provided
                $validationRules['helmet_quantity'] = 'nullable|integer|min:0';
                $validationRules['tshirt_quantity'] = 'nullable|integer|min:0';
            }

            $this->validate($validationRules);

            // Check if at least one equipment quantity is specified
            $helmet = $this->helmet_quantity ?: 0;
            $tshirt = $this->tshirt_quantity ?: 0;

            if ($helmet <= 0 && $tshirt <= 0) {
                Flux::toast('Invalid Quantity', 'Please enter at least one quantity for Safety Helmets or T-shirts.');
                return;
            }

            // For total_stocks deduction, ensure at least one usage type is specified
            if ($this->deduction_source === 'total_stocks') {
                $useHelmets = $this->for_use_helmets ?: 0;
                $useTshirts = $this->for_use_tshirts ?: 0;
                $storeHelmets = $this->for_storing_helmets ?: 0;
                $storeTshirts = $this->for_storing_tshirts ?: 0;

                if ($useHelmets <= 0 && $useTshirts <= 0 && $storeHelmets <= 0 && $storeTshirts <= 0) {
                    Flux::toast('Invalid Usage', 'Please specify quantities for "For Contractor Use" and/or "For ABM Storing" equipment.');
                    return;
                }
            }

            // Check stock availability based on deduction source
            if ($this->deduction_source === 'total_stocks') {
                // Check main inventory stock
                $stockCheck = StockManagement::checkStockAvailability($helmet, $tshirt);

                if (!$stockCheck['all_available']) {
                    $errors = [];
                    if (!$stockCheck['helmet_available'] && $helmet > 0) {
                        $errors[] = "Only {$stockCheck['helmet_stock']} safety helmets available in main inventory (requested: {$helmet})";
                    }
                    if (!$stockCheck['tshirt_available'] && $tshirt > 0) {
                        $errors[] = "Only {$stockCheck['tshirt_stock']} t-shirts available in main inventory (requested: {$tshirt})";
                    }
                    Flux::toast('Insufficient Stock', implode('. ', $errors) . '.', 5000, 'warning');
                    return;
                }
            } else {
                // Check ABM centre storage
                $abmStorage = ABMStorage::getOrCreateForRegion($this->region);
                $stockCheck = $abmStorage->checkAvailability($helmet, $tshirt);

                if (!$stockCheck['all_available']) {
                    $errors = [];
                    if (!$stockCheck['helmet_available'] && $helmet > 0) {
                        $errors[] = "Only {$stockCheck['helmet_stock']} safety helmets available in {$abmStorage->region_name} storage (requested: {$helmet})";
                    }
                    if (!$stockCheck['tshirt_available'] && $tshirt > 0) {
                        $errors[] = "Only {$stockCheck['tshirt_stock']} t-shirts available in {$abmStorage->region_name} storage (requested: {$tshirt})";
                    }
                    Flux::toast('Insufficient Stock', implode('. ', $errors) . '.', 5000, 'warning');
                    return;
                }
            }

            // Create the inventory record with new fields
            StockInventory::create([
                'staff_name' => $this->staff_name,
                'region' => $this->region,
                'warehouse' => $this->warehouse,
                'item_type' => 'safety_equipment', // Updated to reflect separated tracking
                'for_use_stock' => $this->deduction_source === 'total_stocks' ? ($this->for_use_stock ?: 0) : 0,
                'for_storing' => $this->deduction_source === 'total_stocks' ? ($this->for_storing ?: 0) : ($helmet + $tshirt),
                'quantity' => $this->quantity,
                'helmet_quantity' => $helmet,
                'tshirt_quantity' => $tshirt,
                'deduction_source' => $this->deduction_source,
                // Add separated usage fields
                'for_use_helmets' => $this->deduction_source === 'total_stocks' ? ($this->for_use_helmets ?: 0) : 0,
                'for_use_tshirts' => $this->deduction_source === 'total_stocks' ? ($this->for_use_tshirts ?: 0) : 0,
                'for_storing_helmets' => $this->deduction_source === 'total_stocks' ? ($this->for_storing_helmets ?: 0) : $helmet,
                'for_storing_tshirts' => $this->deduction_source === 'total_stocks' ? ($this->for_storing_tshirts ?: 0) : $tshirt,
                'remarks' => $this->remarks,
                'distribution_date' => $this->distribution_date,
            ]);

            // Update stock levels based on deduction source
            if ($this->deduction_source === 'total_stocks') {
                // Deduct from main inventory
                StockManagement::deductStock($helmet, $tshirt);

                // If there are quantities for storing, add them to ABM centre storage
                $storeHelmets = $this->for_storing_helmets ?: 0;
                $storeTshirts = $this->for_storing_tshirts ?: 0;

                if ($storeHelmets > 0 || $storeTshirts > 0) {
                    $abmStorage = ABMStorage::getOrCreateForRegion($this->region);
                    $abmStorage->addStock($storeHelmets, $storeTshirts, auth()->user()->name ?? 'Staff Distribution');
                }
            } else {
                // Deduct from ABM centre storage
                $abmStorage = ABMStorage::getOrCreateForRegion($this->region);
                $abmStorage->deductStock($helmet, $tshirt, auth()->user()->name ?? 'System');
            }

            // Reset form first to avoid issues with toast rendering
            $helmetQty = $helmet;
            $tshirtQty = $tshirt;
            $warehouse = $this->warehouse;

            $this->reset(['staff_name', 'region', 'warehouse', 'for_use_stock', 'for_storing', 'quantity', 'remarks', 'helmet_quantity', 'tshirt_quantity', 'deduction_source', 'for_use_helmets', 'for_use_tshirts', 'for_storing_helmets', 'for_storing_tshirts']);
            $this->distribution_date = now()->format('Y-m-d');

            // Show success message after reset
            $message = "Successfully distributed ";
            if ($helmetQty > 0) $message .= "{$helmetQty} safety helmets";
            if ($helmetQty > 0 && $tshirtQty > 0) $message .= " and ";
            if ($tshirtQty > 0) $message .= "{$tshirtQty} t-shirts";
            $message .= " to {$warehouse}.";

            Flux::toast($message, "Distribution Recorded", 5000, 'success');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get the first validation error message
            $errors = $e->validator->errors();
            $firstError = $errors->first();

            Flux::toast('Validation Error', $firstError ?: 'Please check all required fields and try again.', 5000, 'warning');

            // Log validation errors for debugging
            \Log::warning('Validation failed', [
                'errors' => $errors->toArray(),
                'form_data' => [
                    'staff_name' => $this->staff_name,
                    'region' => $this->region,
                    'warehouse' => $this->warehouse,
                    'deduction_source' => $this->deduction_source,
                    'helmet_quantity' => $this->helmet_quantity,
                    'tshirt_quantity' => $this->tshirt_quantity,
                ]
            ]);
        } catch (\Exception $e) {
            // Show actual error in development, generic message in production
            $errorMessage = config('app.debug')
                ? $e->getMessage()
                : 'An unexpected error occurred while saving the record. Please try again.';

            Flux::toast('Error', $errorMessage, 8000, 'danger');

            // Log the error for debugging
            \Log::error('StockInventoryForm submission error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_data' => [
                    'staff_name' => $this->staff_name,
                    'region' => $this->region,
                    'warehouse' => $this->warehouse,
                    'deduction_source' => $this->deduction_source,
                    'helmet_quantity' => $this->helmet_quantity,
                    'tshirt_quantity' => $this->tshirt_quantity,
                ]
            ]);
        }
    }

    public function render()
    {
        return view('livewire.stock-inventory-form');
    }
}
