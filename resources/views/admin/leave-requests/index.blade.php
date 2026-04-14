@extends('layouts.app')

@section('title', 'Manajemen Pengajuan Cuti')
@section('page-kicker', 'Admin Backup Review')
@section('page-title', 'Manajemen Pengajuan Cuti')
@section('page-subtitle', 'Admin dapat mengambil alih proses review cuti saat dibutuhkan, tanpa mengubah alur approval yang sudah ada di HRD.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card warning"><div class="stat-icon"><i class="bi bi-hourglass-split"></i></div><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">Pending</div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card success"><div class="stat-icon"><i class="bi bi-check-circle"></i></div><div class="stat-value">{{ $stats['approved'] }}</div><div class="stat-label">Disetujui</div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card danger"><div class="stat-icon"><i class="bi bi-x-circle"></i></div><div class="stat-value">{{ $stats['rejected'] }}</div><div class="stat-label">Ditolak</div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info"><div class="stat-icon"><i class="bi bi-funnel"></i></div><div class="stat-value">{{ $stats['filtered'] }}</div><div class="stat-label">Hasil Filter</div></div>
        </div>
    </div>

    <div class="filter-card">
        <form action="{{ route('admin.leave-requests') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Cari karyawan</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama, ID karyawan, departemen">
            </div>
            <div class="col-lg-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label">Jenis cuti</label>
                <select class="form-select" name="leave_type">
                    <option value="">Semua jenis</option>
                    <option value="sick" {{ request('leave_type') === 'sick' ? 'selected' : '' }}>Cuti Sakit</option>
                    <option value="annual" {{ request('leave_type') === 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="unpaid" {{ request('leave_type') === 'unpaid' ? 'selected' : '' }}>Cuti Tanpa Bayaran</option>
                    <option value="other" {{ request('leave_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Per halaman</label>
                <select class="form-select" name="per_page">
                    @foreach([10,15,25,50,100] as $option)
                        <option value="{{ $option }}" {{ (int) request('per_page', $perPage) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('admin.leave-requests') }}" class="btn btn-outline-primary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="fw-bold fs-5">Daftar pengajuan cuti</div>
            <div class="small text-muted">Fungsi ini disediakan sebagai backup approval saat HRD tidak tersedia.</div>
        </div>
        <div class="card-body p-0">
            @if($leaveRequests->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Karyawan</th>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $leave)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $leave->user->name }}</div>
                                        <div class="small text-muted">{{ $leave->user->employee_id }}{{ $leave->user->shift ? ' · ' . $leave->user->shift->name : '' }}</div>
                                    </td>
                                    <td><span class="badge rounded-pill bg-{{ getLeaveTypeBadge($leave->leave_type) }}">{{ getLeaveTypeText($leave->leave_type) }}</span></td>
                                    <td>{{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }}</td>
                                    <td>{{ $leave->total_days }} hari</td>
                                    <td><span class="badge rounded-pill bg-{{ getLeaveStatusBadge($leave->status) }}">{{ getLeaveStatusText($leave->status) }}</span></td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.leave-requests.detail', $leave->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                            @if($leave->status === 'pending')
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $leave->id }}"><i class="bi bi-check-lg"></i></button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}"><i class="bi bi-x-lg"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <div class="fw-semibold mb-1">Tidak ada pengajuan untuk filter ini</div>
                </div>
            @endif
        </div>
        @if($leaveRequests->hasPages())
            <div class="card-body border-top">
                {{ $leaveRequests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($leaveRequests as $leave)
    @if($leave->status === 'pending')
        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Setujui Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="{{ route('admin.leave-requests.approve', $leave->id) }}" method="POST" class="admin-leave-form" data-action-label="menyetujui">@csrf<div class="modal-body"><textarea class="form-control" name="review_notes" rows="4" placeholder="Catatan opsional"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Setujui</button></div></form></div></div>
        </div>
        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Tolak Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="{{ route('admin.leave-requests.reject', $leave->id) }}" method="POST" class="admin-leave-form" data-action-label="menolak">@csrf<div class="modal-body"><textarea class="form-control" name="review_notes" rows="4" required placeholder="Alasan penolakan"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Tolak</button></div></form></div></div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.admin-leave-form').forEach(form => {
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
