@extends('layouts.app')

@section('title', 'Ubah Password')
@section('page-kicker', 'Account Security')
@section('page-title', 'Ubah Password')
@section('page-subtitle', 'Perbarui password akun Anda dengan form keamanan yang lebih bersih dan konsisten untuk semua role.')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="hero-panel mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <div class="page-kicker text-white-50">Security Update</div>
                    <h3 class="mb-2">Jaga akun tetap aman dengan password yang kuat dan mudah Anda kelola.</h3>
                    <p class="muted mb-0">Gunakan kombinasi huruf, angka, dan panjang minimal 8 karakter. Setelah berhasil, sesi login tetap aman dan data akun tidak berubah.</p>
                </div>
                <div class="col-lg-4">
                    <div class="data-summary-item" style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.16);color:#fff;">
                        <div class="small text-white-50">Role aktif</div>
                        <div class="fw-semibold">{{ getRoleText(auth()->user()->role) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <div class="fw-bold fs-5">Form perubahan password</div>
                        <div class="small text-muted">Isi semua field berikut untuk menyimpan password baru.</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('change-password.post') }}" method="POST" id="changePasswordForm" class="d-grid gap-4">
                            @csrf
                            <div class="data-summary-item">
                                <label class="form-label">Password saat ini <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-primary" type="button" onclick="togglePassword('current_password')"><i class="bi bi-eye" id="current_password_icon"></i></button>
                                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="data-summary-item">
                                <label class="form-label">Password baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" minlength="8" required>
                                    <button class="btn btn-outline-primary" type="button" onclick="togglePassword('new_password')"><i class="bi bi-eye" id="new_password_icon"></i></button>
                                    @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="small text-muted mt-2">Minimal 8 karakter.</div>
                            </div>

                            <div class="data-summary-item">
                                <label class="form-label">Konfirmasi password baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" minlength="8" required>
                                    <button class="btn btn-outline-primary" type="button" onclick="togglePassword('new_password_confirmation')"><i class="bi bi-eye" id="new_password_confirmation_icon"></i></button>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">Ubah Password</button>
                                <button type="reset" class="btn btn-outline-primary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="fw-bold">Panduan keamanan</div>
                    </div>
                    <div class="card-body">
                        <div class="data-summary">
                            <div class="data-summary-item">
                                <div class="fw-semibold mb-2">Checklist password yang baik</div>
                                <ul class="mb-0 ps-3">
                                    <li>Minimal 8 karakter.</li>
                                    <li>Campurkan huruf besar dan kecil bila memungkinkan.</li>
                                    <li>Tambahkan angka atau simbol agar lebih kuat.</li>
                                    <li>Hindari data pribadi yang mudah ditebak.</li>
                                </ul>
                            </div>
                            <div class="data-summary-item">
                                <div class="fw-semibold mb-2">Setelah password diubah</div>
                                <p class="mb-0 text-muted">Pastikan Anda mengingat password baru dan jangan membagikannya ke orang lain. Jika akun dipakai bersama perangkat kantor, logout setelah selesai.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        field.type = field.type === 'password' ? 'text' : 'password';
        icon.className = field.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }

    document.getElementById('changePasswordForm').addEventListener('submit', function (event) {
        const password = document.getElementById('new_password').value;
        const confirmation = document.getElementById('new_password_confirmation').value;

        if (password.length < 8) {
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
