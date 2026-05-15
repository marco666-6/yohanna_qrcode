<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Services\EmployeeAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:employee']);
    }

    /**
     * Employee Dashboard
     */
    public function dashboard(EmployeeAttendanceService $employeeAttendanceService)
    {
        $user = auth()->user();
        $attendanceContext = $employeeAttendanceService->buildContext($user);
        $todayAttendance = $attendanceContext['attendance'];

        // Monthly statistics
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $monthlyQuery = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd]);
        
        $monthlyStats = [
            'total_days' => (clone $monthlyQuery)->count(),
            'on_time' => (clone $monthlyQuery)->where('status', 'on_time')->count(),
            'late' => (clone $monthlyQuery)->where('status', 'late')->count(),
            'incomplete' => (clone $monthlyQuery)->where('status', 'incomplete')->count(),
            'hours' => round((float) (clone $monthlyQuery)->sum('total_hours'), 2),
        ];

        // Recent attendances
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->with('shift')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Pending leave requests
        $pendingLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Unread notifications
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $recentNotifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $upcomingLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        $attendanceTrend = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [now()->copy()->subDays(13)->toDateString(), now()->toDateString()])
            ->orderBy('date')
            ->get()
            ->groupBy(fn ($attendance) => $attendance->date->format('Y-m-d'));

        $trendLabels = collect(range(13, 0))->map(fn ($day) => now()->subDays($day)->translatedFormat('d M'));
        $trendPresent = collect(range(13, 0))->map(function ($day) use ($attendanceTrend) {
            $key = now()->subDays($day)->format('Y-m-d');
            return optional($attendanceTrend->get($key)?->first())->check_in ? 1 : 0;
        });
        $trendHours = collect(range(13, 0))->map(function ($day) use ($attendanceTrend) {
            $key = now()->subDays($day)->format('Y-m-d');
            return round((float) optional($attendanceTrend->get($key)?->first())->total_hours, 2);
        });

        return view('employee.dashboard', compact(
            'todayAttendance',
            'monthlyStats',
            'recentAttendances',
            'pendingLeaves',
            'unreadNotifications',
            'recentNotifications',
            'upcomingLeaves',
            'trendLabels',
            'trendPresent',
            'trendHours',
            'attendanceContext'
        ));
    }

    /**
     * Scanner Page
     */
    public function scanner(EmployeeAttendanceService $employeeAttendanceService)
    {
        $attendanceContext = $employeeAttendanceService->buildContext(auth()->user());
        $attendanceContextPayload = $employeeAttendanceService->serializeContext($attendanceContext);

        return view('employee.scanner', compact('attendanceContext', 'attendanceContextPayload'));
    }

    /**
     * Attendance History
     */
    public function attendanceHistory(Request $request)
    {
        $user = auth()->user();
        $perPage = min(max((int) $request->input('per_page', 15), 10), 100);
        $selectedMonth = (int) ($request->input('month') ?: now()->month);
        $selectedYear = (int) ($request->input('year') ?: now()->year);
        $query = Attendance::where('user_id', $user->id)
            ->with('shift')
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->when($request->filled('status'), fn (Builder $builder) => $builder->where('status', $request->status));

        $statsQuery = clone $query;
        $attendances = $query->orderBy('date', 'desc')->paginate($perPage)->withQueryString();

        // Statistics for the selected period
        $stats = [
            'total' => $attendances->total(),
            'on_time' => (clone $statsQuery)->where('status', 'on_time')->count(),
            'late' => (clone $statsQuery)->where('status', 'late')->count(),
            'incomplete' => (clone $statsQuery)->where('status', 'incomplete')->count(),
            'hours' => round((float) (clone $statsQuery)->sum('total_hours'), 2),
        ];

        return view('employee.attendance-history', compact('attendances', 'stats', 'selectedMonth', 'selectedYear', 'perPage'));
    }

    /**
     * Leave Requests - Index
     */
    public function leaveRequests(Request $request)
    {
        $user = auth()->user();
        $perPage = min(max((int) $request->input('per_page', 10), 10), 100);
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('reviewer')
            ->when($request->filled('status'), fn (Builder $builder) => $builder->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $leaveStats = [
            'pending' => LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('employee.leave-requests.index', compact('leaveRequests', 'leaveStats', 'perPage'));
    }

    /**
     * Leave Requests - Create
     */
    public function createLeaveRequest()
    {
        return view('employee.leave-requests.create');
    }

    /**
     * Leave Requests - Store
     */
    public function storeLeaveRequest(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:sick,annual,unpaid,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'leave_type.required' => 'Jenis cuti wajib dipilih',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'reason.required' => 'Alasan wajib diisi',
            'attachment.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'attachment.max' => 'Ukuran file maksimal 2MB',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('leave_attachments', $filename, 'public');
            $data['attachment'] = $path;
        }

        $leaveRequest = LeaveRequest::create($data);

        ActivityLog::log('CREATE_LEAVE_REQUEST', "Created leave request from {$validated['start_date']} to {$validated['end_date']}");

        // Notify HRD
        $hrdUsers = \App\Models\User::where('role', 'hrd')->get();
        foreach ($hrdUsers as $hrd) {
            Notification::create([
                'user_id' => $hrd->id,
                'type' => 'leave_request',
                'title' => 'Pengajuan Cuti Baru',
                'message' => auth()->user()->name . ' mengajukan cuti dari ' . formatDate($validated['start_date']) . ' sampai ' . formatDate($validated['end_date']),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('employee.leave-requests')
            ->with('success', 'Pengajuan cuti berhasil dikirim');
    }

    /**
     * Leave Requests - Detail
     */
    public function leaveRequestDetail($id)
    {
        $leaveRequest = LeaveRequest::where('user_id', auth()->id())
            ->with('reviewer')
            ->findOrFail($id);

        return view('employee.leave-requests.detail', compact('leaveRequest'));
    }

    /**
     * Notifications
     */
    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Mark all as read
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('employee.notifications', compact('notifications'));
    }

    /**
     * Profile
     */
    public function profile()
    {
        $user = auth()->user()->load('shift');
        return view('employee.profile', compact('user'));
    }
}
