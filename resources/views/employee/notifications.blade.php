@extends('layouts.app')

@section('title', 'Notifikasi')

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
        <a class="nav-link active" href="{{ route('employee.notifications') }}">
            <i class="bi bi-bell"></i> Notifikasi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.profile') }}">
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
                    <i class="bi bi-bell me-2"></i>
                    Notifikasi
                </h2>
                <p class="text-muted mb-0">Lihat semua notifikasi sistem</p>
            </div>
        </div>
    </div>
</div>

@if($notifications->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Daftar Notifikasi
                        </h5>
                        <span class="badge bg-primary">
                            {{ $notifications->total() }} Total
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @php
                                            $iconClass = match($notification->type) {
                                                'check_in' => 'bi-box-arrow-in-right text-success',
                                                'check_out' => 'bi-box-arrow-right text-danger',
                                                'leave_approved' => 'bi-check-circle text-success',
                                                'leave_rejected' => 'bi-x-circle text-danger',
                                                'leave_request' => 'bi-calendar-event text-warning',
                                                'attendance_note' => 'bi-sticky text-info',
                                                default => 'bi-info-circle text-primary',
                                            };
                                        @endphp
                                        <i class="bi {{ $iconClass }} me-2 fs-4"></i>
                                        <h6 class="mb-0">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ formatDateTime($notification->created_at) }}
                                    </small>
                                </div>
                                <div class="ms-3">
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary">Baru</span>
                                    @else
                                        <span class="badge bg-secondary">Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 5rem;"></i>
                    <h4 class="mt-4 text-muted">Tidak Ada Notifikasi</h4>
                    <p class="text-muted">Anda belum memiliki notifikasi apapun</p>
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-house me-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Information Card -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Tentang Notifikasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Jenis Notifikasi</h6>
                        <ul class="mb-0">
                            <li class="mb-2">
                                <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                <strong>Check-in:</strong> Konfirmasi absensi masuk
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-box-arrow-right text-danger me-2"></i>
                                <strong>Check-out:</strong> Konfirmasi absensi pulang
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Cuti Disetujui:</strong> Pengajuan cuti Anda disetujui
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger me-2"></i>
                                <strong>Cuti Ditolak:</strong> Pengajuan cuti Anda ditolak
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-sticky text-info me-2"></i>
                                <strong>Catatan Kehadiran:</strong> Admin/HRD menambahkan catatan
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Penting</h6>
                        <ul class="mb-0">
                            <li class="mb-2">Notifikasi otomatis ditandai sebagai "dibaca" saat Anda membuka halaman ini</li>
                            <li class="mb-2">Notifikasi baru ditandai dengan badge <span class="badge bg-primary">Baru</span></li>
                            <li class="mb-2">Anda juga akan menerima notifikasi melalui email</li>
                            <li class="mb-2">Periksa notifikasi secara berkala untuk informasi terbaru</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(5px);
    }

    .list-group-item.bg-light {
        border-left: 4px solid #0d6efd;
    }
</style>
@endpush