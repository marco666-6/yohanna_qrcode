@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-kicker', 'My Profile')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Lihat data akun, informasi kerja, dan akses cepat ke pengaturan atau absensi dari halaman profil yang lebih rapi.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="preview-avatar mx-auto mb-3">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="fw-bold fs-4">{{ $user->name }}</div>
                    <div class="text-muted mb-2">{{ $user->position ?: 'Karyawan' }}</div>
                    <span class="badge {{ $user->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ $user->is_active ? 'Akun aktif' : 'Akun tidak aktif' }}</span>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('change-password') }}" class="btn btn-primary">Ubah Password</a>
                        <a href="{{ route('employee.scanner') }}" class="btn btn-outline-primary">Scan Absensi</a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="fw-bold">Informasi akun</div>
                </div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item"><div class="small text-muted">Role</div><div>{{ getRoleText($user->role) }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Email login</div><div>{{ $user->email }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Terdaftar sejak</div><div>{{ formatDate($user->created_at) }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Informasi pribadi</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Nama lengkap</div><div class="fw-semibold">{{ $user->name }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">ID karyawan</div><div class="fw-semibold">{{ $user->employee_id }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Email</div><div>{{ $user->email }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Telepon</div><div>{{ $user->phone ?: '-' }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Departemen</div><div>{{ $user->department ?: '-' }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Posisi</div><div>{{ $user->position ?: '-' }}</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Jadwal kerja</div>
                </div>
                <div class="card-body">
                    @if($user->shift)
                        <div class="row g-3">
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Nama shift</div><div class="fw-semibold">{{ $user->shift->name }}</div></div></div>
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Jam kerja</div><div class="fw-semibold">{{ formatTime($user->shift->start_time) }} - {{ formatTime($user->shift->end_time) }}</div></div></div>
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Toleransi terlambat</div><div>{{ $user->shift->late_tolerance }} menit</div></div></div>
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Status shift</div><div>{{ $user->shift->is_active ? 'Aktif' : 'Tidak aktif' }}</div></div></div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-clock-history"></i>
                            <div class="fw-semibold mb-1">Belum ada shift yang ditetapkan</div>
                            <div class="small">Hubungi admin jika Anda belum mendapatkan jadwal kerja.</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Aksi cepat</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('employee.attendance-history') }}" class="quick-link">
                                <div class="quick-link-icon" style="background:rgba(79,138,102,.14);color:#4f8a66;"><i class="bi bi-clock-history"></i></div>
                                <div><div class="fw-semibold">Riwayat</div><div class="small text-muted">Cek attendance</div></div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('employee.leave-requests') }}" class="quick-link">
                                <div class="quick-link-icon" style="background:rgba(200,138,77,.14);color:#c88a4d;"><i class="bi bi-calendar-event"></i></div>
                                <div><div class="fw-semibold">Cuti</div><div class="small text-muted">Lihat pengajuan</div></div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('employee.notifications') }}" class="quick-link">
                                <div class="quick-link-icon" style="background:rgba(138,127,197,.14);color:#8a7fc5;"><i class="bi bi-bell"></i></div>
                                <div><div class="fw-semibold">Notifikasi</div><div class="small text-muted">Lihat update</div></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .preview-avatar {
        width: 96px;
        height: 96px;
        border-radius: 30px;
        display: grid;
        place-items: center;
        font-size: 2.1rem;
        font-weight: 800;
        color: #fffaf9;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }
</style>
@endpush
