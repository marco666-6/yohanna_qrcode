<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Shift;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    /**
     * Generate new QR code for check-in or check-out
     */
    public function generate(Request $request, QrCodeService $qrCodeService)
    {
        $type = $request->input('type', 'check_in'); // check_in or check_out
        $shiftId = $request->input('shift_id');
        $shift = Shift::findOrFail($shiftId);

        $window = getAttendanceWindow($shift, $type);
        if (!$window['is_open']) {
            return response()->json([
                'success' => false,
                'message' => 'QR hanya bisa digenerate saat window absensi untuk shift ini sedang terbuka.',
                'window' => [
                    'start' => $window['start']->toISOString(),
                    'end' => $window['end']->toISOString(),
                ],
            ], 422);
        }

        $qrCode = $qrCodeService->regenerateForShift($shift, $type);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'code' => $qrCode->code,
            'expires_at' => $qrCode->expires_at->toISOString(),
            'window' => [
                'start' => $window['start']->toISOString(),
                'end' => $window['end']->toISOString(),
            ],
        ]);
    }

    /**
     * Auto-generate QR codes for all active shifts
     */
    public function autoGenerate(QrCodeService $qrCodeService)
    {
        $generatedCodes = $qrCodeService->ensureCurrentWindowQRCodes();

        return response()->json([
            'success' => true,
            'generated_count' => $generatedCodes->count(),
            'codes' => $generatedCodes,
        ]);
    }

    /**
     * Get active QR codes
     */
    public function getActive()
    {
        $qrCodes = QrCode::with('shift')
            ->active()
            ->get()
            ->filter(fn ($qrCode) => $qrCode->isWithinWindow())
            ->values();

        return response()->json([
            'success' => true,
            'qr_codes' => $qrCodes,
        ]);
    }

    /**
     * Validate QR code
     */
    public function validate(Request $request)
    {
        $code = $request->input('code');

        $qrCode = QrCode::where('code', $code)
            ->with('shift')
            ->first();

        if (!$qrCode) {
            return response()->json([
                'valid' => false,
                'message' => 'QR Code tidak ditemukan',
            ]);
        }

        if (!$qrCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'QR Code sudah kadaluarsa',
            ]);
        }

        return response()->json([
            'valid' => true,
            'qr_code' => $qrCode,
            'message' => 'QR Code valid',
        ]);
    }

    /**
     * Display QR code page for admin
     */
    public function show()
    {
        return view('admin.qr-code');
    }
}
