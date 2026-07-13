<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\QrCode;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Services\AttendanceLocationService;
use App\Services\EmployeeAttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceNotification;

class AttendanceController extends Controller
{
    /**
     * Show scanning page for employee
     */
    public function showScanner(EmployeeAttendanceService $employeeAttendanceService)
    {
        $attendanceContext = $employeeAttendanceService->buildContext(auth()->user());
        $attendanceContextPayload = $employeeAttendanceService->serializeContext($attendanceContext);

        return view('employee.scanner', compact('attendanceContext', 'attendanceContextPayload'));
    }

    /**
     * Process QR code scan
     */
    public function scan(Request $request, AttendanceLocationService $attendanceLocationService)
    {
        $request->validate([
            'code' => 'required|string',
            'location_latitude' => 'nullable|numeric|between:-90,90',
            'location_longitude' => 'nullable|numeric|between:-180,180',
            'location_accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = auth()->user();
        $qrCode = QrCode::where('code', $request->code)
            ->with('shift')
            ->first();

        // Validate QR Code exists
        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid',
            ], 400);
        }

        // Validate QR Code is still active
        if (!$qrCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kadaluarsa',
            ], 400);
        }

        // Check user shift matches
        if ($user->shift_id != $qrCode->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak sesuai dengan shift Anda',
            ], 400);
        }

        if (!$qrCode->isWithinWindow()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code berada di luar window absensi yang diizinkan',
            ], 400);
        }

        $locationValidation = $attendanceLocationService->validateScanLocation($request);
        if (!$locationValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $locationValidation['message'],
                'distance_meters' => $locationValidation['distance_meters'],
                'accuracy_meters' => $locationValidation['accuracy_meters'],
            ], 400);
        }

        $today = now()->format('Y-m-d');

        if ($qrCode->type === 'check_in') {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();
            return $this->processCheckIn($user, $qrCode, $attendance, $locationValidation);
        } else {
            $attendance = Attendance::openForCheckout($user->id, $qrCode->shift_id)->first();
            return $this->processCheckOut($user, $qrCode, $attendance, $locationValidation);
        }
    }

    /**
     * Process check-in
     */
    private function processCheckIn($user, $qrCode, $attendance, array $locationValidation)
    {
        if ($attendance && $attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini',
            ], 400);
        }

        $checkInTime = now();
        $shift = $qrCode->shift;
        $shiftStart = Carbon::parse($shift->start_time);
        $tolerance = $shift->late_tolerance;

        // Determine status
        $status = $checkInTime->lte($shiftStart->copy()->addMinutes($tolerance)) 
            ? 'on_time' 
            : 'late';

        if ($attendance) {
            $attendance->update([
                'check_in' => $checkInTime->format('H:i:s'),
                'check_in_qr_id' => $qrCode->id,
                'status' => $status,
            ]);
        } else {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'shift_id' => $qrCode->shift_id,
                'date' => now()->format('Y-m-d'),
                'check_in' => $checkInTime->format('H:i:s'),
                'check_in_qr_id' => $qrCode->id,
                'status' => $status,
            ]);
        }

        // Log activity
        $locationLog = $locationValidation['distance_meters'] !== null
            ? " within {$locationValidation['distance_meters']}m from configured location"
            : '';
        ActivityLog::log('CHECK_IN', "Check-in successful at {$checkInTime->format('H:i:s')}{$locationLog}");

        // Send notification
        $this->sendNotification($user, 'check_in', $status);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil',
            'status' => $status,
            'check_in_time' => $checkInTime->format('H:i:s'),
            'distance_meters' => $locationValidation['distance_meters'],
            'accuracy_meters' => $locationValidation['accuracy_meters'],
            'attendance' => $attendance,
        ]);
    }

    /**
     * Process check-out
     */
    private function processCheckOut($user, $qrCode, $attendance, array $locationValidation)
    {
        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in',
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini',
            ], 400);
        }

        $checkOutTime = now();
        $checkInTime = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->check_in);
        if ($checkOutTime->lte($checkInTime)) {
            $checkOutTime->addDay();
        }
        $totalHours = $checkOutTime->diffInMinutes($checkInTime) / 60;

        $attendance->update([
            'check_out' => $checkOutTime->format('H:i:s'),
            'check_out_qr_id' => $qrCode->id,
            'total_hours' => round($totalHours, 2),
        ]);

        $attendance->updateStatus();

        // Log activity
        $locationLog = $locationValidation['distance_meters'] !== null
            ? " within {$locationValidation['distance_meters']}m from configured location"
            : '';
        ActivityLog::log('CHECK_OUT', "Check-out successful at {$checkOutTime->format('H:i:s')}{$locationLog}");

        // Send notification
        $this->sendNotification($user, 'check_out', $attendance->status);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil',
            'status' => $attendance->status,
            'check_out_time' => $checkOutTime->format('H:i:s'),
            'total_hours' => $attendance->total_hours,
            'distance_meters' => $locationValidation['distance_meters'],
            'accuracy_meters' => $locationValidation['accuracy_meters'],
            'attendance' => $attendance,
        ]);
    }

    /**
     * View attendance history
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::where('user_id', $user->id)
            ->with(['shift', 'checkInQr', 'checkOutQr'])
            ->orderBy('date', 'desc');

        if ($request->has('month') && $request->has('year')) {
            $query->whereYear('date', $request->year)
                  ->whereMonth('date', $request->month);
        }

        $attendances = $query->paginate(20);

        return view('employee.attendance-history', compact('attendances'));
    }

    /**
     * Get today's attendance status
     */
    public function todayStatus(EmployeeAttendanceService $employeeAttendanceService)
    {
        $user = auth()->user();
        $context = $employeeAttendanceService->buildContext($user);
        $attendance = $context['attendance'];

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'context' => $employeeAttendanceService->serializeContext($context),
        ]);
    }

    /**
     * Send notification
     */
    private function sendNotification($user, $type, $status)
    {
        $titles = [
            'check_in' => 'Check-in Berhasil',
            'check_out' => 'Check-out Berhasil',
        ];

        $messages = [
            'check_in' => $status === 'on_time' 
                ? 'Anda check-in tepat waktu pada ' . now()->format('H:i:s')
                : 'Anda check-in terlambat pada ' . now()->format('H:i:s'),
            'check_out' => 'Anda check-out pada ' . now()->format('H:i:s'),
        ];

        // Create in-app notification
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $titles[$type],
            'message' => $messages[$type],
            'created_at' => now(),
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new AttendanceNotification($user, $type, $status));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }
    }
}
