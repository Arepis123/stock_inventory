<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include 'safety_equipment' and 'tshirt'
        DB::statement("ALTER TABLE stock_inventories MODIFY COLUMN item_type ENUM('safety_helmet','shirt','tshirt','helmet_shirt_set','safety_equipment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE stock_inventories MODIFY COLUMN item_type ENUM('safety_helmet','shirt','helmet_shirt_set') NOT NULL");
    }
};
