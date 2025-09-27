<?php

namespace App\Livewire\Admin;

use App\Models\Region;
use App\Models\Warehouse;
use Livewire\Component;
use Flux\Flux;

class WarehouseManagement extends Component
{
    public $name = '';
    public $region_id = '';
    public $is_active = true;
    public $editingWarehouse = null;
    public $showModal = false;

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(Warehouse $warehouse)
    {
        $this->editingWarehouse = $warehouse;
        $this->name = $warehouse->name;
        $this->region_id = $warehouse->region_id;
        $this->is_active = $warehouse->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'is_active' => 'boolean',
        ]);

        if ($this->editingWarehouse) {
            $this->editingWarehouse->update([
                'name' => $this->name,
                'region_id' => $this->region_id,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Assessment location updated successfully!');
        } else {
            Warehouse::create([
                'name' => $this->name,
                'region_id' => $this->region_id,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Assessment location created successfully!');
        }

        $this->closeModal();
    }

    public function delete(Warehouse $warehouse)
    {
        $warehouse->delete();
        Flux::toast('Assessment location deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingWarehouse = null;
        $this->name = '';
        $this->region_id = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.warehouse-management', [
            'warehouses' => Warehouse::with('region')->get(),
            'regions' => Region::where('is_active', true)->get()
        ]);
    }
}
