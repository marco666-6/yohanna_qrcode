@extends('layouts.app')

@section('title', 'Profil')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.scanner') }}">
            <i class="bi bi-qr-code-scan"></i> Scan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.attendance-history') }}">
            <i class="bi bi-clock-history"></i> Riwayat Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.notifications') }}">
            <i class="bi bi-bell"></i> Notifikasi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('employee.profile') }}">
            <i class="bi bi-person"></i> Profil
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-person me-2"></i>
                    Profil Saya
                </h2>
                <p class="text-muted mb-0">Informasi pribadi dan data karyawan</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px;">
                        <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->position ?? 'Karyawan' }}</p>
                <span class="badge bg-{{ getRoleBadge($user->role) }} mb-3">
                    {{ getRoleText($user->role) }}
                </span>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                        <i class="bi bi-circle-fill me-1"></i>
                        {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('change-password') }}" class="btn btn-outline-primary">
                        <i class="bi bi-key me-2"></i>
                        Ubah Password
                    </a>
                    <a href="{{ route('employee.scanner') }}" class="btn btn-outline-success">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Scan Absensi
                    </a>
                    <a href="{{ route('employee.attendance-history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history me-2"></i>
                        Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-vcard me-2"></i>
                    Informasi Pribadi
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Nama Lengkap</label>
                        <p class="mb-0"><strong>{{ $user->name }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">ID Karyawan</label>
                        <p class="mb-0">
                            <span class="badge bg-secondary fs-6">{{ $user->employee_id }}</span>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0">
                            <i class="bi bi-envelope text-primary me-2"></i>
                            <strong>{{ $user->email }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Nomor Telepon</label>
                        <p class="mb-0">
                            <i class="bi bi-telephone text-success me-2"></i>
                            {{ $user->phone ?? '-' }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Departemen</label>
                        <p class="mb-0">
                            <i class="bi bi-building text-info me-2"></i>
                            {{ $user->department ?? '-' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Posisi</label>
                        <p class="mb-0">
                            <i class="bi bi-briefcase text-warning me-2"></i>
                            {{ $user->position ?? '-' }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="text-muted small">Terdaftar Sejak</label>
                        <p class="mb-0">
                            <i class="bi bi-calendar-check text-primary me-2"></i>
                            {{ formatDate($user->created_at) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Status Akun</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Schedule -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock me-2"></i>
                    Jadwal Kerja
                </h5>
            </div>
            <div class="card-body">
                @if($user->shift)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small">Shift</label>
                                <p class="mb-0">
                                    <span class="badge bg-primary fs-6">{{ $user->shift->name }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small">Jam Kerja</label>
                                <p class="mb-0">
                                    <i class="bi bi-clock text-success me-2"></i>
                                    <strong>
                                        {{ formatTime($user->shift->start_time) }} - 
                                        {{ formatTime($user->shift->end_time) }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small">Toleransi Keterlambatan</label>
                                <p class="mb-0">
                                    <i class="bi bi-hourglass-split text-warning me-2"></i>
                                    <strong>{{ $user->shift->late_tolerance }} menit</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small">Status Shift</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $user->shift->is_active ? 'success' : 'danger' }}">
                                        {{ $user->shift->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Belum ada jadwal shift yang ditetapkan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Information -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    Informasi Akun
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Role/Peran</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ getRoleBadge($user->role) }} fs-6">
                                {{ getRoleText($user->role) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Email Login</label>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                </div>
                
                <hr>

                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Keamanan Akun</strong>
                    <p class="mb-2 mt-2">
                        Untuk menjaga keamanan akun Anda, pastikan untuk:
                    </p>
                    <ul class="mb-0">
                        <li>Menggunakan password yang kuat dan unik</li>
                        <li>Tidak membagikan password kepada siapapun</li>
                        <li>Mengganti password secara berkala</li>
                        <li>Logout setelah selesai menggunakan sistem</li>
                    </ul>
                    <a href="{{ route('change-password') }}" class="btn btn-sm btn-info mt-3">
                        <i class="bi bi-key me-2"></i>
                        Ubah Password Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-light {
        transition: all 0.3s ease;
    }

    .bg-light:hover {
        background-color: #e9ecef !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush
