@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-kicker', 'Control Center')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Pantau kesiapan operasional absensi, aktivitas admin, dan kondisi kehadiran harian dari satu panel.')

@section('content')
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center position-relative">
            <div class="col-xl-8">
                <div class="small text-uppercase fw-bold muted" style="letter-spacing:.2em;">Attendance Overview</div>
                <h2 class="mt-2 mb-2 fw-bold">Operasional absensi hari ini berjalan {{ $attendanceRate >= 85 ? 'stabil' : 'perlu perhatian' }}</h2>
                <p class="muted mb-4">Sistem disesuaikan dengan alur proposal: admin mengelola karyawan, shift, QR aktif, dan koreksi data tanpa memutus alur HRD maupun karyawan.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $activeEmployees }} karyawan aktif</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $todayAttendance }} hadir hari ini</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $totalShifts }} shift terdaftar</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $activeQrCodes->count() }} QR sedang aktif</span>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-4 border border-white border-opacity-10">
                    <div class="small muted">Attendance Rate</div>
                    <div class="display-6 fw-bold">{{ $attendanceRate }}%</div>
                    <div class="muted mb-3">Persentase hadir terhadap total karyawan aktif hari ini.</div>
                    <div class="d-flex justify-content-between small text-white-50">
                        <span>Tepat waktu {{ $todayOnTime }}</span>
                        <span>Terlambat {{ $todayLate }}</span>
                        <span>Absen {{ $todayAbsent }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach([
            ['label' => 'Karyawan Aktif', 'value' => $activeEmployees, 'helper' => $inactiveEmployees . ' nonaktif', 'icon' => 'bi-people', 'tone' => 'primary'],
            ['label' => 'Hadir Hari Ini', 'value' => $todayAttendance, 'helper' => $attendanceRate . '% dari total aktif', 'icon' => 'bi-calendar-check', 'tone' => 'success'],
            ['label' => 'Terlambat', 'value' => $todayLate, 'helper' => $todayIncomplete . ' belum check-out', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Tidak Hadir', 'value' => $todayAbsent, 'helper' => 'Perlu evaluasi atau koreksi', 'icon' => 'bi-x-octagon', 'tone' => 'danger'],
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
                        <div class="fw-bold fs-5">Tren operasional 7 hari terakhir</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <select class="form-select" id="adminChartType" style="min-width:120px;">
                            <option value="bar">Bar</option>
                            <option value="line">Line</option>
                            <option value="doughnut">Doughnut</option>
                        </select>
                        <select class="form-select" id="adminChartDataset" style="min-width:190px;">
                            <option value="attendance">Kehadiran Harian</option>
                            <option value="punctuality">Tepat Waktu vs Terlambat</option>
                            <option value="department">Sebaran Departemen</option>
                            <option value="shift">Distribusi Shift</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-shell">
                        <canvas id="adminDashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="page-kicker">Quick Access</div>
                    <div class="fw-bold fs-5">Aksi cepat admin</div>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    @foreach([
                        ['label' => 'Tambah Karyawan', 'route' => route('admin.employees.create'), 'icon' => 'bi-person-plus', 'tone' => 'primary'],
                        ['label' => 'Kelola Shift', 'route' => route('admin.shifts'), 'icon' => 'bi-clock-history', 'tone' => 'success'],
                        ['label' => 'Koreksi Kehadiran', 'route' => route('admin.attendances'), 'icon' => 'bi-pencil-square', 'tone' => 'warning'],
                        ['label' => 'Generate QR Shift', 'route' => route('admin.qr-code'), 'icon' => 'bi-qr-code', 'tone' => 'info'],
                    ] as $action)
                        <a href="{{ $action['route'] }}" class="quick-link">
                            <span class="quick-link-icon bg-{{ $action['tone'] }} bg-opacity-10 text-{{ $action['tone'] }}">
                                <i class="bi {{ $action['icon'] }}"></i>
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $action['label'] }}</div>
                                <div class="small text-muted">Masuk ke modul terkait tanpa memutus alur kerja.</div>
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
                    <div class="fw-bold fs-5">QR shift aktif</div>
                    <a href="{{ route('admin.qr-code') }}" class="btn btn-outline-primary btn-sm">Kelola QR</a>
                </div>
                <div class="card-body">
                    @forelse($activeQrCodes as $qr)
                        <div class="data-summary-item mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $qr->shift->name ?? 'Shift tidak ditemukan' }}</div>
                                    <div class="small text-muted">{{ $qr->type === 'check_in' ? 'QR Check-in' : 'QR Check-out' }}</div>
                                </div>
                                <span class="badge rounded-pill {{ $qr->type === 'check_in' ? 'badge-soft-success' : 'badge-soft-info' }}">
                                    {{ $qr->expires_at->format('H:i:s') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-qr-code"></i>
                            <div class="fw-semibold mb-1">Belum ada QR aktif</div>
                            <div class="small mb-3">Generate QR berdasarkan shift untuk mulai proses absensi.</div>
                            <a href="{{ route('admin.qr-code') }}" class="btn btn-primary btn-sm">Generate QR</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold fs-5">Aktivitas terbaru</div>
                    <span class="soft-chip"><i class="bi bi-activity"></i>{{ $recentActivities->count() }} aktivitas</span>
                </div>
                <div class="card-body">
                    @forelse($recentActivities as $activity)
                        <div class="d-flex gap-3 py-3 border-bottom" style="border-color:rgba(129,101,104,.1)!important;">
                            <div class="quick-link-icon bg-light text-primary flex-shrink-0">
                                <i class="bi {{ str_contains($activity->action, 'DELETE') ? 'bi-trash3' : (str_contains($activity->action, 'UPDATE') ? 'bi-pencil' : (str_contains($activity->action, 'CHECK') ? 'bi-calendar-check' : 'bi-stars')) }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $activity->user->name ?? 'Sistem' }}</div>
                                <div class="small text-muted">{{ $activity->description }}</div>
                            </div>
                            <div class="small text-muted text-end">{{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-clock-history"></i>
                            <div class="fw-semibold mb-1">Belum ada aktivitas</div>
                            <div class="small">Aktivitas login, kelola data, dan absensi akan muncul di sini.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold fs-5">Karyawan terbaru</div>
                    <a href="{{ route('admin.employees') }}" class="btn btn-outline-primary btn-sm">Semua Data</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama</th>
                                    <th>Departemen</th>
                                    <th>Shift</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEmployees as $employee)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $employee->name }}</div>
                                            <div class="small text-muted">{{ $employee->employee_id }}</div>
                                        </td>
                                        <td>{{ $employee->department ?: 'Belum diatur' }}</td>
                                        <td>{{ $employee->shift?->name ?: '-' }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $employee->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data karyawan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="fw-bold fs-5">Ringkasan keputusan hari ini</div>
                </div>
                <div class="card-body data-summary">
                    <div class="data-summary-item">
                        <div class="small text-muted">Kehadiran valid</div>
                        <div class="fs-4 fw-bold">{{ $todayOnTime + $todayLate }}</div>
                        <div class="small text-muted">Tercatat melalui check-in aktif dan siap dipantau HRD.</div>
                    </div>
                    <div class="data-summary-item">
                        <div class="small text-muted">Data perlu tindak lanjut</div>
                        <div class="fs-4 fw-bold">{{ $todayIncomplete + $todayAbsent }}</div>
                        <div class="small text-muted">Gabungan belum check-out dan belum hadir yang perlu ditinjau.</div>
                    </div>
                    <div class="data-summary-item">
                        <div class="small text-muted">Basis data karyawan</div>
                        <div class="fs-4 fw-bold">{{ $totalEmployees }}</div>
                        <div class="small text-muted">Gunakan filter, pagination, dan export agar tetap ringan saat data tumbuh besar.</div>
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
        const chartCanvas = document.getElementById('adminDashboardChart');
        const typeSelect = document.getElementById('adminChartType');
        const datasetSelect = document.getElementById('adminChartDataset');
        let chart;

        const datasets = {
            attendance: {
                labels: @json($weeklyTrend['labels']),
                datasets: [
                    { label: 'Hadir', data: @json($weeklyTrend['present']), borderColor: '#c97570', backgroundColor: 'rgba(201,117,112,.25)' },
                    { label: 'Absen', data: @json($weeklyTrend['absent']), borderColor: '#ba5d57', backgroundColor: 'rgba(186,93,87,.2)' }
                ]
            },
            punctuality: {
                labels: @json($weeklyTrend['labels']),
                datasets: [
                    { label: 'Tepat Waktu', data: @json($weeklyTrend['on_time']), borderColor: '#4f8a66', backgroundColor: 'rgba(79,138,102,.24)' },
                    { label: 'Terlambat', data: @json($weeklyTrend['late']), borderColor: '#c88a4d', backgroundColor: 'rgba(200,138,77,.24)' }
                ]
            },
            department: {
                labels: @json($departmentBreakdown->pluck('department_name')),
                datasets: [
                    { label: 'Karyawan', data: @json($departmentBreakdown->pluck('total')), borderColor: '#c97570', backgroundColor: ['#c97570','#df9a95','#8b5557','#f0c6c3','#ba5d57','#e5b5aa'] }
                ]
            },
            shift: {
                labels: @json($shiftBreakdown->pluck('name')),
                datasets: [
                    { label: 'Karyawan per Shift', data: @json($shiftBreakdown->pluck('employee_total')), borderColor: '#8a7fc5', backgroundColor: ['#8a7fc5','#b7a8df','#c97570','#df9a95','#4f8a66','#c88a4d'] }
                ]
            }
        };

        function renderChart() {
            const selected = datasets[datasetSelect.value];
            const selectedType = typeSelect.value;
            if (chart) chart.destroy();
            chart = new Chart(chartCanvas, {
                type: selectedType,
                data: {
                    labels: selected.labels,
                    datasets: selected.datasets.map(dataset => ({
                        ...dataset,
                        fill: selectedType === 'line',
                        tension: .35,
                        borderWidth: 2,
                        pointBackgroundColor: dataset.borderColor
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: selectedType === 'doughnut' ? {} : {
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
