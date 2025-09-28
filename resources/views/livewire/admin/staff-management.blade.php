<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Staff Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage staff members and their status</p>
    </div>
    <flux:card class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="lg">Staff Management</flux:heading>
                <flux:subheading>Manage staff members and their status</flux:subheading>
            </div>
            <flux:button wire:click="create" size="sm" variant="primary" icon="plus">Add Staff</flux:button>
        </div>

        <!-- Staff Table -->
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column align="center">Status</flux:table.column>
                    <flux:table.column align="center">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($staff as $staffMember)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-medium">{{ $staffMember->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($staffMember->is_active)
                                    <flux:badge size="sm" color="green">Active</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red">Inactive</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="flex justify-center space-x-0">
                                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $staffMember->id }})" icon="pencil">Edit</flux:button>
                                    <flux:button size="sm" variant="subtle" wire:click="delete({{ $staffMember->id }})" icon="trash" wire:confirm="Are you sure you want to delete this staff member?">Delete</flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="text-center py-8">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon.users class="w-8 h-8 mx-auto mb-1 opacity-50" />
                                    <p class="font-medium">No staff members found</p>
                                    <p class="text-sm">Create your first staff member to get started.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <!-- Modal -->
    <flux:modal wire:model="showModal" name="staff-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingStaff ? 'Edit' : 'Create' }} Staff</flux:heading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., John Smith" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field variant="inline">
                    <flux:checkbox wire:model="is_active" checked/>
                    <flux:label>Active</flux:label>
                    <flux:error name="is_active" />
                </flux:field>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="subtle" wire:click="closeModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingStaff ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
