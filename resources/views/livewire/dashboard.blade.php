<flux:main class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>Overview of your stock inventory system</flux:subheading>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <flux:icon.archive-box class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div>
                    <flux:heading size="sm">Total Distributed</flux:heading>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalDistributed) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Helmet & Shirt Sets</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <flux:icon.clipboard-document-list class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div>
                    <flux:heading size="sm">Total Records</flux:heading>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalRecords) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Distribution Records</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <flux:icon.cube class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div>
                    <flux:heading size="sm">Total Stock</flux:heading>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->totalStock) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">In Inventory</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <flux:icon.check-circle class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <div>
                    <flux:heading size="sm">Available Stock</flux:heading>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->availableStock) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ready for Distribution</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                        <flux:icon.qr-code class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
                <div>
                    <flux:heading size="sm">QR Scans</flux:heading>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->completedQrScans) }}/{{ number_format($this->totalQrScans) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Completed/Total</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Quick Actions and QR Activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <flux:card>
            <flux:heading size="sm" class="mb-4">Quick Actions</flux:heading>
            <div class="space-y-3">
                <flux:button wire:navigate href="{{ route('stock-inventory') }}" variant="outline" icon="plus" class="w-full justify-start">
                    Add New Distribution
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.qrcode') }}" variant="outline" icon="qr-code" class="w-full justify-start">
                    QR Code Management
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.stock') }}" variant="outline" icon="cog" class="w-full justify-start">
                    Manage Stock
                </flux:button>
                <flux:button wire:navigate href="{{ route('admin.distributions') }}" variant="outline" icon="clipboard-document-list" class="w-full justify-start">
                    View All Distributions
                </flux:button>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="sm">Recent QR Scan Activity</flux:heading>
                <flux:button wire:navigate href="{{ route('admin.qrcode') }}" variant="ghost" size="sm">
                    View All
                </flux:button>
            </div>

            @if($this->qrScans->count() > 0)
                <div class="space-y-3">
                    @foreach($this->qrScans as $qrScan)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($qrScan->status === 'Completed')
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    @elseif($qrScan->status === 'Scanned')
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    @else
                                        <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if($qrScan->distribution)
                                            {{ $qrScan->distribution->staff_name }}
                                        @else
                                            Token: {{ Str::limit($qrScan->token, 8) }}...
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $qrScan->scanned_at ? $qrScan->scanned_at->format('M j, g:i A') : 'Not scanned yet' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($qrScan->status === 'Completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($qrScan->status === 'Scanned') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $qrScan->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <flux:icon.qr-code class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                    <p class="text-sm text-gray-600 dark:text-gray-400">No QR scan activity yet</p>
                </div>
            @endif
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4">System Status</flux:heading>
            <div class="space-y-3">
                @if($this->availableStock <= 50)
                    <flux:badge color="red" size="lg" class="w-full justify-center">
                        <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                        Low Stock Warning!
                    </flux:badge>
                @elseif($this->availableStock <= 100)
                    <flux:badge color="yellow" size="lg" class="w-full justify-center">
                        <flux:icon.exclamation-triangle class="w-4 h-4 mr-2" />
                        Stock Running Low
                    </flux:badge>
                @else
                    <flux:badge color="green" size="lg" class="w-full justify-center">
                        <flux:icon.check-circle class="w-4 h-4 mr-2" />
                        Stock Levels Normal
                    </flux:badge>
                @endif

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex justify-between">
                        <span>Total Distributions Today:</span>
                        <span class="font-medium">{{ number_format(\App\Models\StockInventory::whereDate('created_at', today())->count()) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Last Distribution:</span>
                        <span class="font-medium">
                            @if($lastDistribution = \App\Models\StockInventory::latest()->first())
                                {{ $lastDistribution->created_at->diffForHumans() }}
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