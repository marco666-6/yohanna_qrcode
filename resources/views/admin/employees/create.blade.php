@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('page-kicker', 'Employee Setup')
@section('page-title', 'Tambah Karyawan Baru')
@section('page-subtitle', 'Lengkapi identitas, penempatan shift, dan kredensial awal dalam form yang lebih rapi dan mudah dicek.')

@section('content')
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker text-white-50">Employee Onboarding</div>
                <h3 class="mb-2">Siapkan akun karyawan baru dengan data kerja yang lengkap sejak awal.</h3>
                <p class="muted mb-0">Preview di sisi kanan akan mengikuti isian form agar admin lebih mudah mengecek kembali sebelum menyimpan.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('admin.employees') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke daftar
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Form data karyawan</div>
                    <div class="small text-muted">Field bertanda bintang wajib diisi.</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.employees.store') }}" method="POST" id="employeeForm" class="d-grid gap-4">
                        @csrf

                        <section class="data-summary">
                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Informasi pribadi</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ID karyawan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required>
                                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Informasi pekerjaan</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Departemen</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department') }}">
                                        @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Posisi / jabatan</label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}">
                                        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Shift kerja <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                            <option value="">Pilih shift kerja</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->name }} ({{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="data-summary-item">
                                <div class="fw-semibold mb-3">Kredensial awal</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" minlength="8" required>
                                            <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password')"><i class="bi bi-eye" id="password_icon"></i></button>
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="small text-muted mt-2">Minimal 8 karakter.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" required>
                                            <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye" id="password_confirmation_icon"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
                            <button type="reset" class="btn btn-outline-primary">Reset Form</button>
                            <a href="{{ route('admin.employees') }}" class="btn btn-outline-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold">Preview ringkas</div>
                    <div class="small text-muted">Akan berubah sesuai isian form.</div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="preview-avatar mx-auto mb-3" id="preview_initial">?</div>
                        <div class="fw-bold fs-5" id="preview_name">Nama belum diisi</div>
                        <div class="text-muted" id="preview_position">Posisi belum diisi</div>
                    </div>
                    <div class="data-summary">
                        <div class="data-summary-item">
                            <div class="small text-muted">Email</div>
                            <div id="preview_email">-</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">ID karyawan</div>
                            <div id="preview_employee_id">-</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Departemen</div>
                            <div id="preview_department">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold">Catatan pengisian</div>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li>Gunakan email yang benar karena akan menjadi akun login.</li>
                        <li>ID karyawan harus unik dan konsisten dengan administrasi internal.</li>
                        <li>Pemilihan shift akan memengaruhi alur absensi QR dan perhitungan keterlambatan.</li>
                        <li>Password awal sebaiknya segera diganti oleh karyawan setelah login pertama.</li>
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

    function syncPreview() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const employeeId = document.getElementById('employee_id').value.trim();
        const department = document.getElementById('department').value.trim();
        const position = document.getElementById('position').value.trim();

        document.getElementById('preview_name').textContent = name || 'Nama belum diisi';
        document.getElementById('preview_email').textContent = email || '-';
        document.getElementById('preview_employee_id').textContent = employeeId || '-';
        document.getElementById('preview_department').textContent = department || '-';
        document.getElementById('preview_position').textContent = position || 'Posisi belum diisi';
        document.getElementById('preview_initial').textContent = (name.charAt(0) || '?').toUpperCase();
    }

    ['name', 'email', 'employee_id', 'department', 'position'].forEach(id => {
        document.getElementById(id).addEventListener('input', syncPreview);
    });
    syncPreview();

    document.getElementById('employeeForm').addEventListener('submit', function (event) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        if (password.length < 8) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Password terlalu pendek', text: 'Password minimal 8 karakter.' });
            return;
        }

        if (password !== confirmation) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Password tidak cocok', text: 'Konfirmasi password harus sama dengan password.' });
        }
    });
</script>
@endpush
