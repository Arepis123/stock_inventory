<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrScan extends Model
{
    protected $fillable = [
        'token',
        'is_active',
        'expires_at',
        'max_attempts',
        'attempt_count',
        'failed_attempts',
        'ip_address',
        'user_agent',
        'scanned_at',
        'completed_distribution',
        'distribution_id',
        'scan_metadata',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_distribution' => 'boolean',
        'is_active' => 'boolean',
        'scan_metadata' => 'array',
        'failed_attempts' => 'array',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(StockInventory::class, 'distribution_id');
    }

    public function getStatusAttribute(): string
    {
        if ($this->completed_distribution) {
            return 'Completed';
        } elseif ($this->scanned_at) {
            return 'Scanned';
        } else {
            return 'Pending';
        }
    }

    public function getLocationInfoAttribute(): string
    {
        $metadata = $this->scan_metadata ?? [];
        $location = [];

        if (isset($metadata['country'])) {
            $location[] = $metadata['country'];
        }
        if (isset($metadata['city'])) {
            $location[] = $metadata['city'];
        }

        return implode(', ', $location) ?: 'Unknown';
    }

    public function getDeviceInfoAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        $userAgent = $this->user_agent;

        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile Device';
        } elseif (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) {
            return 'Tablet';
        } else {
            return 'Desktop/Laptop';
        }
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->attempt_count >= $this->max_attempts;
    }

    public function canBeUsed(): bool
    {
        return $this->is_active
            && !$this->isExhausted();
    }

    public function getTotalScansAttribute(): int
    {
        return $this->scan_metadata['total_scans'] ?? ($this->scanned_at ? 1 : 0);
    }

    public function recordFailedAttempt(string $ip, string $userAgent, string $reason = 'Invalid attempt'): void
    {
        $attempts = $this->failed_attempts ?? [];
        $attempts[] = [
            'timestamp' => now()->toISOString(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'reason' => $reason,
        ];

        $this->update([
            'attempt_count' => $this->attempt_count + 1,
            'failed_attempts' => $attempts,
        ]);
    }
}