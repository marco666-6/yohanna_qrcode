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
use Illuminate\Database\Eloquent\Builder;

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
        $today = now()->toDateString();
        
        // Today's statistics
        $totalEmployees = User::where('role', 'employee')->where('is_active', true)->count();
        $todayPresent = Attendance::where('date', $today)
            ->whereNotNull('check_in')
            ->count();
        $todayAbsent = $totalEmployees - $todayPresent;
        $todayLate = Attendance::where('date', $today)
            ->where('status', 'late')
            ->count();
        $todayIncomplete = Attendance::where('date', $today)
            ->where('status', 'incomplete')
            ->count();

        // Pending leave requests
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();

        // Monthly statistics
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $monthlyQuery = Attendance::query()->whereBetween('date', [$monthStart, $monthEnd]);
        
        $monthlyStats = [
            'total_attendance' => (clone $monthlyQuery)->count(),
            'on_time' => (clone $monthlyQuery)->where('status', 'on_time')->count(),
            'late' => (clone $monthlyQuery)->where('status', 'late')->count(),
            'incomplete' => (clone $monthlyQuery)->where('status', 'incomplete')->count(),
            'hours' => round((float) (clone $monthlyQuery)->sum('total_hours'), 2),
        ];

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $chartData = $this->getWeeklyChartData();

        $departmentStats = User::query()
            ->employees()
            ->active()
            ->selectRaw("COALESCE(NULLIF(department, ''), 'Belum diatur') as department_name, COUNT(*) as total")
            ->groupBy('department_name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $lateLeaders = User::query()
            ->employees()
            ->withCount(['attendances as late_count' => function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('date', [$monthStart, $monthEnd])->where('status', 'late');
            }])
            ->orderByDesc('late_count')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $pendingLeaveItems = LeaveRequest::query()
            ->with('user')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        return view('hrd.dashboard', compact(
            'totalEmployees',
            'todayPresent',
            'todayAbsent',
            'todayLate',
            'todayIncomplete',
            'pendingLeaves',
            'monthlyStats',
            'recentActivities',
            'chartData',
            'departmentStats',
            'lateLeaders',
            'pendingLeaveItems'
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
        $perPage = min(max((int) $request->input('per_page', 20), 10), 100);
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

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('user', function (Builder $userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $statsQuery = clone $query;
        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Statistics
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'on_time' => (clone $statsQuery)->where('status', 'on_time')->count(),
            'late' => (clone $statsQuery)->where('status', 'late')->count(),
            'incomplete' => (clone $statsQuery)->where('status', 'incomplete')->count(),
            'absent' => (clone $statsQuery)->where('status', 'absent')->count(),
            'hours' => round((float) (clone $statsQuery)->sum('total_hours'), 2),
        ];

        $chartBase = (clone $statsQuery)
            ->selectRaw('DATE(date) as report_date')
            ->selectRaw("SUM(CASE WHEN status = 'on_time' THEN 1 ELSE 0 END) as on_time_total")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_total")
            ->selectRaw("SUM(CASE WHEN status = 'incomplete' THEN 1 ELSE 0 END) as incomplete_total")
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->limit(14)
            ->get();

        $reportChart = [
            'labels' => $chartBase->pluck('report_date')->map(fn ($date) => Carbon::parse($date)->translatedFormat('d M')),
            'on_time' => $chartBase->pluck('on_time_total'),
            'late' => $chartBase->pluck('late_total'),
            'incomplete' => $chartBase->pluck('incomplete_total'),
        ];

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('hrd.attendance-report', compact('attendances', 'stats', 'employees', 'perPage', 'reportChart'));
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
        $perPage = min(max((int) $request->input('per_page', 15), 10), 100);
        $query = LeaveRequest::with(['user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('user', function (Builder $userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $stats = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('hrd.leave-requests.index', compact('leaveRequests', 'stats', 'perPage'));
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
