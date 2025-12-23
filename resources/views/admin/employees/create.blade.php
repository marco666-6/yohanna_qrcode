@extends('layouts.app')

@section('title', 'Tambah Karyawan')

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
                    <i class="bi bi-person-plus me-2"></i>
                    Tambah Karyawan Baru
                </h2>
                <p class="text-muted mb-0">Lengkapi form di bawah untuk menambah karyawan baru</p>
            </div>
            <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Form Data Karyawan
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.employees.store') }}" method="POST" id="employeeForm">
                    @csrf

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
                                       value="{{ old('name') }}"
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
                                       value="{{ old('email') }}"
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
                                       value="{{ old('employee_id') }}"
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
                                       value="{{ old('phone') }}"
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
                                       value="{{ old('department') }}"
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
                                       value="{{ old('position') }}"
                                       placeholder="Contoh: Staff, Manager">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
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
                                                {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }} 
                                            ({{ date('H:i', strtotime($shift->start_time)) }} - {{ date('H:i', strtotime($shift->end_time)) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-key me-2"></i>
                            Pengaturan Password
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="Minimal 8 karakter"
                                           required
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
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Ketik ulang password"
                                           required
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Harus sama dengan password</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Simpan Karyawan
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Reset Form
                        </button>
                        <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Panduan Pengisian
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Informasi Pribadi</h6>
                <ul class="small">
                    <li>Nama lengkap sesuai identitas</li>
                    <li>Email akan digunakan untuk login</li>
                    <li>ID Karyawan harus unik</li>
                    <li>Nomor telepon opsional</li>
                </ul>

                <h6 class="text-primary mt-3">Informasi Pekerjaan</h6>
                <ul class="small">
                    <li>Departemen dan posisi opsional</li>
                    <li>Shift kerja wajib dipilih</li>
                    <li>Shift menentukan jadwal absensi</li>
                </ul>

                <h6 class="text-primary mt-3">Password</h6>
                <ul class="small">
                    <li>Minimal 8 karakter</li>
                    <li>Gunakan kombinasi huruf & angka</li>
                    <li>Password dapat diubah nanti</li>
                </ul>

                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <small><strong>Perhatian:</strong> Pastikan semua data sudah benar sebelum disimpan!</small>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Preview
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-preview bg-primary text-white mx-auto">
                        <span id="preview_initial">?</span>
                    </div>
                </div>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Nama:</td>
                        <td><strong id="preview_name">-</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td id="preview_email">-</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ID:</td>
                        <td id="preview_employee_id">-</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Departemen:</td>
                        <td id="preview_department">-</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Posisi:</td>
                        <td id="preview_position">-</td>
                    </tr>
                </table>
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

    // Live preview
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value || '-';
        document.getElementById('preview_name').textContent = name;
        document.getElementById('preview_initial').textContent = name.charAt(0).toUpperCase() || '?';
    });

    document.getElementById('email').addEventListener('input', function() {
        document.getElementById('preview_email').textContent = this.value || '-';
    });

    document.getElementById('employee_id').addEventListener('input', function() {
        document.getElementById('preview_employee_id').textContent = this.value || '-';
    });

    document.getElementById('department').addEventListener('input', function() {
        document.getElementById('preview_department').textContent = this.value || '-';
    });

    document.getElementById('position').addEventListener('input', function() {
        document.getElementById('preview_position').textContent = this.value || '-';
    });

    // Form validation
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Password dan konfirmasi password harus sama!',
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
    });
</script>
@endpush