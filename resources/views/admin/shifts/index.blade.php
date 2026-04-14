@extends('layouts.app')

@section('title', 'Kelola Shift Kerja')
@section('page-kicker', 'Schedule Control')
@section('page-title', 'Kelola Shift Kerja')
@section('page-subtitle', 'Atur jam kerja, toleransi terlambat, dan status shift dengan tampilan yang lebih rapi dan mudah ditinjau.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-value">{{ $shifts->count() }}</div>
                <div class="stat-label">Total Shift</div>
                <div class="stat-helper">Semua jadwal yang tersimpan</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value">{{ $shifts->where('is_active', true)->count() }}</div>
                <div class="stat-label">Shift Aktif</div>
                <div class="stat-helper">Sedang dapat dipakai untuk operasional</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-moon-stars"></i></div>
                <div class="stat-value">{{ $shifts->filter(fn ($shift) => $shift->isOvernight())->count() }}</div>
                <div class="stat-label">Overnight Shift</div>
                <div class="stat-helper">Shift lintas tengah malam</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-value">{{ $shifts->sum('users_count') }}</div>
                <div class="stat-label">Karyawan Terpasang</div>
                <div class="stat-helper">Total user yang memakai shift</div>
            </div>
        </div>
    </div>

    <div class="hero-panel">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker text-white-50">Shift Summary</div>
                <h3 class="mb-2">Jaga struktur kerja tetap konsisten antar pagi, siang, dan malam.</h3>
                <p class="muted mb-0">Gunakan modal di bawah untuk menambah shift baru atau memperbarui shift lama tanpa meninggalkan halaman utama.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Shift
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-bold fs-5">Daftar shift kerja</div>
                <div class="small text-muted">Durasi, toleransi, dan jumlah karyawan tampil dalam satu tabel yang lebih ringkas.</div>
            </div>
            <span class="soft-chip"><i class="bi bi-lightning-charge"></i>Edit cepat lewat modal</span>
        </div>
        <div class="card-body p-0">
            @if($shifts->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Shift</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                                <th>Toleransi</th>
                                <th>Karyawan</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $shift)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $shift->name }}</div>
                                        <div class="small text-muted">{{ $shift->isOvernight() ? 'Overnight shift' : 'Shift reguler' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }}</div>
                                        <div class="small text-muted">{{ $shift->isOvernight() ? 'Melewati tengah malam' : 'Dalam hari yang sama' }}</div>
                                    </td>
                                    <td>{{ rtrim(rtrim(number_format($shift->durationHours(), 2), '0'), '.') }} jam</td>
                                    <td>{{ $shift->late_tolerance }} menit</td>
                                    <td><span class="badge badge-soft-info">{{ $shift->users_count }} karyawan</span></td>
                                    <td>
                                        <span class="badge {{ $shift->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $shift->is_active ? 'Aktif' : 'Tidak aktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editShiftModal"
                                                data-id="{{ $shift->id }}"
                                                data-name="{{ $shift->name }}"
                                                data-start="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}"
                                                data-end="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}"
                                                data-late="{{ $shift->late_tolerance }}"
                                                data-active="{{ $shift->is_active ? 1 : 0 }}"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteShift({{ $shift->id }}, @js($shift->name), {{ $shift->users_count }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-clock-history"></i>
                    <div class="fw-semibold mb-1">Belum ada shift yang dibuat</div>
                    <div class="small mb-3">Tambahkan shift pertama untuk mulai mengatur alur absensi.</div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShiftModal">Tambah Shift</button>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="addShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Tambah shift baru</h5>
                    <div class="small text-muted">Shift langsung aktif setelah disimpan.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.shifts.store') }}" method="POST" id="addShiftForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama shift</label>
                        <input type="text" class="form-control" name="name" id="add_name" placeholder="Contoh: Shift Operasional Pagi" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Waktu mulai</label>
                            <input type="time" class="form-control" name="start_time" id="add_start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu selesai</label>
                            <input type="time" class="form-control" name="end_time" id="add_end_time" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Toleransi keterlambatan</label>
                        <input type="number" class="form-control" name="late_tolerance" id="add_late_tolerance" min="0" max="60" value="15" required>
                        <div class="small text-muted mt-2">Maksimum 60 menit. Shift malam diperbolehkan selama jam selesai tidak sama dengan jam mulai.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Edit shift</h5>
                    <div class="small text-muted">Perbarui waktu kerja, toleransi, atau status shift.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama shift</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Waktu mulai</label>
                            <input type="time" class="form-control" name="start_time" id="edit_start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu selesai</label>
                            <input type="time" class="form-control" name="end_time" id="edit_end_time" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Toleransi keterlambatan</label>
                        <input type="number" class="form-control" name="late_tolerance" id="edit_late_tolerance" min="0" max="60" required>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Status shift</label>
                        <select class="form-select" name="is_active" id="edit_is_active" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function validateShiftTime(formId, startId, endId) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function (event) {
            const startTime = document.getElementById(startId).value;
            const endTime = document.getElementById(endId).value;

            if (startTime === endTime) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Waktu tidak valid',
                    text: 'Waktu selesai tidak boleh sama dengan waktu mulai.'
                });
            }
        });
    }

    validateShiftTime('addShiftForm', 'add_start_time', 'add_end_time');
    validateShiftTime('editShiftForm', 'edit_start_time', 'edit_end_time');

    document.getElementById('editShiftModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_start_time').value = button.getAttribute('data-start');
        document.getElementById('edit_end_time').value = button.getAttribute('data-end');
        document.getElementById('edit_late_tolerance').value = button.getAttribute('data-late');
        document.getElementById('edit_is_active').value = button.getAttribute('data-active');
        document.getElementById('editShiftForm').action = `/admin/shifts/${button.getAttribute('data-id')}`;
    });

    function deleteShift(id, name, usersCount) {
        if (usersCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Shift masih dipakai',
                text: `${name} masih dipakai oleh ${usersCount} karyawan. Pindahkan dulu sebelum menghapus.`
            });
            return;
        }

        appDeleteConfirm(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/shifts/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }, {
            title: 'Hapus shift ini?',
            text: `Shift ${name} akan dihapus permanen.`
        });
    }
</script>
@endpush
