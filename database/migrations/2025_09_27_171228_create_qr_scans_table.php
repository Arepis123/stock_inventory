<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->boolean('completed_distribution')->default(false);
            $table->foreignId('distribution_id')->nullable()->constrained('stock_inventories')->onDelete('set null');
            $table->json('scan_metadata')->nullable(); // Store additional scan info
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};