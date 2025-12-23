<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HrdController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password.post');

    // ADMIN ROUTES
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Employee Management
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [AdminController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/employees/{id}/edit', [AdminController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/employees/{id}', [AdminController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{id}', [AdminController::class, 'deleteEmployee'])->name('employees.delete');
        Route::get('/employees/{id}', [AdminController::class, 'employeeDetail'])->name('employees.detail');
        Route::get('/employees/export/excel', [AdminController::class, 'exportEmployees'])->name('employees.export');
        
        // Shift Management
        Route::get('/shifts', [AdminController::class, 'shifts'])->name('shifts');
        Route::post('/shifts', [AdminController::class, 'storeShift'])->name('shifts.store');
        Route::put('/shifts/{id}', [AdminController::class, 'updateShift'])->name('shifts.update');
        Route::delete('/shifts/{id}', [AdminController::class, 'deleteShift'])->name('shifts.delete');
        
        // Attendance Management
        Route::get('/attendances', [AdminController::class, 'attendances'])->name('attendances');
        Route::post('/attendances/force-add', [AdminController::class, 'forceAddAttendance'])->name('attendances.force-add');
        Route::put('/attendances/{id}', [AdminController::class, 'updateAttendance'])->name('attendances.update');
        
        // QR Code
        Route::get('/qr-code', [AdminController::class, 'qrCodePage'])->name('qr-code');
    });

    // EMPLOYEE ROUTES
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::get('/scanner', [EmployeeController::class, 'scanner'])->name('scanner');
        Route::get('/attendance-history', [EmployeeController::class, 'attendanceHistory'])->name('attendance-history');
        
        // Leave Requests
        Route::get('/leave-requests', [EmployeeController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('/leave-requests/create', [EmployeeController::class, 'createLeaveRequest'])->name('leave-requests.create');
        Route::post('/leave-requests', [EmployeeController::class, 'storeLeaveRequest'])->name('leave-requests.store');
        Route::get('/leave-requests/{id}', [EmployeeController::class, 'leaveRequestDetail'])->name('leave-requests.detail');
        
        // Notifications
        Route::get('/notifications', [EmployeeController::class, 'notifications'])->name('notifications');
        
        // Profile
        Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    });

    // HRD ROUTES
    Route::middleware('role:hrd')->prefix('hrd')->name('hrd.')->group(function () {
        Route::get('/dashboard', [HrdController::class, 'dashboard'])->name('dashboard');
        
        // Attendance Report
        Route::get('/attendance-report', [HrdController::class, 'attendanceReport'])->name('attendance-report');
        Route::get('/attendance-report/export/excel', [HrdController::class, 'exportAttendanceExcel'])->name('attendance-report.export.excel');
        Route::get('/attendance-report/export/pdf', [HrdController::class, 'exportAttendancePdf'])->name('attendance-report.export.pdf');
        Route::post('/attendance/{id}/notes', [HrdController::class, 'addAttendanceNotes'])->name('attendance.notes');
        
        // Leave Requests
        Route::get('/leave-requests', [HrdController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('/leave-requests/{id}', [HrdController::class, 'leaveRequestDetail'])->name('leave-requests.detail');
        Route::post('/leave-requests/{id}/approve', [HrdController::class, 'approveLeaveRequest'])->name('leave-requests.approve');
        Route::post('/leave-requests/{id}/reject', [HrdController::class, 'rejectLeaveRequest'])->name('leave-requests.reject');
        
        // Statistics
        Route::get('/statistics', [HrdController::class, 'statistics'])->name('statistics');
    });

    // ATTENDANCE ROUTES (for all authenticated users)
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('/attendance/today-status', [AttendanceController::class, 'todayStatus'])->name('attendance.today-status');

    // QR CODE API ROUTES
    Route::post('/qr-code/generate', [QrCodeController::class, 'generate'])->name('qr-code.generate');
    Route::get('/qr-code/auto-generate', [QrCodeController::class, 'autoGenerate'])->name('qr-code.auto-generate');
    Route::get('/qr-code/active', [QrCodeController::class, 'getActive'])->name('qr-code.active');
    Route::post('/qr-code/validate', [QrCodeController::class, 'validate'])->name('qr-code.validate');
});

Route::get('/test-email', function() {
    try {
        Mail::raw('Test email dari sistem absensi', function($message) {
            $message->to('marcophilips73@gmail.com')
                    ->subject('Test Email');
        });
        return 'Email berhasil dikirim!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});