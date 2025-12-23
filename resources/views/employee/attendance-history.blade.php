@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.scanner') }}">
            <i class="bi bi-qr-code-scan"></i> Scan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('employee.attendance-history') }}">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-clock-history me-2"></i>
                    Riwayat Absensi
                </h2>
                <p class="text-muted mb-0">Lihat riwayat kehadiran Anda</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('employee.attendance-history') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <select class="form-select" name="month">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (request('month', now()->month) == $i) ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tahun</label>
                <select class="form-select" name="year">
                    @for($year = now()->year; $year >= now()->year - 2; $year--)
                        <option value="{{ $year }}" {{ (request('year', now()->year) == $year) ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('employee.attendance-history') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kehadiran</p>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-calendar3 text-primary" style="font-size: 2rem;"></i>
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
                        <h3 class="mb-0">{{ $stats['on_time'] }}</h3>
                        <small class="text-success">
                            @if($stats['total'] > 0)
                                {{ number_format(($stats['on_time'] / $stats['total']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
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
                        <h3 class="mb-0">{{ $stats['late'] }}</h3>
                        <small class="text-warning">
                            @if($stats['total'] > 0)
                                {{ number_format(($stats['late'] / $stats['total']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                    <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
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
                        <h3 class="mb-0">{{ $stats['incomplete'] }}</h3>
                        <small class="text-info">
                            @if($stats['total'] > 0)
                                {{ number_format(($stats['incomplete'] / $stats['total']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                    <i class="bi bi-exclamation-circle text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>
            Riwayat Absensi
            @if(request('month') && request('year'))
                - {{ DateTime::createFromFormat('!m', request('month'))->format('F') }} {{ request('year') }}
            @else
                - {{ now()->format('F Y') }}
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if($attendances->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Jam</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>
                                <strong>{{ formatDate($attendance->date) }}</strong>
                                <br>
                                <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                            </td>
                            <td>
                                @if($attendance->shift)
                                    <span class="badge bg-secondary">
                                        {{ $attendance->shift->name }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ formatTime($attendance->shift->start_time) }} - {{ formatTime($attendance->shift->end_time) }}
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_in)
                                    <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                    <strong>{{ formatTime($attendance->check_in) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out)
                                    <i class="bi bi-box-arrow-right text-danger me-1"></i>
                                    <strong>{{ formatTime($attendance->check_out) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->total_hours > 0)
                                    <strong>{{ number_format($attendance->total_hours, 2) }} jam</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ getStatusBadge($attendance->status) }}">
                                    {{ getStatusText($attendance->status) }}
                                </span>
                            </td>
                            <td>
                                @if($attendance->notes)
                                    <button type="button" 
                                            class="btn btn-sm btn-info" 
                                            data-bs-toggle="tooltip" 
                                            title="{{ $attendance->notes }}">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="mt-3">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3 mb-3">Tidak ada riwayat absensi untuk periode yang dipilih</p>
                @if(!request('month') && !request('year'))
                    <a href="{{ route('employee.scanner') }}" class="btn btn-primary">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Mulai Absensi
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Summary Card -->
@if($attendances->count() > 0)
<div class="card mt-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="bi bi-graph-up me-2"></i>
            Ringkasan Periode Ini
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Performa Kehadiran</h6>
                <div class="progress mb-2" style="height: 25px;">
                    @php
                        $onTimePercentage = $stats['total'] > 0 ? ($stats['on_time'] / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: {{ $onTimePercentage }}%">
                        {{ number_format($onTimePercentage, 1) }}% Tepat Waktu
                    </div>
                </div>
                
                <div class="progress mb-2" style="height: 25px;">
                    @php
                        $latePercentage = $stats['total'] > 0 ? ($stats['late'] / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-warning" 
                         role="progressbar" 
                         style="width: {{ $latePercentage }}%">
                        {{ number_format($latePercentage, 1) }}% Terlambat
                    </div>
                </div>

                <div class="progress" style="height: 25px;">
                    @php
                        $incompletePercentage = $stats['total'] > 0 ? ($stats['incomplete'] / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-info" 
                         role="progressbar" 
                         style="width: {{ $incompletePercentage }}%">
                        {{ number_format($incompletePercentage, 1) }}% Belum Check-out
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h6 class="text-muted">Informasi Tambahan</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        Total hari hadir: <strong>{{ $stats['total'] }} hari</strong>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock text-success me-2"></i>
                        Total jam kerja: <strong>{{ number_format($attendances->sum('total_hours'), 2) }} jam</strong>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-graph-up text-info me-2"></i>
                        Rata-rata per hari: 
                        <strong>
                            @if($stats['total'] > 0)
                                {{ number_format($attendances->sum('total_hours') / $stats['total'], 2) }} jam
                            @else
                                0 jam
                            @endif
                        </strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
</script>
@endpush