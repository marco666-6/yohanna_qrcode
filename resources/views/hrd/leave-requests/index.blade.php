@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

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
            @if($stats['pending'] > 0)
                <span class="badge bg-danger ms-2">{{ $stats['pending'] }}</span>
            @endif
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
                    <i class="bi bi-calendar-event me-2"></i>
                    Manajemen Pengajuan Cuti
                </h2>
                <p class="text-muted mb-0">Kelola dan review pengajuan cuti karyawan</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Persetujuan</p>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        <small class="text-warning">Perlu Ditinjau</small>
                    </div>
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Disetujui</p>
                        <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        <small class="text-success">Total Approved</small>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                        <small class="text-danger">Total Rejected</small>
                    </div>
                    <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('hrd.leave-requests') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Menunggu Persetujuan
                    </option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                        Disetujui
                    </option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                        Ditolak
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('hrd.leave-requests') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Leave Requests Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Daftar Pengajuan Cuti
        </h5>
        <span class="badge bg-primary">{{ $leaveRequests->total() }} Pengajuan</span>
    </div>
    <div class="card-body">
        @if($leaveRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <th>Karyawan</th>
                            <th>Jenis Cuti</th>
                            <th>Periode Cuti</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $leave)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ formatDateTime($leave->created_at) }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $leave->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $leave->user->employee_id }}</small>
                                <br>
                                <small class="text-muted">{{ $leave->user->department ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ getLeaveTypeBadge($leave->leave_type) }}">
                                    {{ getLeaveTypeText($leave->leave_type) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ formatDate($leave->start_date) }}</strong>
                                <br>
                                <small class="text-muted">sampai</small>
                                <br>
                                <strong>{{ formatDate($leave->end_date) }}</strong>
                            </td>
                            <td>
                                <strong>{{ $leave->total_days }}</strong> hari
                            </td>
                            <td>
                                <span class="badge bg-{{ getLeaveStatusBadge($leave->status) }}">
                                    {{ getLeaveStatusText($leave->status) }}
                                </span>
                                @if($leave->status != 'pending')
                                    <br>
                                    <small class="text-muted">
                                        oleh {{ $leave->reviewer->name ?? '-' }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        {{ $leave->reviewed_at ? $leave->reviewed_at->diffForHumans() : '-' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('hrd.leave-requests.detail', $leave->id) }}" 
                                       class="btn btn-sm btn-info"
                                       data-bs-toggle="tooltip"
                                       title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($leave->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal{{ $leave->id }}"
                                                title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $leave->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Setujui Pengajuan Cuti
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('hrd.leave-requests.approve', $leave->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i>
                                                Anda akan menyetujui pengajuan cuti berikut:
                                            </div>
                                            
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted">Karyawan:</td>
                                                    <td><strong>{{ $leave->user->name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Jenis Cuti:</td>
                                                    <td>
                                                        <span class="badge bg-{{ getLeaveTypeBadge($leave->leave_type) }}">
                                                            {{ getLeaveTypeText($leave->leave_type) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Periode:</td>
                                                    <td>
                                                        {{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }}
                                                        ({{ $leave->total_days }} hari)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Alasan:</td>
                                                    <td>{{ $leave->reason }}</td>
                                                </tr>
                                            </table>

                                            <div class="mb-3">
                                                <label class="form-label">Catatan (Opsional)</label>
                                                <textarea class="form-control" name="review_notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
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
                        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-x-circle me-2"></i>
                                            Tolak Pengajuan Cuti
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('hrd.leave-requests.reject', $leave->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Anda akan menolak pengajuan cuti berikut:
                                            </div>
                                            
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted">Karyawan:</td>
                                                    <td><strong>{{ $leave->user->name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Jenis Cuti:</td>
                                                    <td>
                                                        <span class="badge bg-{{ getLeaveTypeBadge($leave->leave_type) }}">
                                                            {{ getLeaveTypeText($leave->leave_type) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Periode:</td>
                                                    <td>
                                                        {{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }}
                                                        ({{ $leave->total_days }} hari)
                                                    </td>
                                                </tr>
                                            </table>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Alasan Penolakan <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" name="review_notes" rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                                                <small class="text-muted">Alasan wajib diisi untuk memberitahu karyawan</small>
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaveRequests->hasPages())
                <div class="mt-3">
                    {{ $leaveRequests->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">
                    @if(request('status'))
                        Tidak ada pengajuan cuti dengan status "{{ getLeaveStatusText(request('status')) }}"
                    @else
                        Belum ada pengajuan cuti
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

@if($stats['pending'] > 0)
<!-- Quick Action Card -->
<div class="card mt-3 border-warning">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="bi bi-lightning me-2"></i>
            Tindakan Diperlukan
        </h6>
    </div>
    <div class="card-body">
        <p class="mb-2">
            <i class="bi bi-exclamation-circle text-warning me-2"></i>
            Ada <strong>{{ $stats['pending'] }}</strong> pengajuan cuti yang menunggu persetujuan Anda.
        </p>
        <a href="{{ route('hrd.leave-requests', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-eye me-2"></i>
            Lihat Pengajuan Pending
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

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