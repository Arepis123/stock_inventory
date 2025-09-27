<div class="max-w-6xl mx-auto p-6">
    <flux:card class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="lg">Staff Management</flux:heading>
                <flux:subheading>Manage staff members and their status</flux:subheading>
            </div>
            <flux:button wire:click="create" variant="primary" icon="plus">Add Staff</flux:button>
        </div>

        <!-- Staff Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">Name</th>
                        <th class="text-left py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                        <th class="text-left py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $staffMember)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td class="py-3 px-4">{{ $staffMember->name }}</td>
                            <td class="py-3 px-4">
                                @if($staffMember->is_active)
                                    <flux:badge size="sm" color="green">Active</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red">Inactive</flux:badge>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $staffMember->id }})" icon="pencil">Edit</flux:button>
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $staffMember->id }})" icon="trash" wire:confirm="Are you sure you want to delete this staff member?">Delete</flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </flux:card>

    <!-- Modal -->
    <flux:modal wire:model="showModal" name="staff-modal">
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

                <flux:field>
                    <flux:checkbox wire:model="is_active">Active</flux:checkbox>
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
