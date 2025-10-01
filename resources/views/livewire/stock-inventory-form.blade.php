<div class="max-w-4xl mx-auto p-6">
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Safety Equipment Distribution Form</flux:heading>
            <flux:subheading>Record distribution of safety helmets and t-shirts with flexible stock deduction options</flux:subheading>
        </div>

        @if ($errors->any())
            <flux:callout variant="danger" icon="exclamation-triangle">
                <flux:callout.heading>Validation Errors</flux:callout.heading>
                <flux:callout.text>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </flux:callout.text>
            </flux:callout>
        @endif

        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Staff Name -->
                <flux:field>
                    <flux:label>Staff Name <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model="staff_name" variant="listbox" searchable placeholder="Select your name" required>
                        @foreach($this->staffNames as $name)
                            <flux:select.option value="{{ $name }}">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="staff_name" />
                </flux:field>

                <!-- Distribution Date -->
                <flux:field>
                    <flux:label>Distribution Date <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:date-picker wire:model="distribution_date" required />
                    <flux:error name="distribution_date" />
                </flux:field>

                <!-- ABM Centre -->
                <flux:field>
                    <flux:label>ABM Centre <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model.live="region" variant="listbox" placeholder="Select ABM centre" required>
                        @foreach($this->regions as $region)
                            <flux:select.option value="{{ $region->code }}">{{ $region->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="region" />
                    
                </flux:field>

                <!-- Assessment Location -->
                <flux:field>
                    <flux:label>Assessment Location <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model="warehouse" variant="listbox" searchable placeholder="Select assessment location" :disabled="!$region" required>
                        @foreach($this->availableWarehouses as $warehouse)
                            <flux:select.option value="{{ $warehouse->name }}">{{ $warehouse->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse" />
                    @if(!$region)
                        <flux:description>Please select an ABM centre first</flux:description>                
                    @endif
                </flux:field>

                <!-- Stock Deduction Source -->
                <flux:field>
                    <flux:label>Stock Deduction Source <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model.live="deduction_source" variant="listbox" placeholder="Select deduction source" required>
                        <flux:select.option value="total_stocks">Deduct from Total Stocks</flux:select.option>
                        <flux:select.option value="abm_storage">Deduct from ABM Centre Storage</flux:select.option>
                    </flux:select>
                    <flux:error name="deduction_source" />
                    <flux:description>Choose whether to deduct from total inventory or ABM centre storage</flux:description>
                </flux:field>
            </div>

            <!-- Safety Equipment Quantities (Only for ABM Storage deduction) -->
            @if($deduction_source === 'abm_storage')
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-4">
                    <flux:heading size="sm" class="text-gray-700 dark:text-gray-300">Safety Equipment Quantities</flux:heading>
                    <flux:description class="text-red-600 dark:text-red-400">* At least one equipment type quantity is required</flux:description>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Safety Helmet Quantity -->
                        <flux:field>
                            <flux:label>Safety Helmet Quantity</flux:label>
                            <flux:input type="number" wire:model.live="helmet_quantity" placeholder="Enter helmet quantity" min="0" />
                            <flux:error name="helmet_quantity" />
                        </flux:field>

                        <!-- T-shirt Quantity -->
                        <flux:field>
                            <flux:label>T-shirt Quantity</flux:label>
                            <flux:input type="number" wire:model.live="tshirt_quantity" placeholder="Enter t-shirt quantity" min="0" />
                            <flux:error name="tshirt_quantity" />
                        </flux:field>
                    </div>
                </div>
            @endif

            <!-- Usage Type (Conditional) -->
            @if($deduction_source === 'total_stocks')
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 space-y-6">
                    <flux:heading size="sm" class="text-blue-700 dark:text-blue-300">Usage Distribution</flux:heading>

                    <!-- For Contractor Use Section -->
                    <div class="space-y-4">
                        <flux:heading size="xs" class="text-blue-600 dark:text-blue-400">For Contractor Use</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Safety Helmets</flux:label>
                                <flux:input type="number" wire:model.live="for_use_helmets" placeholder="0" min="0" />
                                <flux:error name="for_use_helmets" />
                            </flux:field>
                            <flux:field>
                                <flux:label>T-shirts</flux:label>
                                <flux:input type="number" wire:model.live="for_use_tshirts" placeholder="0" min="0" />
                                <flux:error name="for_use_tshirts" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- For Storing Section -->
                    <div class="space-y-4 border-t border-blue-200 dark:border-blue-700 pt-4">
                        <flux:heading size="xs" class="text-blue-600 dark:text-blue-400">For Storing</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Safety Helmets</flux:label>
                                <flux:input type="number" wire:model.live="for_storing_helmets" placeholder="0" min="0" />
                                <flux:error name="for_storing_helmets" />
                            </flux:field>
                            <flux:field>
                                <flux:label>T-shirts</flux:label>
                                <flux:input type="number" wire:model.live="for_storing_tshirts" placeholder="0" min="0" />
                                <flux:error name="for_storing_tshirts" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- Usage Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-2">Usage Summary:</div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">For Use:</span>
                                {{ ($for_use_helmets ?: 0) + ($for_use_tshirts ?: 0) }} items
                                @if(($for_use_helmets ?: 0) > 0 || ($for_use_tshirts ?: 0) > 0)
                                    <div class="text-xs text-gray-500">
                                        H:{{ $for_use_helmets ?: 0 }} | T:{{ $for_use_tshirts ?: 0 }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium">For Storing:</span>
                                {{ ($for_storing_helmets ?: 0) + ($for_storing_tshirts ?: 0) }} items
                                @if(($for_storing_helmets ?: 0) > 0 || ($for_storing_tshirts ?: 0) > 0)
                                    <div class="text-xs text-gray-500">
                                        H:{{ $for_storing_helmets ?: 0 }} | T:{{ $for_storing_tshirts ?: 0 }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>

            <!-- Summary Section -->
            <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-4 space-y-4 my-4">
                <flux:heading size="sm" class="text-green-700 dark:text-green-300">Distribution Summary</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Helmets -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $helmet_quantity ?? 0 }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Safety Helmets</div>
                    </div>

                    <!-- Total T-shirts -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $tshirt_quantity ?? 0 }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">T-shirts</div>
                    </div>

                    <!-- Deduction Source -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @if($deduction_source === 'total_stocks')
                                From Total Stocks
                            @elseif($deduction_source === 'abm_storage')
                                From ABM Storage
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Deduction Source</div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <flux:field>
                <flux:label>Remarks (Optional)</flux:label>
                <flux:textarea wire:model="remarks" placeholder="Add any additional notes or remarks" rows="3" />
                <flux:error name="remarks" />
            </flux:field>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 mt-3">
                <flux:button
                    type="button"
                    variant="subtle"
                    wire:click="resetForm"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="resetForm">Reset</span>
                    <span wire:loading wire:target="resetForm">Resetting...</span>
                </flux:button>
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="submit">Save Distribution Record</span>
                    <span wire:loading wire:target="submit">Saving...</span>
                </flux:button>
            </div>
        </form>
    </flux:card>

</div>
