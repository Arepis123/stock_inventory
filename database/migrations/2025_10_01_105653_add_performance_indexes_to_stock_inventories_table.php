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
        Schema::table('stock_inventories', function (Blueprint $table) {
            // Primary date-based indexes (most critical for dashboard queries)
            $table->index('created_at', 'stock_inventories_created_at_idx');
            $table->index('distribution_date', 'stock_inventories_distribution_date_idx');

            // Composite indexes for date range queries with grouping
            $table->index(['created_at', 'region'], 'stock_inventories_created_region_idx');
            $table->index(['created_at', 'warehouse'], 'stock_inventories_created_warehouse_idx');
            $table->index(['created_at', 'staff_name'], 'stock_inventories_created_staff_idx');

            // Regional and warehouse analysis indexes
            $table->index('region', 'stock_inventories_region_idx');
            $table->index('warehouse', 'stock_inventories_warehouse_idx');
            $table->index('staff_name', 'stock_inventories_staff_name_idx');

            // Deduction source filtering
            $table->index('deduction_source', 'stock_inventories_deduction_source_idx');
            $table->index(['region', 'deduction_source'], 'stock_inventories_region_deduction_idx');

            // Item type analysis
            $table->index('item_type', 'stock_inventories_item_type_idx');

            // Composite indexes for complex dashboard queries
            $table->index(['region', 'created_at'], 'stock_inventories_region_created_idx');
            $table->index(['warehouse', 'created_at'], 'stock_inventories_warehouse_created_idx');

            // Export and reporting optimization
            $table->index(['created_at', 'region', 'warehouse'], 'stock_inventories_export_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_inventories', function (Blueprint $table) {
            // Drop all indexes
            $table->dropIndex('stock_inventories_created_at_idx');
            $table->dropIndex('stock_inventories_distribution_date_idx');
            $table->dropIndex('stock_inventories_created_region_idx');
            $table->dropIndex('stock_inventories_created_warehouse_idx');
            $table->dropIndex('stock_inventories_created_staff_idx');
            $table->dropIndex('stock_inventories_region_idx');
            $table->dropIndex('stock_inventories_warehouse_idx');
            $table->dropIndex('stock_inventories_staff_name_idx');
            $table->dropIndex('stock_inventories_deduction_source_idx');
            $table->dropIndex('stock_inventories_region_deduction_idx');
            $table->dropIndex('stock_inventories_item_type_idx');
            $table->dropIndex('stock_inventories_region_created_idx');
            $table->dropIndex('stock_inventories_warehouse_created_idx');
            $table->dropIndex('stock_inventories_export_idx');
        });
    }
};
