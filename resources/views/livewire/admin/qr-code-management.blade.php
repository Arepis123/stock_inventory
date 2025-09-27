<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">      
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">QR Code Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage QR codes for stock distribution access and monitor scan activity.</p>            
    </div>

    <!-- Current QR Code Section -->
    <flux:card class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-6">
            <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Current Active QR Code</flux:heading>
            <div class="flex space-x-2">
                @if($currentQrToken)
                    <flux:button wire:click="toggleQrDisplay" variant="filled"
                             class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-zinc-800">
                        <flux:icon.eye class="w-4 h-4 mr-2" />
                        Show QR Code
                    </flux:button>
                    <flux:button wire:click="toggleQrActivation" variant="{{ $currentQrActive ? 'danger' : 'primary' }}">
                        @if($currentQrActive)
                            Deactivate QR Code
                        @else
                            Activate QR Code
                        @endif
                    </flux:button>
                @endif
                <flux:button wire:click="generateNewQrCode" variant="primary">
                    Generate New QR Code
                </flux:button>
            </div>
        </div>

        @if($currentQrToken)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900 dark:text-gray-100">Token:</span>
                            <code class="bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded text-sm text-gray-900 dark:text-gray-100">{{ $currentQrToken }}</code>
                        </div>
                        <div class="flex items-start space-x-2">
                            <span class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">URL:</span>
                            <code class="bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded text-xs break-all text-gray-900 dark:text-gray-100 flex-1">{{ route('qr.scan', ['token' => $currentQrToken]) }}</code>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900 dark:text-gray-100">Status:</span>
                            @if($currentQrActive)
                                <flux:badge color="green" size="sm">
                                    <flux:icon.check-circle class="w-3 h-3 mr-1" />
                                    Active
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    <flux:icon.x-circle class="w-3 h-3 mr-1" />
                                    Deactivated
                                </flux:badge>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="text-center py-12">
                <flux:icon.qr-code class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                <flux:subheading class="text-gray-600 dark:text-gray-400">No active QR code. Generate one to get started.</flux:subheading>
            </div>
        @endif
    </flux:card>

    <!-- QR Scan Activity -->
    <flux:card class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700">
        <flux:heading size="lg" class="text-gray-900 dark:text-gray-100 mb-6">QR Scan Activity</flux:heading>

        @if($qrScans->count() > 0)
            <div class="overflow-x-auto">
                <flux:table :paginate="$qrScans">
                    <flux:table.columns>
                        <flux:table.column>Token</flux:table.column>
                        <flux:table.column>Active</flux:table.column>
                        <flux:table.column>Scan Count</flux:table.column>
                        <flux:table.column>Last Scanned</flux:table.column>
                        <flux:table.column>Device Info</flux:table.column>
                        <flux:table.column>IP Address</flux:table.column>
                        <flux:table.column>Distribution</flux:table.column>
                        <flux:table.column align="center">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($qrScans as $qrScan)
                            <flux:table.row>
                                <flux:table.cell>
                                    <code class="text-xs bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded text-gray-900 dark:text-gray-100">{{ Str::limit($qrScan->token, 16) }}</code>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($qrScan->is_active)
                                        <flux:badge color="green" size="sm">
                                            <flux:icon.check class="w-3 h-3 mr-1" />
                                            Yes
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">
                                            <flux:icon.x-mark class="w-3 h-3 mr-1" />
                                            No
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($qrScan->total_scans > 0)
                                        <flux:badge color="blue" size="sm">
                                            <flux:icon.eye class="w-3 h-3 mr-1" />
                                            {{ $qrScan->total_scans }} scan{{ $qrScan->total_scans > 1 ? 's' : '' }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="gray" size="sm">
                                            <flux:icon.minus-circle class="w-3 h-3 mr-1" />
                                            Not scanned
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $qrScan->scanned_at ? $qrScan->scanned_at->format('M j, Y g:i A') : '-' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $qrScan->deviceInfo }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $qrScan->ip_address ?? '-' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($qrScan->distribution)
                                        <div>
                                            <div class="font-medium">{{ $qrScan->distribution->staff_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $qrScan->distribution->quantity }} items</div>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    @if(!$qrScan->completed_distribution)
                                        <flux:button
                                            wire:click="deleteQrCode({{ $qrScan->id }})"
                                            variant="danger"
                                            size="sm"
                                            wire:confirm="Are you sure you want to delete this QR code?"
                                        >
                                            <flux:icon.trash class="w-3 h-3" />
                                        </flux:button>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @else
            <div class="text-center py-12">
                <flux:icon.clipboard-document-list class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                <flux:subheading class="text-gray-600 dark:text-gray-400">No QR scan activity yet.</flux:subheading>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Generate a QR code and share it with staff to see scan activity here.</p>
            </div>
        @endif
    </flux:card>

    <!-- QR Code Modal -->
    <flux:modal name="qr-code-modal" wire:model="showQrCode" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">QR Code for Distribution Access</flux:heading>
                <flux:text class="mt-2">Share this QR code with staff to access the stock distribution form</flux:text>
            </div>

            @if($currentQrToken)
                <div class="text-center space-y-4">
                    <div class="flex justify-center">
                        <img src="{{ route('qr.generate') }}" alt="QR Code" class="border border-gray-200 dark:border-zinc-700 rounded-lg bg-white p-4">
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                        <p class="font-medium">Token: <code class="bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded text-xs">{{ $currentQrToken }}</code></p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon.exclamation-triangle class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                    <p class="text-gray-600 dark:text-gray-400">No active QR code available</p>
                </div>
            @endif

            <div class="flex">
                <flux:spacer />
                <flux:button wire:click="toggleQrDisplay" variant="primary">
                    Close
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>