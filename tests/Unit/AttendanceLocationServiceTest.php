<?php

namespace Tests\Unit;

use App\Services\AttendanceLocationService;
use PHPUnit\Framework\TestCase;

class AttendanceLocationServiceTest extends TestCase
{
    public function test_distance_between_same_coordinates_is_zero(): void
    {
        $service = new AttendanceLocationService();

        $distance = $service->calculateDistanceMeters(-6.2, 106.816666, -6.2, 106.816666);

        $this->assertSame(0.0, $distance);
    }

    public function test_distance_between_nearby_coordinates_is_calculated_in_meters(): void
    {
        $service = new AttendanceLocationService();

        $distance = $service->calculateDistanceMeters(-6.2, 106.816666, -6.2009, 106.816666);

        $this->assertGreaterThan(99, $distance);
        $this->assertLessThan(101, $distance);
    }
}
