@extends('layouts.app')

@section('title', 'Kelola Karyawan')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.employees') }}">
            <i class="bi bi-people"></i> Kelola Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.shifts') }}">
            <i class="bi bi-clock-history"></i> Kelola Shift
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.attendances') }}">
            <i class="bi bi-calendar-check"></i> Kelola Kehadiran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.qr-code') }}">
            <i class="bi bi-qr-code"></i> QR Code
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-people me-2"></i>
                    Kelola Karyawan
                </h2>
                <p class="text-muted mb-0">Manajemen data karyawan perusahaan</p>
            </div>
            <div>
                <a href="{{ route('admin.employees.export') }}" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-excel me-2"></i>
                    Export Excel
                </a>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Karyawan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Karyawan</p>
                        <h3 class="mb-0">{{ $employees->total() }}</h3>
                    </div>
                    <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Karyawan Aktif</p>
                        <h3 class="mb-0">{{ $employees->where('is_active', true)->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tidak Aktif</p>
                        <h3 class="mb-0">{{ $employees->where('is_active', false)->count() }}</h3>
                    </div>
                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Halaman Ini</p>
                        <h3 class="mb-0">{{ $employees->count() }}</h3>
                    </div>
                    <i class="bi bi-file-earmark-text text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Daftar Karyawan
                </h5>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari karyawan...">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="employeeTable">
                <thead class="table-light">
                    <tr>
                        <th>ID Karyawan</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Departemen</th>
                        <th>Posisi</th>
                        <th>Shift</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>
                            <strong>{{ $employee->employee_id }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    @if($employee->phone)
                                        <br><small class="text-muted">
                                            <i class="bi bi-telephone me-1"></i>{{ $employee->phone }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <i class="bi bi-envelope me-1"></i>
                            {{ $employee->email }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $employee->department ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $employee->position ?? '-' }}</td>
                        <td>
                            @if($employee->shift)
                                <span class="badge bg-info">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $employee->shift->name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.employees.detail', $employee->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->name }}')"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada data karyawan</p>
                            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Tambah Karyawan Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="mt-3">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#employeeTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Delete employee function
    function deleteEmployee(id, name) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus karyawan:<br><strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/employees/${id}`;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush