@extends('layouts.app')

@section('title', 'Ajukan Cuti')
@section('page-kicker', 'Leave Request')
@section('page-title', 'Ajukan Pengajuan Cuti')
@section('page-subtitle', 'Kirim cuti dengan form yang lebih rapi, preview durasi yang jelas, dan panduan singkat sebelum disubmit.')

@section('content')
<div class="d-grid gap-4">
    <div class="hero-panel">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker text-white-50">Employee Request</div>
                <h3 class="mb-2">Sampaikan kebutuhan cuti dengan data yang jelas agar review lebih cepat.</h3>
                <p class="muted mb-0">Pilih jenis cuti, tentukan periode, dan tambahkan alasan atau lampiran pendukung bila diperlukan.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('employee.leave-requests') }}" class="btn btn-light">Kembali ke Daftar</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="fw-bold fs-5">Form pengajuan cuti</div>
                    <div class="small text-muted">Isi dengan lengkap agar HRD atau admin lebih mudah memproses permintaan Anda.</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.leave-requests.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm" class="d-grid gap-4">
                        @csrf
                        <div class="data-summary-item">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis cuti <span class="text-danger">*</span></label>
                                    <select class="form-select @error('leave_type') is-invalid @enderror" id="leave_type" name="leave_type" required>
                                        <option value="">Pilih jenis cuti</option>
                                        <option value="sick" {{ old('leave_type') === 'sick' ? 'selected' : '' }}>Cuti Sakit</option>
                                        <option value="annual" {{ old('leave_type') === 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                                        <option value="unpaid" {{ old('leave_type') === 'unpaid' ? 'selected' : '' }}>Cuti Tanpa Bayaran</option>
                                        <option value="other" {{ old('leave_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('leave_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="data-summary-item" id="durationInfo" style="display:none;">
                            <div class="small text-muted">Estimasi durasi</div>
                            <div class="fw-semibold"><span id="durationDays">0</span> hari kalender</div>
                        </div>

                        <div class="data-summary-item">
                            <label class="form-label">Alasan cuti <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="6" maxlength="500" required>{{ old('reason') }}</textarea>
                            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="small text-muted mt-2"><span id="reasonCount">0</span>/500 karakter</div>
                        </div>

                        <div class="data-summary-item">
                            <label class="form-label">Lampiran pendukung</label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                            @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="small text-muted mt-2">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB.</div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                            <a href="{{ route('employee.leave-requests') }}" class="btn btn-outline-primary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="fw-bold">Panduan singkat</div>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li>Jelaskan alasan dengan singkat tetapi jelas.</li>
                        <li>Untuk cuti sakit, lampiran pendukung akan sangat membantu proses review.</li>
                        <li>Pastikan tanggal akhir tidak lebih awal dari tanggal mulai.</li>
                        <li>Status awal pengajuan adalah pending sampai direview admin atau HRD.</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="fw-bold">Jenis cuti</div>
                </div>
                <div class="card-body">
                    <div class="data-summary">
                        <div class="data-summary-item"><span class="badge rounded-pill bg-warning text-dark mb-2">Cuti Sakit</span><div class="small text-muted">Untuk kondisi kesehatan yang menghambat aktivitas kerja.</div></div>
                        <div class="data-summary-item"><span class="badge rounded-pill bg-primary mb-2">Cuti Tahunan</span><div class="small text-muted">Digunakan untuk keperluan pribadi atau keluarga.</div></div>
                        <div class="data-summary-item"><span class="badge rounded-pill bg-secondary mb-2">Cuti Tanpa Bayaran</span><div class="small text-muted">Untuk kebutuhan mendesak di luar kuota cuti reguler.</div></div>
                        <div class="data-summary-item"><span class="badge rounded-pill bg-info mb-2">Lainnya</span><div class="small text-muted">Digunakan untuk alasan lain yang masih perlu dijelaskan di form.</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateDuration() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        if (!start || !end) {
            document.getElementById('durationInfo').style.display = 'none';
            return;
        }

        const startDate = new Date(start);
        const endDate = new Date(end);
        if (endDate < startDate) {
            document.getElementById('durationInfo').style.display = 'none';
            return;
        }

        const diffDays = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('durationDays').textContent = diffDays;
        document.getElementById('durationInfo').style.display = 'block';
    }

    document.getElementById('start_date').addEventListener('change', function () {
        document.getElementById('end_date').min = this.value;
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
        updateDuration();
    });
    document.getElementById('end_date').addEventListener('change', updateDuration);

    document.getElementById('reason').addEventListener('input', function () {
        document.getElementById('reasonCount').textContent = this.value.length;
    });
    document.getElementById('reasonCount').textContent = document.getElementById('reason').value.length;

    document.getElementById('attachment').addEventListener('change', function () {
        if (this.files[0] && this.files[0].size > 2048000) {
            Swal.fire({ icon: 'error', title: 'File terlalu besar', text: 'Ukuran file maksimal 2MB.' });
            this.value = '';
        }
    });

    document.getElementById('leaveForm').addEventListener('submit', function (event) {
        const start = new Date(document.getElementById('start_date').value);
        const end = new Date(document.getElementById('end_date').value);
        if (end < start) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Tanggal tidak valid', text: 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.' });
        }
    });

    updateDuration();
</script>
@endpush
