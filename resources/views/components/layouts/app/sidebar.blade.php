<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scrollbar-hide">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 scrollbar-hide overflow-y-auto">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    logo="{{ asset('images/logo-clab.png') }}"
                    logo:dark="{{ asset('images/logo-clab.png') }}"
                    name="SKKP Inventory"
                />  

                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <div class="px-3 py-2 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('MAIN') }}</h3>
                </div>                
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
                <flux:sidebar.item icon="clipboard-document-list" :href="route('stock-inventory')" :current="request()->routeIs('stock-inventory')" wire:navigate>{{ __('Stock Inventory') }}</flux:sidebar.item>

                <div class="px-3 py-2 mt-4 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('ADMIN') }}</h3>
                </div>
                <flux:sidebar.item icon="table-cells" :href="route('admin.distributions')" :current="request()->routeIs('admin.distributions')" wire:navigate>Distribution Records</flux:sidebar.item>
                <flux:sidebar.item icon="archive-box" :href="route('admin.stock')" :current="request()->routeIs('admin.stock')" wire:navigate>Stock Management</flux:sidebar.item>                
                <flux:sidebar.item icon="qr-code" :href="route('admin.qrcode')" :current="request()->routeIs('admin.qrcode')" wire:navigate>QR Code Management</flux:sidebar.item>
        
                <div class="px-3 py-2 mt-4 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('CONFIGURATION') }}</h3>
                </div>  
                <flux:sidebar.item icon="map" :href="route('admin.regions')" :current="request()->routeIs('admin.regions')" wire:navigate>ABM Centres</flux:sidebar.item>
                <flux:sidebar.item icon="building-office" :href="route('admin.warehouses')" :current="request()->routeIs('admin.warehouses')" wire:navigate>Assessment Locations</flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('admin.staff')" :current="request()->routeIs('admin.staff')" wire:navigate>Staff</flux:sidebar.item>              
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <!-- <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="cog-6-tooth" :href="route('settings.profile')" wire:navigate>{{ __('Settings') }}</flux:sidebar.item>
            </flux:sidebar.nav> -->

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile :name="auth()->user()->name" />

                <flux:menu>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile :name="auth()->user()->name" />

                <flux:menu>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <flux:toast />

        @livewireScripts
        @fluxScripts
    </body>
</html>
