<?php

namespace App\Livewire\Admin;

use App\Models\Staff;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class StaffManagement extends Component
{
    use WithPagination;
    public $name = '';
    public $is_active = true;
    public $editingStaff = null;
    public $showModal = false;

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(Staff $staff)
    {
        $this->editingStaff = $staff;
        $this->name = $staff->name;
        $this->is_active = $staff->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($this->editingStaff) {
            $this->editingStaff->update([
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Staff updated successfully!');
        } else {
            Staff::create([
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            Flux::toast('Staff created successfully!');
        }

        $this->closeModal();
    }

    public function delete(Staff $staff)
    {
        $staff->delete();
        Flux::toast('Staff deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingStaff = null;
        $this->name = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.staff-management', [
            'staff' => Staff::orderBy('name')->paginate(10)
        ]);
    }
}
