<?php

if (!function_exists('formatDate')) {
    /**
     * Format date to Indonesian format
     */
    function formatDate($date, $format = 'd F Y')
    {
        if (!$date) return '-';
        
        $months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        $formatted = \Carbon\Carbon::parse($date)->format($format);
        
        return str_replace(array_keys($months), array_values($months), $formatted);
    }
}

if (!function_exists('formatTime')) {
    /**
     * Format time
     */
    function formatTime($time)
    {
        if (!$time) return '-';
        return \Carbon\Carbon::parse($time)->format('H:i');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime
     */
    function formatDateTime($datetime)
    {
        if (!$datetime) return '-';
        return formatDate($datetime, 'd F Y') . ' ' . formatTime($datetime);
    }
}

if (!function_exists('getStatusBadge')) {
    /**
     * Get Bootstrap badge class for attendance status
     */
    function getStatusBadge($status)
    {
        return match($status) {
            'on_time' => 'success',
            'late' => 'warning',
            'incomplete' => 'info',
            'absent' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('getStatusText')) {
    /**
     * Get Indonesian text for attendance status
     */
    function getStatusText($status)
    {
        return match($status) {
            'on_time' => 'Tepat Waktu',
            'late' => 'Terlambat',
            'incomplete' => 'Belum Check-out',
            'absent' => 'Tidak Hadir',
            default => 'Tidak Diketahui',
        };
    }
}

if (!function_exists('getLeaveTypeBadge')) {
    /**
     * Get Bootstrap badge class for leave type
     */
    function getLeaveTypeBadge($type)
    {
        return match($type) {
            'sick' => 'warning',
            'annual' => 'primary',
            'unpaid' => 'secondary',
            'other' => 'info',
            default => 'secondary',
        };
    }
}

if (!function_exists('getLeaveTypeText')) {
    /**
     * Get Indonesian text for leave type
     */
    function getLeaveTypeText($type)
    {
        return match($type) {
            'sick' => 'Cuti Sakit',
            'annual' => 'Cuti Tahunan',
            'unpaid' => 'Cuti Tanpa Bayaran',
            'other' => 'Lainnya',
            default => 'Tidak Diketahui',
        };
    }
}

if (!function_exists('getLeaveStatusBadge')) {
    /**
     * Get Bootstrap badge class for leave status
     */
    function getLeaveStatusBadge($status)
    {
        return match($status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('getLeaveStatusText')) {
    /**
     * Get Indonesian text for leave status
     */
    function getLeaveStatusText($status)
    {
        return match($status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }
}

if (!function_exists('getRoleBadge')) {
    /**
     * Get Bootstrap badge class for user role
     */
    function getRoleBadge($role)
    {
        return match($role) {
            'admin' => 'danger',
            'hrd' => 'primary',
            'employee' => 'success',
            default => 'secondary',
        };
    }
}

if (!function_exists('getRoleText')) {
    /**
     * Get Indonesian text for user role
     */
    function getRoleText($role)
    {
        return match($role) {
            'admin' => 'Administrator',
            'hrd' => 'HRD',
            'employee' => 'Karyawan',
            default => 'Tidak Diketahui',
        };
    }
}

if (!function_exists('calculateWorkingDays')) {
    /**
     * Calculate working days between two dates (excluding weekends)
     */
    function calculateWorkingDays($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        $days = 0;
        
        while ($start->lte($end)) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }
        
        return $days;
    }
}

if (!function_exists('isWithinAttendanceWindow')) {
    /**
     * Check if current time is within attendance window
     */
    function isWithinAttendanceWindow($shift, $type = 'check_in')
    {
        $now = now();
        $targetTime = $type === 'check_in' 
            ? \Carbon\Carbon::parse($shift->start_time)
            : \Carbon\Carbon::parse($shift->end_time);
        
        $windowStart = $targetTime->copy()->subMinutes(config('attendance.qr_code_before_minutes', 30));
        $windowEnd = $targetTime->copy()->addMinutes(config('attendance.qr_code_after_minutes', 45));
        
        return $now->between($windowStart, $windowEnd);
    }
}

if (!function_exists('getAttendanceWindow')) {
    /**
     * Return attendance window details for a shift and QR type.
     */
    function getAttendanceWindow($shift, $type = 'check_in')
    {
        $targetTime = $type === 'check_in'
            ? \Carbon\Carbon::parse($shift->start_time)
            : \Carbon\Carbon::parse($shift->end_time);

        $windowStart = $targetTime->copy()->subMinutes(config('attendance.qr_code_before_minutes', 30));
        $windowEnd = $targetTime->copy()->addMinutes(config('attendance.qr_code_after_minutes', 45));

        return [
            'target' => $targetTime,
            'start' => $windowStart,
            'end' => $windowEnd,
            'is_open' => now()->between($windowStart, $windowEnd),
        ];
    }
}

if (!function_exists('attendancePercentage')) {
    /**
     * Safely calculate a percentage.
     */
    function attendancePercentage($value, $total, $decimals = 1)
    {
        if ((float) $total <= 0) {
            return 0;
        }

        return round(((float) $value / (float) $total) * 100, $decimals);
    }
}

if (!function_exists('attendanceStatusIcon')) {
    /**
     * Get icon for attendance status.
     */
    function attendanceStatusIcon($status)
    {
        return match($status) {
            'on_time' => 'bi-check-circle',
            'late' => 'bi-alarm',
            'incomplete' => 'bi-hourglass-split',
            'absent' => 'bi-x-octagon',
            default => 'bi-question-circle',
        };
    }
}
