<flux:main class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Distribution Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage all stock distribution records</p>
            </div>

            <!-- Top Right Numbers - Hidden on mobile, visible on desktop -->
            <div class="hidden sm:flex gap-8">
                <div class="text-center">
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->totalDistributed) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Distributed</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->totalRecords) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Records</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Visible on mobile, hidden on desktop -->
        <div class="grid grid-cols-2 gap-4 mt-4 sm:hidden">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->totalDistributed) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Distributed</div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->totalRecords) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Records</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 mx-2">
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters & Export
                    </span>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="space-y-4 pt-4 mx-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                            <!-- Staff Name Filter -->
                            <flux:field>
                                <flux:label>Staff Name</flux:label>
                                <flux:select wire:model.live="selectedStaff" size="sm" variant="listbox" placeholder="All staff">
                                    @foreach($this->staffNames as $staffName)
                                        <flux:select.option value="{{ $staffName }}">{{ $staffName }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Region Filter -->
                            <flux:field>
                                <flux:label>ABM Centre</flux:label>
                                <flux:select wire:model.live="selectedRegion" size="sm" variant="listbox" placeholder="All centres">
                                    @foreach($this->regions as $region)
                                        <flux:select.option value="{{ $region->code }}">{{ $region->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Warehouse Filter -->
                            <flux:field>
                                <flux:label>Assessment Location</flux:label>
                                <flux:select wire:model.live="selectedWarehouse" size="sm" variant="listbox" placeholder="All locations">
                                    @foreach($this->warehouses as $warehouse)
                                        <flux:select.option value="{{ $warehouse }}">{{ $warehouse }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Date From -->
                            <flux:field>
                                <flux:label>From Date</flux:label>
                                <flux:date-picker wire:model.live="dateFrom" size="sm" with-today/>
                            </flux:field>

                            <!-- Date To -->
                            <flux:field>
                                <flux:label>To Date</flux:label>
                                <flux:date-picker wire:model.live="dateTo" size="sm" with-today/>
                            </flux:field>

                            <!-- Clear Filters -->
                            <flux:field>
                                <flux:label>&nbsp;</flux:label>
                                <flux:button wire:click="clearFilters" variant="primary" class="w-full" size="sm">
                                    Clear Filters
                                </flux:button>
                            </flux:field>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex gap-3 pt-2 border-t border-gray-200 dark:border-zinc-700">
                            <flux:button
                                variant="filled"
                                size="sm"
                                onclick="exportToExcel()"
                                icon="document-arrow-down"
                                class="bg-green-600 hover:bg-green-700"
                            >
                                Export Excel
                            </flux:button>
                            <flux:button
                                variant="filled"
                                size="sm"
                                onclick="exportToPdf()"
                                icon="document-text"
                                class="bg-red-600 hover:bg-red-700"
                            >
                                Export PDF
                            </flux:button>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Search -->
    <div class="mb-6 mx-2">
        <flux:field>
            <flux:label>Search Distribution Records</flux:label>
            <flux:input wire:model.live="search" placeholder="Search by staff name, warehouse, or remarks..." />
        </flux:field>
    </div>

    <!-- Distribution Records Table -->
    <flux:card>
        <flux:heading size="sm" class="mb-4">Distribution Records</flux:heading>

        <div class="overflow-x-auto">
            <flux:table :paginate="$this->distributions">
                <flux:table.columns>
                    <flux:table.column align="center">No</flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'distribution_date'"
                        :direction="$sortDirection"
                        wire:click="sort('distribution_date')"
                    >Date</flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'staff_name'"
                        :direction="$sortDirection"
                        wire:click="sort('staff_name')"
                    >Staff Name</flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'region'"
                        :direction="$sortDirection"
                        wire:click="sort('region')"
                    >ABM Centre</flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'warehouse'"
                        :direction="$sortDirection"
                        wire:click="sort('warehouse')"
                    >Assessment Location</flux:table.column>
                    <flux:table.column
                        align="center"
                        sortable
                        :sorted="$sortBy === 'for_use_stock'"
                        :direction="$sortDirection"
                        wire:click="sort('for_use_stock')"
                    >For Use</flux:table.column>
                    <flux:table.column
                        align="center"
                        sortable
                        :sorted="$sortBy === 'for_storing'"
                        :direction="$sortDirection"
                        wire:click="sort('for_storing')"
                    >For Storing</flux:table.column>
                    <flux:table.column
                        align="center"
                    >Source</flux:table.column>
                    <flux:table.column>Remarks</flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'created_at'"
                        :direction="$sortDirection"
                        wire:click="sort('created_at')"
                    >Created</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->distributions as $distribution)
                        <flux:table.row>
                            <flux:table.cell align="center">
                                {{ (($this->distributions->currentPage() - 1) * $this->distributions->perPage()) + $loop->iteration }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ \Carbon\Carbon::parse($distribution->distribution_date)->format('d M Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium">{{ $distribution->staff_name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    color="{{
                                        $distribution->region === 'east' ? 'blue' :
                                        ($distribution->region === 'west' ? 'green' :
                                        ($distribution->region === 'north' ? 'purple' :
                                        ($distribution->region === 'south' ? 'orange' : 'gray')))
                                    }}"
                                    size="sm"
                                >
                                    {{ $this->getRegionName($distribution->region) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm">{{ $distribution->warehouse }}</div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($distribution->for_use_stock > 0 || ($distribution->for_use_helmets > 0 || $distribution->for_use_tshirts > 0))
                                    <div class="text-sm">
                                        <div class="font-medium dark:text-white">{{ number_format($distribution->for_use_stock ?: (($distribution->for_use_helmets ?: 0) + ($distribution->for_use_tshirts ?: 0))) }}</div>
                                        @if(($distribution->for_use_helmets ?: 0) > 0 || ($distribution->for_use_tshirts ?: 0) > 0)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-0">
                                                @if(($distribution->for_use_helmets ?: 0) > 0)<span class="px-1">H:{{ $distribution->for_use_helmets }}</span>@endif
                                                @if(($distribution->for_use_helmets ?: 0) > 0 && ($distribution->for_use_tshirts ?: 0) > 0) @endif
                                                @if(($distribution->for_use_tshirts ?: 0) > 0)<span class="px-1">T:{{ $distribution->for_use_tshirts }}</span>@endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($distribution->for_storing > 0 || ($distribution->for_storing_helmets > 0 || $distribution->for_storing_tshirts > 0))
                                    <div class="text-sm">
                                        <div class="font-medium dark:text-white">{{ number_format($distribution->for_storing ?: (($distribution->for_storing_helmets ?: 0) + ($distribution->for_storing_tshirts ?: 0))) }}</div>
                                        @if(($distribution->for_storing_helmets ?: 0) > 0 || ($distribution->for_storing_tshirts ?: 0) > 0)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-0">
                                                @if(($distribution->for_storing_helmets ?: 0) > 0)<span class="px-1">H:{{ $distribution->for_storing_helmets }}</span>@endif
                                                @if(($distribution->for_storing_helmets ?: 0) > 0 && ($distribution->for_storing_tshirts ?: 0) > 0) @endif
                                                @if(($distribution->for_storing_tshirts ?: 0) > 0)<span class="px-1">T:{{ $distribution->for_storing_tshirts }}</span>@endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($distribution->deduction_source)
                                    <flux:badge
                                        color="{{ $distribution->deduction_source === 'total_stocks' ? 'zinc' : 'zinc' }}"
                                        size="sm"
                                    >
                                        {{ $distribution->deduction_source === 'total_stocks' ? 'Main Stock' : 'ABM Storage' }}
                                    </flux:badge>
                                @else
                                    <span class="text-gray-400 text-xs">Legacy</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                    {{ $distribution->remarks ?: '-' }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="flex items-center justify-center">
                                <div class="text-xs text-gray-500 flex items-center">
                                    {{ $distribution->created_at->format('d M Y H:i') }} <br/>
                                    {{ $distribution->created_at->format('H:i') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="11" class="text-center py-8">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon.clipboard-document-list class="w-8 h-8 mx-auto mb-1 opacity-50" />
                                    <p class=" font-medium">No distribution records found</p>
                                    <p class="text-sm">Try adjusting your filters or check back later.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
</flux:main>

<script>
function exportToExcel() {
    // Get current filter values
    const search = @json($search);
    const selectedStaff = @json($selectedStaff);
    const selectedRegion = @json($selectedRegion);
    const selectedWarehouse = @json($selectedWarehouse);
    const dateFrom = @json($dateFrom);
    const dateTo = @json($dateTo);

    // Build query parameters
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (selectedStaff) params.append('selectedStaff', selectedStaff);
    if (selectedRegion) params.append('selectedRegion', selectedRegion);
    if (selectedWarehouse) params.append('selectedWarehouse', selectedWarehouse);
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);

    // Create download URL
    const url = '{{ route("admin.export.distributions") }}?' + params.toString();

    // Trigger download
    window.open(url, '_blank');
}

function exportToPdf() {
    // Get current filter values
    const search = @json($search);
    const selectedStaff = @json($selectedStaff);
    const selectedRegion = @json($selectedRegion);
    const selectedWarehouse = @json($selectedWarehouse);
    const dateFrom = @json($dateFrom);
    const dateTo = @json($dateTo);

    // Build query parameters
    const params = new URLSearchParams();
    params.append('format', 'pdf');
    if (search) params.append('search', search);
    if (selectedStaff) params.append('selectedStaff', selectedStaff);
    if (selectedRegion) params.append('selectedRegion', selectedRegion);
    if (selectedWarehouse) params.append('selectedWarehouse', selectedWarehouse);
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);

    // Create download URL
    const url = '{{ route("admin.export.distributions") }}?' + params.toString();

    // Trigger download
    window.open(url, '_blank');
}
</script>
