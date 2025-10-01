<?php

namespace App\Livewire\Admin;

use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class RegionManagement extends Component
{
    use WithPagination;
    public $name = '';
    public $code = '';
    public $is_active = true;
    public $editingRegion = null;
    public $showModal = false;

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(Region $region)
    {
        $this->editingRegion = $region;
        $this->name = $region->name;
        $this->code = $region->code;
        $this->is_active = $region->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:regions,code,' . ($this->editingRegion?->id ?? 'NULL'),
            'is_active' => 'boolean',
        ]);

        if ($this->editingRegion) {
            $this->editingRegion->update([
                'name' => $this->name,
                'code' => $this->code,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Region updated successfully!');
        } else {
            Region::create([
                'name' => $this->name,
                'code' => $this->code,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Region created successfully!');
        }

        $this->closeModal();
    }

    public function delete(Region $region)
    {
        $region->delete();
        Flux::toast('Region deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingRegion = null;
        $this->name = '';
        $this->code = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.region-management', [
            'regions' => Region::withCount('warehouses')->paginate(10)
        ]);
    }
}
