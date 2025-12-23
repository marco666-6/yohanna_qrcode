<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:employee']);
    }

    /**
     * Employee Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');
        
        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->with('shift')
            ->first();

        // Monthly statistics
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');
        
        $monthlyStats = [
            'total_days' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->count(),
            'on_time' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'on_time')
                ->count(),
            'late' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'late')
                ->count(),
            'incomplete' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'incomplete')
                ->count(),
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

        return view('employee.dashboard', compact(
            'todayAttendance',
            'monthlyStats',
            'recentAttendances',
            'pendingLeaves',
            'unreadNotifications'
        ));
    }

    /**
     * Scanner Page
     */
    public function scanner()
    {
        return view('employee.scanner');
    }

    /**
     * Attendance History
     */
    public function attendanceHistory(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::where('user_id', $user->id)->with('shift');

        // Filter by month and year
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('date', $request->year)
                  ->whereMonth('date', $request->month);
        } else {
            // Default to current month
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        // Statistics for the selected period
        $stats = [
            'total' => $attendances->total(),
            'on_time' => Attendance::where('user_id', $user->id)
                ->whereYear('date', $request->year ?? now()->year)
                ->whereMonth('date', $request->month ?? now()->month)
                ->where('status', 'on_time')
                ->count(),
            'late' => Attendance::where('user_id', $user->id)
                ->whereYear('date', $request->year ?? now()->year)
                ->whereMonth('date', $request->month ?? now()->month)
                ->where('status', 'late')
                ->count(),
            'incomplete' => Attendance::where('user_id', $user->id)
                ->whereYear('date', $request->year ?? now()->year)
                ->whereMonth('date', $request->month ?? now()->month)
                ->where('status', 'incomplete')
                ->count(),
        ];

        return view('employee.attendance-history', compact('attendances', 'stats'));
    }

    /**
     * Leave Requests - Index
     */
    public function leaveRequests()
    {
        $user = auth()->user();
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('employee.leave-requests.index', compact('leaveRequests'));
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