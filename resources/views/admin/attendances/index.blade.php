@extends('layouts.app')

@section('title', 'Kelola Kehadiran')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.employees') }}">
            <i class="bi bi-people"></i> Kelola Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.shifts') }}">
            <i class="bi bi-clock-history"></i> Kelola Shift
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.attendances') }}">
            <i class="bi bi-calendar-check"></i> Kelola Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.qr-code') }}">
            <i class="bi bi-qr-code"></i> QR Code
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-calendar-check me-2"></i>
                    Kelola Kehadiran
                </h2>
                <p class="text-muted mb-0">Manajemen data kehadiran karyawan</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#forceAddModal">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah/Update Kehadiran
            </button>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.attendances') }}" method="GET" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" 
                           class="form-control" 
                           name="date" 
                           value="{{ request('date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Karyawan</label>
                    <select class="form-select select2" name="user_id">
                        <option value="">-- Semua Karyawan --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="on_time" {{ request('status') == 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Belum Check-out</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.attendances') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>
            Data Kehadiran
            @if(request('date'))
                - {{ formatDate(request('date')) }}
            @endif
        </h5>
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
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>
                                <strong>{{ formatDate($attendance->date) }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $attendance->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $attendance->user->employee_id }}</small>
                                </div>
                            </td>
                            <td>
                                @if($attendance->shift)
                                    <span class="badge bg-secondary">
                                        {{ $attendance->shift->name }}
                                    </span>
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
                                    <strong>{{ number_format($attendance->total_hours, 2) }} jam</strong>
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
                                    <i class="bi bi-file-text text-info" 
                                       title="{{ $attendance->notes }}" 
                                       data-bs-toggle="tooltip"></i>
                                @else
                                    -
                                @endif
                                @if($attendance->creator)
                                    <br><small class="text-muted">
                                        <i class="bi bi-person-badge"></i> 
                                        {{ $attendance->creator->name }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-warning" 
                                        onclick="editAttendance({{ $attendance->id }}, '{{ $attendance->date }}', '{{ $attendance->user->name }}', '{{ $attendance->check_in }}', '{{ $attendance->check_out }}', '{{ $attendance->status }}', '{{ addslashes($attendance->notes ?? '') }}')"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Tidak ada data kehadiran untuk filter yang dipilih</p>
            </div>
        @endif
    </div>
</div>

<!-- Force Add/Update Modal -->
<div class="modal fade" id="forceAddModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah/Update Kehadiran Manual
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.attendances.force-add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Gunakan fitur ini hanya untuk koreksi atau keperluan khusus.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="force_user_id" class="form-label">
                                Karyawan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select select2" id="force_user_id" name="user_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->name }} ({{ $emp->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="force_date" class="form-label">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="force_date" 
                                   name="date"
                                   value="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="force_check_in" class="form-label">
                                Check-in <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="force_check_in" 
                                   name="check_in"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="force_check_out" class="form-label">
                                Check-out
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="force_check_out" 
                                   name="check_out">
                        </div>

                        <div class="col-md-12">
                            <label for="force_status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="force_status" name="status" required>
                                <option value="on_time">Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="incomplete">Belum Check-out</option>
                                <option value="absent">Tidak Hadir</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="force_notes" class="form-label">
                                Catatan
                            </label>
                            <textarea class="form-control" 
                                      id="force_notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Alasan atau keterangan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Kehadiran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAttendanceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong id="edit_employee_name"></strong> - <span id="edit_date_display"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_check_in" class="form-label">
                                Check-in <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="edit_check_in" 
                                   name="check_in"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_check_out" class="form-label">
                                Check-out
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="edit_check_out" 
                                   name="check_out">
                        </div>

                        <div class="col-md-12">
                            <label for="edit_status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="on_time">Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="incomplete">Belum Check-out</option>
                                <option value="absent">Tidak Hadir</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="edit_notes" class="form-label">
                                Catatan
                            </label>
                            <textarea class="form-control" 
                                      id="edit_notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Alasan atau keterangan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-2"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Edit attendance function
    function editAttendance(id, date, employeeName, checkIn, checkOut, status, notes) {
        document.getElementById('edit_employee_name').textContent = employeeName;
        document.getElementById('edit_date_display').textContent = date;
        document.getElementById('edit_check_in').value = checkIn || '';
        document.getElementById('edit_check_out').value = checkOut || '';
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = notes || '';
        
        const form = document.getElementById('editAttendanceForm');
        form.action = `/admin/attendances/${id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
        modal.show();
    }
</script>
@endpush