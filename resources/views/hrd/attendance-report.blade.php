@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-kicker', 'Attendance Report')
@section('page-title', 'Laporan Kehadiran')
@section('page-subtitle', 'Filter laporan secara fleksibel, pantau tren status, lalu export sesuai periode yang dibutuhkan.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        @foreach([
            ['label' => 'Total Record', 'value' => $stats['total'], 'helper' => $stats['hours'] . ' jam kerja tercatat', 'icon' => 'bi-database', 'tone' => 'primary'],
            ['label' => 'Tepat Waktu', 'value' => $stats['on_time'], 'helper' => attendancePercentage($stats['on_time'], $stats['total']) . '%', 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Terlambat', 'value' => $stats['late'], 'helper' => attendancePercentage($stats['late'], $stats['total']) . '%', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Belum Check-out', 'value' => $stats['incomplete'], 'helper' => 'Perlu follow up', 'icon' => 'bi-hourglass-split', 'tone' => 'info'],
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

    <div class="filter-card">
        <form action="{{ route('hrd.attendance-report') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-2">
                <label class="form-label">Tanggal mulai</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Tanggal selesai</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Bulan</label>
                <select class="form-select" name="month">
                    <option value="">Semua</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (string) request('month') === (string) $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Tahun</label>
                <select class="form-select" name="year">
                    <option value="">Semua</option>
                    @for($year = now()->year; $year >= now()->year - 2; $year--)
                        <option value="{{ $year }}" {{ (string) request('year') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua</option>
                    <option value="on_time" {{ request('status') === 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>Belum Check-out</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Per halaman</label>
                <select class="form-select" name="per_page">
                    @foreach([10,20,25,50,100] as $option)
                        <option value="{{ $option }}" {{ (int) request('per_page', $perPage) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Cari karyawan</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama, ID karyawan, departemen">
            </div>
            <div class="col-lg-4">
                <label class="form-label">Karyawan</label>
                <select class="form-select select2" name="user_id">
                    <option value="">Semua karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (string) request('user_id') === (string) $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('hrd.attendance-report') }}" class="btn btn-outline-primary">Reset</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">Export</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Tren laporan yang dapat dikonfigurasi</div>
                <div class="small text-muted">Visual ringkas untuk membantu analisis status berdasarkan hasil filter.</div>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select" id="reportChartType" style="min-width:120px;">
                    <option value="bar">Bar</option>
                    <option value="line">Line</option>
                    <option value="doughnut">Doughnut</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-shell">
                <canvas id="reportChartCanvas"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Data kehadiran terfilter</div>
                <div class="small text-muted">Menampilkan {{ $attendances->count() }} data dari {{ $attendances->total() }} hasil.</div>
            </div>
            <span class="soft-chip"><i class="bi bi-table"></i>Siap untuk filter multi-periode dan pagination besar</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Karyawan</th>
                            <th>Shift</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Jam</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ formatDate($attendance->date) }}</div>
                                    <div class="small text-muted">{{ $attendance->date->translatedFormat('l') }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $attendance->user->name }}</div>
                                    <div class="small text-muted">{{ $attendance->user->employee_id }}</div>
                                </td>
                                <td>{{ $attendance->shift?->name ?: '-' }}</td>
                                <td>{{ $attendance->check_in ? formatTime($attendance->check_in) : '-' }}</td>
                                <td>{{ $attendance->check_out ? formatTime($attendance->check_out) : '-' }}</td>
                                <td>{{ $attendance->total_hours ? number_format($attendance->total_hours, 2) . ' jam' : '-' }}</td>
                                <td><span class="badge rounded-pill bg-{{ getStatusBadge($attendance->status) }}">{{ getStatusText($attendance->status) }}</span></td>
                                <td>{{ $attendance->notes ?: '-' }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#notesModal{{ $attendance->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <div class="fw-semibold mb-1">Tidak ada data untuk filter saat ini</div>
                                        <div class="small">Ubah filter atau gunakan rentang tanggal lain.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($attendances->hasPages())
            <div class="card-body border-top">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($attendances as $attendance)
    <div class="modal fade" id="notesModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Tambah catatan kehadiran</h5>
                        <div class="small text-muted">{{ $attendance->user->name }} · {{ formatDate($attendance->date) }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('hrd.attendance.notes', $attendance->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <textarea class="form-control" name="notes" rows="4" required>{{ $attendance->notes }}</textarea>
                        <div class="small text-muted mt-2">Catatan akan dikirimkan sebagai notifikasi ke karyawan terkait.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal mulai</label>
                    <input type="date" class="form-control" id="export_start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal selesai</label>
                    <input type="date" class="form-control" id="export_end_date" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" onclick="exportData('excel')"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</button>
                    <button type="button" class="btn btn-outline-primary" onclick="exportData('pdf')"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('reportChartCanvas');
        const typeSelect = document.getElementById('reportChartType');
        let chart;

        function renderChart() {
            const type = typeSelect.value;
            if (chart) chart.destroy();
            chart = new Chart(chartCanvas, {
                type,
                data: {
                    labels: @json($reportChart['labels']),
                    datasets: [
                        { label: 'Tepat Waktu', data: @json($reportChart['on_time']), borderColor: '#4f8a66', backgroundColor: 'rgba(79,138,102,.22)', fill: type === 'line', tension: .35, borderWidth: 2 },
                        { label: 'Terlambat', data: @json($reportChart['late']), borderColor: '#c88a4d', backgroundColor: 'rgba(200,138,77,.22)', fill: type === 'line', tension: .35, borderWidth: 2 },
                        { label: 'Belum Check-out', data: @json($reportChart['incomplete']), borderColor: '#8a7fc5', backgroundColor: 'rgba(138,127,197,.22)', fill: type === 'line', tension: .35, borderWidth: 2 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: type === 'doughnut' ? {} : { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        typeSelect.addEventListener('change', renderChart);
        renderChart();
    });

    function exportData(format) {
        const startDate = document.getElementById('export_start_date').value;
        const endDate = document.getElementById('export_end_date').value;
        if (!startDate || !endDate || new Date(startDate) > new Date(endDate)) {
            Swal.fire({ icon: 'warning', title: 'Periode tidak valid', text: 'Pastikan tanggal mulai dan selesai sudah benar.' });
            return;
        }
        const base = format === 'excel' ? @js(route('hrd.attendance-report.export.excel')) : @js(route('hrd.attendance-report.export.pdf'));
        window.location.href = `${base}?start_date=${startDate}&end_date=${endDate}`;
    }
</script>
@endpush
