<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <title>Stock Inventory Distribution - {{ config('app.name') }}</title>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="min-h-screen py-8">
            <livewire:stock-inventory-form />
        </div>

        <flux:toast />

        @livewireScripts
        @fluxScripts
    </body>
</html>