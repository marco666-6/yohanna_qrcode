@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('page-kicker', 'Employee Update')
@section('page-title', 'Edit Data Karyawan')
@section('page-subtitle', 'Perbarui identitas, penempatan shift, status akun, dan password opsional tanpa meninggalkan pola layout baru.')

@section('content')
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker text-white-50">Employee Maintenance</div>
                <h3 class="mb-2">{{ $employee->name }} siap diperbarui.</h3>
                <p class="muted mb-0">Gunakan halaman ini untuk sinkronisasi data kerja, perubahan status akun, atau reset password bila diperlukan.</p>
            </div>
            <div class="col-lg-4 text-lg-end d-flex flex-wrap gap-2 justify-content-lg-end">
                <a href="{{ route('admin.employees.detail', $employee->id) }}" class="btn btn-light">Lihat Detail</a>
                <a href="{{ route('admin.employees') }}" class="btn btn-outline-light" style="border-color:rgba(255,255,255,.35);color:#fff;">Kembali</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Form edit karyawan</div>
                    <div class="small text-muted">Kosongkan password jika tidak ingin mengubah kredensial akun.</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" id="employeeForm" class="d-grid gap-4">
                        @csrf
                        @method('PUT')

                        <section class="data-summary">
                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Informasi pribadi</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ID karyawan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Informasi pekerjaan</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Departemen</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $employee->department) }}">
                                        @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Posisi / jabatan</label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position) }}">
                                        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Shift kerja <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                            <option value="">Pilih shift kerja</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->name }} ({{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status akun <span class="text-danger">*</span></label>
                                        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                                            <option value="1" {{ old('is_active', $employee->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ old('is_active', $employee->is_active) == 0 ? 'selected' : '' }}>Tidak aktif</option>
                                        </select>
                                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Password baru opsional</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Password baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" minlength="8">
                                            <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password')"><i class="bi bi-eye" id="password_icon"></i></button>
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi password baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8">
                                            <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye" id="password_confirmation_icon"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Update Data</button>
                            <a href="{{ route('admin.employees.detail', $employee->id) }}" class="btn btn-outline-primary">Buka Detail</a>
                            <a href="{{ route('admin.employees') }}" class="btn btn-outline-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold">Ringkasan akun</div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="preview-avatar mx-auto mb-3">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                        <div class="fw-bold fs-5">{{ $employee->name }}</div>
                        <div class="text-muted">{{ $employee->employee_id }}</div>
                    </div>
                    <div class="data-summary">
                        <div class="data-summary-item">
                            <div class="small text-muted">Email</div>
                            <div>{{ $employee->email }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Shift saat ini</div>
                            <div>{{ $employee->shift?->name ?? '-' }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Status</div>
                            <span class="badge {{ $employee->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                {{ $employee->is_active ? 'Aktif' : 'Tidak aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold">Hal yang perlu diperhatikan</div>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li>Mengubah email akan mengubah identitas login karyawan.</li>
                        <li>Status tidak aktif akan memblokir login tanpa menghapus histori attendance.</li>
                        <li>Perubahan shift memengaruhi window absensi dan laporan kerja berikutnya.</li>
                        <li>Password hanya berubah jika dua field password diisi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .preview-avatar {
        width: 88px;
        height: 88px;
        border-radius: 28px;
        display: grid;
        place-items: center;
        font-size: 2rem;
        font-weight: 800;
        color: #fffaf9;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        field.type = field.type === 'password' ? 'text' : 'password';
        icon.className = field.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }

    document.getElementById('employeeForm').addEventListener('submit', function (event) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        if ((password || confirmation) && password.length < 8) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Password terlalu pendek', text: 'Password baru minimal 8 karakter.' });
            return;
        }

        if (password !== confirmation) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Password tidak cocok', text: 'Konfirmasi password harus sama dengan password baru.' });
        }
    });
</script>
@endpush
