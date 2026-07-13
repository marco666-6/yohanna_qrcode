<?php

return [
    'qr_code_expiry_seconds' => (int) env('QR_CODE_EXPIRY_SECONDS', 30),
    'qr_code_before_minutes' => (int) env('QR_CODE_BEFORE_MINUTES', 30),
    'qr_code_after_minutes' => (int) env('QR_CODE_AFTER_MINUTES', 45),
    'default_late_tolerance' => (int) env('DEFAULT_LATE_TOLERANCE', 15),
    'working_hours_minimum' => (int) env('WORKING_HOURS_MINIMUM', 8),
    'location' => [
        'enabled' => (bool) env('ATTENDANCE_LOCATION_ENABLED', false),
        'latitude' => env('ATTENDANCE_LOCATION_LATITUDE') !== null && env('ATTENDANCE_LOCATION_LATITUDE') !== '' ? (float) env('ATTENDANCE_LOCATION_LATITUDE') : null,
        'longitude' => env('ATTENDANCE_LOCATION_LONGITUDE') !== null && env('ATTENDANCE_LOCATION_LONGITUDE') !== '' ? (float) env('ATTENDANCE_LOCATION_LONGITUDE') : null,
        'radius_meters' => (int) env('ATTENDANCE_LOCATION_RADIUS_METERS', 100),
        'max_accuracy_meters' => (int) env('ATTENDANCE_LOCATION_MAX_ACCURACY_METERS', 150),
        'label' => env('ATTENDANCE_LOCATION_LABEL', 'Lokasi kantor'),
    ],
];
