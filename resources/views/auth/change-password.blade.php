@extends('layouts.app')

@section('title', 'Ubah Password')

@section('sidebar')
    @if(auth()->user()->isAdmin())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
    @elseif(auth()->user()->isHRD())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('hrd.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
    @endif
    
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('change-password') }}">
            <i class="bi bi-key"></i> Ubah Password
        </a>
    </li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h2 class="page-title">
            <i class="bi bi-key me-2"></i>
            Ubah Password
        </h2>

        <div class="card">
            <div class="card-body p-4">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Perhatian:</strong> Password baru harus minimal 8 karakter dan mengandung kombinasi huruf dan angka untuk keamanan yang lebih baik.
                </div>

                <form action="{{ route('change-password.post') }}" method="POST" id="changePasswordForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="current_password" class="form-label">
                            <i class="bi bi-lock me-1"></i>
                            Password Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password"
                                   placeholder="Masukkan password saat ini"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="bi bi-eye" id="current_password_icon"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="form-label">
                            <i class="bi bi-shield-lock me-1"></i>
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" 
                                   name="new_password"
                                   placeholder="Masukkan password baru (min. 8 karakter)"
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                <i class="bi bi-eye" id="new_password_icon"></i>
                            </button>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Password minimal 8 karakter</div>
                    </div>

                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label">
                            <i class="bi bi-shield-check me-1"></i>
                            Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation"
                                   placeholder="Ketik ulang password baru"
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                            </button>
                        </div>
                        <div class="form-text">Ketik ulang password baru untuk konfirmasi</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Ubah Password
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Reset
                        </button>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali
                            </a>
                        @elseif(auth()->user()->isHRD())
                            <a href="{{ route('hrd.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali
                            </a>
                        @else
                            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Requirements Card -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Tips Membuat Password yang Kuat
                </h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Gunakan minimal 8 karakter</li>
                    <li>Kombinasikan huruf besar dan huruf kecil</li>
                    <li>Tambahkan angka dan karakter khusus</li>
                    <li>Hindari menggunakan informasi pribadi</li>
                    <li>Jangan gunakan password yang sama dengan akun lain</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

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
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Password baru dan konfirmasi password tidak sama!',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
        
        if (newPassword.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Terlalu Pendek',
                text: 'Password baru harus minimal 8 karakter!',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
    });

    // Password strength indicator
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        // You can add visual feedback here if needed
    });
</script>
@endpush