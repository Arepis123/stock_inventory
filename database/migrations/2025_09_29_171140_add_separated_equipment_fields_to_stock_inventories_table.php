<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_inventories', function (Blueprint $table) {
            // Add separated equipment tracking fields
            $table->integer('helmet_quantity')->default(0)->after('quantity');
            $table->integer('tshirt_quantity')->default(0)->after('helmet_quantity');

            // Add deduction source field
            $table->enum('deduction_source', ['total_stocks', 'abm_storage'])
                  ->default('total_stocks')
                  ->after('tshirt_quantity');

            // Keep existing fields for backward compatibility, but make them nullable
            $table->integer('quantity')->nullable()->change();
            $table->integer('for_use_stock')->nullable()->change();
            $table->integer('for_storing')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_inventories', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn(['helmet_quantity', 'tshirt_quantity', 'deduction_source']);

            // Restore original field constraints
            $table->integer('quantity')->nullable(false)->change();
            $table->integer('for_use_stock')->nullable(false)->change();
            $table->integer('for_storing')->nullable(false)->change();
        });
    }
};