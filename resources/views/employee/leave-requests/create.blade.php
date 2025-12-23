@extends('layouts.app')

@section('title', 'Ajukan Cuti Baru')

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
                    <i class="bi bi-plus-circle me-2"></i>
                    Ajukan Cuti Baru
                </h2>
                <p class="text-muted mb-0">Isi formulir pengajuan cuti dengan lengkap</p>
            </div>
            <a href="{{ route('employee.leave-requests') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    Formulir Pengajuan Cuti
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employee.leave-requests.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm">
                    @csrf

                    <div class="mb-3">
                        <label for="leave_type" class="form-label">
                            Jenis Cuti <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('leave_type') is-invalid @enderror" 
                                id="leave_type" 
                                name="leave_type" 
                                required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>
                                Cuti Sakit
                            </option>
                            <option value="annual" {{ old('leave_type') == 'annual' ? 'selected' : '' }}>
                                Cuti Tahunan
                            </option>
                            <option value="unpaid" {{ old('leave_type') == 'unpaid' ? 'selected' : '' }}>
                                Cuti Tanpa Bayaran
                            </option>
                            <option value="other" {{ old('leave_type') == 'other' ? 'selected' : '' }}>
                                Lainnya
                            </option>
                        </select>
                        @error('leave_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih jenis cuti yang sesuai dengan keperluan Anda</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}"
                                   min="{{ now()->addDays(1)->format('Y-m-d') }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}"
                                   min="{{ now()->addDays(1)->format('Y-m-d') }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="alert alert-info" id="durationInfo" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Durasi Cuti:</strong> <span id="durationDays">0</span> hari
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">
                            Alasan Cuti <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" 
                                  id="reason" 
                                  name="reason" 
                                  rows="5" 
                                  placeholder="Jelaskan alasan pengajuan cuti Anda..."
                                  maxlength="500"
                                  required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <span id="reasonCount">0</span>/500 karakter
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label">
                            Lampiran <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="file" 
                               class="form-control @error('attachment') is-invalid @enderror" 
                               id="attachment" 
                               name="attachment"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Format: PDF, JPG, JPEG, PNG. Maksimal 2MB. 
                            <br>
                            <em>Untuk cuti sakit, lampirkan surat keterangan dokter jika tersedia.</em>
                        </small>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('employee.leave-requests') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Information Card -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips Pengajuan
                </h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li class="mb-2">Ajukan cuti minimal <strong>3 hari</strong> sebelumnya</li>
                    <li class="mb-2">Pastikan alasan cuti dijelaskan dengan jelas</li>
                    <li class="mb-2">Lampirkan dokumen pendukung jika diperlukan</li>
                    <li class="mb-2">Cek email untuk notifikasi persetujuan</li>
                </ul>
            </div>
        </div>

        <!-- Leave Types Info -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Jenis Cuti
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-warning mb-2">Cuti Sakit</span>
                    <p class="small text-muted mb-0">
                        Untuk kondisi kesehatan yang tidak memungkinkan bekerja. Disarankan melampirkan surat keterangan dokter.
                    </p>
                </div>
                <hr>
                <div class="mb-3">
                    <span class="badge bg-primary mb-2">Cuti Tahunan</span>
                    <p class="small text-muted mb-0">
                        Cuti yang diberikan setiap tahun untuk keperluan pribadi atau keluarga.
                    </p>
                </div>
                <hr>
                <div class="mb-3">
                    <span class="badge bg-secondary mb-2">Cuti Tanpa Bayaran</span>
                    <p class="small text-muted mb-0">
                        Cuti tanpa menerima gaji untuk keperluan mendesak.
                    </p>
                </div>
                <hr>
                <div class="mb-0">
                    <span class="badge bg-info mb-2">Lainnya</span>
                    <p class="small text-muted mb-0">
                        Keperluan lain yang mendesak dan tidak termasuk kategori di atas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate duration
    function calculateDuration() {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (startDate && endDate && endDate >= startDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            $('#durationDays').text(diffDays);
            $('#durationInfo').slideDown();
        } else {
            $('#durationInfo').slideUp();
        }
    }

    $('#start_date, #end_date').on('change', calculateDuration);

    // Update end_date minimum when start_date changes
    $('#start_date').on('change', function() {
        $('#end_date').attr('min', $(this).val());
        if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
            $('#end_date').val($(this).val());
        }
        calculateDuration();
    });

    // Character counter for reason
    $('#reason').on('input', function() {
        const count = $(this).val().length;
        $('#reasonCount').text(count);
    });

    // Initial count
    $('#reasonCount').text($('#reason').val().length);

    // Form validation
    $('#leaveForm').on('submit', function(e) {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (endDate < startDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Mengirim Pengajuan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // File input preview
    $('#attachment').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2048000) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                });
                $(this).val('');
            }
        }
    });
});
</script>
@endpush