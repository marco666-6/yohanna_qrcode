@extends('layouts.app')

@section('title', 'Detail Karyawan')
@section('page-kicker', 'Employee Profile')
@section('page-title', 'Detail Karyawan')
@section('page-subtitle', 'Lihat gambaran identitas, penempatan kerja, dan ringkasan performa kehadiran dalam satu halaman yang lebih bersih.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="preview-avatar mx-auto mb-3">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                    <div class="fw-bold fs-4">{{ $employee->name }}</div>
                    <div class="text-muted mb-3">{{ $employee->employee_id }}</div>
                    <span class="badge {{ $employee->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                        {{ $employee->is_active ? 'Karyawan aktif' : 'Karyawan tidak aktif' }}
                    </span>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary">Edit Karyawan</a>
                        <button type="button" class="btn btn-outline-primary" onclick="deleteEmployee({{ $employee->id }}, @js($employee->name))">Hapus Karyawan</button>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="fw-bold">Informasi kontak</div>
                </div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item">
                            <div class="small text-muted">Email</div>
                            <div>{{ $employee->email }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Telepon</div>
                            <div>{{ $employee->phone ?: '-' }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Terdaftar sejak</div>
                            <div>{{ formatDate($employee->created_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                        <div class="stat-value">{{ $stats['total_days'] }}</div>
                        <div class="stat-label">Total Hari</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="stat-value">{{ $stats['on_time'] }}</div>
                        <div class="stat-label">Tepat Waktu</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="bi bi-alarm"></i></div>
                        <div class="stat-value">{{ $stats['late'] }}</div>
                        <div class="stat-label">Terlambat</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card danger">
                        <div class="stat-icon"><i class="bi bi-x-octagon"></i></div>
                        <div class="stat-value">{{ $stats['absent'] }}</div>
                        <div class="stat-label">Tidak Hadir</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Informasi pekerjaan</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Departemen</div>
                                <div class="fw-semibold">{{ $employee->department ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Posisi / jabatan</div>
                                <div class="fw-semibold">{{ $employee->position ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Shift kerja</div>
                                <div class="fw-semibold">{{ $employee->shift?->name ?: '-' }}</div>
                                @if($employee->shift)
                                    <div class="small text-muted mt-1">{{ formatTime($employee->shift->start_time) }} - {{ formatTime($employee->shift->end_time) }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Toleransi terlambat</div>
                                <div class="fw-semibold">{{ $employee->shift?->late_tolerance ? $employee->shift->late_tolerance . ' menit' : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="fw-bold fs-5">Riwayat kehadiran terbaru</div>
                        <div class="small text-muted">Maksimal 30 record terakhir untuk memudahkan peninjauan cepat.</div>
                    </div>
                    <span class="soft-chip"><i class="bi bi-table"></i>{{ $employee->attendances->count() }} record tampil</span>
                </div>
                <div class="card-body p-0">
                    @if($employee->attendances->count())
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->attendances as $attendance)
                                        <tr>
                                            <td class="ps-4">{{ formatDate($attendance->date) }}</td>
                                            <td>{{ $attendance->shift?->name ?: '-' }}</td>
                                            <td>{{ $attendance->check_in ? formatTime($attendance->check_in) : '-' }}</td>
                                            <td>{{ $attendance->check_out ? formatTime($attendance->check_out) : '-' }}</td>
                                            <td>{{ $attendance->total_hours ? number_format($attendance->total_hours, 2) . ' jam' : '-' }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ getStatusBadge($attendance->status) }}">
                                                    {{ getStatusText($attendance->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <div class="fw-semibold mb-1">Belum ada riwayat kehadiran</div>
                            <div class="small">Data attendance akan muncul setelah karyawan mulai menggunakan sistem absensi.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('admin.employees.delete', $employee->id) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .preview-avatar {
        width: 96px;
        height: 96px;
        border-radius: 30px;
        display: grid;
        place-items: center;
        font-size: 2.1rem;
        font-weight: 800;
        color: #fffaf9;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    function deleteEmployee(id, name) {
        appDeleteConfirm(() => document.getElementById('delete-form').submit(), {
            title: 'Hapus karyawan ini?',
            text: `Data ${name} akan dihapus permanen dari daftar karyawan.`
        });
    }
</script>
@endpush
