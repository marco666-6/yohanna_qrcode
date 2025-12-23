@extends('layouts.app')

@section('title', 'Kelola Shift')

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
        <a class="nav-link active" href="{{ route('admin.shifts') }}">
            <i class="bi bi-clock-history"></i> Kelola Shift
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.attendances') }}">
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
                    <i class="bi bi-clock-history me-2"></i>
                    Kelola Shift Kerja
                </h2>
                <p class="text-muted mb-0">Manajemen shift dan jadwal kerja karyawan</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Shift
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Shift</p>
                        <h3 class="mb-0">{{ $shifts->count() }}</h3>
                    </div>
                    <i class="bi bi-clock text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Shift Aktif</p>
                        <h3 class="mb-0">{{ $shifts->where('is_active', true)->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Karyawan di Shift</p>
                        <h3 class="mb-0">{{ $shifts->sum('users_count') }}</h3>
                    </div>
                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shifts Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>
            Daftar Shift Kerja
        </h5>
    </div>
    <div class="card-body">
        @if($shifts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Shift</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th>Toleransi</th>
                            <th>Karyawan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                        <tr>
                            <td>
                                <strong>
                                    <i class="bi bi-clock me-2 text-primary"></i>
                                    {{ $shift->name }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-sunrise me-1"></i>
                                    {{ formatTime($shift->start_time) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="bi bi-sunset me-1"></i>
                                    {{ formatTime($shift->end_time) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $start = \Carbon\Carbon::parse($shift->start_time);
                                    $end = \Carbon\Carbon::parse($shift->end_time);
                                    $duration = $start->diffInHours($end);
                                @endphp
                                <strong>{{ $duration }} Jam</strong>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    {{ $shift->late_tolerance }} Menit
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $shift->users_count }} Orang
                                </span>
                            </td>
                            <td>
                                @if($shift->is_active)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-warning" 
                                            onclick="editShift({{ $shift->id }}, '{{ $shift->name }}', '{{ $shift->start_time }}', '{{ $shift->end_time }}', {{ $shift->late_tolerance }}, {{ $shift->is_active ? 1 : 0 }})"
                                            title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteShift({{ $shift->id }}, '{{ $shift->name }}', {{ $shift->users_count }})"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clock text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3 mb-3">Belum ada shift kerja yang terdaftar</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Shift Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Shift Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.shifts.store') }}" method="POST" id="addShiftForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">
                            Nama Shift <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="add_name" 
                               name="name"
                               placeholder="Contoh: Shift Pagi"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_start_time" class="form-label">
                                Waktu Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="add_start_time" 
                                   name="start_time"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_end_time" class="form-label">
                                Waktu Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="add_end_time" 
                                   name="end_time"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_late_tolerance" class="form-label">
                            Toleransi Keterlambatan (Menit) <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="add_late_tolerance" 
                               name="late_tolerance"
                               min="0"
                               max="60"
                               value="15"
                               required>
                        <small class="text-muted">Maksimal 60 menit</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Shift baru akan otomatis aktif setelah dibuat</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Simpan Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Shift Modal -->
<div class="modal fade" id="editShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">
                            Nama Shift <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_name" 
                               name="name"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time" class="form-label">
                                Waktu Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="edit_start_time" 
                                   name="start_time"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time" class="form-label">
                                Waktu Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="edit_end_time" 
                                   name="end_time"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_late_tolerance" class="form-label">
                            Toleransi Keterlambatan (Menit) <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="edit_late_tolerance" 
                               name="late_tolerance"
                               min="0"
                               max="60"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="edit_is_active" name="is_active" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-2"></i>
                        Update Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Edit shift function
    function editShift(id, name, startTime, endTime, lateTolerance, isActive) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_time').value = endTime;
        document.getElementById('edit_late_tolerance').value = lateTolerance;
        document.getElementById('edit_is_active').value = isActive;
        
        const form = document.getElementById('editShiftForm');
        form.action = `/admin/shifts/${id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('editShiftModal'));
        modal.show();
    }

    // Delete shift function
    function deleteShift(id, name, usersCount) {
        if (usersCount > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Menghapus',
                html: `Shift <strong>${name}</strong> masih digunakan oleh <strong>${usersCount} karyawan</strong>.<br><br>Pindahkan karyawan ke shift lain terlebih dahulu sebelum menghapus shift ini.`,
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus shift:<br><strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/shifts/${id}`;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Form validation
    document.getElementById('addShiftForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('add_start_time').value;
        const endTime = document.getElementById('add_end_time').value;
        
        if (startTime >= endTime) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Waktu Tidak Valid',
                text: 'Waktu selesai harus lebih besar dari waktu mulai!',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
    });

    document.getElementById('editShiftForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('edit_start_time').value;
        const endTime = document.getElementById('edit_end_time').value;
        
        if (startTime >= endTime) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Waktu Tidak Valid',
                text: 'Waktu selesai harus lebih besar dari waktu mulai!',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
    });
</script>
@endpush