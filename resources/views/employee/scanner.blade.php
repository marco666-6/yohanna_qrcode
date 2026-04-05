@extends('layouts.app')

@section('title', 'Scan Absensi')
@section('page-kicker', 'QR Attendance')
@section('page-title', 'Scan Absensi')
@section('page-subtitle', 'Gunakan QR resmi dari admin sesuai shift untuk check-in dan check-out secara cepat dan aman.')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold fs-5">Scanner QR</div>
                    <div class="small text-muted">Pastikan kamera aktif dan QR berasal dari shift yang sesuai.</div>
                </div>
                <span class="soft-chip"><i class="bi bi-clock-history"></i><span id="currentTime">{{ now()->format('H:i:s') }}</span></span>
            </div>
            <div class="card-body">
                <div class="scanner-shell mb-4">
                    <div id="scanner" class="scanner-container">
                        <div class="scanner-overlay">
                            <div class="scanner-frame"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button id="startScanBtn" class="btn btn-primary" onclick="startScanner()"><i class="bi bi-camera me-1"></i>Mulai Scan</button>
                    <button id="stopScanBtn" class="btn btn-outline-primary" onclick="stopScanner()" style="display:none;"><i class="bi bi-stop-circle me-1"></i>Berhenti</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="fw-bold fs-5">Status hari ini</div>
            </div>
            <div class="card-body" id="statusContent">
                <div class="text-center py-3 text-muted">Memuat status...</div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <div class="fw-bold fs-5">Input manual</div>
            </div>
            <div class="card-body">
                <form id="manualScanForm" onsubmit="return manualScan(event)">
                    <div class="mb-3">
                        <label class="form-label">Kode QR</label>
                        <input type="text" class="form-control" id="manualQrCode" placeholder="Masukkan kode QR" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Submit Kode</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="fw-bold fs-5">Panduan singkat</div>
            </div>
            <div class="card-body">
                <ol class="small text-muted mb-0 ps-3">
                    <li>Pilih tombol mulai scan lalu izinkan akses kamera.</li>
                    <li>Arahkan ke QR resmi yang ditampilkan admin sesuai shift Anda.</li>
                    <li>Tunggu konfirmasi sistem untuk check-in atau check-out.</li>
                    <li>Jika kamera bermasalah, gunakan input manual sebagai cadangan.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .scanner-shell {
        padding: 1rem;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(201,117,112,.08), rgba(255,255,255,.9));
        border: 1px solid rgba(129,101,104,.12);
    }
    .scanner-container {
        position: relative;
        width: 100%;
        min-height: 420px;
        border-radius: 24px;
        overflow: hidden;
        background: linear-gradient(135deg, #3f2528, #8b5557);
    }
    .scanner-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .scanner-frame {
        width: 260px;
        height: 260px;
        border-radius: 24px;
        border: 3px solid rgba(255,255,255,.86);
        box-shadow: 0 0 0 9999px rgba(45,24,27,.48);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;

    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateTime, 1000);
    updateTime();

    function renderStatus(attendance) {
        if (!attendance) {
            document.getElementById('statusContent').innerHTML = `
                <div class="empty-state py-3">
                    <i class="bi bi-calendar-x"></i>
                    <div class="fw-semibold mb-1">Belum ada absensi hari ini</div>
                    <div class="small">Lakukan scan QR untuk memulai kehadiran Anda.</div>
                </div>
            `;
            return;
        }

        document.getElementById('statusContent').innerHTML = `
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Shift</div>
                <div class="fw-semibold">${attendance.shift?.name || '-'}</div>
            </div>
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Check-in</div>
                <div class="fw-semibold">${attendance.check_in ? attendance.check_in.substring(0, 5) : '-'}</div>
            </div>
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Check-out</div>
                <div class="fw-semibold">${attendance.check_out ? attendance.check_out.substring(0, 5) : '-'}</div>
            </div>
            <div class="data-summary-item">
                <div class="small text-muted">Status</div>
                <div class="fw-semibold">${getStatusText(attendance.status)}</div>
            </div>
        `;
    }

    function loadTodayStatus() {
        $.get('/attendance/today-status')
            .done(response => renderStatus(response.attendance))
            .fail(() => {
                document.getElementById('statusContent').innerHTML = '<div class="text-danger small">Gagal memuat status absensi.</div>';
            });
    }

    function startScanner() {
        html5QrCode = new Html5Qrcode('scanner');
        html5QrCode.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess)
            .then(() => {
                isScanning = true;
                document.getElementById('startScanBtn').style.display = 'none';
                document.getElementById('stopScanBtn').style.display = 'inline-block';
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Kamera tidak tersedia', text: 'Izinkan akses kamera lalu coba kembali.' }));
    }

    function stopScanner() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                document.getElementById('startScanBtn').style.display = 'inline-block';
                document.getElementById('stopScanBtn').style.display = 'none';
            });
        }
    }

    function onScanSuccess(decodedText) {
        if (isScanning) {
            stopScanner();
            processQRCode(decodedText);
        }
    }

    function processQRCode(code) {
        $.post('/attendance/scan', { code })
            .done(response => {
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    html: `<div class="small">Status: <strong>${getStatusText(response.status)}</strong><br>Waktu: <strong>${response.check_in_time || response.check_out_time}</strong></div>`
                }).then(loadTodayStatus);
            })
            .fail(xhr => {
                Swal.fire({ icon: 'error', title: 'Scan gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses QR.' });
            });
    }

    function manualScan(event) {
        event.preventDefault();
        const code = document.getElementById('manualQrCode').value.trim();
        if (code) {
            processQRCode(code);
            document.getElementById('manualQrCode').value = '';
        }
        return false;
    }

    function getStatusText(status) {
        return {
            on_time: 'Tepat Waktu',
            late: 'Terlambat',
            incomplete: 'Belum Check-out',
            absent: 'Tidak Hadir'
        }[status] || 'Tidak diketahui';
    }

    document.addEventListener('DOMContentLoaded', loadTodayStatus);
    window.addEventListener('beforeunload', stopScanner);
</script>
@endpush
