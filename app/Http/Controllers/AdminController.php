<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\QrCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('is_active', true)->count();
        $totalShifts = Shift::count();
        
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('date', $today)->count();
        $todayOnTime = Attendance::where('date', $today)->where('status', 'on_time')->count();
        $todayLate = Attendance::where('date', $today)->where('status', 'late')->count();
        $todayAbsent = User::where('role', 'employee')->where('is_active', true)->count() - $todayAttendance;

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Active QR Codes
        $activeQrCodes = QrCode::with('shift')
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'totalShifts',
            'todayAttendance',
            'todayOnTime',
            'todayLate',
            'todayAbsent',
            'recentActivities',
            'activeQrCodes'
        ));
    }

    /**
     * Employee Management - Index
     */
    public function employees()
    {
        $employees = User::with('shift')
            ->where('role', 'employee')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Employee Management - Create
     */
    public function createEmployee()
    {
        $shifts = Shift::active()->get();
        return view('admin.employees.create', compact('shifts'));
    }

    /**
     * Employee Management - Store
     */
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|string|max:50|unique:users,employee_id',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'shift_id' => 'required|exists:shifts,id',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'employee_id.required' => 'ID Karyawan wajib diisi',
            'employee_id.unique' => 'ID Karyawan sudah terdaftar',
            'shift_id.required' => 'Shift wajib dipilih',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'shift_id' => $validated['shift_id'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'is_active' => true,
        ]);

        ActivityLog::log('CREATE_EMPLOYEE', "Created employee: {$user->name}");

        return redirect()->route('admin.employees')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    /**
     * Employee Management - Edit
     */
    public function editEmployee($id)
    {
        $employee = User::findOrFail($id);
        $shifts = Shift::active()->get();
        return view('admin.employees.edit', compact('employee', 'shifts'));
    }

    /**
     * Employee Management - Update
     */
    public function updateEmployee(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'employee_id' => 'required|string|max:50|unique:users,employee_id,' . $id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'shift_id' => 'required|exists:shifts,id',
            'is_active' => 'required|boolean',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'shift_id' => $validated['shift_id'],
            'is_active' => $validated['is_active'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $employee->update($data);

        ActivityLog::log('UPDATE_EMPLOYEE', "Updated employee: {$employee->name}");

        return redirect()->route('admin.employees')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }

    /**
     * Employee Management - Delete
     */
    public function deleteEmployee($id)
    {
        $employee = User::findOrFail($id);
        $name = $employee->name;
        
        $employee->delete();

        ActivityLog::log('DELETE_EMPLOYEE', "Deleted employee: {$name}");

        return redirect()->route('admin.employees')
            ->with('success', 'Karyawan berhasil dihapus');
    }

    /**
     * Employee Management - Detail
     */
    public function employeeDetail($id)
    {
        $employee = User::with(['shift', 'attendances' => function($q) {
            $q->orderBy('date', 'desc')->limit(30);
        }])->findOrFail($id);

        $stats = [
            'total_days' => Attendance::where('user_id', $id)->count(),
            'on_time' => Attendance::where('user_id', $id)->where('status', 'on_time')->count(),
            'late' => Attendance::where('user_id', $id)->where('status', 'late')->count(),
            'absent' => Attendance::where('user_id', $id)->where('status', 'absent')->count(),
        ];

        return view('admin.employees.detail', compact('employee', 'stats'));
    }

    /**
     * Export Employees to Excel
     */
    public function exportEmployees()
    {
        ActivityLog::log('EXPORT_EMPLOYEES', 'Exported employees data to Excel');
        
        return Excel::download(new EmployeesExport, 'employees_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Shift Management - Index
     */
    public function shifts()
    {
        $shifts = Shift::withCount('users')->orderBy('start_time')->get();
        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Shift Management - Store
     */
    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_tolerance' => 'required|integer|min:0|max:60',
        ], [
            'name.required' => 'Nama shift wajib diisi',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'end_time.required' => 'Waktu selesai wajib diisi',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'late_tolerance.required' => 'Toleransi keterlambatan wajib diisi',
        ]);

        $shift = Shift::create($validated);

        ActivityLog::log('CREATE_SHIFT', "Created shift: {$shift->name}");

        return redirect()->route('admin.shifts')
            ->with('success', 'Shift berhasil ditambahkan');
    }

    /**
     * Shift Management - Update
     */
    public function updateShift(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_tolerance' => 'required|integer|min:0|max:60',
            'is_active' => 'required|boolean',
        ]);

        $shift->update($validated);

        ActivityLog::log('UPDATE_SHIFT', "Updated shift: {$shift->name}");

        return redirect()->route('admin.shifts')
            ->with('success', 'Shift berhasil diperbarui');
    }

    /**
     * Shift Management - Delete
     */
    public function deleteShift($id)
    {
        $shift = Shift::findOrFail($id);
        
        if ($shift->users()->count() > 0) {
            return redirect()->route('admin.shifts')
                ->with('error', 'Tidak dapat menghapus shift yang masih digunakan karyawan');
        }

        $name = $shift->name;
        $shift->delete();

        ActivityLog::log('DELETE_SHIFT', "Deleted shift: {$name}");

        return redirect()->route('admin.shifts')
            ->with('success', 'Shift berhasil dihapus');
    }

    /**
     * Attendance Management - Index
     */
    public function attendances(Request $request)
    {
        $query = Attendance::with(['user', 'shift']);

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', now()->format('Y-m-d'));
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

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Attendance Management - Force Add
     */
    public function forceAddAttendance(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:on_time,late,incomplete,absent',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $data = [
            'user_id' => $validated['user_id'],
            'shift_id' => $user->shift_id,
            'date' => $validated['date'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ];

        if ($validated['check_in'] && $validated['check_out']) {
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $data['total_hours'] = $checkOut->diffInMinutes($checkIn) / 60;
        }

        Attendance::updateOrCreate(
            ['user_id' => $validated['user_id'], 'date' => $validated['date']],
            $data
        );

        ActivityLog::log('FORCE_ADD_ATTENDANCE', "Force added attendance for {$user->name} on {$validated['date']}");

        return redirect()->route('admin.attendances')
            ->with('success', 'Data kehadiran berhasil ditambahkan');
    }

    /**
     * Attendance Management - Update
     */
    public function updateAttendance(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:on_time,late,incomplete,absent',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ];

        if ($validated['check_in'] && $validated['check_out']) {
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $data['total_hours'] = $checkOut->diffInMinutes($checkIn) / 60;
        }

        $attendance->update($data);

        ActivityLog::log('UPDATE_ATTENDANCE', "Updated attendance for {$attendance->user->name} on {$attendance->date}");

        return redirect()->route('admin.attendances')
            ->with('success', 'Data kehadiran berhasil diperbarui');
    }

    /**
     * QR Code Page
     */
    public function qrCodePage()
    {
        $shifts = Shift::active()->get();
        return view('admin.qr-code', compact('shifts'));
    }
}