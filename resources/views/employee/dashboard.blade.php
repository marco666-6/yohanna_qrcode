@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page-kicker', 'My Attendance')
@section('page-title', 'Dashboard Karyawan')
@section('page-subtitle', 'Lihat status absensi hari ini, QR otomatis sesuai shift, performa bulanan, dan pengingat penting dari satu tempat.')

@section('content')
@php
    $attendanceNotice = $attendanceContext['notice'];
    $attendanceWindow = $attendanceContext['window'];
@endphp
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center position-relative">
            <div class="col-xl-8">
                <div class="small text-uppercase fw-bold muted" style="letter-spacing:.2em;">Daily Attendance</div>
                <h2 class="mt-2 mb-2 fw-bold">
                    {{ $attendanceNotice['title'] }}
                </h2>
                <p class="muted mb-4">{{ $attendanceNotice['message'] }} QR absensi akan dimunculkan otomatis di menu absensi Anda saat window shift terbuka.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ auth()->user()->employee_id }}</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ auth()->user()->shift?->name ?: 'Shift belum diatur' }}</span>
                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $monthlyStats['hours'] }} jam bulan ini</span>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-4 border border-white border-opacity-10">
                    <div class="small muted">Status Hari Ini</div>
                    <div class="fs-4 fw-bold mb-2">{{ $todayAttendance ? getStatusText($todayAttendance->status) : 'Belum Absen' }}</div>
                    <div class="muted">
                        Check-in {{ $todayAttendance?->check_in ? formatTime($todayAttendance->check_in) : '-' }} |
                        Check-out {{ $todayAttendance?->check_out ? formatTime($todayAttendance->check_out) : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-{{ $attendanceNotice['variant'] }} mb-0">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold">{{ $attendanceNotice['title'] }}</div>
                <div class="small">
                    {{ $attendanceNotice['message'] }}
                    @if($attendanceWindow)
                        Window {{ strtolower($attendanceContext['next_action_label']) }}: {{ $attendanceWindow['start']->format('H:i') }} - {{ $attendanceWindow['end']->format('H:i') }}.
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($attendanceContext['shift'])
                    <span class="soft-chip">
                        <i class="bi bi-clock"></i>{{ formatTime($attendanceContext['shift']->start_time) }} - {{ formatTime($attendanceContext['shift']->end_time) }}
                    </span>
                @endif
                <a href="{{ route('employee.scanner') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-qr-code me-1"></i>{{ $attendanceContext['active_qr'] ? $attendanceContext['action_button_label'] : 'Buka Absensi Saya' }}
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach([
            ['label' => 'Hadir Bulan Ini', 'value' => $monthlyStats['total_days'], 'helper' => 'Total record absensi', 'icon' => 'bi-calendar3', 'tone' => 'primary'],
            ['label' => 'Tepat Waktu', 'value' => $monthlyStats['on_time'], 'helper' => attendancePercentage($monthlyStats['on_time'], $monthlyStats['total_days']) . '%', 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Terlambat', 'value' => $monthlyStats['late'], 'helper' => attendancePercentage($monthlyStats['late'], $monthlyStats['total_days']) . '%', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Pending Cuti', 'value' => $pendingLeaves, 'helper' => $unreadNotifications . ' notifikasi belum dibaca', 'icon' => 'bi-bell', 'tone' => 'info'],
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
                        <div class="page-kicker">Performance</div>
                        <div class="fw-bold fs-5">Tren 14 hari terakhir</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <select class="form-select" id="employeeChartType" style="min-width:120px;">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                        <select class="form-select" id="employeeChartDataset" style="min-width:180px;">
                            <option value="presence">Kehadiran Harian</option>
                            <option value="hours">Jam Kerja</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-shell">
                        <canvas id="employeeDashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="page-kicker">Quick Access</div>
                    <div class="fw-bold fs-5">Aksi utama</div>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    @foreach([
                        ['label' => 'Absensi & QR Saya', 'route' => route('employee.scanner'), 'icon' => 'bi-qr-code-scan', 'tone' => 'primary'],
                        ['label' => 'Ajukan Cuti', 'route' => route('employee.leave-requests.create'), 'icon' => 'bi-calendar-plus', 'tone' => 'warning'],
                        ['label' => 'Riwayat Absensi', 'route' => route('employee.attendance-history'), 'icon' => 'bi-clock-history', 'tone' => 'success'],
                        ['label' => 'Notifikasi', 'route' => route('employee.notifications'), 'icon' => 'bi-bell', 'tone' => 'info'],
                    ] as $action)
                        <a href="{{ $action['route'] }}" class="quick-link">
                            <span class="quick-link-icon bg-{{ $action['tone'] }} bg-opacity-10 text-{{ $action['tone'] }}">
                                <i class="bi {{ $action['icon'] }}"></i>
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $action['label'] }}</div>
                                <div class="small text-muted">Akses cepat sesuai alur kerja harian.</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold fs-5">Riwayat 7 absensi terakhir</div>
                    <a href="{{ route('employee.attendance-history') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Shift</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendances as $attendance)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ formatDate($attendance->date) }}</div>
                                            <div class="small text-muted">{{ $attendance->date->translatedFormat('l') }}</div>
                                        </td>
                                        <td>{{ $attendance->shift?->name ?: '-' }}</td>
                                        <td>{{ $attendance->check_in ? formatTime($attendance->check_in) : '-' }}</td>
                                        <td>{{ $attendance->check_out ? formatTime($attendance->check_out) : '-' }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-{{ getStatusBadge($attendance->status) }}">
                                                {{ getStatusText($attendance->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat absensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Pengajuan cuti terdekat</div>
                </div>
                <div class="card-body">
                    @forelse($upcomingLeaves as $leave)
                        <div class="data-summary-item mb-3">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold">{{ getLeaveTypeText($leave->leave_type) }}</div>
                                    <div class="small text-muted">{{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }}</div>
                                </div>
                                <span class="badge rounded-pill badge-soft-success">{{ $leave->total_days }} hari</span>
                            </div>
                        </div>
                    @empty
                        <div class="small text-muted">Belum ada cuti yang sudah disetujui untuk periode mendatang.</div>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Notifikasi terbaru</div>
                </div>
                <div class="card-body">
                    @forelse($recentNotifications as $notification)
                        <div class="d-flex gap-3 py-3 border-bottom" style="border-color:rgba(129,101,104,.1)!important;">
                            <div class="quick-link-icon bg-light text-primary flex-shrink-0">
                                <i class="bi bi-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <div class="small text-muted">{{ $notification->message }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="small text-muted">Belum ada notifikasi terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('employeeDashboardChart');
        const typeSelect = document.getElementById('employeeChartType');
        const datasetSelect = document.getElementById('employeeChartDataset');
        let chart;

        const datasets = {
            presence: {
                labels: @json($trendLabels),
                datasets: [
                    { label: 'Hadir (1/0)', data: @json($trendPresent), borderColor: '#c97570', backgroundColor: 'rgba(201,117,112,.2)' }
                ]
            },
            hours: {
                labels: @json($trendLabels),
                datasets: [
                    { label: 'Jam Kerja', data: @json($trendHours), borderColor: '#4f8a66', backgroundColor: 'rgba(79,138,102,.22)' }
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
                    scales: {
                        y: { beginAtZero: true }
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
