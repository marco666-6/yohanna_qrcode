<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestNotification;

class HrdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:hrd']);
    }

    /**
     * HRD Dashboard
     */
    public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        // Today's statistics
        $totalEmployees = User::where('role', 'employee')->where('is_active', true)->count();
        $todayPresent = Attendance::where('date', $today)
            ->whereNotNull('check_in')
            ->count();
        $todayAbsent = $totalEmployees - $todayPresent;
        $todayLate = Attendance::where('date', $today)
            ->where('status', 'late')
            ->count();

        // Pending leave requests
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();

        // Monthly statistics
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');
        
        $monthlyStats = [
            'total_attendance' => Attendance::whereBetween('date', [$monthStart, $monthEnd])->count(),
            'on_time' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'on_time')->count(),
            'late' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'late')->count(),
            'incomplete' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'incomplete')->count(),
        ];

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Attendance chart data (last 7 days)
        $chartData = $this->getWeeklyChartData();

        return view('hrd.dashboard', compact(
            'totalEmployees',
            'todayPresent',
            'todayAbsent',
            'todayLate',
            'pendingLeaves',
            'monthlyStats',
            'recentActivities',
            'chartData'
        ));
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyChartData()
    {
        $dates = [];
        $onTimeData = [];
        $lateData = [];
        $absentData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('d M');

            $onTimeData[] = Attendance::where('date', $date)
                ->where('status', 'on_time')->count();
            
            $lateData[] = Attendance::where('date', $date)
                ->where('status', 'late')->count();
            
            $totalEmployees = User::where('role', 'employee')->where('is_active', true)->count();
            $present = Attendance::where('date', $date)->whereNotNull('check_in')->count();
            $absentData[] = $totalEmployees - $present;
        }

        return [
            'dates' => $dates,
            'onTime' => $onTimeData,
            'late' => $lateData,
            'absent' => $absentData,
        ];
    }

    /**
     * Attendance Report
     */
    public function attendanceReport(Request $request)
    {
        $query = Attendance::with(['user', 'shift']);

        // Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('date', $request->year)
                  ->whereMonth('date', $request->month);
        } else {
            // Default to current month
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => $query->count(),
            'on_time' => (clone $query)->where('status', 'on_time')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'incomplete' => (clone $query)->where('status', 'incomplete')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
        ];

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('hrd.attendance-report', compact('attendances', 'stats', 'employees'));
    }

    /**
     * Export Attendance to Excel
     */
    public function exportAttendanceExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        ActivityLog::log('EXPORT_ATTENDANCE_EXCEL', "Exported attendance from {$startDate} to {$endDate}");

        return Excel::download(
            new AttendanceExport($startDate, $endDate),
            'attendance_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    /**
     * Export Attendance to PDF
     */
    public function exportAttendancePdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = Attendance::with(['user', 'shift'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'total' => $attendances->count(),
            'on_time' => $attendances->where('status', 'on_time')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'incomplete' => $attendances->where('status', 'incomplete')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        ActivityLog::log('EXPORT_ATTENDANCE_PDF', "Exported attendance from {$startDate} to {$endDate}");

        $pdf = Pdf::loadView('hrd.exports.attendance-pdf', compact('attendances', 'stats', 'startDate', 'endDate'));
        
        return $pdf->download('attendance_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    /**
     * Leave Requests Management
     */
    public function leaveRequests(Request $request)
    {
        $query = LeaveRequest::with(['user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('hrd.leave-requests.index', compact('leaveRequests', 'stats'));
    }

    /**
     * Leave Request Detail
     */
    public function leaveRequestDetail($id)
    {
        $leaveRequest = LeaveRequest::with(['user', 'reviewer'])->findOrFail($id);
        return view('hrd.leave-requests.detail', compact('leaveRequest'));
    }

    /**
     * Approve Leave Request
     */
    public function approveLeaveRequest(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        // Create notification for employee
        Notification::create([
            'user_id' => $leaveRequest->user_id,
            'type' => 'leave_approved',
            'title' => 'Pengajuan Cuti Disetujui',
            'message' => 'Pengajuan cuti Anda dari ' . formatDate($leaveRequest->start_date) . ' sampai ' . formatDate($leaveRequest->end_date) . ' telah disetujui.',
            'created_at' => now(),
        ]);

        // Send email notification
        try {
            Mail::to($leaveRequest->user->email)->send(
                new LeaveRequestNotification($leaveRequest, 'approved')
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        ActivityLog::log('APPROVE_LEAVE', "Approved leave request for {$leaveRequest->user->name}");

        return redirect()->route('hrd.leave-requests')
            ->with('success', 'Pengajuan cuti berhasil disetujui');
    }

    /**
     * Reject Leave Request
     */
    public function rejectLeaveRequest(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $request->validate([
            'review_notes' => 'required|string|max:500',
        ], [
            'review_notes.required' => 'Alasan penolakan wajib diisi',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        // Create notification for employee
        Notification::create([
            'user_id' => $leaveRequest->user_id,
            'type' => 'leave_rejected',
            'title' => 'Pengajuan Cuti Ditolak',
            'message' => 'Pengajuan cuti Anda dari ' . formatDate($leaveRequest->start_date) . ' sampai ' . formatDate($leaveRequest->end_date) . ' ditolak. Alasan: ' . $request->review_notes,
            'created_at' => now(),
        ]);

        // Send email notification
        try {
            Mail::to($leaveRequest->user->email)->send(
                new LeaveRequestNotification($leaveRequest, 'rejected')
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        ActivityLog::log('REJECT_LEAVE', "Rejected leave request for {$leaveRequest->user->name}");

        return redirect()->route('hrd.leave-requests')
            ->with('success', 'Pengajuan cuti berhasil ditolak');
    }

    /**
     * Add correction notes to attendance
     */
    public function addAttendanceNotes(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'notes' => 'required|string|max:500',
        ], [
            'notes.required' => 'Catatan wajib diisi',
        ]);

        $attendance->update([
            'notes' => $request->notes,
        ]);

        // Notify employee
        Notification::create([
            'user_id' => $attendance->user_id,
            'type' => 'attendance_note',
            'title' => 'Catatan Kehadiran',
            'message' => 'HRD menambahkan catatan pada kehadiran Anda tanggal ' . formatDate($attendance->date) . ': ' . $request->notes,
            'created_at' => now(),
        ]);

        ActivityLog::log('ADD_ATTENDANCE_NOTES', "Added notes to attendance for {$attendance->user->name} on {$attendance->date}");

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }

    /**
     * Statistics Page
     */
    public function statistics()
    {
        // Overall statistics
        $totalEmployees = User::where('role', 'employee')->where('is_active', true)->count();
        $totalAttendance = Attendance::count();
        
        // Monthly data for chart
        $monthlyData = $this->getMonthlyStatistics();
        
        // Department statistics
        $departmentStats = User::where('role', 'employee')
            ->where('is_active', true)
            ->selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->get();

        // Current month statistics with absent count
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');
        
        $monthlyStats = [
            'total_attendance' => Attendance::whereBetween('date', [$monthStart, $monthEnd])->count(),
            'on_time' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'on_time')->count(),
            'late' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'late')->count(),
            'incomplete' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'incomplete')->count(),
            'absent' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'absent')->count(),
        ];

        return view('hrd.statistics', compact('totalEmployees', 'totalAttendance', 'monthlyData', 'departmentStats', 'monthlyStats'));
    }

    /**
     * Get monthly statistics for chart
     */
    private function getMonthlyStatistics()
    {
        $months = [];
        $onTimeData = [];
        $lateData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();

            $onTimeData[] = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'on_time')->count();
            
            $lateData[] = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')->count();
        }

        return [
            'months' => $months,
            'onTime' => $onTimeData,
            'late' => $lateData,
        ];
    }
}