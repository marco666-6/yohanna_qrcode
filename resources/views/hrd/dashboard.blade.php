@extends('layouts.app')

@section('title', 'Dashboard HRD')
@section('page-kicker', 'HR Insight')
@section('page-title', 'Dashboard HRD')
@section('page-subtitle', 'Analisis kedisiplinan, antrian cuti, dan koreksi operasional untuk membantu evaluasi SDM.')

@section('content')
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center position-relative">
            <div class="col-xl-8">
                <div class="small text-uppercase fw-bold muted" style="letter-spacing:.2em;">Human Resource Overview</div>
                <h2 class="mt-2 mb-2 fw-bold">Fokus HRD hari ini: {{ $pendingLeaves > 0 ? 'review cuti yang masih menunggu' : 'pantau stabilitas kehadiran dan koreksi seperlunya' }}</h2>
                <p class="muted mb-4">Panel ini menampilkan statistik yang masuk akal untuk aplikasi absensi: hadir, terlambat, belum check-out, serta insight per departemen dan karyawan yang paling sering terlambat.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $totalEmployees }} karyawan aktif</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $todayPresent }} hadir hari ini</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $pendingLeaves }} cuti pending</span>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-4 border border-white border-opacity-10">
                    <div class="small muted">Kondisi Hari Ini</div>
                    <div class="display-6 fw-bold">{{ attendancePercentage($todayPresent, $totalEmployees) }}%</div>
                    <div class="muted">Rasio kehadiran terhadap karyawan aktif.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach([
            ['label' => 'Hadir Hari Ini', 'value' => $todayPresent, 'helper' => attendancePercentage($todayPresent, $totalEmployees) . '% tingkat hadir', 'icon' => 'bi-calendar-check', 'tone' => 'success'],
            ['label' => 'Tidak Hadir', 'value' => $todayAbsent, 'helper' => 'Belum check-in sampai saat ini', 'icon' => 'bi-person-x', 'tone' => 'danger'],
            ['label' => 'Terlambat', 'value' => $todayLate, 'helper' => $todayIncomplete . ' belum check-out', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Cuti Pending', 'value' => $pendingLeaves, 'helper' => 'Perlu review HRD', 'icon' => 'bi-calendar2-week', 'tone' => 'info'],
        ] as $item)
            <div class="col-6 col-xl-3">
                <div class="stat-card {{ $item['tone'] }}">
                    <div class="stat-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                    <div class="stat-value">{{ $item['value'] }}</div>
                    <div class="stat-label">{{ $item['label'] }}</div>
                    <div class="stat-helper">{{ $item['helper'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="page-kicker">Analytics</div>
                        <div class="fw-bold fs-5">Statistik HRD yang dapat dikonfigurasi</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <select class="form-select" id="hrdChartType" style="min-width:120px;">
                            <option value="bar">Bar</option>
                            <option value="line">Line</option>
                            <option value="doughnut">Doughnut</option>
                        </select>
                        <select class="form-select" id="hrdChartDataset" style="min-width:210px;">
                            <option value="weekly">Kehadiran 7 Hari</option>
                            <option value="department">Sebaran Departemen</option>
                            <option value="monthly">Ringkasan Bulan Ini</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-shell">
                        <canvas id="hrdDashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="page-kicker">Quick Access</div>
                    <div class="fw-bold fs-5">Prioritas HRD</div>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    @foreach([
                        ['label' => 'Laporan Kehadiran', 'route' => route('hrd.attendance-report'), 'icon' => 'bi-file-earmark-bar-graph', 'tone' => 'primary'],
                        ['label' => 'Review Cuti', 'route' => route('hrd.leave-requests'), 'icon' => 'bi-calendar2-week', 'tone' => 'warning'],
                        ['label' => 'Statistik Lanjutan', 'route' => route('hrd.statistics'), 'icon' => 'bi-graph-up-arrow', 'tone' => 'success'],
                    ] as $action)
                        <a href="{{ $action['route'] }}" class="quick-link">
                            <span class="quick-link-icon bg-{{ $action['tone'] }} bg-opacity-10 text-{{ $action['tone'] }}">
                                <i class="bi {{ $action['icon'] }}"></i>
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $action['label'] }}</div>
                                <div class="small text-muted">Masuk ke modul evaluasi tanpa pindah alur manual.</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold fs-5">Antrian cuti menunggu</div>
                    <a href="{{ route('hrd.leave-requests', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($pendingLeaveItems as $leave)
                        <div class="data-summary-item mb-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $leave->user->name }}</div>
                                    <div class="small text-muted">{{ getLeaveTypeText($leave->leave_type) }}</div>
                                </div>
                                <span class="badge rounded-pill badge-soft-warning">{{ $leave->total_days }} hari</span>
                            </div>
                            <div class="small text-muted mt-2">{{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }}</div>
                        </div>
                    @empty
                        <div class="small text-muted">Tidak ada pengajuan cuti yang menunggu persetujuan saat ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold fs-5">Karyawan paling sering terlambat bulan ini</div>
                    <span class="soft-chip"><i class="bi bi-alarm"></i>Perlu coaching</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Karyawan</th>
                                    <th>Departemen</th>
                                    <th>Keterlambatan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lateLeaders as $employee)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $employee->name }}</div>
                                            <div class="small text-muted">{{ $employee->employee_id }}</div>
                                        </td>
                                        <td>{{ $employee->department ?: 'Belum diatur' }}</td>
                                        <td><span class="badge rounded-pill badge-soft-warning">{{ $employee->late_count }} kali</span></td>
                                        <td>
                                            <span class="badge rounded-pill {{ $employee->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data keterlambatan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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
        const chartCanvas = document.getElementById('hrdDashboardChart');
        const typeSelect = document.getElementById('hrdChartType');
        const datasetSelect = document.getElementById('hrdChartDataset');
        let chart;

        const datasets = {
            weekly: {
                labels: @json($chartData['dates']),
                datasets: [
                    { label: 'Tepat Waktu', data: @json($chartData['onTime']), borderColor: '#4f8a66', backgroundColor: 'rgba(79,138,102,.22)' },
                    { label: 'Terlambat', data: @json($chartData['late']), borderColor: '#c88a4d', backgroundColor: 'rgba(200,138,77,.22)' },
                    { label: 'Tidak Hadir', data: @json($chartData['absent']), borderColor: '#ba5d57', backgroundColor: 'rgba(186,93,87,.2)' }
                ]
            },
            department: {
                labels: @json($departmentStats->pluck('department_name')),
                datasets: [
                    { label: 'Karyawan', data: @json($departmentStats->pluck('total')), borderColor: '#c97570', backgroundColor: ['#c97570','#df9a95','#8b5557','#f0c6c3','#ba5d57','#e5b5aa'] }
                ]
            },
            monthly: {
                labels: ['Tepat Waktu', 'Terlambat', 'Belum Check-out'],
                datasets: [
                    { label: 'Bulan Ini', data: [{{ $monthlyStats['on_time'] }}, {{ $monthlyStats['late'] }}, {{ $monthlyStats['incomplete'] }}], borderColor: '#8a7fc5', backgroundColor: ['#4f8a66','#c88a4d','#8a7fc5'] }
                ]
            }
        };

        function renderChart() {
            const selected = datasets[datasetSelect.value];
            const type = typeSelect.value;
            if (chart) chart.destroy();
            chart = new Chart(chartCanvas, {
                type,
                data: {
                    labels: selected.labels,
                    datasets: selected.datasets.map(dataset => ({
                        ...dataset,
                        fill: type === 'line',
                        tension: .35,
                        borderWidth: 2,
                        pointBackgroundColor: dataset.borderColor
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: type === 'doughnut' ? {} : {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        typeSelect.addEventListener('change', renderChart);
        datasetSelect.addEventListener('change', renderChart);
        renderChart();
    });
</script>
@endpush
