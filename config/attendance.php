<?php

return [
    'qr_code_expiry_seconds' => (int) env('QR_CODE_EXPIRY_SECONDS', 30),
    'qr_code_before_minutes' => (int) env('QR_CODE_BEFORE_MINUTES', 30),
    'qr_code_after_minutes' => (int) env('QR_CODE_AFTER_MINUTES', 45),
    'default_late_tolerance' => (int) env('DEFAULT_LATE_TOLERANCE', 15),
    'working_hours_minimum' => (int) env('WORKING_HOURS_MINIMUM', 8),
];
