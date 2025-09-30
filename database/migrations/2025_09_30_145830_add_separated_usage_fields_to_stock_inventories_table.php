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
            // Add separated usage tracking fields
            $table->integer('for_use_helmets')->default(0)->after('deduction_source');
            $table->integer('for_use_tshirts')->default(0)->after('for_use_helmets');
            $table->integer('for_storing_helmets')->default(0)->after('for_use_tshirts');
            $table->integer('for_storing_tshirts')->default(0)->after('for_storing_helmets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_inventories', function (Blueprint $table) {
            $table->dropColumn(['for_use_helmets', 'for_use_tshirts', 'for_storing_helmets', 'for_storing_tshirts']);
        });
    }
};
