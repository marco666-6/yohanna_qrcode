<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\Shift;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QrCodeService
{
    public function ensureCurrentWindowQRCodes(): Collection
    {
        $results = collect();

        foreach (Shift::active()->get() as $shift) {
            foreach (['check_in', 'check_out'] as $type) {
                $window = getAttendanceWindow($shift, $type);

                if (!$window['is_open']) {
                    QrCode::where('shift_id', $shift->id)
                        ->where('type', $type)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                    continue;
                }

                $results->push($this->ensureActiveCode($shift, $type));
            }
        }

        return $results->filter()->values();
    }

    public function regenerateForShift(Shift $shift, string $type): QrCode
    {
        QrCode::where('shift_id', $shift->id)
            ->where('type', $type)
            ->update(['is_active' => false]);

        return $this->createCode($shift, $type);
    }

    public function ensureActiveCode(Shift $shift, string $type): ?QrCode
    {
        $activeCode = QrCode::query()
            ->where('shift_id', $shift->id)
            ->where('type', $type)
            ->where('is_active', true)
            ->where('expires_at', '>', now()->addSeconds(10))
            ->latest('expires_at')
            ->first();

        if ($activeCode) {
            return $activeCode;
        }

        QrCode::where('shift_id', $shift->id)
            ->where('type', $type)
            ->update(['is_active' => false]);

        return $this->createCode($shift, $type);
    }

    protected function createCode(Shift $shift, string $type): QrCode
    {
        return QrCode::create([
            'code' => $this->generateUniqueCode(),
            'type' => $type,
            'shift_id' => $shift->id,
            'generated_at' => now(),
            'expires_at' => now()->addSeconds(config('attendance.qr_code_expiry_seconds', 30)),
            'is_active' => true,
        ]);
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = Str::random(32);
        } while (QrCode::where('code', $code)->exists());

        return $code;
    }
}
