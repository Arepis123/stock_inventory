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
        // QR Scans table indexes
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->index(['is_active', 'expires_at', 'created_at'], 'qr_scans_active_expires_created_idx');
            $table->index('created_at', 'qr_scans_created_at_idx');
            $table->index('scanned_at', 'qr_scans_scanned_at_idx');
            $table->index('completed_distribution', 'qr_scans_completed_distribution_idx');
        });

        // Regions table indexes
        Schema::table('regions', function (Blueprint $table) {
            $table->index('is_active', 'regions_is_active_idx');
            $table->index('name', 'regions_name_idx');
        });

        // Warehouses table indexes
        Schema::table('warehouses', function (Blueprint $table) {
            $table->index(['region_id', 'is_active'], 'warehouses_region_active_idx');
            $table->index('is_active', 'warehouses_is_active_idx');
            $table->index('name', 'warehouses_name_idx');
        });

        // Staff table indexes
        Schema::table('staff', function (Blueprint $table) {
            $table->index('name', 'staff_name_idx');
            $table->index('is_active', 'staff_is_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop QR Scans indexes
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropIndex('qr_scans_active_expires_created_idx');
            $table->dropIndex('qr_scans_created_at_idx');
            $table->dropIndex('qr_scans_scanned_at_idx');
            $table->dropIndex('qr_scans_completed_distribution_idx');
        });

        // Drop Regions indexes
        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex('regions_is_active_idx');
            $table->dropIndex('regions_name_idx');
        });

        // Drop Warehouses indexes
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropIndex('warehouses_region_active_idx');
            $table->dropIndex('warehouses_is_active_idx');
            $table->dropIndex('warehouses_name_idx');
        });

        // Drop Staff indexes
        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('staff_name_idx');
            $table->dropIndex('staff_is_active_idx');
        });
    }
};
