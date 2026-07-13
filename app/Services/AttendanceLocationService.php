<?php

namespace App\Services;

use Illuminate\Http\Request;

class AttendanceLocationService
{
    public function isEnabled(): bool
    {
        return (bool) config('attendance.location.enabled', false);
    }

    public function getConfiguredLocation(): ?array
    {
        $latitude = config('attendance.location.latitude');
        $longitude = config('attendance.location.longitude');

        if ($latitude === null || $longitude === null) {
            return null;
        }

        return [
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'radius_meters' => (int) config('attendance.location.radius_meters', 100),
            'max_accuracy_meters' => (int) config('attendance.location.max_accuracy_meters', 150),
            'label' => config('attendance.location.label', 'Lokasi kantor'),
        ];
    }

    public function validateScanLocation(Request $request): array
    {
        if (!$this->isEnabled()) {
            return [
                'valid' => true,
                'message' => 'Validasi lokasi tidak aktif.',
                'distance_meters' => null,
                'accuracy_meters' => null,
            ];
        }

        $office = $this->getConfiguredLocation();
        if (!$office) {
            return [
                'valid' => false,
                'message' => 'Lokasi absensi belum dikonfigurasi oleh admin/server.',
                'distance_meters' => null,
                'accuracy_meters' => null,
            ];
        }

        if (!$request->filled('location_latitude') || !$request->filled('location_longitude')) {
            return [
                'valid' => false,
                'message' => 'Absensi membutuhkan izin lokasi perangkat. Aktifkan GPS/lokasi lalu coba lagi.',
                'distance_meters' => null,
                'accuracy_meters' => null,
            ];
        }

        $accuracy = $request->input('location_accuracy');
        $distance = $this->calculateDistanceMeters(
            (float) $request->input('location_latitude'),
            (float) $request->input('location_longitude'),
            $office['latitude'],
            $office['longitude']
        );
        $roundedDistance = round($distance, 2);
        $roundedAccuracy = $accuracy !== null ? round((float) $accuracy, 2) : null;

        if ($accuracy !== null && (float) $accuracy > $office['max_accuracy_meters']) {
            return [
                'valid' => false,
                'message' => 'Akurasi lokasi belum cukup baik. Pastikan GPS aktif, tunggu beberapa detik, lalu coba lagi.',
                'distance_meters' => $roundedDistance,
                'accuracy_meters' => $roundedAccuracy,
            ];
        }

        if ($distance > $office['radius_meters']) {
            return [
                'valid' => false,
                'message' => 'Absensi ditolak karena lokasi perangkat berada di luar radius ' . $office['radius_meters'] . ' meter dari ' . $office['label'] . '.',
                'distance_meters' => $roundedDistance,
                'accuracy_meters' => $roundedAccuracy,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Lokasi perangkat valid.',
            'distance_meters' => $roundedDistance,
            'accuracy_meters' => $roundedAccuracy,
        ];
    }

    public function calculateDistanceMeters(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $earthRadiusMeters = 6371000;

        $fromLatitudeRad = deg2rad($fromLatitude);
        $toLatitudeRad = deg2rad($toLatitude);
        $deltaLatitude = deg2rad($toLatitude - $fromLatitude);
        $deltaLongitude = deg2rad($toLongitude - $fromLongitude);

        $a = sin($deltaLatitude / 2) ** 2
            + cos($fromLatitudeRad) * cos($toLatitudeRad) * sin($deltaLongitude / 2) ** 2;

        return $earthRadiusMeters * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
