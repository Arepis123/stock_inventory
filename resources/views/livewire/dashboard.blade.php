<flux:main class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>Overview of your stock inventory system</flux:subheading>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
    </div>

    <!-- Quick Actions and System Status -->
    <div class="grid grid-cols-1 gap-6 mb-6">
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