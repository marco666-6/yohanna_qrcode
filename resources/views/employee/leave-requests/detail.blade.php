@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')

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
        <a class="nav-link active" href="{{ route('employee.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.notifications') }}">
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
                    <i class="bi bi-file-text me-2"></i>
                    Detail Pengajuan Cuti
                </h2>
                <p class="text-muted mb-0">Informasi lengkap pengajuan cuti Anda</p>
            </div>
            <a href="{{ route('employee.leave-requests') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Information Card -->
        <div class="card mb-3">
            <div class="card-header bg-{{ getLeaveStatusBadge($leaveRequest->status) }} text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Pengajuan
                    </h5>
                    <span class="badge bg-white text-{{ getLeaveStatusBadge($leaveRequest->status) }}">
                        {{ getLeaveStatusText($leaveRequest->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Jenis Cuti</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ getLeaveTypeBadge($leaveRequest->leave_type) }} fs-6">
                                {{ getLeaveTypeText($leaveRequest->leave_type) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Durasi</label>
                        <p class="mb-0">
                            <strong class="fs-5">{{ $leaveRequest->total_days }} Hari</strong>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Mulai</label>
                        <p class="mb-0">
                            <i class="bi bi-calendar-check text-success me-2"></i>
                            <strong>{{ formatDate($leaveRequest->start_date) }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Selesai</label>
                        <p class="mb-0">
                            <i class="bi bi-calendar-x text-danger me-2"></i>
                            <strong>{{ formatDate($leaveRequest->end_date) }}</strong>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="text-muted small">Alasan Cuti</label>
                    <div class="alert alert-light border">
                        {{ $leaveRequest->reason }}
                    </div>
                </div>

                @if($leaveRequest->attachment)
                <hr>
                <div class="mb-3">
                    <label class="text-muted small">Lampiran</label>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-paperclip text-primary me-2 fs-4"></i>
                        <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-2"></i>
                            Download Lampiran
                        </a>
                    </div>
                </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Pengajuan</label>
                        <p class="mb-0">
                            <i class="bi bi-clock-history text-info me-2"></i>
                            {{ formatDateTime($leaveRequest->created_at) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ getLeaveStatusBadge($leaveRequest->status) }} fs-6">
                                {{ getLeaveStatusText($leaveRequest->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Information (if reviewed) -->
        @if($leaveRequest->status !== 'pending')
        <div class="card">
            <div class="card-header bg-{{ $leaveRequest->status === 'approved' ? 'success' : 'danger' }} text-white">
                <h5 class="mb-0">
                    <i class="bi bi-{{ $leaveRequest->status === 'approved' ? 'check-circle' : 'x-circle' }} me-2"></i>
                    Hasil Review
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Direview Oleh</label>
                        <p class="mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            <strong>{{ $leaveRequest->reviewer->name ?? '-' }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Review</label>
                        <p class="mb-0">
                            <i class="bi bi-calendar-check text-info me-2"></i>
                            {{ $leaveRequest->reviewed_at ? formatDateTime($leaveRequest->reviewed_at) : '-' }}
                        </p>
                    </div>
                </div>

                @if($leaveRequest->review_notes)
                <hr>
                <div>
                    <label class="text-muted small">Catatan Review</label>
                    <div class="alert alert-{{ $leaveRequest->status === 'approved' ? 'success' : 'danger' }} border">
                        <i class="bi bi-{{ $leaveRequest->status === 'approved' ? 'check-circle' : 'info-circle' }} me-2"></i>
                        {{ $leaveRequest->review_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Status Timeline -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Status Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="d-flex align-items-start mb-3">
                            <div class="timeline-marker bg-success"></div>
                            <div class="ms-3">
                                <h6 class="mb-1">Pengajuan Dibuat</h6>
                                <small class="text-muted">{{ formatDateTime($leaveRequest->created_at) }}</small>
                            </div>
                        </div>
                    </div>

                    @if($leaveRequest->status !== 'pending')
                    <div class="timeline-item">
                        <div class="d-flex align-items-start mb-3">
                            <div class="timeline-marker bg-{{ $leaveRequest->status === 'approved' ? 'success' : 'danger' }}"></div>
                            <div class="ms-3">
                                <h6 class="mb-1">
                                    {{ $leaveRequest->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </h6>
                                <small class="text-muted">
                                    {{ formatDateTime($leaveRequest->reviewed_at) }}
                                </small>
                                <br>
                                <small class="text-muted">oleh {{ $leaveRequest->reviewer->name ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="timeline-item">
                        <div class="d-flex align-items-start mb-3">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="ms-3">
                                <h6 class="mb-1">Menunggu Review</h6>
                                <small class="text-muted">HRD sedang meninjau pengajuan Anda</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi
                </h6>
            </div>
            <div class="card-body">
                @if($leaveRequest->status === 'pending')
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <strong>Menunggu Persetujuan</strong>
                    <p class="mb-0 small mt-2">
                        Pengajuan cuti Anda sedang ditinjau oleh HRD. Anda akan menerima notifikasi setelah diproses.
                    </p>
                </div>
                @elseif($leaveRequest->status === 'approved')
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Cuti Disetujui</strong>
                    <p class="mb-0 small mt-2">
                        Pengajuan cuti Anda telah disetujui. Pastikan menyelesaikan pekerjaan sebelum cuti dimulai.
                    </p>
                </div>
                @else
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>Cuti Ditolak</strong>
                    <p class="mb-0 small mt-2">
                        Pengajuan cuti Anda ditolak. Lihat catatan review untuk informasi lebih lanjut.
                    </p>
                </div>
                @endif

                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        <strong>Durasi:</strong> {{ $leaveRequest->total_days }} hari kerja
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-calendar-range text-info me-2"></i>
                        <strong>Periode:</strong> 
                        {{ formatDate($leaveRequest->start_date, 'd M') }} - 
                        {{ formatDate($leaveRequest->end_date, 'd M Y') }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-tag text-success me-2"></i>
                        <strong>Jenis:</strong> {{ getLeaveTypeText($leaveRequest->leave_type) }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-marker {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px currentColor;
        position: absolute;
        left: 0;
        top: 5px;
    }
</style>
@endpush