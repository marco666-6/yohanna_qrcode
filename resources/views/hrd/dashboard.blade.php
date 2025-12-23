@extends('layouts.app')

@section('title', 'Dashboard HRD')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('hrd.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.attendance-report') }}">
            <i class="bi bi-file-earmark-text"></i> Laporan Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
            @if($pendingLeaves > 0)
                <span class="badge bg-danger ms-2">{{ $pendingLeaves }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.statistics') }}">
            <i class="bi bi-graph-up"></i> Statistik
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="page-title">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard HRD
        </h2>
        <p class="text-muted">Selamat datang, <strong>{{ auth()->user()->name }}</strong>! - {{ formatDate(now()) }}</p>
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
                        <small class="text-primary">Karyawan Aktif</small>
                    </div>
                    <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
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
                        <h3 class="mb-0">{{ $todayPresent }}</h3>
                        <small class="text-success">
                            @if($totalEmployees > 0)
                                {{ number_format(($todayPresent / $totalEmployees) * 100, 1) }}%
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
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tidak Hadir</p>
                        <h3 class="mb-0">{{ $todayAbsent }}</h3>
                        <small class="text-danger">Hari Ini</small>
                    </div>
                    <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
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
                        <h3 class="mb-0">{{ $todayLate }}</h3>
                        <small class="text-warning">Hari Ini</small>
                    </div>
                    <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Leave Requests Alert -->
@if($pendingLeaves > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Perhatian!</strong> Ada <strong>{{ $pendingLeaves }}</strong> pengajuan cuti yang menunggu persetujuan Anda.
    <a href="{{ route('hrd.leave-requests') }}" class="alert-link">Lihat sekarang</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 mb-4">
    <!-- Attendance Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Grafik Kehadiran (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <div id="attendanceChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-month me-2"></i>
                    Statistik Bulan Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Kehadiran</span>
                        <strong>{{ $monthlyStats['total_attendance'] }}</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tepat Waktu</span>
                        <strong>{{ $monthlyStats['on_time'] }}</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        @php
                            $onTimePercentage = $monthlyStats['total_attendance'] > 0 
                                ? ($monthlyStats['on_time'] / $monthlyStats['total_attendance']) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $onTimePercentage }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Terlambat</span>
                        <strong>{{ $monthlyStats['late'] }}</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        @php
                            $latePercentage = $monthlyStats['total_attendance'] > 0 
                                ? ($monthlyStats['late'] / $monthlyStats['total_attendance']) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-warning" style="width: {{ $latePercentage }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Belum Check-out</span>
                        <strong>{{ $monthlyStats['incomplete'] }}</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        @php
                            $incompletePercentage = $monthlyStats['total_attendance'] > 0 
                                ? ($monthlyStats['incomplete'] / $monthlyStats['total_attendance']) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-info" style="width: {{ $incompletePercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('hrd.attendance-report') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Lihat Laporan
                    </a>
                    <a href="{{ route('hrd.leave-requests') }}" class="btn btn-warning">
                        <i class="bi bi-calendar-event me-2"></i>
                        Kelola Cuti
                        @if($pendingLeaves > 0)
                            <span class="badge bg-danger ms-2">{{ $pendingLeaves }}</span>
                        @endif
                    </a>
                    <a href="{{ route('hrd.statistics') }}" class="btn btn-info">
                        <i class="bi bi-graph-up me-2"></i>
                        Lihat Statistik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>
            Aktivitas Terkini
        </h5>
    </div>
    <div class="card-body">
        @if($recentActivities->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $activity->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($activity->user)
                                    <strong>{{ $activity->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $activity->user->employee_id }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $activity->action }}</span>
                            </td>
                            <td>
                                <small>{{ $activity->description ?? '-' }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Belum ada aktivitas terkini</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Attendance Chart
    Highcharts.chart('attendanceChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: @json($chartData['dates'])
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Karyawan'
            }
        },
        legend: {
            align: 'center',
            verticalAlign: 'bottom'
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        series: [{
            name: 'Tepat Waktu',
            data: @json($chartData['onTime']),
            color: '#198754'
        }, {
            name: 'Terlambat',
            data: @json($chartData['late']),
            color: '#ffc107'
        }, {
            name: 'Tidak Hadir',
            data: @json($chartData['absent']),
            color: '#dc3545'
        }],
        credits: {
            enabled: false
        }
    });
</script>
@endpush