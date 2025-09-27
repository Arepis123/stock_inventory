<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\QrScan;
use App\Models\StockInventory;

class QrCodeController extends Controller
{
    public function generateDistributionQr()
    {
        // Get the current active QR token
        $latestQr = QrScan::whereNull('scanned_at')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestQr) {
            // No active QR code found, return empty QR or error
            return response()->json(['error' => 'No active QR code found'], 404);
        }

        $url = route('qr.scan', ['token' => $latestQr->token]);

        $qrCode = QrCode::size(200)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }

    public function scanRedirect(Request $request, $token)
    {
        $qrScan = QrScan::where('token', $token)->first();

        if (!$qrScan) {
            return redirect()->route('home')->with('error', 'Invalid QR code.');
        }

        // Check if QR code can be used
        if (!$qrScan->canBeUsed()) {
            $reason = 'Invalid attempt';

            if (!$qrScan->is_active) {
                $reason = 'QR code deactivated';
            } elseif ($qrScan->isExhausted()) {
                $reason = 'Too many attempts';
            }

            // Record failed attempt
            $qrScan->recordFailedAttempt($request->ip(), $request->userAgent(), $reason);

            return redirect()->route('home')->with('error', $this->getSecurityErrorMessage($reason));
        }

        // Basic bot detection
        if ($this->detectBot($request)) {
            $qrScan->recordFailedAttempt($request->ip(), $request->userAgent(), 'Bot detected');
            return redirect()->route('home')->with('error', 'Automated access detected. Please use a regular browser.');
        }

        // Record the scan (update latest scan info and add to scan history)
        $scanHistory = $qrScan->scan_metadata['scan_history'] ?? [];
        $scanHistory[] = [
            'scanned_at' => now()->toISOString(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'scanned_from_url' => $request->fullUrl(),
            'referrer' => $request->header('referer'),
        ];

        $qrScan->update([
            'scanned_at' => now(), // Keep this for latest scan time
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'scan_metadata' => array_merge($qrScan->scan_metadata ?? [], [
                'scan_history' => $scanHistory,
                'total_scans' => count($scanHistory),
                'last_scan_url' => $request->fullUrl(),
                'last_scan_referrer' => $request->header('referer'),
            ]),
        ]);

        // Store the QR scan ID in session for later use
        session(['current_qr_scan_id' => $qrScan->id]);

        return redirect()->route('stock-distribution');
    }

    public function generateStockItemQr($stockId)
    {
        $stock = StockInventory::findOrFail($stockId);
        $url = route('stock.view', ['id' => $stockId]);

        $qrCode = QrCode::size(200)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }

    private function detectBot($request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        // Common bot signatures
        $botSignatures = [
            'bot', 'crawler', 'spider', 'scraper', 'wget', 'curl',
            'python', 'php', 'java', 'node', 'go-http', 'ruby',
            'http_request', 'libwww', 'selenium', 'phantom',
        ];

        foreach ($botSignatures as $signature) {
            if (str_contains($userAgent, $signature)) {
                return true;
            }
        }

        // Check for missing common browser headers
        if (!$request->hasHeader('Accept') || !$request->hasHeader('Accept-Language')) {
            return true;
        }

        // Check for suspiciously fast requests (basic rate limiting)
        $cacheKey = 'qr_scan_rate_' . $request->ip();
        $requests = cache()->get($cacheKey, []);

        // Remove requests older than 1 minute
        $requests = array_filter($requests, fn($time) => $time > now()->subMinute()->timestamp);

        if (count($requests) > 5) { // Max 5 requests per minute
            return true;
        }

        // Record this request
        $requests[] = now()->timestamp;
        cache()->put($cacheKey, $requests, 300); // Cache for 5 minutes

        return false;
    }

    private function getSecurityErrorMessage(string $reason): string
    {
        return match($reason) {
            'QR code deactivated' => 'This QR code has been deactivated by the administrator.',
            'Too many attempts' => 'This QR code has been blocked due to too many failed attempts.',
            default => 'Invalid QR code or access denied.',
        };
    }
}