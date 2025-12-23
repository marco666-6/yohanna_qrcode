@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('employee.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.scanner') }}">
            <i class="bi bi-qr-code-scan"></i> Scan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.attendance-history') }}">
            <i class="bi bi-clock-history"></i> Riwayat Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.notifications') }}">
            <i class="bi bi-bell"></i> Notifikasi
            @if($unreadNotifications > 0)
                <span class="badge bg-danger ms-2">{{ $unreadNotifications }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.profile') }}">
            <i class="bi bi-person"></i> Profil
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="page-title">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </h2>
        <p class="text-muted">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
    </div>
</div>

<!-- Today's Attendance Status -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-day me-2"></i>
                    Status Absensi Hari Ini - {{ formatDate(now()) }}
                </h5>
            </div>
            <div class="card-body">
                @if($todayAttendance)
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="status-box">
                                <i class="bi bi-calendar-check text-primary" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2">Shift</h6>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $todayAttendance->shift->name ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="status-box">
                                <i class="bi bi-box-arrow-in-right text-success" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2">Check-in</h6>
                                <p class="mb-0">
                                    @if($todayAttendance->check_in)
                                        <strong>{{ formatTime($todayAttendance->check_in) }}</strong>
                                    @else
                                        <span class="text-muted">Belum Check-in</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="status-box">
                                <i class="bi bi-box-arrow-right text-danger" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2">Check-out</h6>
                                <p class="mb-0">
                                    @if($todayAttendance->check_out)
                                        <strong>{{ formatTime($todayAttendance->check_out) }}</strong>
                                    @else
                                        <span class="text-muted">Belum Check-out</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="status-box">
                                <i class="bi bi-alarm text-info" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ getStatusBadge($todayAttendance->status) }}">
                                        {{ getStatusText($todayAttendance->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($todayAttendance->check_in && $todayAttendance->check_out)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Anda telah menyelesaikan absensi hari ini. Total jam kerja: <strong>{{ number_format($todayAttendance->total_hours, 2) }} jam</strong>
                        </div>
                    @elseif($todayAttendance->check_in)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Anda sudah check-in. Jangan lupa check-out saat pulang!
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-3">Anda belum melakukan absensi hari ini</p>
                        <a href="{{ route('employee.scanner') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            Scan Absensi Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kehadiran</p>
                        <h3 class="mb-0">{{ $monthlyStats['total_days'] }}</h3>
                        <small class="text-muted">Bulan Ini</small>
                    </div>
                    <i class="bi bi-calendar3 text-primary" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tepat Waktu</p>
                        <h3 class="mb-0">{{ $monthlyStats['on_time'] }}</h3>
                        <small class="text-success">
                            @if($monthlyStats['total_days'] > 0)
                                {{ number_format(($monthlyStats['on_time'] / $monthlyStats['total_days']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Terlambat</p>
                        <h3 class="mb-0">{{ $monthlyStats['late'] }}</h3>
                        <small class="text-warning">
                            @if($monthlyStats['total_days'] > 0)
                                {{ number_format(($monthlyStats['late'] / $monthlyStats['total_days']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                    <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Belum Check-out</p>
                        <h3 class="mb-0">{{ $monthlyStats['incomplete'] }}</h3>
                        <small class="text-info">Bulan Ini</small>
                    </div>
                    <i class="bi bi-exclamation-circle text-info" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Attendance -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Riwayat Absensi (7 Hari Terakhir)
                </h5>
                <a href="{{ route('employee.attendance-history') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recentAttendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Total Jam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>{{ formatDate($attendance->date) }}</strong>
                                    </td>
                                    <td>
                                        @if($attendance->check_in)
                                            <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                            {{ formatTime($attendance->check_in) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            <i class="bi bi-box-arrow-right text-danger me-1"></i>
                                            {{ formatTime($attendance->check_out) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->total_hours > 0)
                                            {{ number_format($attendance->total_hours, 2) }} jam
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getStatusBadge($attendance->status) }}">
                                            {{ getStatusText($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada riwayat absensi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Informasi Anda
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Nama:</td>
                        <td><strong>{{ auth()->user()->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">ID:</td>
                        <td><strong>{{ auth()->user()->employee_id }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dept:</td>
                        <td>{{ auth()->user()->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Posisi:</td>
                        <td>{{ auth()->user()->position ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Shift:</td>
                        <td>
                            @if(auth()->user()->shift)
                                <span class="badge bg-info">{{ auth()->user()->shift->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Pengajuan Cuti
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-muted">Menunggu Persetujuan</p>
                        <h3 class="mb-0">{{ $pendingLeaves }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                </div>
                <a href="{{ route('employee.leave-requests') }}" class="btn btn-warning btn-sm w-100 mt-3">
                    <i class="bi bi-eye me-2"></i>
                    Lihat Detail
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('employee.scanner') }}" class="btn btn-primary">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Scan Absensi
                    </a>
                    <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-warning">
                        <i class="bi bi-plus-circle me-2"></i>
                        Ajukan Cuti
                    </a>
                    <a href="{{ route('employee.attendance-history') }}" class="btn btn-info">
                        <i class="bi bi-clock-history me-2"></i>
                        Riwayat Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-box {
        padding: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
</style>
@endpush