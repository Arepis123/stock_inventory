<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StockManagement;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, get the existing helmet_shirt_set stock data
        $existingStock = StockManagement::where('item_type', 'helmet_shirt_set')->first();

        if ($existingStock) {
            // Create separate records for safety_helmet and tshirt
            // Assuming equal distribution of existing stock between helmet and t-shirt
            $helmetStock = StockManagement::firstOrCreate(
                ['item_type' => 'safety_helmet'],
                [
                    'total_stock' => $existingStock->total_stock,
                    'allocated_stock' => $existingStock->allocated_stock,
                    'available_stock' => $existingStock->available_stock,
                    'notes' => 'Migrated from helmet_shirt_set stock',
                ]
            );

            $tshirtStock = StockManagement::firstOrCreate(
                ['item_type' => 'tshirt'],
                [
                    'total_stock' => $existingStock->total_stock,
                    'allocated_stock' => $existingStock->allocated_stock,
                    'available_stock' => $existingStock->available_stock,
                    'notes' => 'Migrated from helmet_shirt_set stock',
                ]
            );
        } else {
            // Create new records with zero stock if none exist
            StockManagement::firstOrCreate(
                ['item_type' => 'safety_helmet'],
                [
                    'total_stock' => 0,
                    'allocated_stock' => 0,
                    'available_stock' => 0,
                    'notes' => 'Initial safety helmet stock record',
                ]
            );

            StockManagement::firstOrCreate(
                ['item_type' => 'tshirt'],
                [
                    'total_stock' => 0,
                    'allocated_stock' => 0,
                    'available_stock' => 0,
                    'notes' => 'Initial t-shirt stock record',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the helmet and t-shirt stocks
        $helmetStock = StockManagement::where('item_type', 'safety_helmet')->first();
        $tshirtStock = StockManagement::where('item_type', 'tshirt')->first();

        if ($helmetStock || $tshirtStock) {
            // Calculate combined totals (use maximum values to avoid data loss)
            $totalStock = max(
                $helmetStock->total_stock ?? 0,
                $tshirtStock->total_stock ?? 0
            );
            $allocatedStock = max(
                $helmetStock->allocated_stock ?? 0,
                $tshirtStock->allocated_stock ?? 0
            );
            $availableStock = max(
                $helmetStock->available_stock ?? 0,
                $tshirtStock->available_stock ?? 0
            );

            // Restore the helmet_shirt_set record
            StockManagement::updateOrCreate(
                ['item_type' => 'helmet_shirt_set'],
                [
                    'total_stock' => $totalStock,
                    'allocated_stock' => $allocatedStock,
                    'available_stock' => $availableStock,
                    'notes' => 'Restored from separated stocks during rollback',
                ]
            );
        }

        // Remove the separated stock records
        StockManagement::where('item_type', 'safety_helmet')->delete();
        StockManagement::where('item_type', 'tshirt')->delete();
    }
};