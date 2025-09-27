<div class="max-w-6xl mx-auto p-6">
    <!-- Stock Overview -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ number_format($stock->total_stock) }}</flux:heading>
                <flux:subheading>Total Stock</flux:subheading>
                <flux:description>Total helmet & shirt sets in inventory</flux:description>
            </div>
            <div class="text-center">
                <flux:heading size="lg" class="text-orange-600 dark:text-orange-400">{{ number_format($stock->allocated_stock) }}</flux:heading>
                <flux:subheading>Allocated Stock</flux:subheading>
                <flux:description>Sets distributed to staff</flux:description>
            </div>
            <div class="text-center">
                <flux:heading size="lg" class="text-green-600 dark:text-green-400">{{ number_format($stock->available_stock) }}</flux:heading>
                <flux:subheading>Available Stock</flux:subheading>
                <flux:description>Sets available for distribution</flux:description>
            </div>
        </div>
    </flux:card>

    <!-- Stock Management Actions -->
    <flux:card class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="lg">Stock Management</flux:heading>
                <flux:subheading>Manage helmet & shirt set inventory levels</flux:subheading>
            </div>
            <div class="flex space-x-3">
                <flux:button wire:click="addStock" variant="primary" icon="plus">Add Stock</flux:button>
                <flux:button wire:click="deductStock" variant="outline" icon="minus">Deduct Stock</flux:button>
            </div>
        </div>

        <!-- Stock Information -->
        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Item Type:</span>
                    <span class="ml-2">Safety Helmet + Shirt Set</span>
                </div>
                <div>
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Last Updated:</span>
                    <span class="ml-2">{{ $stock->updated_at->format('d M Y, H:i') }}</span>
                </div>
                @if($stock->notes)
                <div class="md:col-span-2">
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Notes:</span>
                    <span class="ml-2">{{ $stock->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Stock Level Warnings -->
        @if($stock->available_stock <= 50)
            <flux:badge size="lg" color="red" class="w-full justify-center">
                <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                Low Stock Warning: Only {{ $stock->available_stock }} sets available!
            </flux:badge>
        @elseif($stock->available_stock <= 100)
            <flux:badge size="lg" color="yellow" class="w-full justify-center">
                <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                Stock Running Low: {{ $stock->available_stock }} sets remaining
            </flux:badge>
        @endif
    </flux:card>

    <!-- Stock History -->
    <flux:card class="mt-6">
        <div class="mb-6">
            <flux:heading size="lg">Stock History</flux:heading>
            <flux:subheading>Recent stock adjustments by administrators</flux:subheading>
        </div>

        @if(count($stockHistory) > 0)
            <div class="space-y-3">
                @foreach($stockHistory as $history)
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <flux:badge
                                        size="sm"
                                        color="{{ $history->action_type === 'add' ? 'green' : 'red' }}"
                                    >
                                        {{ $history->action_display }}
                                    </flux:badge>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $history->quantity_display }} sets
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        by {{ $history->admin_name }}
                                    </span>
                                </div>

                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Stock: {{ number_format($history->total_stock_before) }} → {{ number_format($history->total_stock_after) }}
                                </div>

                                @if($history->notes)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $history->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                <div>{{ $history->created_at->format('M d, Y') }}</div>
                                <div>{{ $history->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <flux:icon.clock class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No history available</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Stock adjustments will appear here once recorded.</p>
            </div>
        @endif
    </flux:card>

    <!-- Modal -->
    <flux:modal wire:model="showModal" name="stock-modal">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $action_type === 'add' ? 'Add Stock' : 'Deduct Stock' }}
                </flux:heading>
                <flux:subheading>
                    {{ $action_type === 'add' ? 'Increase inventory levels' : 'Manually reduce inventory levels' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Quantity</flux:label>
                    <flux:input type="number" wire:model="quantity" placeholder="Enter quantity" min="1" />
                    <flux:error name="quantity" />
                    <flux:description>
                        {{ $action_type === 'add' ? 'Number of helmet & shirt sets to add' : 'Number of helmet & shirt sets to deduct' }}
                    </flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Notes (Optional)</flux:label>
                    <flux:textarea wire:model="notes" placeholder="Add notes about this stock adjustment" rows="3" />
                    <flux:error name="notes" />
                </flux:field>

                @if($action_type === 'deduct')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                        <div class="flex">
                            <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5" />
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Available Stock:</strong> {{ number_format($stock->available_stock) }} sets
                                <br>
                                <span class="text-xs">Cannot deduct more than available stock.</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="subtle" wire:click="closeModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $action_type === 'add' ? 'Add Stock' : 'Deduct Stock' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
