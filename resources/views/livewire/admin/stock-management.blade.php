<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Stock Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage safety equipment inventory and ABM centre storage</p>
    </div>

    <!-- Main Inventory Overview -->
    <flux:card class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <flux:heading size="lg">Main Inventory</flux:heading>
                <flux:subheading>Central warehouse stock levels</flux:subheading>
            </div>
            <div>
                <flux:dropdown>
                    <flux:button icon="ellipsis-horizontal" size="sm"/>
                    <flux:menu>
                        <flux:menu.item wire:click="openMainStockModal('add')" icon="plus">Add Main Stock</flux:menu.item>
                        <flux:menu.item wire:click="openMainStockModal('deduct')" icon="minus">Deduct Main Stock</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item wire:click="openHistoryModal" icon="clock">View Past History</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>                   
            </div>
        </div>

        <div class="flex justify-center items-center space-x-6">
            <!-- Safety Helmets -->
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg min-w-auto sm:min-w-2xs">
                <flux:heading size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($helmetStock->total_stock) }}</flux:heading>
                <flux:subheading>Safety Helmets</flux:subheading>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <div>Available: {{ number_format($helmetStock->available_stock) }}</div>
                    <div>Allocated: {{ number_format($helmetStock->allocated_stock) }}</div>
                </div>
            </div>

            <!-- T-shirts -->
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg min-w-auto sm:min-w-2xs">
                <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ number_format($tshirtStock->total_stock) }}</flux:heading>
                <flux:subheading>T-shirts</flux:subheading>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <div>Available: {{ number_format($tshirtStock->available_stock) }}</div>
                    <div>Allocated: {{ number_format($tshirtStock->allocated_stock) }}</div>
                </div>
            </div>
        </div>

        <!-- Stock Level Warnings -->
        @if($helmetStock->available_stock <= 50 || $tshirtStock->available_stock <= 50)
            <div class="mt-4 space-y-2">
                @if($helmetStock->available_stock <= 50)
                    <flux:badge size="lg" color="red" class="w-full justify-center">
                        <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                        Low Safety Helmet Stock: Only {{ $helmetStock->available_stock }} available!
                    </flux:badge>
                @endif
                @if($tshirtStock->available_stock <= 50)
                    <flux:badge size="lg" color="red" class="w-full justify-center">
                        <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                        Low T-shirt Stock: Only {{ $tshirtStock->available_stock }} available!
                    </flux:badge>
                @endif
            </div>
        @endif
    </flux:card>

    <!-- ABM Centre Storage Overview -->
    <flux:card class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <flux:heading size="lg">ABM Centre Storage</flux:heading>
                <flux:subheading>Individual ABM centre inventory levels</flux:subheading>
            </div>
            <flux:button wire:click="refreshAbmStorage" variant="ghost" icon="arrow-path" size="sm">Refresh</flux:button>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ABM Centre</flux:table.column>
                    <flux:table.column align="center">Safety Helmets</flux:table.column>
                    <flux:table.column align="center">T-shirts</flux:table.column>
                    <flux:table.column align="center">Last Updated</flux:table.column>
                    <flux:table.column align="center">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($abmStorages as $storage)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-medium">{{ $storage->region_name }}</div>
                                <div class="text-sm text-gray-500">{{ ucfirst($storage->region_code) }}</div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="text-lg font-semibold {{ $storage->helmet_stock <= 10 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ number_format($storage->helmet_stock) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="text-lg font-semibold {{ $storage->tshirt_stock <= 10 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ number_format($storage->tshirt_stock) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="text-sm text-gray-500">
                                    {{ $storage->last_updated_at ? $storage->last_updated_at->format('M d, Y') : 'Never' }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:button wire:click="viewAbmDetails('{{ $storage->region_code }}')" variant="subtle" icon="eye" size="xs">View Details</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon.building-office class="w-8 h-8 mx-auto mb-1 opacity-50" />
                                    <p class="font-medium">No ABM centre storage found</p>
                                    <p class="text-sm">ABM storage will be created automatically when needed.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <!-- Main Stock Management Modal -->
    <flux:modal wire:model="showMainStockModal" name="main-stock-modal" class="md:w-96">
        <form wire:submit="saveMainStock" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $action_type === 'add' ? 'Add Main Stock' : 'Deduct Main Stock' }}
                </flux:heading>
                <flux:subheading>
                    {{ $action_type === 'add' ? 'Increase main inventory levels' : 'Manually reduce main inventory levels' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Equipment Type</flux:label>
                    <flux:select wire:model="equipment_type" variant="listbox" placeholder="Select equipment type" required>
                        <flux:select.option value="safety_helmet">Safety Helmet</flux:select.option>
                        <flux:select.option value="tshirt">T-shirt</flux:select.option>
                    </flux:select>
                    <flux:error name="equipment_type" />
                </flux:field>

                <flux:field>
                    <flux:label>Quantity</flux:label>
                    <flux:input type="number" wire:model="quantity" placeholder="Enter quantity" min="1" required />
                    <flux:error name="quantity" />
                </flux:field>

                <flux:field>
                    <flux:label>Notes (Optional)</flux:label>
                    <flux:textarea wire:model="notes" placeholder="Add notes about this stock adjustment" rows="3" />
                    <flux:error name="notes" />
                </flux:field>

                @if($action_type === 'deduct' && $equipment_type)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                        <div class="flex">
                            <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5" />
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Available:</strong>
                                @if($equipment_type === 'safety_helmet')
                                    {{ number_format($helmetStock->available_stock) }} helmets
                                @elseif($equipment_type === 'tshirt')
                                    {{ number_format($tshirtStock->available_stock) }} t-shirts
                                @endif
                                <br>
                                <span class="text-xs">Cannot deduct more than available stock.</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="subtle" wire:click="closeMainStockModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $action_type === 'add' ? 'Add Stock' : 'Deduct Stock' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Stock History Modal -->
    <flux:modal name="stock-history" wire:model="showHistoryModal" class="space-y-6 max-w-4xl">
        <div>
            <flux:heading size="lg">Stock Management History</flux:heading>
            <flux:subheading>Recent additions and deductions to main inventory</flux:subheading>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @if($stockHistory && $stockHistory->count() > 0)
                <div class="space-y-3">
                    @foreach($stockHistory as $history)
                        <div class="border dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center space-x-2">
                                            @if($history->action_type === 'add')
                                                <flux:icon.plus class="w-3 h-3 text-green-600 dark:text-green-400" />
                                                <flux:heading class="text-green-600 dark:text-green-400" variant="strong">{{ $history->action_display }}</flux:heading>
                                            @else
                                                <flux:icon.minus class="w-3 h-3 text-red-600 dark:text-red-400" />
                                                <flux:heading class="text-red-600 dark:text-red-400" variant="strong">{{ $history->action_display }}</flux:heading>
                                            @endif
                                        </div>
                                        <div class="text-gray-900 dark:text-gray-100">                                            
                                            <flux:badge size="sm">{{ ucwords(str_replace('_', ' ', $history->equipment_type)) }}</flux:badge>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center space-x-4">
                                            <span><strong>Quantity:</strong> {{ $history->quantity_display }}</span>
                                            <span><strong>Before:</strong> {{ number_format($history->total_stock_before) }}</span>
                                            <span><strong>After:</strong> {{ number_format($history->total_stock_after) }}</span>
                                        </div>
                                        @if($history->notes)
                                            <div class="mt-1">
                                                <strong>Notes:</strong> {{ $history->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $history->created_at->format('M j, Y') }}</div>
                                    <div>{{ $history->created_at->format('g:i A') }}</div>
                                    <div class="text-xs mt-1">{{ $history->admin_name }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon.clock class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                    <flux:heading size="md" class="text-gray-500 dark:text-gray-400">No History Found</flux:heading>
                    <flux:subheading class="text-gray-400 dark:text-gray-500">Stock management history will appear here</flux:subheading>
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <flux:button type="button" variant="primary" size="sm" wire:click="closeHistoryModal">Close</flux:button>
        </div>
    </flux:modal>

    <!-- ABM Centre Details Modal -->
    <flux:modal name="abm-details" wire:model="showAbmDetailsModal" class="max-w-5xl">
        @if($selectedAbmStorage)
            <div class="space-y-6 overflow-y-auto max-h-[80vh] modal-content" style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    .modal-content::-webkit-scrollbar {
                        display: none;
                    }
                </style>
                <div>
                    <flux:heading size="lg">{{ $selectedAbmStorage->region_name }} ABM Centre</flux:heading>
                    <flux:subheading>Storage details and transaction history</flux:subheading>
                </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Current Helmets -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($selectedAbmStorage->helmet_stock) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Current Helmets</div>
                </div>

                <!-- Current T-shirts -->
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($selectedAbmStorage->tshirt_stock) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Current T-shirts</div>
                </div>

                <!-- Total Received -->
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($selectedAbmStorage->helmet_received + $selectedAbmStorage->tshirt_received) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Received</div>
                </div>

                <!-- Total Distributed -->
                <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($selectedAbmStorage->helmet_distributed + $selectedAbmStorage->tshirt_distributed) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Distributed</div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Helmets Breakdown -->
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <flux:heading size="sm" class="text-blue-600 dark:text-blue-400 mb-3">Safety Helmets</flux:heading>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Total Received:</span>
                            <span class="font-medium">{{ number_format($selectedAbmStorage->helmet_received) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Distributed:</span>
                            <span class="font-medium">{{ number_format($selectedAbmStorage->helmet_distributed) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="font-semibold">Current Stock:</span>
                            <span class="font-semibold {{ $selectedAbmStorage->helmet_stock <= 10 ? 'text-red-600' : '' }}">{{ number_format($selectedAbmStorage->helmet_stock) }}</span>
                        </div>
                    </div>
                </div>

                <!-- T-shirts Breakdown -->
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <flux:heading size="sm" class="text-green-600 dark:text-green-400 mb-3">T-shirts</flux:heading>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Total Received:</span>
                            <span class="font-medium">{{ number_format($selectedAbmStorage->tshirt_received) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Distributed:</span>
                            <span class="font-medium">{{ number_format($selectedAbmStorage->tshirt_distributed) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="font-semibold">Current Stock:</span>
                            <span class="font-semibold {{ $selectedAbmStorage->tshirt_stock <= 10 ? 'text-red-600' : '' }}">{{ number_format($selectedAbmStorage->tshirt_stock) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="md">Transaction History</flux:heading>
                    @if($abmTransactions && $abmTransactions->count() > 3)
                        <flux:button wire:click="toggleTransactionView" variant="filled" size="sm" class="inline">
                            {{ $showAllTransactions ? 'Show Less' : 'View More' }}
                            <flux:icon.chevron-down class="w-4 h-4 ml-1 inline {{ $showAllTransactions ? 'rotate-180' : '' }} transition-transform" />
                        </flux:button>
                    @endif
                </div>

                <div class="max-h-64 overflow-y-auto">
                    @if($abmTransactions && $abmTransactions->count() > 0)
                        <div class="space-y-3">
                            @foreach($showAllTransactions ? $abmTransactions : $abmTransactions->take(3) as $transaction)
                                <div class="border dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex items-center space-x-2">
                                                    @if($transaction->deduction_source === 'total_stocks')
                                                        <flux:icon.arrow-down class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                                                        <flux:heading class="text-blue-600 dark:text-blue-400" variant="strong">Stock Transfer</flux:heading>
                                                    @else
                                                        <flux:icon.arrow-up class="w-3 h-3 text-green-600 dark:text-green-400" />
                                                        <flux:heading class="text-green-600 dark:text-green-400" variant="strong">Distribution</flux:heading>
                                                    @endif
                                                </div>
                                                <div class="text-gray-900 dark:text-gray-100">
                                                    <flux:badge size="sm" color="{{ $transaction->deduction_source === 'total_stocks' ? 'blue' : 'green' }}">
                                                        {{ $transaction->deduction_source === 'total_stocks' ? 'From Main Stock' : 'From ABM Storage' }}
                                                    </flux:badge>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center space-x-4 mb-1">
                                                    <span><strong>Staff:</strong> {{ $transaction->staff_name }}</span>
                                                    <span><strong>Location:</strong> {{ $transaction->warehouse }}</span>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    @if($transaction->helmet_quantity > 0)
                                                        <span><strong>Helmets:</strong> {{ $transaction->helmet_quantity }}</span>
                                                    @endif
                                                    @if($transaction->tshirt_quantity > 0)
                                                        <span><strong>T-shirts:</strong> {{ $transaction->tshirt_quantity }}</span>
                                                    @endif
                                                </div>
                                                @if($transaction->deduction_source === 'total_stocks')
                                                    <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                        @if($transaction->for_storing_helmets > 0 || $transaction->for_storing_tshirts > 0)
                                                            <span>Added to storage: </span>
                                                            @if($transaction->for_storing_helmets > 0)<span class="bg-blue-100 dark:bg-blue-900 px-1 rounded">H:{{ $transaction->for_storing_helmets }}</span>@endif
                                                            @if($transaction->for_storing_tshirts > 0)<span class="bg-blue-100 dark:bg-blue-900 px-1 rounded">T:{{ $transaction->for_storing_tshirts }}</span>@endif
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($transaction->remarks)
                                                    <div class="mt-1">
                                                        <strong>Remarks:</strong> {{ $transaction->remarks }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $transaction->distribution_date ? \Carbon\Carbon::parse($transaction->distribution_date)->format('M j, Y') : $transaction->created_at->format('M j, Y') }}</div>
                                            <div>{{ $transaction->created_at->format('g:i A') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($abmTransactions->count() > 3 && !$showAllTransactions)
                            <div class="text-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Showing 3 of {{ $abmTransactions->count() }} transactions
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <flux:icon.archive-box class="w-8 h-8 text-gray-400 dark:text-gray-600 mx-auto mb-2" />
                            <flux:heading size="md" class="text-gray-500 dark:text-gray-400">No Transactions Found</flux:heading>
                            <flux:subheading class="text-gray-400 dark:text-gray-500">Transaction history will appear here</flux:subheading>
                        </div>
                    @endif
                </div>
            </div>

                <div class="flex justify-end pt-4">
                    <flux:button type="button" variant="primary" size="sm" wire:click="closeAbmDetailsModal">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

</div>