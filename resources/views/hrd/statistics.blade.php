@extends('layouts.app')

@section('title', 'Statistik Kehadiran')
@section('page-kicker', 'Attendance Analytics')
@section('page-title', 'Statistik Kehadiran')
@section('page-subtitle', 'Baca performa kehadiran lintas bulan, distribusi departemen, dan proporsi status dengan chart yang selaras dengan layout baru.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-value">{{ $totalEmployees }}</div>
                <div class="stat-label">Karyawan Aktif</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-database"></i></div>
                <div class="stat-value">{{ number_format($totalAttendance) }}</div>
                <div class="stat-label">Total Record</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value">{{ attendancePercentage($monthlyStats['on_time'] ?? 0, ($monthlyStats['total_attendance'] ?? 0)) }}%</div>
                <div class="stat-label">On-time Rate</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-alarm"></i></div>
                <div class="stat-value">{{ attendancePercentage($monthlyStats['late'] ?? 0, ($monthlyStats['total_attendance'] ?? 0)) }}%</div>
                <div class="stat-label">Late Rate</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="fw-bold fs-5">Tren 6 bulan terakhir</div>
                        <div class="small text-muted">Bandingkan kehadiran tepat waktu dan terlambat dari bulan ke bulan.</div>
                    </div>
                    <select class="form-select" id="trendChartType" style="max-width: 140px;">
                        <option value="bar">Bar</option>
                        <option value="line">Line</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-shell">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="fw-bold fs-5">Distribusi status bulan ini</div>
                </div>
                <div class="card-body">
                    <div class="chart-shell" style="min-height:280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div class="data-summary-item text-center">
                                <div class="fw-bold text-success">{{ $monthlyStats['on_time'] ?? 0 }}</div>
                                <div class="small text-muted">Tepat Waktu</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="data-summary-item text-center">
                                <div class="fw-bold text-warning">{{ $monthlyStats['late'] ?? 0 }}</div>
                                <div class="small text-muted">Terlambat</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="data-summary-item text-center">
                                <div class="fw-bold" style="color:#8a7fc5;">{{ $monthlyStats['incomplete'] ?? 0 }}</div>
                                <div class="small text-muted">Incomplete</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="data-summary-item text-center">
                                <div class="fw-bold text-danger">{{ $monthlyStats['absent'] ?? 0 }}</div>
                                <div class="small text-muted">Tidak Hadir</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Komposisi departemen</div>
                </div>
                <div class="card-body">
                    @if($departmentStats->count())
                        <div class="chart-shell" style="min-height:300px;">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-building"></i>
                            <div class="fw-semibold mb-1">Belum ada data departemen</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="fw-bold fs-5">Ringkasan performa bulan ini</div>
                </div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Total kehadiran tercatat</div>
                                <div class="small text-muted">Akumulasi semua status attendance bulan ini</div>
                            </div>
                            <div class="fw-bold fs-4">{{ $monthlyStats['total_attendance'] ?? 0 }}</div>
                        </div>
                        <div class="data-summary-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Rata-rata per karyawan</div>
                                <div class="small text-muted">Estimasi jumlah record attendance per karyawan aktif</div>
                            </div>
                            <div class="fw-bold fs-4">{{ number_format($totalEmployees > 0 ? (($monthlyStats['total_attendance'] ?? 0) / $totalEmployees) : 0, 1) }}</div>
                        </div>
                        <div class="data-summary-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">On-time vs Late</div>
                                <div class="small text-muted">Proporsi utama kedisiplinan masuk kerja</div>
                            </div>
                            <div class="fw-bold fs-4">{{ ($monthlyStats['on_time'] ?? 0) }}/{{ ($monthlyStats['late'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trendCanvas = document.getElementById('trendChart');
        const statusCanvas = document.getElementById('statusChart');
        const departmentCanvas = document.getElementById('departmentChart');
        const trendTypeSelect = document.getElementById('trendChartType');
        let trendChart;

        function renderTrendChart() {
            if (trendChart) trendChart.destroy();
            trendChart = new Chart(trendCanvas, {
                type: trendTypeSelect.value,
                data: {
                    labels: @json($monthlyData['months']),
                    datasets: [
                        {
                            label: 'Tepat Waktu',
                            data: @json($monthlyData['onTime']),
                            borderColor: '#4f8a66',
                            backgroundColor: 'rgba(79,138,102,.2)',
                            tension: .35,
                            borderWidth: 2
                        },
                        {
                            label: 'Terlambat',
                            data: @json($monthlyData['late']),
                            borderColor: '#c88a4d',
                            backgroundColor: 'rgba(200,138,77,.2)',
                            tension: .35,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        trendTypeSelect.addEventListener('change', renderTrendChart);
        renderTrendChart();

        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Incomplete', 'Tidak Hadir'],
                datasets: [{
                    data: [
                        {{ $monthlyStats['on_time'] ?? 0 }},
                        {{ $monthlyStats['late'] ?? 0 }},
                        {{ $monthlyStats['incomplete'] ?? 0 }},
                        {{ $monthlyStats['absent'] ?? 0 }}
                    ],
                    backgroundColor: ['#4f8a66', '#c88a4d', '#8a7fc5', '#ba5d57']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        if (departmentCanvas) {
            new Chart(departmentCanvas, {
                type: 'bar',
                data: {
                    labels: @json($departmentStats->pluck('department')->map(fn ($department) => $department ?: 'Belum diatur')),
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: @json($departmentStats->pluck('count')),
                        backgroundColor: ['#c97570', '#e0a19d', '#8a7fc5', '#4f8a66', '#c88a4d', '#ba5d57']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }
    });
</script>
@endpush
