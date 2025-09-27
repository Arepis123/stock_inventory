<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('is_active');
            $table->integer('max_attempts')->default(3)->after('expires_at');
            $table->integer('attempt_count')->default(0)->after('max_attempts');
            $table->json('failed_attempts')->nullable()->after('attempt_count');
        });
    }

    public function down(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'max_attempts', 'attempt_count', 'failed_attempts']);
        });
    }
};