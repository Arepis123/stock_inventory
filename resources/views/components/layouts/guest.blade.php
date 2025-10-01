<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scrollbar-hide">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 scrollbar-hide overflow-y-auto">
        <flux:header class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <div class="w-full flex justify-center px-3 py-6 sm:px-6 sm:py-4">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo-clab.png') }}" alt="Company Logo" class="h-6 w-auto mr-2 sm:h-8 sm:mr-3">
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">SKKP Inventory - Stock Distribution</h1>
                </div>
            </div>
        </flux:header>

        <main class="container mx-auto px-6 py-6 sm:px-6sm:py-6">
            {{ $slot }}
        </main>

        <flux:toast />

        @livewireScripts
        @fluxScripts
    </body>
</html>