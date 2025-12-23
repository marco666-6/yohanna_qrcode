@extends('layouts.app')

@section('title', 'Laporan Kehadiran')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('hrd.attendance-report') }}">
            <i class="bi bi-file-earmark-text"></i> Laporan Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Laporan Kehadiran
                </h2>
                <p class="text-muted mb-0">Kelola dan pantau kehadiran karyawan</p>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="bi bi-download me-2"></i>
                    Export Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="bi bi-funnel me-2"></i>
            Filter Laporan
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('hrd.attendance-report') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <select class="form-select" name="month">
                    <option value="">Semua</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select class="form-select" name="year">
                    <option value="">Semua</option>
                    @for($year = now()->year; $year >= now()->year - 2; $year--)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="on_time" {{ request('status') == 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Belum Check-out</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select class="form-select select2" name="user_id">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('hrd.attendance-report') }}" class="btn btn-secondary">
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
                        <p class="text-muted mb-1">Total Data</p>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Record</small>
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
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>
            Data Kehadiran
        </h5>
        <span class="badge bg-primary">{{ $attendances->total() }} Record</span>
    </div>
    <div class="card-body">
        @if($attendances->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Karyawan</th>
                            <th>Shift</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Jam</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
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
                                <strong>{{ $attendance->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $attendance->user->employee_id }}</small>
                            </td>
                            <td>
                                @if($attendance->shift)
                                    <span class="badge bg-secondary">{{ $attendance->shift->name }}</span>
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
                                    <strong>{{ number_format($attendance->total_hours, 2) }}</strong> jam
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
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#notesModal{{ $attendance->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Notes Modal -->
                        <div class="modal fade" id="notesModal{{ $attendance->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil me-2"></i>
                                            Tambah Catatan
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('hrd.attendance.notes', $attendance->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Karyawan</label>
                                                <input type="text" class="form-control" value="{{ $attendance->user->name }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal</label>
                                                <input type="text" class="form-control" value="{{ formatDate($attendance->date) }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="notes" rows="4" required>{{ $attendance->notes }}</textarea>
                                                <small class="text-muted">Catatan akan dikirimkan ke karyawan terkait</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="bi bi-save me-2"></i>
                                                Simpan Catatan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Tidak ada data kehadiran untuk filter yang dipilih</p>
            </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-download me-2"></i>
                    Export Laporan Kehadiran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Pilih format dan periode untuk export laporan kehadiran:</p>
                
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="export_start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="export_end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success" onclick="exportData('excel')">
                                <i class="bi bi-file-earmark-excel me-2"></i>
                                Export ke Excel (.xlsx)
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="exportData('pdf')">
                                <i class="bi bi-file-earmark-pdf me-2"></i>
                                Export ke PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Export function
    function exportData(format) {
        const startDate = document.getElementById('export_start_date').value;
        const endDate = document.getElementById('export_end_date').value;

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Mohon lengkapi tanggal mulai dan selesai',
            });
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai',
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Sedang memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let url = '';
        if (format === 'excel') {
            url = "{{ route('hrd.attendance-report.export.excel') }}";
        } else {
            url = "{{ route('hrd.attendance-report.export.pdf') }}";
        }

        url += `?start_date=${startDate}&end_date=${endDate}`;

        // Download file
        window.location.href = url;

        // Close modal and loading
        setTimeout(() => {
            Swal.close();
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Laporan berhasil diexport',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1000);
    }
</script>
@endpush