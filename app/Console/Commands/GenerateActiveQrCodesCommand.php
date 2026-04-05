<?php

namespace App\Console\Commands;

use App\Services\QrCodeService;
use Illuminate\Console\Command;

class GenerateActiveQrCodesCommand extends Command
{
    protected $signature = 'attendance:generate-active-qr {--quiet-output : Suppress informational output}';

    protected $description = 'Generate and maintain active QR codes for attendance windows';

    public function handle(QrCodeService $qrCodeService): int
    {
        $codes = $qrCodeService->ensureCurrentWindowQRCodes();

        if (!$this->option('quiet-output')) {
            $this->info("QR aktif dipastikan untuk {$codes->count()} window.");
        }

        return self::SUCCESS;
    }
}
