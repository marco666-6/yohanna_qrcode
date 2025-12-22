<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    /**
     * Generate new QR code for check-in or check-out
     */
    public function generate(Request $request)
    {
        $type = $request->input('type', 'check_in'); // check_in or check_out
        $shiftId = $request->input('shift_id');

        // Deactivate old QR codes
        QrCode::where('type', $type)
            ->where('shift_id', $shiftId)
            ->update(['is_active' => false]);

        // Generate new QR code
        $qrCode = QrCode::create([
            'code' => $this->generateUniqueCode(),
            'type' => $type,
            'shift_id' => $shiftId,
            'generated_at' => now(),
            'expires_at' => now()->addSeconds(config('app.qr_code_expiry_seconds', 30)),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'code' => $qrCode->code,
            'expires_at' => $qrCode->expires_at->toISOString(),
        ]);
    }

    /**
     * Auto-generate QR codes for all active shifts
     */
    public function autoGenerate()
    {
        $shifts = Shift::active()->get();
        $currentTime = now();
        $generatedCodes = [];

        foreach ($shifts as $shift) {
            $shiftStart = Carbon::parse($shift->start_time);
            $shiftEnd = Carbon::parse($shift->end_time);

            // Check if within check-in window (30 min before to 45 min after)
            $checkInStart = $shiftStart->copy()->subMinutes(30);
            $checkInEnd = $shiftStart->copy()->addMinutes(45);

            // Check if within check-out window (30 min before to 45 min after)
            $checkOutStart = $shiftEnd->copy()->subMinutes(30);
            $checkOutEnd = $shiftEnd->copy()->addMinutes(45);

            // Generate check-in QR
            if ($currentTime->between($checkInStart, $checkInEnd)) {
                $qrCode = $this->generateForShift($shift, 'check_in');
                $generatedCodes[] = $qrCode;
            }

            // Generate check-out QR
            if ($currentTime->between($checkOutStart, $checkOutEnd)) {
                $qrCode = $this->generateForShift($shift, 'check_out');
                $generatedCodes[] = $qrCode;
            }
        }

        return response()->json([
            'success' => true,
            'generated_count' => count($generatedCodes),
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
            ->get();

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
     * Helper: Generate unique code
     */
    private function generateUniqueCode()
    {
        do {
            $code = Str::random(32);
        } while (QrCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Helper: Generate QR for specific shift
     */
    private function generateForShift(Shift $shift, $type)
    {
        // Deactivate old codes
        QrCode::where('shift_id', $shift->id)
            ->where('type', $type)
            ->update(['is_active' => false]);

        // Create new code
        return QrCode::create([
            'code' => $this->generateUniqueCode(),
            'type' => $type,
            'shift_id' => $shift->id,
            'generated_at' => now(),
            'expires_at' => now()->addSeconds(config('app.qr_code_expiry_seconds', 30)),
            'is_active' => true,
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