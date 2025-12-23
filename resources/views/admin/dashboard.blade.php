@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.employees') }}">
            <i class="bi bi-people"></i> Kelola Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.shifts') }}">
            <i class="bi bi-clock-history"></i> Kelola Shift
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.attendances') }}">
            <i class="bi bi-calendar-check"></i> Kelola Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.qr-code') }}">
            <i class="bi bi-qr-code"></i> QR Code
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="page-title">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard Administrator
        </h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}! Berikut adalah ringkasan sistem hari ini.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Karyawan</p>
                        <h3 class="mb-0">{{ $totalEmployees }}</h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $activeEmployees }} Aktif
                        </small>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Hadir Hari Ini</p>
                        <h3 class="mb-0">{{ $todayAttendance }}</h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up me-1"></i>
                            {{ $todayOnTime }} Tepat Waktu
                        </small>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Terlambat Hari Ini</p>
                        <h3 class="mb-0">{{ $todayLate }}</h3>
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Perlu Perhatian
                        </small>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tidak Hadir</p>
                        <h3 class="mb-0">{{ $todayAbsent }}</h3>
                        <small class="text-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Hari Ini
                        </small>
                    </div>
                    <div class="text-danger" style="font-size: 2.5rem;">
                        <i class="bi bi-x-octagon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Active QR Codes -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-qr-code me-2"></i>
                    QR Code Aktif
                </h5>
                <a href="{{ route('admin.qr-code') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Kelola QR
                </a>
            </div>
            <div class="card-body">
                @if($activeQrCodes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Shift</th>
                                    <th>Tipe</th>
                                    <th>Kadaluarsa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeQrCodes as $qr)
                                <tr>
                                    <td>
                                        <strong>{{ $qr->shift->name ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        @if($qr->type === 'check_in')
                                            <span class="badge bg-success">Check-In</span>
                                        @else
                                            <span class="badge bg-info">Check-Out</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $qr->expires_at->format('H:i:s') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($qr->isValid())
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Expired
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-qr-code text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Tidak ada QR Code aktif saat ini</p>
                        <a href="{{ route('admin.qr-code') }}" class="btn btn-sm btn-primary">
                            Generate QR Code
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>
                    Aktivitas Terbaru
                </h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if($recentActivities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentActivities as $activity)
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    @if(str_contains($activity->action, 'LOGIN'))
                                        <i class="bi bi-box-arrow-in-right text-success fs-5"></i>
                                    @elseif(str_contains($activity->action, 'LOGOUT'))
                                        <i class="bi bi-box-arrow-right text-danger fs-5"></i>
                                    @elseif(str_contains($activity->action, 'CHECK'))
                                        <i class="bi bi-calendar-check text-primary fs-5"></i>
                                    @else
                                        <i class="bi bi-gear text-secondary fs-5"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $activity->user->name ?? 'System' }}</strong>
                                        <small class="text-muted">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <small class="text-muted">{{ $activity->description }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada aktivitas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admin.employees.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-person-plus d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Tambah Karyawan</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.shifts') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-clock d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Kelola Shift</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.attendances') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-calendar-check d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Kelola Kehadiran</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.employees.export') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-download d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Export Data</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="bi bi-calendar3 text-primary fs-3"></i>
                        <p class="mb-0 mt-2"><strong>{{ now()->format('d F Y') }}</strong></p>
                        <small class="text-muted">Tanggal Hari Ini</small>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-clock text-success fs-3"></i>
                        <p class="mb-0 mt-2"><strong id="currentTime">{{ now()->format('H:i:s') }}</strong></p>
                        <small class="text-muted">Waktu Saat Ini</small>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-diagram-3 text-info fs-3"></i>
                        <p class="mb-0 mt-2"><strong>{{ $totalShifts }}</strong></p>
                        <small class="text-muted">Total Shift</small>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-shield-check text-warning fs-3"></i>
                        <p class="mb-0 mt-2"><strong>Administrator</strong></p>
                        <small class="text-muted">Level Akses Anda</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update current time every second
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    setInterval(updateTime, 1000);
    updateTime();
</script>
@endpush