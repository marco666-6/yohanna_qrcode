@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

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
                    <i class="bi bi-calendar-event me-2"></i>
                    Pengajuan Cuti
                </h2>
                <p class="text-muted mb-0">Kelola pengajuan cuti dan ketidakhadiran Anda</p>
            </div>
            <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Ajukan Cuti Baru
            </a>
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
                        <h3 class="mb-0">{{ $leaveRequests->where('status', 'pending')->count() }}</h3>
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
                        <h3 class="mb-0">{{ $leaveRequests->where('status', 'approved')->count() }}</h3>
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
                        <h3 class="mb-0">{{ $leaveRequests->where('status', 'rejected')->count() }}</h3>
                    </div>
                    <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Requests Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Daftar Pengajuan Cuti
        </h5>
    </div>
    <div class="card-body">
        @if($leaveRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $leave)
                        <tr>
                            <td>
                                <span class="badge bg-{{ getLeaveTypeBadge($leave->leave_type) }}">
                                    {{ getLeaveTypeText($leave->leave_type) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ formatDate($leave->start_date) }}</strong>
                            </td>
                            <td>
                                <strong>{{ formatDate($leave->end_date) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $leave->total_days }} hari
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 200px;">
                                    {{ Str::limit($leave->reason, 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ getLeaveStatusBadge($leave->status) }}">
                                    {{ getLeaveStatusText($leave->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ formatDate($leave->created_at) }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('employee.leave-requests.detail', $leave->id) }}" 
                                   class="btn btn-sm btn-info"
                                   data-bs-toggle="tooltip"
                                   title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaveRequests->hasPages())
                <div class="mt-3">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3 mb-3">Belum ada pengajuan cuti</p>
                <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Ajukan Cuti Sekarang
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Information Card -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="bi bi-info-circle me-2"></i>
            Informasi Pengajuan Cuti
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Jenis Cuti</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge bg-warning me-2">Cuti Sakit</span>
                        - Untuk kondisi kesehatan yang tidak memungkinkan bekerja
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-primary me-2">Cuti Tahunan</span>
                        - Cuti yang diberikan setiap tahun
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-secondary me-2">Cuti Tanpa Bayaran</span>
                        - Cuti tanpa menerima gaji
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-info me-2">Lainnya</span>
                        - Keperluan lain yang mendesak
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Ketentuan</h6>
                <ul class="mb-0">
                    <li class="mb-2">Pengajuan cuti minimal 3 hari sebelum tanggal cuti</li>
                    <li class="mb-2">Untuk cuti sakit dapat dilampirkan surat keterangan dokter</li>
                    <li class="mb-2">HRD akan meninjau dan memproses pengajuan Anda</li>
                    <li class="mb-2">Notifikasi akan dikirim saat pengajuan disetujui atau ditolak</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
</script>
@endpush