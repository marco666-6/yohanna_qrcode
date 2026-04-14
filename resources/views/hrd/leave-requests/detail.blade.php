@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('page-kicker', 'Leave Detail')
@section('page-title', 'Detail Pengajuan Cuti')
@section('page-subtitle', 'Baca konteks pengajuan, cek data karyawan, lalu ambil keputusan dengan tampilan yang lebih tenang dan mudah dipahami.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="hero-panel">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="page-kicker text-white-50">Current Status</div>
                        <h3 class="mb-2">{{ getLeaveStatusText($leaveRequest->status) }}</h3>
                        <p class="muted mb-0">Pengajuan dari {{ $leaveRequest->user->name }} untuk {{ getLeaveTypeText($leaveRequest->leave_type) }} selama {{ $leaveRequest->total_days }} hari.</p>
                    </div>
                    <div class="align-self-lg-start">
                        <span class="badge bg-white text-dark">{{ $leaveRequest->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="fw-bold fs-5">Informasi pengajuan</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Jenis cuti</div>
                                <div class="fw-semibold">{{ getLeaveTypeText($leaveRequest->leave_type) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Durasi</div>
                                <div class="fw-semibold">{{ $leaveRequest->total_days }} hari</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Tanggal mulai</div>
                                <div class="fw-semibold">{{ formatDate($leaveRequest->start_date) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-summary-item h-100">
                                <div class="small text-muted">Tanggal selesai</div>
                                <div class="fw-semibold">{{ formatDate($leaveRequest->end_date) }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="data-summary-item">
                                <div class="small text-muted mb-2">Alasan pengajuan</div>
                                <div>{{ $leaveRequest->reason }}</div>
                            </div>
                        </div>
                        @if($leaveRequest->attachment)
                            <div class="col-12">
                                <div class="data-summary-item">
                                    <div class="small text-muted mb-2">Lampiran</div>
                                    <a href="{{ Storage::url($leaveRequest->attachment) }}" target="_blank" class="btn btn-outline-primary">Lihat Lampiran</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($leaveRequest->review_notes)
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="fw-bold fs-5">Catatan review</div>
                    </div>
                    <div class="card-body">
                        <div class="data-summary-item">
                            <div>{{ $leaveRequest->review_notes }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if($leaveRequest->status === 'pending')
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="fw-bold fs-5">Aksi review</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveModal">Setujui Pengajuan</button>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak Pengajuan</button>
                            <a href="{{ route('hrd.leave-requests') }}" class="btn btn-outline-primary">Kembali ke daftar</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="fw-bold">Profil karyawan</div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="preview-avatar mx-auto mb-3">{{ strtoupper(substr($leaveRequest->user->name, 0, 1)) }}</div>
                        <div class="fw-bold fs-5">{{ $leaveRequest->user->name }}</div>
                        <div class="text-muted">{{ $leaveRequest->user->employee_id }}</div>
                    </div>
                    <div class="data-summary">
                        <div class="data-summary-item">
                            <div class="small text-muted">Departemen</div>
                            <div>{{ $leaveRequest->user->department ?: '-' }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Posisi</div>
                            <div>{{ $leaveRequest->user->position ?: '-' }}</div>
                        </div>
                        <div class="data-summary-item">
                            <div class="small text-muted">Shift</div>
                            <div>{{ $leaveRequest->user->shift?->name ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="fw-bold">Timeline</div>
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
                                <div class="fw-semibold">{{ $leaveRequest->status === 'pending' ? 'Menunggu review' : 'Sudah direview' }}</div>
                                <div class="small text-muted">
                                    @if($leaveRequest->reviewed_at)
                                        {{ formatDateTime($leaveRequest->reviewed_at) }} oleh {{ $leaveRequest->reviewer->name ?? '-' }}
                                    @else
                                        Belum ada keputusan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($leaveRequest->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Setujui pengajuan cuti</h5>
                        <div class="small text-muted">{{ $leaveRequest->user->name }} · {{ $leaveRequest->total_days }} hari</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('hrd.leave-requests.approve', $leaveRequest->id) }}" method="POST" class="leave-decision-form" data-action-label="menyetujui">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Catatan opsional</label>
                            <textarea class="form-control" name="review_notes" rows="4" placeholder="Tambahkan catatan jika diperlukan."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Setujui Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Tolak pengajuan cuti</h5>
                        <div class="small text-muted">{{ $leaveRequest->user->name }} · {{ $leaveRequest->total_days }} hari</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('hrd.leave-requests.reject', $leaveRequest->id) }}" method="POST" class="leave-decision-form" data-action-label="menolak">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="review_notes" rows="4" required placeholder="Jelaskan alasan penolakan dengan jelas."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .preview-avatar {
        width: 84px;
        height: 84px;
        border-radius: 28px;
        display: grid;
        place-items: center;
        font-size: 1.9rem;
        font-weight: 800;
        color: #fffaf9;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }

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

@push('scripts')
<script>
    document.querySelectorAll('.leave-decision-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi tindakan',
                text: `Apakah Anda yakin ingin ${this.dataset.actionLabel} pengajuan cuti ini?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
