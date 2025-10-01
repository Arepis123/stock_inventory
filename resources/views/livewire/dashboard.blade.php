<flux:main class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>Overview of your stock inventory system</flux:subheading>
        </div>
    </div>

    <!-- Primary Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <flux:icon.archive-box class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <flux:heading size="sm">Total Distributed</flux:heading>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalDistributed) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Sets Distributed</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <flux:icon.clipboard-document-list class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <flux:heading size="sm">Total Records</flux:heading>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalRecords) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Distribution Records</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <flux:icon.cube class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <flux:heading size="sm">Total Stock</flux:heading>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalStock) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">In Inventory</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <flux:icon.check-circle class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <flux:heading size="sm">Available Stock</flux:heading>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->availableStock) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ready to Distribute</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Equipment Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.shield-check class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" />
                Equipment Distribution
            </flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Safety Helmets</p>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($this->totalHelmetsDistributed) }}</p>
                        </div>
                        <svg class="w-8 h-8 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/>
                            <path d="M14 6a6 6 0 0 1 6 6v3"/>
                            <path d="M4 15v-3a6 6 0 0 1 6-6"/>
                            <rect x="2" y="15" width="20" height="4" rx="1"/>
                        </svg> 
                    </div>
                </div>
                <div class="bg-teal-50 dark:bg-teal-900/30 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">T-Shirts</p>
                            <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($this->totalShirtsDistributed) }}</p>
                        </div>     
                        <svg class="w-8 h-8 text-teal-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>
                        </svg>                  
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.chart-bar class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400" />
                Usage Breakdown
            </flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">For Use</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($this->totalForUse) }}</p>
                        </div>
                        <flux:icon.user-group class="w-8 h-8 text-emerald-400" />
                    </div>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/30 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">For Storing</p>
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($this->totalForStoring) }}</p>
                        </div>
                        <flux:icon.archive-box class="w-8 h-8 text-amber-400" />
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Time-based Statistics -->
    <flux:card class="mb-6">
        <flux:heading size="sm" class="mb-4 flex items-center">
            <flux:icon.calendar class="w-5 h-5 mr-2 text-sky-600 dark:text-sky-400" />
            Distribution Trends
        </flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Today -->
            <div class="border-l-4 border-sky-500 pl-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Today</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Distributions:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->distributionsToday) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quantity:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->quantityDistributedToday) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">QR Scans:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->qrScansToday) }}</span>
                    </div>
                </div>
            </div>

            <!-- This Week -->
            <div class="border-l-4 border-violet-500 pl-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">This Week</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Distributions:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->distributionsThisWeek) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quantity:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->quantityDistributedThisWeek) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Avg. per Day:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->distributionsThisWeek / 7, 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="border-l-4 border-pink-500 pl-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">This Month</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Distributions:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->distributionsThisMonth) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quantity:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->quantityDistributedThisMonth) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Avg. per Day:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->averageDailyDistribution, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </flux:card>

    <!-- Regional & QR Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Regional Breakdown -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.map-pin class="w-5 h-5 mr-2 text-rose-600 dark:text-rose-400" />
                Regional Distribution
            </flux:heading>
            @if($this->regionalBreakdown->count() > 0)
                <div class="space-y-3">
                    @foreach($this->regionalBreakdown as $region)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $region['region_display'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($region['count']) }} distributions</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ number_format($region['total_quantity']) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">units</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">No regional data available</p>
            @endif
        </flux:card>

        <!-- QR Code Statistics -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.qr-code class="w-5 h-5 mr-2 text-fuchsia-600 dark:text-fuchsia-400" />
                QR Code Analytics
            </flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-fuchsia-50 dark:bg-fuchsia-900/30 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total QR Codes</p>
                    <p class="text-3xl font-bold text-fuchsia-600 dark:text-fuchsia-400">{{ number_format($this->totalQrScans) }}</p>
                </div>
                <div class="bg-cyan-50 dark:bg-cyan-900/30 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active Codes</p>
                    <p class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ number_format($this->activeQrCodes) }}</p>
                </div>
                <div class="bg-lime-50 dark:bg-lime-900/30 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-lime-600 dark:text-lime-400">{{ number_format($this->completedQrScans) }}</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/30 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($this->pendingQrScans) }}</p>
                </div>
            </div>
            @if($this->totalQrScans > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Completion Rate:</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format(($this->completedQrScans / $this->totalQrScans) * 100, 1) }}%
                        </span>
                    </div>
                </div>
            @endif
        </flux:card>
    </div>

    <!-- Warehouse Breakdown & Stock Health -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Warehouses -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.building-office class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                Top Warehouses
            </flux:heading>
            @if($this->warehouseBreakdown->count() > 0)
                <div class="space-y-3">
                    @foreach($this->warehouseBreakdown as $warehouse)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $warehouse->warehouse }}</p>
                                <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    @php
                                        $maxQuantity = $this->warehouseBreakdown->max('total_quantity');
                                        $percentage = $maxQuantity > 0 ? ($warehouse->total_quantity / $maxQuantity) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($warehouse->total_quantity) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($warehouse->count) }} records</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">No warehouse data available</p>
            @endif
        </flux:card>

        <!-- Stock Health Metrics -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.chart-pie class="w-5 h-5 mr-2 text-violet-600 dark:text-violet-400" />
                Stock Health Metrics
            </flux:heading>
            <div class="space-y-4">
                <!-- Stock Utilization -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock Utilization Rate</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $this->stockUtilizationRate }}%</span>
                    </div>
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-violet-500 to-purple-600 h-3 rounded-full" style="width: {{ min($this->stockUtilizationRate, 100) }}%"></div>
                    </div>
                </div>

                <!-- Available vs Distributed -->
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Available</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->availableStock) }}</p>
                        @if($this->totalStock > 0)
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ number_format(($this->availableStock / $this->totalStock) * 100, 1) }}%
                            </p>
                        @endif
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Distributed</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->totalDistributed) }}</p>
                        @if($this->totalStock > 0)
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ number_format(($this->totalDistributed / $this->totalStock) * 100, 1) }}%
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Stock Status Badge -->
                <div class="mt-4">
                    @if($this->availableStock <= 50)
                        <flux:badge color="red" size="lg" class="w-full justify-center">
                            <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                            Critical Stock Level!
                        </flux:badge>
                    @elseif($this->availableStock <= 100)
                        <flux:badge color="yellow" size="lg" class="w-full justify-center">
                            <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                            Low Stock Warning
                        </flux:badge>
                    @else
                        <flux:badge color="green" size="lg" class="w-full justify-center">
                            <flux:icon.check-circle class="w-4 h-4 mr-2" />
                            Stock Levels Healthy
                        </flux:badge>
                    @endif
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Distributions -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.clock class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" />
                Recent Distributions
            </flux:heading>
            @if($this->recentDistributions->count() > 0)
                <div class="space-y-3">
                    @foreach($this->recentDistributions as $distribution)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $distribution->staff_name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <flux:badge size="sm" color="zinc">{{ $distribution->region_display }}</flux:badge>
                                    <flux:badge size="sm" color="zinc">{{ $distribution->warehouse }}</flux:badge>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($distribution->quantity) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $distribution->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <flux:button wire:navigate href="{{ route('admin.distributions') }}" variant="outline" class="w-full">
                        View All Distributions
                    </flux:button>
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No distributions yet</p>
            @endif
        </flux:card>

        <!-- Quick Actions -->
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center">
                <flux:icon.bolt class="w-5 h-5 mr-2 text-yellow-600 dark:text-yellow-400" />
                Quick Actions
            </flux:heading>
            <div class="space-y-3">
                <flux:button wire:navigate href="{{ route('stock-inventory') }}" variant="primary" icon="plus" class="w-full justify-start">
                    Add New Distribution
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.qrcode') }}" variant="outline" icon="qr-code" class="w-full justify-start">
                    QR Code Management
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.stock') }}" variant="outline" icon="cog" class="w-full justify-start">
                    Manage Stock Settings
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.distributions') }}" variant="outline" icon="clipboard-document-list" class="w-full justify-start">
                    View All Distributions
                </flux:button>
            </div>

            <!-- System Info -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Avg. Daily Distribution:</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->averageDailyDistribution, 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Last Distribution:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            @if($this->lastDistribution)
                                {{ $this->lastDistribution->created_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</flux:main>