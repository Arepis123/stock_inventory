<?php

namespace App\Livewire\Admin;

use App\Models\QrScan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Flux\Flux;

class QrCodeManagement extends Component
{
    use WithPagination;

    public $currentQrToken = '';
    public $showQrCode = false;
    public $currentQrActive = false;

    public function mount()
    {
        $this->loadCurrentQrToken();
    }

    public function loadCurrentQrToken()
    {
        $latestQr = QrScan::whereNull('scanned_at')
            ->orderBy('created_at', 'desc')
            ->first();

        $this->currentQrToken = $latestQr ? $latestQr->token : '';
        $this->currentQrActive = $latestQr ? $latestQr->is_active : false;
    }

    public function generateNewQrCode()
    {
        // Disable old unused QR codes
        QrScan::whereNull('scanned_at')->delete();

        // Generate new QR code
        $token = Str::random(32);

        QrScan::create([
            'token' => $token,
            'is_active' => true,
            'expires_at' => null, // No expiration - valid until admin regenerates
            'max_attempts' => 3, // Allow 3 failed attempts before blocking
            'scan_metadata' => [
                'generated_at' => now()->toISOString(),
                'generated_by' => auth()->user()->name ?? 'System',
            ]
        ]);

        $this->currentQrToken = $token;
        $this->currentQrActive = true;
        $this->showQrCode = true;

        Flux::toast('New QR Code generated successfully!', 'QR Code Generated', 3000, 'success');
    }

    public function toggleQrDisplay()
    {
        $this->showQrCode = !$this->showQrCode;
    }

    public function toggleQrActivation()
    {
        if (!$this->currentQrToken) {
            Flux::toast('No QR code to activate/deactivate.', 'Error', 3000, 'danger');
            return;
        }

        $latestQr = QrScan::where('token', $this->currentQrToken)->first();

        if (!$latestQr) {
            Flux::toast('QR code not found.', 'Error', 3000, 'danger');
            return;
        }

        $latestQr->update(['is_active' => !$latestQr->is_active]);
        $this->currentQrActive = $latestQr->is_active;

        $status = $latestQr->is_active ? 'activated' : 'deactivated';
        Flux::toast("QR code {$status} successfully!", 'QR Code ' . ucfirst($status), 3000, 'success');
    }

    public function deleteQrCode($id)
    {
        $qrScan = QrScan::findOrFail($id);

        if ($qrScan->completed_distribution) {
            Flux::toast('Cannot delete QR code that has completed distribution.', 'Error', 3000, 'danger');
            return;
        }

        $qrScan->delete();

        if ($qrScan->token === $this->currentQrToken) {
            $this->loadCurrentQrToken();
        }

        Flux::toast('QR code deleted successfully.', 'Deleted', 3000, 'success');
    }

    public function render()
    {
        $qrScans = QrScan::with('distribution')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.qr-code-management', [
            'qrScans' => $qrScans,
        ])->layout('components.layouts.app');
    }
}