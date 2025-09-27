<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('stock-inventory', 'stock-inventory')
    ->name('stock-inventory');

// QR Code routes
Route::get('qr/generate', [\App\Http\Controllers\QrCodeController::class, 'generateDistributionQr'])
    ->name('qr.generate');

Route::get('qr/scan/{token}', [\App\Http\Controllers\QrCodeController::class, 'scanRedirect'])
    ->name('qr.scan');

Route::get('stock-distribution', \App\Livewire\StockDistribution::class)
    ->middleware(['throttle:10,1', \App\Http\Middleware\QrFormRateLimit::class])
    ->name('stock-distribution');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('regions', \App\Livewire\Admin\RegionManagement::class)->name('regions');
    Route::get('warehouses', \App\Livewire\Admin\WarehouseManagement::class)->name('warehouses');
    Route::get('staff', \App\Livewire\Admin\StaffManagement::class)->name('staff');
    Route::get('stock', \App\Livewire\Admin\StockManagement::class)->name('stock');
    Route::get('distributions', \App\Livewire\Admin\DistributionManagement::class)->name('distributions');
    Route::get('qrcode', \App\Livewire\Admin\QrCodeManagement::class)->name('qrcode');
    Route::get('export/distributions', [\App\Http\Controllers\ExportController::class, 'exportDistributions'])->name('export.distributions');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
