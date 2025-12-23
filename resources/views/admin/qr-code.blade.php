@extends('layouts.app')

@section('title', 'QR Code Generator')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.employees') }}">
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
        <a class="nav-link active" href="{{ route('admin.qr-code') }}">
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
                    <i class="bi bi-qr-code me-2"></i>
                    QR Code Generator
                </h2>
                <p class="text-muted mb-0">Generate dan kelola QR Code untuk absensi</p>
            </div>
            <div>
                <button type="button" class="btn btn-success" onclick="autoGenerateQR()">
                    <i class="bi bi-lightning-charge me-2"></i>
                    Auto Generate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-info-circle text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Durasi QR Code</h6>
                        <p class="mb-0 text-muted small">QR Code berlaku selama <strong>30 detik</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-clock text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Window Absensi</h6>
                        <p class="mb-0 text-muted small"><strong>30 menit</strong> sebelum hingga <strong>45 menit</strong> sesudah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-arrow-clockwise text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Auto Refresh</h6>
                        <p class="mb-0 text-muted small">QR Code refresh otomatis setiap <strong>30 detik</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Generation Section -->
<div class="row g-3 mb-4">
    @foreach($shifts as $shift)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock me-2"></i>
                    {{ $shift->name }}
                </h5>
                <small>{{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Check-in QR -->
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="text-success">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Check-in
                            </h6>
                            <div id="qr-checkin-{{ $shift->id }}" class="qr-container mb-3">
                                <div class="qr-placeholder">
                                    <i class="bi bi-qr-code" style="font-size: 3rem;"></i>
                                    <p class="mt-2 text-muted">Tidak ada QR aktif</p>
                                </div>
                            </div>
                            <button type="button" 
                                    class="btn btn-success btn-sm w-100" 
                                    onclick="generateQR({{ $shift->id }}, 'check_in')">
                                <i class="bi bi-qr-code me-2"></i>
                                Generate Check-in
                            </button>
                            <div id="timer-checkin-{{ $shift->id }}" class="mt-2 text-muted small"></div>
                        </div>
                    </div>

                    <!-- Check-out QR -->
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Check-out
                            </h6>
                            <div id="qr-checkout-{{ $shift->id }}" class="qr-container mb-3">
                                <div class="qr-placeholder">
                                    <i class="bi bi-qr-code" style="font-size: 3rem;"></i>
                                    <p class="mt-2 text-muted">Tidak ada QR aktif</p>
                                </div>
                            </div>
                            <button type="button" 
                                    class="btn btn-danger btn-sm w-100" 
                                    onclick="generateQR({{ $shift->id }}, 'check_out')">
                                <i class="bi bi-qr-code me-2"></i>
                                Generate Check-out
                            </button>
                            <div id="timer-checkout-{{ $shift->id }}" class="mt-2 text-muted small"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Help Section -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-question-circle me-2"></i>
            Panduan Penggunaan
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Manual Generate</h6>
                <ol>
                    <li>Pilih shift yang ingin digenerate QR Code</li>
                    <li>Klik tombol "Generate Check-in" atau "Generate Check-out"</li>
                    <li>QR Code akan muncul dan berlaku selama 30 detik</li>
                    <li>Karyawan dapat scan QR Code menggunakan aplikasi</li>
                </ol>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">Auto Generate</h6>
                <ol>
                    <li>Klik tombol "Auto Generate" di pojok kanan atas</li>
                    <li>Sistem akan otomatis generate QR untuk semua shift aktif</li>
                    <li>QR hanya muncul dalam window absensi (30 menit sebelum - 45 menit sesudah)</li>
                    <li>QR akan refresh otomatis setiap 30 detik</li>
                </ol>
            </div>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Penting:</strong> Pastikan QR Code ditampilkan di lokasi yang mudah diakses karyawan saat check-in/check-out.
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .qr-container {
        min-height: 250px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        padding: 20px;
    }

    .qr-placeholder {
        text-align: center;
        color: #6c757d;
    }

    .qr-container img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .qr-active {
        border-color: #198754;
        background-color: #d1e7dd;
    }

    .qr-expired {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .countdown {
        font-weight: bold;
        font-size: 1.1rem;
    }

    .countdown.warning {
        color: #ffc107;
    }

    .countdown.danger {
        color: #dc3545;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    let timers = {};

    // Generate QR Code
    function generateQR(shiftId, type) {
        $.ajax({
            url: '/qr-code/generate',
            method: 'POST',
            data: {
                shift_id: shiftId,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    displayQRCode(shiftId, type, response.code, response.expires_at);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'QR Code Generated',
                        text: 'QR Code berhasil dibuat dan siap digunakan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal membuat QR Code. Silakan coba lagi.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

    // Display QR Code
    function displayQRCode(shiftId, type, code, expiresAt) {
        const containerId = `qr-${type.replace('_', '')}-${shiftId}`;
        const timerContainerId = `timer-${type.replace('_', '')}-${shiftId}`;
        const container = document.getElementById(containerId);
        
        // Clear existing content
        container.innerHTML = '';
        container.classList.add('qr-active');
        
        // Generate QR Code
        new QRCode(container, {
            text: code,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Start countdown timer
        startCountdown(timerContainerId, expiresAt, shiftId, type);
    }

    // Countdown timer
    function startCountdown(timerId, expiresAt, shiftId, type) {
        // Clear existing timer
        if (timers[timerId]) {
            clearInterval(timers[timerId]);
        }

        const timerElement = document.getElementById(timerId);
        const expiryTime = new Date(expiresAt).getTime();

        timers[timerId] = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryTime - now;

            if (distance < 0) {
                clearInterval(timers[timerId]);
                timerElement.innerHTML = '<span class="text-danger">QR Code Expired</span>';
                
                const containerId = `qr-${type.replace('_', '')}-${shiftId}`;
                const container = document.getElementById(containerId);
                container.classList.remove('qr-active');
                container.classList.add('qr-expired');
                
                setTimeout(() => {
                    container.innerHTML = `
                        <div class="qr-placeholder">
                            <i class="bi bi-qr-code" style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">QR Code Expired</p>
                        </div>
                    `;
                    container.classList.remove('qr-expired');
                }, 2000);
            } else {
                const seconds = Math.floor(distance / 1000);
                let className = 'countdown';
                
                if (seconds <= 10) {
                    className += ' danger';
                } else if (seconds <= 20) {
                    className += ' warning';
                }
                
                timerElement.innerHTML = `<span class="${className}">Berlaku: ${seconds} detik</span>`;
            }
        }, 100);
    }

    // Auto Generate for all shifts
    function autoGenerateQR() {
        Swal.fire({
            title: 'Auto Generate QR Code',
            text: 'Generate QR Code untuk semua shift yang sedang dalam window absensi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle me-2"></i>Ya, Generate',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/qr-code/auto-generate',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            if (response.generated_count > 0) {
                                response.codes.forEach(qr => {
                                    displayQRCode(qr.shift_id, qr.type, qr.code, qr.expires_at);
                                });
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: `${response.generated_count} QR Code berhasil digenerate`,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Tidak Ada QR',
                                    text: 'Tidak ada shift yang dalam window absensi saat ini',
                                    confirmButtonColor: '#0d6efd'
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal auto generate QR Code',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    // Load active QR codes on page load
    $(document).ready(function() {
        $.ajax({
            url: '/qr-code/active',
            method: 'GET',
            success: function(response) {
                if (response.success && response.qr_codes.length > 0) {
                    response.qr_codes.forEach(qr => {
                        displayQRCode(qr.shift_id, qr.type, qr.code, qr.expires_at);
                    });
                }
            }
        });
    });
</script>
@endpush