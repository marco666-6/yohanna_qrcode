@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.employees') }}">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-person-badge me-2"></i>
                    Detail Karyawan
                </h2>
                <p class="text-muted mb-0">Informasi lengkap karyawan</p>
            </div>
            <div>
                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning me-2">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Data
                </a>
                <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Employee Profile -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-large bg-primary text-white mx-auto mb-3">
                    <span>{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                </div>
                <h4 class="mb-1">{{ $employee->name }}</h4>
                <p class="text-muted mb-3">{{ $employee->employee_id }}</p>
                
                @if($employee->is_active)
                    <span class="badge bg-success mb-3">
                        <i class="bi bi-check-circle me-1"></i>
                        Karyawan Aktif
                    </span>
                @else
                    <span class="badge bg-danger mb-3">
                        <i class="bi bi-x-circle me-1"></i>
                        Tidak Aktif
                    </span>
                @endif

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Karyawan
                    </a>
                    <button class="btn btn-danger" onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->name }}')">
                        <i class="bi bi-trash me-2"></i>
                        Hapus Karyawan
                    </button>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Informasi Kontak
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope me-2 text-primary"></i>
                        <strong>{{ $employee->email }}</strong>
                    </div>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Telepon</small>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone me-2 text-success"></i>
                        <strong>{{ $employee->phone ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details -->
    <div class="col-lg-8">
        <!-- Employment Info -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-briefcase me-2"></i>
                    Informasi Pekerjaan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Departemen</label>
                        <p class="mb-0">
                            <strong>{{ $employee->department ?? '-' }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Posisi/Jabatan</label>
                        <p class="mb-0">
                            <strong>{{ $employee->position ?? '-' }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Shift Kerja</label>
                        <p class="mb-0">
                            @if($employee->shift)
                                <span class="badge bg-info">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $employee->shift->name }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ formatTime($employee->shift->start_time) }} - {{ formatTime($employee->shift->end_time) }}
                                </small>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Toleransi Keterlambatan</label>
                        <p class="mb-0">
                            @if($employee->shift)
                                <strong>{{ $employee->shift->late_tolerance }} Menit</strong>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Terdaftar Sejak</label>
                        <p class="mb-0">
                            <strong>{{ formatDate($employee->created_at) }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Terakhir Diperbarui</label>
                        <p class="mb-0">
                            <strong>{{ formatDate($employee->updated_at) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Statistics -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Statistik Kehadiran
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="stat-box">
                            <h3 class="mb-0 text-primary">{{ $stats['total_days'] }}</h3>
                            <small class="text-muted">Total Hari</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-box">
                            <h3 class="mb-0 text-success">{{ $stats['on_time'] }}</h3>
                            <small class="text-muted">Tepat Waktu</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-box">
                            <h3 class="mb-0 text-warning">{{ $stats['late'] }}</h3>
                            <small class="text-muted">Terlambat</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-box">
                            <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
                            <small class="text-muted">Tidak Hadir</small>
                        </div>
                    </div>
                </div>

                @if($stats['total_days'] > 0)
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Persentase Tepat Waktu</small>
                            <div class="progress" style="height: 25px;">
                                @php
                                    $onTimePercentage = ($stats['on_time'] / $stats['total_days']) * 100;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $onTimePercentage }}%">
                                    {{ number_format($onTimePercentage, 1) }}%
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Persentase Keterlambatan</small>
                            <div class="progress" style="height: 25px;">
                                @php
                                    $latePercentage = ($stats['late'] / $stats['total_days']) * 100;
                                @endphp
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $latePercentage }}%">
                                    {{ number_format($latePercentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    Riwayat Kehadiran Terbaru (30 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                @if($employee->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Total Jam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->attendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>{{ formatDate($attendance->date) }}</strong>
                                    </td>
                                    <td>
                                        @if($attendance->shift)
                                            <span class="badge bg-secondary">
                                                {{ $attendance->shift->name }}
                                            </span>
                                        @else
                                            -
                                        @endif
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
                                            <strong>{{ number_format($attendance->total_hours, 2) }} jam</strong>
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
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada riwayat kehadiran</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('admin.employees.delete', $employee->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
    }
    
    .stat-box {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    function deleteEmployee(id, name) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus karyawan:<br><strong>${name}</strong>?<br><br><span class="text-danger">Tindakan ini tidak dapat dibatalkan!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>
@endpush