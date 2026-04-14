@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('page-kicker', 'Request Detail')
@section('page-title', 'Detail Pengajuan Cuti')
@section('page-subtitle', 'Lihat status terbaru, periode cuti, catatan reviewer, dan lampiran pendukung dalam tampilan yang lebih rapi.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="hero-panel">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="page-kicker text-white-50">Current Status</div>
                        <h3 class="mb-2">{{ getLeaveStatusText($leaveRequest->status) }}</h3>
                        <p class="muted mb-0">{{ getLeaveTypeText($leaveRequest->leave_type) }} selama {{ $leaveRequest->total_days }} hari.</p>
                    </div>
                    <span class="badge bg-white text-dark align-self-start">{{ $leaveRequest->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Informasi pengajuan</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Jenis cuti</div><div class="fw-semibold">{{ getLeaveTypeText($leaveRequest->leave_type) }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Durasi</div><div class="fw-semibold">{{ $leaveRequest->total_days }} hari</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Tanggal mulai</div><div class="fw-semibold">{{ formatDate($leaveRequest->start_date) }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Tanggal selesai</div><div class="fw-semibold">{{ formatDate($leaveRequest->end_date) }}</div></div></div>
                        <div class="col-12"><div class="data-summary-item"><div class="small text-muted mb-2">Alasan</div><div>{{ $leaveRequest->reason }}</div></div></div>
                        @if($leaveRequest->attachment)
                            <div class="col-12">
                                <div class="data-summary-item">
                                    <div class="small text-muted mb-2">Lampiran</div>
                                    <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank" class="btn btn-outline-primary">Download Lampiran</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($leaveRequest->status !== 'pending')
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="fw-bold fs-5">Hasil review</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Direview oleh</div><div class="fw-semibold">{{ $leaveRequest->reviewer->name ?? '-' }}</div></div></div>
                            <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Tanggal review</div><div class="fw-semibold">{{ $leaveRequest->reviewed_at ? formatDateTime($leaveRequest->reviewed_at) : '-' }}</div></div></div>
                            @if($leaveRequest->review_notes)
                                <div class="col-12"><div class="data-summary-item"><div class="small text-muted mb-2">Catatan reviewer</div><div>{{ $leaveRequest->review_notes }}</div></div></div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold">Timeline status</div>
                </div>
                <div class="card-body">
                    <div class="timeline-line">
                        <div class="timeline-row">
                            <div class="timeline-dot"></div>
                            <div>
                                <div class="fw-semibold">Pengajuan dibuat</div>
                                <div class="small text-muted">{{ formatDateTime($leaveRequest->created_at) }}</div>
                            </div>
                        </div>
                        <div class="timeline-row">
                            <div class="timeline-dot {{ $leaveRequest->status === 'pending' ? 'pending' : '' }}"></div>
                            <div>
                                <div class="fw-semibold">{{ $leaveRequest->status === 'pending' ? 'Menunggu review' : getLeaveStatusText($leaveRequest->status) }}</div>
                                <div class="small text-muted">
                                    @if($leaveRequest->reviewed_at)
                                        {{ formatDateTime($leaveRequest->reviewed_at) }}
                                    @else
                                        Belum ada keputusan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold">Ringkasan cepat</div>
                </div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item"><div class="small text-muted">Status saat ini</div><div class="fw-semibold">{{ getLeaveStatusText($leaveRequest->status) }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Periode</div><div>{{ formatDate($leaveRequest->start_date, 'd M') }} - {{ formatDate($leaveRequest->end_date, 'd M Y') }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Durasi</div><div>{{ $leaveRequest->total_days }} hari</div></div>
                    </div>
                    <div class="d-grid mt-3">
                        <a href="{{ route('employee.leave-requests') }}" class="btn btn-outline-primary">Kembali ke daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-line {
        position: relative;
        display: grid;
        gap: 1.4rem;
    }

    .timeline-line::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: rgba(129, 101, 104, 0.18);
    }

    .timeline-row {
        position: relative;
        display: flex;
        gap: 1rem;
    }

    .timeline-dot {
        width: 20px;
        height: 20px;
        border-radius: 999px;
        background: var(--success);
        box-shadow: 0 0 0 4px rgba(79, 138, 102, 0.16);
        z-index: 1;
        margin-top: 2px;
    }

    .timeline-dot.pending {
        background: var(--warning);
        box-shadow: 0 0 0 4px rgba(200, 138, 77, 0.16);
    }
</style>
@endpush
