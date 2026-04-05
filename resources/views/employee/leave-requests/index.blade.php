@extends('layouts.app')

@section('title', 'Pengajuan Cuti')
@section('page-kicker', 'Leave Management')
@section('page-title', 'Pengajuan Cuti')
@section('page-subtitle', 'Kelola pengajuan cuti dengan status yang jelas, pagination ringan, dan alur yang sesuai kebutuhan aplikasi absensi.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        @foreach([
            ['label' => 'Menunggu', 'value' => $leaveStats['pending'], 'helper' => 'Masih menunggu review HRD', 'icon' => 'bi-hourglass-split', 'tone' => 'warning'],
            ['label' => 'Disetujui', 'value' => $leaveStats['approved'], 'helper' => 'Sudah bisa dijadwalkan', 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Ditolak', 'value' => $leaveStats['rejected'], 'helper' => 'Periksa alasan penolakan', 'icon' => 'bi-x-circle', 'tone' => 'danger'],
            ['label' => 'Total Pengajuan', 'value' => $leaveRequests->total(), 'helper' => 'Menyesuaikan filter saat ini', 'icon' => 'bi-calendar-event', 'tone' => 'primary'],
        ] as $item)
            <div class="col-6 col-xl-3">
                <div class="stat-card {{ $item['tone'] }}">
                    <div class="stat-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                    <div class="stat-value">{{ $item['value'] }}</div>
                    <div class="stat-label">{{ $item['label'] }}</div>
                    <div class="stat-helper">{{ $item['helper'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="filter-card">
        <form action="{{ route('employee.leave-requests') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
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
            <div class="col-lg-7 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="{{ route('employee.leave-requests') }}" class="btn btn-outline-primary">Reset</a>
                <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Ajukan Cuti Baru</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Daftar pengajuan cuti</div>
                <div class="small text-muted">Menampilkan {{ $leaveRequests->count() }} data dari {{ $leaveRequests->total() }} hasil.</div>
            </div>
            <span class="soft-chip"><i class="bi bi-info-circle"></i>Ajukan lebih awal agar approval tidak menumpuk</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Jenis</th>
                            <th>Periode</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $leave)
                            <tr>
                                <td class="ps-4"><span class="badge rounded-pill bg-{{ getLeaveTypeBadge($leave->leave_type) }}">{{ getLeaveTypeText($leave->leave_type) }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ formatDate($leave->start_date) }}</div>
                                    <div class="small text-muted">s/d {{ formatDate($leave->end_date) }}</div>
                                </td>
                                <td>{{ $leave->total_days }} hari</td>
                                <td>{{ \Illuminate\Support\Str::limit($leave->reason, 70) }}</td>
                                <td><span class="badge rounded-pill bg-{{ getLeaveStatusBadge($leave->status) }}">{{ getLeaveStatusText($leave->status) }}</span></td>
                                <td>{{ formatDate($leave->created_at) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('employee.leave-requests.detail', $leave->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <div class="fw-semibold mb-1">Belum ada pengajuan cuti</div>
                                        <div class="small">Ajukan cuti baru saat memang dibutuhkan dan sertakan alasan yang jelas.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leaveRequests->hasPages())
            <div class="card-body border-top">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
