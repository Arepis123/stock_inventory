<div class="max-w-4xl mx-auto p-3 sm:p-6">
    @if($scanned)
        <div x-data="{ visible: true }" x-show="visible" x-collapse class="mb-6">
            <div x-show="visible" x-transition>
                <flux:callout icon="check-badge" variant="success">
                    <flux:callout.heading >QR Code Scanned Successfully</flux:callout.heading>
                    <flux:callout.text>You can now proceed with the stock distribution.</flux:callout.text>
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>
    @endif

    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Stock Distribution Form</flux:heading>
            <flux:subheading>Record the distribution of helmet & shirt sets to staff members</flux:subheading>
        </div>

        <form wire:submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Staff Name -->
                <flux:field>
                    <flux:label>Staff Name</flux:label>
                    <flux:select wire:model.live="staff_name" variant="listbox" searchable placeholder="Select staff member">
                        @foreach($this->staffNames as $name)
                            <flux:select.option value="{{ $name }}">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="staff_name" />
                </flux:field>

                <!-- Distribution Date -->
                <flux:field>
                    <flux:label>Distribution Date</flux:label>
                    <flux:date-picker wire:model="distribution_date" />
                    <flux:error name="distribution_date" />
                </flux:field>

                <!-- ABM Centre -->
                <flux:field>
                    <flux:label>ABM Centre</flux:label>
                    <flux:select wire:model.live="region" variant="listbox" placeholder="Select ABM centre">
                        @foreach($this->regions as $regionObj)
                            @if($regionObj && $regionObj->name && $regionObj->code)
                                <flux:select.option value="{{ $regionObj->code }}">{{ $regionObj->name }}</flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>
                    <flux:error name="region" />
                </flux:field>

                <!-- Assessment Location -->
                <flux:field>
                    <flux:label>Assessment Location</flux:label>
                    <flux:select wire:model="warehouse" variant="listbox" searchable placeholder="Select assessment location" :disabled="!$region">
                        @foreach($this->availableWarehouses as $warehouseObj)
                            @if($warehouseObj && $warehouseObj->name)
                                <flux:select.option value="{{ $warehouseObj->name }}">{{ $warehouseObj->name }}</flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse" />
                    @if(!$region)
                        <flux:description>Please select an ABM centre first</flux:description>
                    @endif
                </flux:field>


                <!-- For Contractor Use -->
                <flux:field>
                    <flux:label>For Contractor Use</flux:label>
                    <flux:input type="number" wire:model.live="for_use_stock" placeholder="Enter quantity for use" min="0" />
                    <flux:error name="for_use_stock" />
                    <flux:description>Quantity for immediate use</flux:description>
                </flux:field>

                <!-- For Storing -->
                <flux:field>
                    <flux:label>For Storing</flux:label>
                    <flux:input type="number" wire:model.live="for_storing" placeholder="Enter quantity for storing" min="0" />
                    <flux:error name="for_storing" />
                    <flux:description>Quantity for storage</flux:description>
                </flux:field>
            </div>

            <!-- Total Quantity (readonly) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Total Quantity (Safety Helmet + Shirt Set)</flux:label>
                    <flux:input type="number" wire:model="quantity" readonly class="bg-gray-50 dark:bg-gray-700" />
                    <flux:description>Auto-calculated total (For Use + For Storing)</flux:description>
                </flux:field>
            </div>

            <!-- Remarks -->
            <flux:field>
                <flux:label>Remarks (Optional)</flux:label>
                <flux:textarea wire:model="remarks" placeholder="Add any additional notes or remarks" rows="3" />
                <flux:error name="remarks" />
            </flux:field>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="subtle" wire:click="$refresh">Reset</flux:button>
                <flux:button type="submit" variant="primary">Save Distribution Record</flux:button>
            </div>
        </form>
    </flux:card>

</div>