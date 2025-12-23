@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.employees') }}">
            <i class="bi bi-people"></i> Kelola Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.shifts') }}">
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
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Karyawan
                </h2>
                <p class="text-muted mb-0">Perbarui data karyawan: <strong>{{ $employee->name }}</strong></p>
            </div>
            <div>
                <a href="{{ route('admin.employees.detail', $employee->id) }}" class="btn btn-info me-2">
                    <i class="bi bi-eye me-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Form Edit Data Karyawan
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" id="employeeForm">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person me-2"></i>
                            Informasi Pribadi
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name"
                                       value="{{ old('name', $employee->name) }}"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email"
                                       value="{{ old('email', $employee->email) }}"
                                       placeholder="contoh@email.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">
                                    ID Karyawan <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" 
                                       name="employee_id"
                                       value="{{ old('employee_id', $employee->employee_id) }}"
                                       placeholder="Contoh: EMP001"
                                       required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">ID unik untuk karyawan</small>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    Nomor Telepon
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone"
                                       value="{{ old('phone', $employee->phone) }}"
                                       placeholder="08123456789">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-briefcase me-2"></i>
                            Informasi Pekerjaan
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="department" class="form-label">
                                    Departemen
                                </label>
                                <input type="text" 
                                       class="form-control @error('department') is-invalid @enderror" 
                                       id="department" 
                                       name="department"
                                       value="{{ old('department', $employee->department) }}"
                                       placeholder="Contoh: IT, HR, Finance">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="position" class="form-label">
                                    Posisi/Jabatan
                                </label>
                                <input type="text" 
                                       class="form-control @error('position') is-invalid @enderror" 
                                       id="position" 
                                       name="position"
                                       value="{{ old('position', $employee->position) }}"
                                       placeholder="Contoh: Staff, Manager">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="shift_id" class="form-label">
                                    Shift Kerja <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2 @error('shift_id') is-invalid @enderror" 
                                        id="shift_id" 
                                        name="shift_id"
                                        required>
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" 
                                                {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }} 
                                            ({{ date('H:i', strtotime($shift->start_time)) }} - {{ date('H:i', strtotime($shift->end_time)) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label">
                                    Status Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        id="is_active" 
                                        name="is_active"
                                        required>
                                    <option value="1" {{ old('is_active', $employee->is_active) == 1 ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="0" {{ old('is_active', $employee->is_active) == 0 ? 'selected' : '' }}>
                                        Tidak Aktif
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Status "Tidak Aktif" akan menonaktifkan akses login</small>
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-key me-2"></i>
                            Ubah Password (Opsional)
                        </h6>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Kosongkan field password jika tidak ingin mengubah password
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="Minimal 8 karakter"
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password_icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Ketik ulang password"
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Harus sama dengan password baru</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-2"></i>
                            Update Data
                        </button>
                        <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Info Karyawan
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-preview bg-primary text-white mx-auto">
                        <span>{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                    </div>
                    <h5 class="mt-3 mb-1">{{ $employee->name }}</h5>
                    <p class="text-muted mb-0">{{ $employee->employee_id }}</p>
                </div>
                
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td><strong>{{ $employee->email }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon:</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Departemen:</td>
                        <td>{{ $employee->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Posisi:</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Shift:</td>
                        <td>
                            @if($employee->shift)
                                <span class="badge bg-info">{{ $employee->shift->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terdaftar:</td>
                        <td>{{ formatDate($employee->created_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Warning Card -->
        <div class="card mt-3 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Perhatian
                </h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li>Perubahan email akan mengubah kredensial login</li>
                    <li>Perubahan shift akan mempengaruhi jadwal absensi</li>
                    <li>Status "Tidak Aktif" akan menonaktifkan akses login karyawan</li>
                    <li>Pastikan semua data sudah benar sebelum menyimpan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Form validation
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        // Only validate if password is being changed
        if (password || confirmPassword) {
            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Tidak Cocok',
                    text: 'Password baru dan konfirmasi password harus sama!',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Terlalu Pendek',
                    text: 'Password minimal 8 karakter!',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
        }
    });
</script>
@endpush