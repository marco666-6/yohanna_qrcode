@extends('layouts.app')

@section('title', 'Scan Absensi')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('employee.scanner') }}">
            <i class="bi bi-qr-code-scan"></i> Scan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.attendance-history') }}">
            <i class="bi bi-clock-history"></i> Riwayat Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employee.leave-requests') }}">
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
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-qr-code-scan me-2"></i>
                    Scan Absensi
                </h2>
                <p class="text-muted mb-0">Scan QR Code untuk check-in atau check-out</p>
            </div>
        </div>

        <!-- Current Time & Date -->
        <div class="card mb-3">
            <div class="card-body text-center bg-primary text-white">
                <h2 id="currentTime" class="mb-2">{{ now()->format('H:i:s') }}</h2>
                <p class="mb-0">{{ formatDate(now()) }}</p>
            </div>
        </div>

        <!-- Scanner Card -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-camera me-2"></i>
                    Scanner QR Code
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div id="scanner" class="scanner-container">
                        <video id="video" autoplay></video>
                        <div id="scannerOverlay" class="scanner-overlay">
                            <div class="scanner-frame"></div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button id="startScanBtn" class="btn btn-success btn-lg" onclick="startScanner()">
                        <i class="bi bi-camera me-2"></i>
                        Mulai Scan
                    </button>
                    <button id="stopScanBtn" class="btn btn-danger btn-lg" onclick="stopScanner()" style="display: none;">
                        <i class="bi bi-stop-circle me-2"></i>
                        Berhenti Scan
                    </button>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Cara Scan:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Klik tombol "Mulai Scan"</li>
                        <li>Izinkan akses kamera</li>
                        <li>Arahkan kamera ke QR Code yang ditampilkan admin</li>
                        <li>Tunggu hingga QR Code terdeteksi otomatis</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Today's Status -->
        <div class="card mt-3" id="statusCard">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Status Absensi Hari Ini
                </h6>
            </div>
            <div class="card-body" id="statusContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat status...</p>
                </div>
            </div>
        </div>

        <!-- Manual Input (if needed) -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-keyboard me-2"></i>
                    Input Manual QR Code
                </h6>
            </div>
            <div class="card-body">
                <form id="manualScanForm" onsubmit="return manualScan(event)">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="manualQrCode" 
                               placeholder="Masukkan kode QR..."
                               required>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check-circle me-2"></i>
                            Submit
                        </button>
                    </div>
                    <small class="text-muted">Gunakan jika kamera tidak berfungsi</small>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .scanner-container {
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }

    #video {
        width: 100%;
        height: auto;
        display: block;
    }

    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scanner-frame {
        width: 250px;
        height: 250px;
        border: 3px solid #28a745;
        border-radius: 12px;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            border-color: #28a745;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5), 0 0 20px #28a745;
        }
        50% {
            border-color: #20c997;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5), 0 0 30px #20c997;
        }
    }

    .scanner-container.inactive {
        background: #e9ecef;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scanner-container.inactive::before {
        content: 'Kamera Tidak Aktif';
        color: #6c757d;
        font-size: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;

    // Update current time
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    setInterval(updateTime, 1000);

    // Load today's status
    function loadTodayStatus() {
        $.ajax({
            url: '/attendance/today-status',
            method: 'GET',
            success: function(response) {
                if (response.success && response.attendance) {
                    const att = response.attendance;
                    let statusHtml = `
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="bi bi-box-arrow-in-right text-success" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2"><strong>Check-in</strong></p>
                                <p class="mb-0">${att.check_in ? att.check_in : '<span class="text-muted">Belum</span>'}</p>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-box-arrow-right text-danger" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2"><strong>Check-out</strong></p>
                                <p class="mb-0">${att.check_out ? att.check_out : '<span class="text-muted">Belum</span>'}</p>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-info-circle text-info" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2"><strong>Status</strong></p>
                                <p class="mb-0"><span class="badge bg-${getStatusBadge(att.status)}">${getStatusText(att.status)}</span></p>
                            </div>
                        </div>
                    `;
                    document.getElementById('statusContent').innerHTML = statusHtml;
                } else {
                    document.getElementById('statusContent').innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted mb-0">Belum ada absensi hari ini</p>
                        </div>
                    `;
                }
            },
            error: function() {
                document.getElementById('statusContent').innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat status
                    </div>
                `;
            }
        });
    }

    // Start scanner
    function startScanner() {
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrCode = new Html5Qrcode("scanner");

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isScanning = true;
            document.getElementById('startScanBtn').style.display = 'none';
            document.getElementById('stopScanBtn').style.display = 'inline-block';
        }).catch((err) => {
            Swal.fire({
                icon: 'error',
                title: 'Kamera Tidak Tersedia',
                text: 'Pastikan Anda memberikan izin akses kamera',
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // Stop scanner
    function stopScanner() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                document.getElementById('startScanBtn').style.display = 'inline-block';
                document.getElementById('stopScanBtn').style.display = 'none';
            });
        }
    }

    // On scan success
    function onScanSuccess(decodedText, decodedResult) {
        if (isScanning) {
            stopScanner();
            processQRCode(decodedText);
        }
    }

    // On scan failure (do nothing)
    function onScanFailure(error) {
        // Silent - no action needed
    }

    // Process QR Code
    function processQRCode(code) {
        $.ajax({
            url: '/attendance/scan',
            method: 'POST',
            data: { code: code },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        html: `
                            <p class="mb-1"><strong>Waktu:</strong> ${response.check_in_time || response.check_out_time}</p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge bg-${getStatusBadge(response.status)}">${getStatusText(response.status)}</span></p>
                        `,
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        loadTodayStatus();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan Gagal',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response?.message || 'Terjadi kesalahan saat memproses QR Code',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

    // Manual scan
    function manualScan(event) {
        event.preventDefault();
        const code = document.getElementById('manualQrCode').value.trim();
        
        if (code) {
            processQRCode(code);
            document.getElementById('manualQrCode').value = '';
        }
        
        return false;
    }

    // Helper function to get status badge
    function getStatusBadge(status) {
        const badges = {
            'on_time': 'success',
            'late': 'warning',
            'incomplete': 'info',
            'absent': 'danger'
        };
        return badges[status] || 'secondary';
    }

    // Helper function to get status text
    function getStatusText(status) {
        const texts = {
            'on_time': 'Tepat Waktu',
            'late': 'Terlambat',
            'incomplete': 'Belum Check-out',
            'absent': 'Tidak Hadir'
        };
        return texts[status] || 'Tidak Diketahui';
    }

    // Load status on page load
    $(document).ready(function() {
        loadTodayStatus();
    });

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        if (isScanning) {
            stopScanner();
        }
    });
</script>
@endpush