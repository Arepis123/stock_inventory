<flux:main class="space-y-6">
    @if($scanned)
        <flux:card class="border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20">
            <div class="flex items-center">
                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                <div>
                    <flux:heading size="sm" class="text-green-800 dark:text-green-200">QR Code Scanned Successfully</flux:heading>
                    <flux:subheading class="text-green-700 dark:text-green-300">You can now proceed with the stock distribution.</flux:subheading>
                </div>
            </div>
        </flux:card>
    @endif

    <flux:card class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700">
        <div class="mb-6">
            <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Stock Distribution Form</flux:heading>
            <flux:subheading class="text-gray-600 dark:text-gray-400">Record the distribution of helmet & shirt sets to staff members.</flux:subheading>
        </div>

        <form wire:submit="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Staff Name -->
                <flux:field>
                    <flux:label for="staff_name" class="text-gray-900 dark:text-gray-100">Staff Name *</flux:label>
                    <flux:select wire:model.live="staff_name" id="staff_name" placeholder="Select staff member..."
                             class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100">
                        @foreach($this->staffNames as $name)
                            <flux:select.option value="{{ $name }}">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="staff_name" />
                </flux:field>

                <!-- Region -->
                <flux:field>
                    <flux:label for="region" class="text-gray-900 dark:text-gray-100">Region *</flux:label>
                    <flux:select wire:model.live="region" id="region" placeholder="Select region..."
                             class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100">
                        @foreach($this->regions as $regionObj)
                            <flux:select.option value="{{ $regionObj->code }}">{{ $regionObj->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="region" />
                </flux:field>

                <!-- Warehouse -->
                <flux:field>
                    <flux:label for="warehouse" class="text-gray-900 dark:text-gray-100">Warehouse *</flux:label>
                    <flux:select wire:model="warehouse" id="warehouse" placeholder="Select warehouse..." :disabled="!$region"
                             class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 disabled:bg-gray-100 dark:disabled:bg-zinc-700">
                        @foreach($this->availableWarehouses as $warehouseObj)
                            <flux:select.option value="{{ $warehouseObj->name }}">{{ $warehouseObj->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse" />
                </flux:field>

                <!-- Distribution Date -->
                <flux:field>
                    <flux:label for="distribution_date" class="text-gray-900 dark:text-gray-100">Distribution Date *</flux:label>
                    <flux:input type="date" wire:model="distribution_date" id="distribution_date"
                            class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100" />
                    <flux:error name="distribution_date" />
                </flux:field>

                <!-- For Use Stock -->
                <flux:field>
                    <flux:label for="for_use_stock" class="text-gray-900 dark:text-gray-100">For Use Stock</flux:label>
                    <flux:input type="number" wire:model.live="for_use_stock" id="for_use_stock" min="0" placeholder="0"
                            class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" />
                    <flux:description class="text-gray-600 dark:text-gray-400">Number of sets for immediate use</flux:description>
                    <flux:error name="for_use_stock" />
                </flux:field>

                <!-- For Storing -->
                <flux:field>
                    <flux:label for="for_storing" class="text-gray-900 dark:text-gray-100">For Storing</flux:label>
                    <flux:input type="number" wire:model.live="for_storing" id="for_storing" min="0" placeholder="0"
                            class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" />
                    <flux:description class="text-gray-600 dark:text-gray-400">Number of sets for storage/backup</flux:description>
                    <flux:error name="for_storing" />
                </flux:field>
            </div>

            <!-- Total Quantity Display -->
            @if($quantity > 0)
                <flux:card class="bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
                    <div class="flex items-center">
                        <flux:icon.calculator class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" />
                        <flux:heading size="sm" class="text-blue-800 dark:text-blue-200">Total Quantity: {{ $quantity }} helmet & shirt sets</flux:heading>
                    </div>
                </flux:card>
            @endif

            <!-- Remarks -->
            <flux:field>
                <flux:label for="remarks" class="text-gray-900 dark:text-gray-100">Remarks</flux:label>
                <flux:textarea wire:model="remarks" id="remarks" rows="3" placeholder="Optional remarks or notes..."
                           class="bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"></flux:textarea>
                <flux:error name="remarks" />
            </flux:field>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                <flux:button type="button" variant="ghost" onclick="history.back()"
                         class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-zinc-800">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary"
                         class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white">
                    <flux:icon.plus class="w-4 h-4 mr-2" />
                    Record Distribution
                </flux:button>
            </div>
        </form>
    </flux:card>
</flux:main>