@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-kicker', 'Attendance History')
@section('page-title', 'Riwayat Absensi')
@section('page-subtitle', 'Lihat histori kehadiran pribadi per bulan dengan pagination yang aman untuk pertumbuhan data jangka panjang.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        @foreach([
            ['label' => 'Total Record', 'value' => $stats['total'], 'helper' => $stats['hours'] . ' jam kerja', 'icon' => 'bi-calendar3', 'tone' => 'primary'],
            ['label' => 'Tepat Waktu', 'value' => $stats['on_time'], 'helper' => attendancePercentage($stats['on_time'], $stats['total']) . '%', 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Terlambat', 'value' => $stats['late'], 'helper' => attendancePercentage($stats['late'], $stats['total']) . '%', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Belum Check-out', 'value' => $stats['incomplete'], 'helper' => 'Perlu perhatian pada hari terkait', 'icon' => 'bi-hourglass-split', 'tone' => 'info'],
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
        <form action="{{ route('employee.attendance-history') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label">Bulan</label>
                <select class="form-select" name="month">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $selectedMonth === $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label">Tahun</label>
                <select class="form-select" name="year">
                    @for($year = now()->year; $year >= now()->year - 2; $year--)
                        <option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-3">
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
                    @foreach([10,15,25,50,100] as $option)
                        <option value="{{ $option }}" {{ (int) request('per_page', $perPage) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 d-grid">
                <button type="submit" class="btn btn-primary">Lihat</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Riwayat absensi bulan {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }}</div>
                <div class="small text-muted">Menampilkan {{ $attendances->count() }} data dari {{ $attendances->total() }} hasil.</div>
            </div>
            <a href="{{ route('employee.scanner') }}" class="btn btn-primary btn-sm"><i class="bi bi-qr-code-scan me-1"></i>Scan Sekarang</a>
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
                            <th>Total Jam</th>
                            <th>Status</th>
                            <th>Catatan</th>
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
                                    <div>{{ $attendance->shift?->name ?: '-' }}</div>
                                    @if($attendance->shift)
                                        <div class="small text-muted">{{ formatTime($attendance->shift->start_time) }} - {{ formatTime($attendance->shift->end_time) }}</div>
                                    @endif
                                </td>
                                <td>{{ $attendance->check_in ? formatTime($attendance->check_in) : '-' }}</td>
                                <td>{{ $attendance->check_out ? formatTime($attendance->check_out) : '-' }}</td>
                                <td>{{ $attendance->total_hours ? number_format($attendance->total_hours, 2) . ' jam' : '-' }}</td>
                                <td><span class="badge rounded-pill bg-{{ getStatusBadge($attendance->status) }}">{{ getStatusText($attendance->status) }}</span></td>
                                <td>{{ $attendance->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <div class="fw-semibold mb-1">Belum ada riwayat pada periode ini</div>
                                        <div class="small">Gunakan scanner untuk mulai mencatat absensi.</div>
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
@endsection
