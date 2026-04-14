@extends('layouts.app')

@section('title', 'Manajemen Pengajuan Cuti')
@section('page-kicker', 'Leave Review')
@section('page-title', 'Manajemen Pengajuan Cuti')
@section('page-subtitle', 'Tinjau pengajuan secara lebih cepat, rapikan prioritas pending, dan proses approve atau reject tanpa modal yang bermasalah.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
                <div class="stat-helper">Perlu review segera</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">Disetujui</div>
                <div class="stat-helper">Sudah diproses positif</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card danger">
                <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Ditolak</div>
                <div class="stat-helper">Perlu alasan yang jelas</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-funnel"></i></div>
                <div class="stat-value">{{ $leaveRequests->total() }}</div>
                <div class="stat-label">Total Hasil</div>
                <div class="stat-helper">Sesuai filter aktif</div>
            </div>
        </div>
    </div>

    <div class="filter-card">
        <form action="{{ route('hrd.leave-requests') }}" method="GET" class="row g-3 align-items-end">
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
            <div class="col-lg-2">
                <label class="form-label">Per halaman</label>
                <select class="form-select" name="per_page">
                    @foreach([10,15,25,50,100] as $option)
                        <option value="{{ $option }}" {{ (int) request('per_page', $perPage) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Terapkan Filter</button>
                <a href="{{ route('hrd.leave-requests') }}" class="btn btn-outline-primary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Daftar pengajuan cuti</div>
                <div class="small text-muted">Semua aksi review sekarang memakai modal yang stabil dan diletakkan di luar tabel.</div>
            </div>
            @if($stats['pending'] > 0)
                <span class="soft-chip"><i class="bi bi-exclamation-circle"></i>{{ $stats['pending'] }} pengajuan menunggu review</span>
            @endif
        </div>
        <div class="card-body p-0">
            @if($leaveRequests->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Karyawan</th>
                                <th>Jenis Cuti</th>
                                <th>Periode</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Diajukan</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $leave)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $leave->user->name }}</div>
                                        <div class="small text-muted">{{ $leave->user->employee_id }}{{ $leave->user->department ? ' · ' . $leave->user->department : '' }}</div>
                                    </td>
                                    <td><span class="badge rounded-pill bg-{{ getLeaveTypeBadge($leave->leave_type) }}">{{ getLeaveTypeText($leave->leave_type) }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ formatDate($leave->start_date) }}</div>
                                        <div class="small text-muted">sampai {{ formatDate($leave->end_date) }}</div>
                                    </td>
                                    <td>{{ $leave->total_days }} hari</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ getLeaveStatusBadge($leave->status) }}">{{ getLeaveStatusText($leave->status) }}</span>
                                        @if($leave->status !== 'pending')
                                            <div class="small text-muted mt-1">{{ $leave->reviewer->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ formatDate($leave->created_at) }}</div>
                                        <div class="small text-muted">{{ $leave->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('hrd.leave-requests.detail', $leave->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($leave->status === 'pending')
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $leave->id }}">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
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
                    <div class="small">Coba ubah status, pencarian, atau jumlah data per halaman.</div>
                </div>
            @endif
        </div>
        @if($leaveRequests->hasPages())
            <div class="card-body border-top">
                {{ $leaveRequests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    @if($stats['pending'] > 0)
        <div class="card border-0" style="background:rgba(200,138,77,.12);box-shadow:none;">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="fw-semibold">Prioritas hari ini</div>
                    <div class="small text-muted">Ada {{ $stats['pending'] }} pengajuan yang masih menunggu keputusan.</div>
                </div>
                <a href="{{ route('hrd.leave-requests', ['status' => 'pending']) }}" class="btn btn-primary">Fokus ke Pending</a>
            </div>
        </div>
    @endif
</div>

@foreach($leaveRequests as $leave)
    @if($leave->status === 'pending')
        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">Setujui pengajuan cuti</h5>
                            <div class="small text-muted">{{ $leave->user->name }} · {{ getLeaveTypeText($leave->leave_type) }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('hrd.leave-requests.approve', $leave->id) }}" method="POST" class="approval-form" data-action-label="menyetujui">
                        @csrf
                        <div class="modal-body">
                            <div class="data-summary-item mb-3">
                                <div class="small text-muted">Periode cuti</div>
                                <div class="fw-semibold">{{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }} ({{ $leave->total_days }} hari)</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan opsional</label>
                                <textarea class="form-control" name="review_notes" rows="4" placeholder="Tambahkan catatan persetujuan bila perlu."></textarea>
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

        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">Tolak pengajuan cuti</h5>
                            <div class="small text-muted">{{ $leave->user->name }} · {{ getLeaveTypeText($leave->leave_type) }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('hrd.leave-requests.reject', $leave->id) }}" method="POST" class="approval-form" data-action-label="menolak">
                        @csrf
                        <div class="modal-body">
                            <div class="data-summary-item mb-3">
                                <div class="small text-muted">Periode cuti</div>
                                <div class="fw-semibold">{{ formatDate($leave->start_date) }} - {{ formatDate($leave->end_date) }} ({{ $leave->total_days }} hari)</div>
                            </div>
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
@endforeach
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.approval-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const actionLabel = this.dataset.actionLabel;
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi tindakan',
                text: `Apakah Anda yakin ingin ${actionLabel} pengajuan cuti ini?`,
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
