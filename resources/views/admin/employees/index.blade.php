@extends('layouts.app')

@section('title', 'Kelola Karyawan')
@section('page-kicker', 'Employee Directory')
@section('page-title', 'Kelola Karyawan')
@section('page-subtitle', 'Gunakan pencarian, filter, dan pagination agar data karyawan tetap ringan walau jumlah record terus bertambah.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        @foreach([
            ['label' => 'Total Karyawan', 'value' => $employeeStats['total'], 'helper' => 'Seluruh akun karyawan', 'icon' => 'bi-people', 'tone' => 'primary'],
            ['label' => 'Aktif', 'value' => $employeeStats['active'], 'helper' => 'Dapat mengakses sistem', 'icon' => 'bi-person-check', 'tone' => 'success'],
            ['label' => 'Nonaktif', 'value' => $employeeStats['inactive'], 'helper' => 'Perlu review data akun', 'icon' => 'bi-person-x', 'tone' => 'danger'],
            ['label' => 'Hasil Filter', 'value' => $employeeStats['filtered'], 'helper' => 'Menyesuaikan filter saat ini', 'icon' => 'bi-funnel', 'tone' => 'info'],
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
        <form action="{{ route('admin.employees') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Cari karyawan</label>
                <input type="text" class="form-control" name="search" value="{{ $filters['search'] }}" placeholder="Nama, email, ID karyawan, posisi">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua</option>
                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Shift</label>
                <select class="form-select" name="shift_id">
                    <option value="">Semua shift</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ (string) request('shift_id') === (string) $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Departemen</label>
                <select class="form-select" name="department">
                    <option value="">Semua</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}" {{ $filters['department'] === $department ? 'selected' : '' }}>{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1">
                <label class="form-label">Per halaman</label>
                <select class="form-select" name="per_page">
                    @foreach([10,15,25,50,100] as $option)
                        <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 d-grid">
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </form>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('admin.employees') }}" class="btn btn-outline-primary btn-sm">Reset Filter</a>
            <a href="{{ route('admin.employees.export') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</a>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Tambah Karyawan</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Direktori karyawan</div>
                <div class="small text-muted">Menampilkan {{ $employees->count() }} data dari {{ $employees->total() }} hasil.</div>
            </div>
            <span class="soft-chip"><i class="bi bi-database"></i>Siap untuk 10k+ data dengan server-side pagination</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Karyawan</th>
                            <th>Kontak</th>
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
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-fallback text-white" style="background:linear-gradient(135deg,#c97570,#df9a95);">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="fw-semibold">{{ $employee->name }}</div>
                                            <div class="small text-muted">{{ $employee->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $employee->email }}</div>
                                    <div class="small text-muted">{{ $employee->phone ?: 'Nomor belum diisi' }}</div>
                                </td>
                                <td>{{ $employee->department ?: 'Belum diatur' }}</td>
                                <td>{{ $employee->position ?: 'Belum diatur' }}</td>
                                <td>{{ $employee->shift?->name ?: '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $employee->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.employees.detail', $employee->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteEmployee({{ $employee->id }}, @js($employee->name))"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <div class="fw-semibold mb-1">Tidak ada data yang cocok</div>
                                        <div class="small">Ubah filter atau tambahkan karyawan baru.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
            <div class="card-body border-top">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function deleteEmployee(id, name) {
        appDeleteConfirm(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/employees/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }, {
            title: 'Hapus karyawan?',
            text: `Data ${name} akan dihapus dari sistem.`
        });
    }
</script>
@endpush
