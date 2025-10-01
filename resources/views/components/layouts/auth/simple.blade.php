<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/background.jpg') }}');">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 bg-black/25 backdrop-blur-sm">
            <div class="flex w-full max-w-sm flex-col gap-2 bg-white/5 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'SKKP Inventory') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
