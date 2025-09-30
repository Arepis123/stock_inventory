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
        Schema::create('abm_storage', function (Blueprint $table) {
            $table->id();
            $table->string('region_code'); // east, west, north, south
            $table->string('region_name'); // East Coast, West Coast, etc.

            // Separate helmet and t-shirt tracking
            $table->integer('helmet_stock')->default(0);
            $table->integer('tshirt_stock')->default(0);

            // Track stock movements
            $table->integer('helmet_received')->default(0); // Total received from main stock
            $table->integer('tshirt_received')->default(0);
            $table->integer('helmet_distributed')->default(0); // Total distributed to staff
            $table->integer('tshirt_distributed')->default(0);

            // Optional fields for tracking
            $table->text('notes')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->string('last_updated_by')->nullable();

            $table->timestamps();

            // Ensure one record per region
            $table->unique('region_code');

            // Indexes for performance
            $table->index('region_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abm_storage');
    }
};