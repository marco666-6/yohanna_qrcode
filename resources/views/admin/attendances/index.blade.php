@extends('layouts.app')

@section('title', 'Kelola Kehadiran')
@section('page-kicker', 'Attendance Control')
@section('page-title', 'Kelola Kehadiran')
@section('page-subtitle', 'Gunakan filter tanggal, karyawan, status, dan pagination agar koreksi data tetap aman dan ringan pada volume besar.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        @foreach([
            ['label' => 'Total Record', 'value' => $attendanceStats['total'], 'helper' => $attendanceStats['hours'] . ' jam kerja tercatat', 'icon' => 'bi-database', 'tone' => 'primary'],
            ['label' => 'Tepat Waktu', 'value' => $attendanceStats['on_time'], 'helper' => 'Status on time', 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Terlambat', 'value' => $attendanceStats['late'], 'helper' => 'Status late', 'icon' => 'bi-alarm', 'tone' => 'warning'],
            ['label' => 'Perlu Koreksi', 'value' => $attendanceStats['incomplete'] + $attendanceStats['absent'], 'helper' => 'Belum checkout atau absent', 'icon' => 'bi-pencil-square', 'tone' => 'danger'],
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
        <form action="{{ route('admin.attendances') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-2">
                <label class="form-label">Tanggal awal</label>
                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Tanggal akhir</label>
                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Karyawan</label>
                <select class="form-select select2" name="user_id">
                    <option value="">Semua karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (string) request('user_id') === (string) $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Shift</label>
                <select class="form-select" name="shift_id">
                    <option value="">Semua shift</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ (string) request('shift_id') === (string) $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua status</option>
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
                <label class="form-label">Cari cepat</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama, ID karyawan, departemen">
            </div>
            <div class="col-lg-8 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('admin.attendances') }}" class="btn btn-outline-primary">Reset</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#forceAddModal">
                    <i class="bi bi-plus-circle me-1"></i>Tambah atau Koreksi
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Data kehadiran</div>
                <div class="small text-muted">Menampilkan {{ $attendances->count() }} data dari {{ $attendances->total() }} hasil.</div>
            </div>
            <span class="soft-chip"><i class="bi bi-clipboard-check"></i>Manual correction tetap tercatat by admin</span>
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
                                <td>{{ $attendance->total_hours > 0 ? number_format($attendance->total_hours, 2) . ' jam' : '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ getStatusBadge($attendance->status) }}">
                                        {{ getStatusText($attendance->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($attendance->notes)
                                        <div class="small">{{ $attendance->notes }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                    @if($attendance->creator)
                                        <div class="small text-muted mt-1">oleh {{ $attendance->creator->name }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            onclick="editAttendance({{ $attendance->id }}, @js($attendance->date->format('Y-m-d')), @js($attendance->user->name), @js($attendance->check_in), @js($attendance->check_out), @js($attendance->status), @js($attendance->notes))">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <div class="fw-semibold mb-1">Tidak ada data kehadiran</div>
                                        <div class="small">Sesuaikan filter atau tambahkan koreksi manual.</div>
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

<div class="modal fade" id="forceAddModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah atau koreksi kehadiran manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.attendances.force-add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Karyawan</label>
                            <select class="form-select select2" name="user_id" required>
                                <option value="">Pilih karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-in</label>
                            <input type="time" class="form-control" name="check_in" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-out</label>
                            <input type="time" class="form-control" name="check_out">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="on_time">Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="incomplete">Belum Check-out</option>
                                <option value="absent">Tidak Hadir</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Tuliskan alasan koreksi atau penyesuaian data."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAttendanceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-light border">
                        <strong id="edit_employee_name"></strong> - <span id="edit_date_display"></span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Check-in</label>
                            <input type="time" class="form-control" id="edit_check_in" name="check_in" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-out</label>
                            <input type="time" class="form-control" id="edit_check_out" name="check_out">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="on_time">Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="incomplete">Belum Check-out</option>
                                <option value="absent">Tidak Hadir</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Kehadiran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editAttendance(id, date, employeeName, checkIn, checkOut, status, notes) {
        document.getElementById('edit_employee_name').textContent = employeeName;
        document.getElementById('edit_date_display').textContent = date;
        document.getElementById('edit_check_in').value = checkIn ? String(checkIn).substring(0, 5) : '';
        document.getElementById('edit_check_out').value = checkOut ? String(checkOut).substring(0, 5) : '';
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = notes || '';
        document.getElementById('editAttendanceForm').action = `/admin/attendances/${id}`;
        new bootstrap.Modal(document.getElementById('editAttendanceModal')).show();
    }
</script>
@endpush
