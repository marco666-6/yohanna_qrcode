@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.attendance-report') }}">
            <i class="bi bi-file-earmark-text"></i> Laporan Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('hrd.leave-requests') }}">
            <i class="bi bi-calendar-event"></i> Pengajuan Cuti
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hrd.statistics') }}">
            <i class="bi bi-graph-up"></i> Statistik
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
                <p class="text-muted mb-0">Informasi lengkap pengajuan cuti karyawan</p>
            </div>
            <a href="{{ route('hrd.leave-requests') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Main Information -->
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card mb-3 border-{{ getLeaveStatusBadge($leaveRequest->status) }}">
            <div class="card-header bg-{{ getLeaveStatusBadge($leaveRequest->status) }} text-white">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Status Pengajuan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Status Saat Ini</h6>
                        <h3>
                            <span class="badge bg-{{ getLeaveStatusBadge($leaveRequest->status) }}" style="font-size: 1.2rem;">
                                {{ getLeaveStatusText($leaveRequest->status) }}
                            </span>
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Tanggal Pengajuan</h6>
                        <p class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            <strong>{{ formatDateTime($leaveRequest->created_at) }}</strong>
                        </p>
                        <small class="text-muted">{{ $leaveRequest->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                @if($leaveRequest->status != 'pending')
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Ditinjau Oleh</h6>
                        <p class="mb-0">
                            <i class="bi bi-person-check me-2"></i>
                            <strong>{{ $leaveRequest->reviewer->name ?? '-' }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Tanggal Review</h6>
                        <p class="mb-0">
                            <i class="bi bi-clock me-2"></i>
                            <strong>{{ $leaveRequest->reviewed_at ? formatDateTime($leaveRequest->reviewed_at) : '-' }}</strong>
                        </p>
                        @if($leaveRequest->reviewed_at)
                        <small class="text-muted">{{ $leaveRequest->reviewed_at->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Leave Details Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Detail Cuti
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Jenis Cuti</h6>
                        <span class="badge bg-{{ getLeaveTypeBadge($leaveRequest->leave_type) }}" style="font-size: 1rem; padding: 8px 16px;">
                            {{ getLeaveTypeText($leaveRequest->leave_type) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Durasi</h6>
                        <p class="mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            <strong style="font-size: 1.2rem;">{{ $leaveRequest->total_days }}</strong> hari
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Tanggal Mulai</h6>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            <strong>{{ formatDate($leaveRequest->start_date) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Tanggal Selesai</h6>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-calendar-x me-2"></i>
                            <strong>{{ formatDate($leaveRequest->end_date) }}</strong>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted mb-2">Alasan Pengajuan</h6>
                <div class="alert alert-light border">
                    <i class="bi bi-chat-left-text me-2"></i>
                    {{ $leaveRequest->reason }}
                </div>

                @if($leaveRequest->attachment)
                <hr>
                <h6 class="text-muted mb-2">Lampiran</h6>
                <a href="{{ Storage::url($leaveRequest->attachment) }}" 
                   target="_blank" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-paperclip me-2"></i>
                    Lihat Lampiran
                </a>
                @endif
            </div>
        </div>

        <!-- Review Notes Card -->
        @if($leaveRequest->review_notes)
        <div class="card mb-3 border-{{ $leaveRequest->status == 'approved' ? 'success' : 'danger' }}">
            <div class="card-header bg-{{ $leaveRequest->status == 'approved' ? 'success' : 'danger' }} text-white">
                <h5 class="mb-0">
                    <i class="bi bi-chat-square-text me-2"></i>
                    Catatan Review
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $leaveRequest->review_notes }}</p>
            </div>
        </div>
        @endif

        <!-- Action Buttons for Pending Requests -->
        @if($leaveRequest->status == 'pending')
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Tindakan Diperlukan
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Pengajuan cuti ini menunggu persetujuan Anda. Silakan tinjau dan berikan keputusan.</p>
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-success flex-grow-1"
                            data-bs-toggle="modal"
                            data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-2"></i>
                        Setujui Pengajuan
                    </button>
                    <button type="button" 
                            class="btn btn-danger flex-grow-1"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-2"></i>
                        Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Employee Information Sidebar -->
    <div class="col-lg-4">
        <!-- Employee Card -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Informasi Karyawan
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                </div>
                
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Nama:</td>
                        <td><strong>{{ $leaveRequest->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">ID Karyawan:</td>
                        <td><strong>{{ $leaveRequest->user->employee_id }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td>{{ $leaveRequest->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon:</td>
                        <td>{{ $leaveRequest->user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Departemen:</td>
                        <td>{{ $leaveRequest->user->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Posisi:</td>
                        <td>{{ $leaveRequest->user->position ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Shift:</td>
                        <td>
                            @if($leaveRequest->user->shift)
                                <span class="badge bg-info">{{ $leaveRequest->user->shift->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-plus-circle"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Pengajuan Dibuat</h6>
                                <p class="text-muted small mb-0">
                                    {{ formatDateTime($leaveRequest->created_at) }}
                                </p>
                                <small class="text-muted">oleh {{ $leaveRequest->user->name }}</small>
                            </div>
                        </div>
                    </div>

                    @if($leaveRequest->status != 'pending')
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-{{ $leaveRequest->status == 'approved' ? 'success' : 'danger' }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-{{ $leaveRequest->status == 'approved' ? 'check' : 'x' }}-circle"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    {{ $leaveRequest->status == 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </h6>
                                <p class="text-muted small mb-0">
                                    {{ $leaveRequest->reviewed_at ? formatDateTime($leaveRequest->reviewed_at) : '-' }}
                                </p>
                                <small class="text-muted">oleh {{ $leaveRequest->reviewer->name ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Menunggu Review</h6>
                                <p class="text-muted small mb-0">Status: Pending</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Setujui Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hrd.leave-requests.approve', $leaveRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-info-circle me-2"></i>
                        Anda akan menyetujui pengajuan cuti <strong>{{ $leaveRequest->user->name }}</strong> 
                        untuk periode <strong>{{ formatDate($leaveRequest->start_date) }}</strong> 
                        sampai <strong>{{ formatDate($leaveRequest->end_date) }}</strong> 
                        ({{ $leaveRequest->total_days }} hari).
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="review_notes" rows="3" placeholder="Tambahkan catatan persetujuan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>
                    Tolak Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hrd.leave-requests.reject', $leaveRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Anda akan menolak pengajuan cuti <strong>{{ $leaveRequest->user->name }}</strong> 
                        untuk periode <strong>{{ formatDate($leaveRequest->start_date) }}</strong> 
                        sampai <strong>{{ formatDate($leaveRequest->end_date) }}</strong> 
                        ({{ $leaveRequest->total_days }} hari).
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="review_notes" rows="4" required placeholder="Jelaskan alasan penolakan dengan jelas..."></textarea>
                        <small class="text-muted">Alasan wajib diisi untuk memberitahu karyawan mengapa pengajuannya ditolak</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-2"></i>
                        Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 45px;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item:last-child .timeline::before {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Confirm before approve/reject
    document.querySelectorAll('form').forEach(form => {
        if (form.action.includes('approve') || form.action.includes('reject')) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const isApprove = form.action.includes('approve');
                const actionText = isApprove ? 'menyetujui' : 'menolak';
                const icon = isApprove ? 'success' : 'warning';
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin ${actionText} pengajuan cuti ini?`,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: isApprove ? '#198754' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Ya, ${actionText}!`,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush