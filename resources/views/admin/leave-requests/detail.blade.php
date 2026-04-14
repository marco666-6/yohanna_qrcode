@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('page-kicker', 'Admin Review')
@section('page-title', 'Detail Pengajuan Cuti')
@section('page-subtitle', 'Admin dapat membaca detail pengajuan dan mengambil keputusan jika perlu menggantikan HRD.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="hero-panel">
                <div class="page-kicker text-white-50">Current Status</div>
                <h3 class="mb-2">{{ getLeaveStatusText($leaveRequest->status) }}</h3>
                <p class="muted mb-0">{{ $leaveRequest->user->name }} mengajukan {{ getLeaveTypeText($leaveRequest->leave_type) }} selama {{ $leaveRequest->total_days }} hari.</p>
            </div>

            <div class="card mt-4">
                <div class="card-header"><div class="fw-bold fs-5">Informasi pengajuan</div></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Jenis cuti</div><div class="fw-semibold">{{ getLeaveTypeText($leaveRequest->leave_type) }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Durasi</div><div class="fw-semibold">{{ $leaveRequest->total_days }} hari</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Tanggal mulai</div><div class="fw-semibold">{{ formatDate($leaveRequest->start_date) }}</div></div></div>
                        <div class="col-md-6"><div class="data-summary-item"><div class="small text-muted">Tanggal selesai</div><div class="fw-semibold">{{ formatDate($leaveRequest->end_date) }}</div></div></div>
                        <div class="col-12"><div class="data-summary-item"><div class="small text-muted mb-2">Alasan</div><div>{{ $leaveRequest->reason }}</div></div></div>
                        @if($leaveRequest->review_notes)
                            <div class="col-12"><div class="data-summary-item"><div class="small text-muted mb-2">Catatan review</div><div>{{ $leaveRequest->review_notes }}</div></div></div>
                        @endif
                    </div>
                </div>
            </div>

            @if($leaveRequest->status === 'pending')
                <div class="card mt-4">
                    <div class="card-header"><div class="fw-bold fs-5">Aksi review admin</div></div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveModal">Setujui</button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak</button>
                        <a href="{{ route('admin.leave-requests') }}" class="btn btn-outline-primary">Kembali</a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><div class="fw-bold">Informasi karyawan</div></div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item"><div class="small text-muted">Nama</div><div>{{ $leaveRequest->user->name }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">ID karyawan</div><div>{{ $leaveRequest->user->employee_id }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Departemen</div><div>{{ $leaveRequest->user->department ?: '-' }}</div></div>
                        <div class="data-summary-item"><div class="small text-muted">Shift</div><div>{{ $leaveRequest->user->shift?->name ?: '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($leaveRequest->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Setujui Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="{{ route('admin.leave-requests.approve', $leaveRequest->id) }}" method="POST" class="admin-leave-detail-form" data-action-label="menyetujui">@csrf<div class="modal-body"><textarea class="form-control" name="review_notes" rows="4" placeholder="Catatan opsional"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Setujui</button></div></form></div></div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Tolak Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="{{ route('admin.leave-requests.reject', $leaveRequest->id) }}" method="POST" class="admin-leave-detail-form" data-action-label="menolak">@csrf<div class="modal-body"><textarea class="form-control" name="review_notes" rows="4" required placeholder="Alasan penolakan"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Tolak</button></div></form></div></div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.admin-leave-detail-form').forEach(form => {
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
                if (result.isConfirmed) this.submit();
            });
        });
    });
</script>
@endpush
