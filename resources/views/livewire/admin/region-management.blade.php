<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">      
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">ABM Centre Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage ABM centres and their configuration</p>            
    </div>
    <flux:card class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="lg">ABM Centre Management</flux:heading>
                <flux:subheading>Manage ABM centres and their configuration</flux:subheading>
            </div>
            <flux:button wire:click="create" size="sm" variant="primary" icon="plus">Add ABM Centre</flux:button>
        </div>

        <!-- Regions Table -->
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column align="center">Assessment Locations</flux:table.column>
                    <flux:table.column align="center">Status</flux:table.column>
                    <flux:table.column align="center">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($regions as $region)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-medium">{{ $region->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc">{{ $region->code }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                {{ $region->warehouses_count }} locations
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($region->is_active)
                                    <flux:badge size="sm" color="green">Active</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red">Inactive</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="flex justify-center space-x-0">
                                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $region->id }})" icon="pencil">Edit</flux:button>
                                    <flux:button size="sm" variant="subtle" wire:click="delete({{ $region->id }})" icon="trash" wire:confirm="Are you sure you want to delete this ABM centre?">Delete</flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon.map class="w-8 h-8 mx-auto mb-1 opacity-50" />
                                    <p class="font-medium">No ABM centres found</p>
                                    <p class="text-sm">Create your first ABM centre to get started.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <!-- Modal -->
    <flux:modal wire:model="showModal" name="region-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingRegion ? 'Edit' : 'Create' }} ABM Centre</flux:heading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., East Coast" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Code</flux:label>
                    <flux:input wire:model="code" placeholder="e.g., east" />
                    <flux:error name="code" />
                </flux:field>  
                
                <flux:field variant="inline">
                    <flux:checkbox wire:model="is_active" checked/>
                    <flux:label>Active</flux:label>
                    <flux:error name="is_active" />
                </flux:field>  
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="subtle" wire:click="closeModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingRegion ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>