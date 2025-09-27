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
            $table->integer('for_use_stock')->default(0)->after('warehouse');
            $table->integer('for_storing')->default(0)->after('for_use_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_inventories', function (Blueprint $table) {
            $table->dropColumn(['for_use_stock', 'for_storing']);
        });
    }
};
