@extends('layouts.app')

@section('title', 'Statistik Kehadiran')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.dashboard') }}">
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
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('hrd.statistics') }}">
            <i class="bi bi-graph-up"></i> Statistik
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="page-title mb-4">
            <i class="bi bi-graph-up me-2"></i>
            Statistik & Analisis Kehadiran
        </h2>
    </div>
</div>

<!-- Overall Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Karyawan</p>
                        <h2 class="mb-0">{{ $totalEmployees }}</h2>
                        <small class="text-primary">Karyawan Aktif</small>
                    </div>
                    <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Record Kehadiran</p>
                        <h2 class="mb-0">{{ number_format($totalAttendance) }}</h2>
                        <small class="text-info">Semua Waktu</small>
                    </div>
                    <i class="bi bi-calendar-check text-info" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend Chart -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-graph-up-arrow me-2"></i>
            Tren Kehadiran 6 Bulan Terakhir
        </h5>
    </div>
    <div class="card-body">
        <div id="monthlyTrendChart" style="height: 350px;"></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Department Statistics -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2"></i>
                    Statistik Per Departemen
                </h5>
            </div>
            <div class="card-body">
                @if($departmentStats->count() > 0)
                    <div id="departmentChart" style="height: 300px;"></div>
                    
                    <hr>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Departemen</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentStats as $dept)
                                <tr>
                                    <td>{{ $dept->department ?? 'Tidak Ada Departemen' }}</td>
                                    <td class="text-end"><strong>{{ $dept->count }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-success">
                                            {{ number_format(($dept->count / $totalEmployees) * 100, 1) }}%
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
                        <p class="text-muted mt-2">Belum ada data departemen</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Status Distribution -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Distribusi Status Kehadiran (Bulan Ini)
                </h5>
            </div>
            <div class="card-body">
                <div id="statusPieChart" style="height: 300px;"></div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            <h4 class="mb-0 mt-2">{{ $monthlyStats['on_time'] ?? 0 }}</h4>
                            <small class="text-muted">Tepat Waktu</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                            <h4 class="mb-0 mt-2">{{ $monthlyStats['late'] ?? 0 }}</h4>
                            <small class="text-muted">Terlambat</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <i class="bi bi-exclamation-circle text-info" style="font-size: 2rem;"></i>
                            <h4 class="mb-0 mt-2">{{ $monthlyStats['incomplete'] ?? 0 }}</h4>
                            <small class="text-muted">Belum Check-out</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                            <h4 class="mb-0 mt-2">{{ $monthlyStats['absent'] ?? 0 }}</h4>
                            <small class="text-muted">Tidak Hadir</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Summary -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-trophy me-2"></i>
            Ringkasan Performa Kehadiran
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <div class="border rounded p-4">
                    @php
                        $total = $monthlyStats['on_time'] + $monthlyStats['late'] + $monthlyStats['incomplete'];
                        $onTimeRate = $total > 0 ? ($monthlyStats['on_time'] / $total) * 100 : 0;
                    @endphp
                    <div class="display-4 fw-bold text-success">
                        {{ number_format($onTimeRate, 1) }}%
                    </div>
                    <h6 class="text-muted mb-0">Tingkat Kehadiran Tepat Waktu</h6>
                    <small class="text-muted">Bulan Ini</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="border rounded p-4">
                    @php
                        $lateRate = $total > 0 ? ($monthlyStats['late'] / $total) * 100 : 0;
                    @endphp
                    <div class="display-4 fw-bold text-warning">
                        {{ number_format($lateRate, 1) }}%
                    </div>
                    <h6 class="text-muted mb-0">Tingkat Keterlambatan</h6>
                    <small class="text-muted">Bulan Ini</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="border rounded p-4">
                    <div class="display-4 fw-bold text-primary">
                        {{ $monthlyStats['total_attendance'] ?? 0 }}
                    </div>
                    <h6 class="text-muted mb-0">Total Kehadiran</h6>
                    <small class="text-muted">Bulan Ini</small>
                </div>
            </div>
            <div class="col-md-3 text-center mb-3">
                <div class="border rounded p-4">
                    @php
                        $avgAttendance = $totalEmployees > 0 ? ($monthlyStats['total_attendance'] / $totalEmployees) : 0;
                    @endphp
                    <div class="display-4 fw-bold text-info">
                        {{ number_format($avgAttendance, 1) }}
                    </div>
                    <h6 class="text-muted mb-0">Rata-rata Kehadiran</h6>
                    <small class="text-muted">Per Karyawan/Bulan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Chart -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Perbandingan Tepat Waktu vs Terlambat (6 Bulan)
                </h5>
            </div>
            <div class="card-body">
                <div id="comparisonChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Monthly Trend Chart
    Highcharts.chart('monthlyTrendChart', {
        chart: {
            type: 'area'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: @json($monthlyData['months'])
        },
        yAxis: {
            title: {
                text: 'Jumlah Kehadiran'
            }
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: [{
            name: 'Tepat Waktu',
            data: @json($monthlyData['onTime']),
            color: '#198754'
        }, {
            name: 'Terlambat',
            data: @json($monthlyData['late']),
            color: '#ffc107'
        }],
        credits: {
            enabled: false
        }
    });

    // Department Chart
    @if($departmentStats->count() > 0)
    Highcharts.chart('departmentChart', {
        chart: {
            type: 'bar'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: @json($departmentStats->pluck('department')->map(function($dept) {
                return $dept ?? 'Tidak Ada Departemen';
            }))
        },
        yAxis: {
            title: {
                text: 'Jumlah Karyawan'
            }
        },
        series: [{
            name: 'Jumlah Karyawan',
            data: @json($departmentStats->pluck('count')),
            colorByPoint: true
        }],
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        }
    });
    @endif

    // Status Pie Chart
    @php
        $totalMonthly = ($monthlyStats['on_time'] ?? 0) + ($monthlyStats['late'] ?? 0) + 
                        ($monthlyStats['incomplete'] ?? 0) + ($monthlyStats['absent'] ?? 0);
    @endphp
    
    Highcharts.chart('statusPieChart', {
        chart: {
            type: 'pie'
        },
        title: {
            text: null
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f}%'
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Jumlah',
            colorByPoint: true,
            data: [{
                name: 'Tepat Waktu',
                y: {{ $monthlyStats['on_time'] ?? 0 }},
                color: '#198754'
            }, {
                name: 'Terlambat',
                y: {{ $monthlyStats['late'] ?? 0 }},
                color: '#ffc107'
            }, {
                name: 'Belum Check-out',
                y: {{ $monthlyStats['incomplete'] ?? 0 }},
                color: '#0dcaf0'
            }, {
                name: 'Tidak Hadir',
                y: {{ $monthlyStats['absent'] ?? 0 }},
                color: '#dc3545'
            }]
        }],
        credits: {
            enabled: false
        }
    });

    // Comparison Chart
    Highcharts.chart('comparisonChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: @json($monthlyData['months'])
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Kehadiran'
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ' kehadiran'
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        series: [{
            name: 'Tepat Waktu',
            data: @json($monthlyData['onTime']),
            color: '#198754'
        }, {
            name: 'Terlambat',
            data: @json($monthlyData['late']),
            color: '#ffc107'
        }],
        credits: {
            enabled: false
        }
    });
</script>
@endpush