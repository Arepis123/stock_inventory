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
        Schema::create('stock_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->enum('region', ['east', 'west', 'north', 'south']);
            $table->string('warehouse');
            $table->enum('item_type', ['safety_helmet', 'shirt']);
            $table->integer('quantity');
            $table->text('remarks')->nullable();
            $table->timestamp('distribution_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_inventories');
    }
};
