@extends('layouts.app')

@section('title', 'Absensi Saya')
@section('page-kicker', 'My Attendance QR')
@section('page-title', 'Absensi Saya')
@section('page-subtitle', 'QR absensi muncul otomatis sesuai shift Anda, dengan validasi lokasi agar check-in/check-out hanya bisa dari area yang diizinkan.')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center gap-3">
                <div>
                    <div class="fw-bold fs-5">QR absensi aktif</div>
                    <div class="small text-muted">Saat window shift terbuka, QR aktif muncul otomatis dan tetap harus melewati validasi lokasi.</div>
                </div>
                <span class="soft-chip"><i class="bi bi-clock-history"></i><span id="currentTime">{{ now()->format('H:i:s') }}</span></span>
            </div>
            <div class="card-body">
                <div id="attendanceNotice"></div>
                <div class="employee-qr-shell mt-3" id="employeeQrShell"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="fw-bold fs-5">Status hari ini</div>
            </div>
            <div class="card-body" id="statusContent"></div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="fw-bold fs-5">Shift & window</div>
            </div>
            <div class="card-body" id="windowContent"></div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="fw-bold fs-5">Validasi lokasi</div>
            </div>
            <div class="card-body" id="locationContent"></div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold fs-5">Scanner cadangan</div>
                    <div class="small text-muted">Tetap tersedia bila Anda perlu scan dari perangkat lain atau kamera eksternal.</div>
                </div>
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

    <div class="col-xl-5">
        <div class="card mb-4">
            <div class="card-header">
                <div class="fw-bold fs-5">Input manual</div>
            </div>
            <div class="card-body">
                <form id="manualScanForm" onsubmit="return manualScan(event)">
                    <div class="mb-3">
                        <label class="form-label">Kode QR</label>
                        <input type="text" class="form-control" id="manualQrCode" placeholder="Masukkan kode QR bila ada" required>
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
                    <li>Jika QR aktif sudah muncul, gunakan tombol check-in/check-out langsung dari halaman ini.</li>
                    <li>QR akan mengikuti shift Anda dan diperbarui otomatis selama window absensi masih terbuka.</li>
                    <li>Saat submit, browser akan meminta izin lokasi untuk memastikan perangkat berada di area kantor.</li>
                    <li>Bila perlu, Anda tetap bisa scan QR dari perangkat lain atau pakai input manual sebagai cadangan, tetapi validasi lokasi tetap berlaku.</li>
                    <li>Perhatikan panel status untuk tahu apakah jam check-in atau check-out sudah dibuka.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .employee-qr-shell {
        padding: 1rem;
        border-radius: 26px;
        background: linear-gradient(180deg, rgba(201,117,112,.07), rgba(255,255,255,.96));
        border: 1px solid rgba(129,101,104,.1);
    }
    .employee-qr-board {
        min-height: 420px;
        border-radius: 28px;
        border: 1px dashed rgba(129,101,104,.22);
        background: linear-gradient(135deg, #fff7f6, #fbf1ef);
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.25rem;
        text-align: center;
    }
    .employee-qr-code {
        width: 100%;
        max-width: 320px;
        margin: 0 auto;
        padding: 18px;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 18px 32px rgba(87, 52, 57, 0.08);
    }
    .employee-qr-code img,
    .employee-qr-code canvas {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
    }
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
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const initialAttendanceContext = @json($attendanceContextPayload);
    const attendanceLocationConfig = @json(config('attendance.location'));
    let attendanceContext = initialAttendanceContext;
    let html5QrCode;
    let isScanning = false;
    let qrCountdownTimer = null;

    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getStatusText(status) {
        return {
            on_time: 'Tepat Waktu',
            late: 'Terlambat',
            incomplete: 'Belum Check-out',
            absent: 'Tidak Hadir'
        }[status] || 'Tidak diketahui';
    }

    function formatDistance(meters) {
        const value = Number(meters);
        if (!Number.isFinite(value)) {
            return null;
        }

        return value >= 1000
            ? `${(value / 1000).toFixed(2)} km`
            : `${value.toFixed(2)} meter`;
    }

    function buildLocationResultHtml(payload) {
        const distance = formatDistance(payload?.distance_meters);
        const accuracy = formatDistance(payload?.accuracy_meters);
        const lines = [];

        if (distance) {
            lines.push(`Jarak dari lokasi absen: <strong>${distance}</strong>`);
        }

        if (accuracy) {
            lines.push(`Akurasi lokasi perangkat: <strong>${accuracy}</strong>`);
        }

        return lines.length ? `<br>${lines.join('<br>')}` : '';
    }

    function renderNotice(context) {
        const notice = context.notice;
        const actionLabel = context.next_action_label ? context.next_action_label.toLowerCase() : 'absensi';
        const windowLabel = context.window
            ? `Window ${actionLabel}: ${context.window.start_time} - ${context.window.end_time}.`
            : '';

        document.getElementById('attendanceNotice').innerHTML = `
            <div class="alert alert-${notice.variant} mb-0">
                <div class="fw-bold">${escapeHtml(notice.title)}</div>
                <div class="small">${escapeHtml(notice.message)} ${escapeHtml(windowLabel)}</div>
            </div>
        `;
    }

    function renderStatus(context) {
        const attendance = context.attendance;
        if (!attendance) {
            document.getElementById('statusContent').innerHTML = `
                <div class="empty-state py-3">
                    <i class="bi bi-calendar-x"></i>
                    <div class="fw-semibold mb-1">Belum ada absensi aktif</div>
                    <div class="small">Status check-in/check-out Anda akan tampil di sini setelah absensi diproses.</div>
                </div>
            `;
            return;
        }

        document.getElementById('statusContent').innerHTML = `
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Shift</div>
                <div class="fw-semibold">${escapeHtml(attendance.shift?.name || context.shift?.name || '-')}</div>
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
                <div class="fw-semibold">${escapeHtml(attendance.status_text || getStatusText(attendance.status))}</div>
            </div>
        `;
    }

    function renderWindow(context) {
        if (!context.shift) {
            document.getElementById('windowContent').innerHTML = `
                <div class="empty-state py-3">
                    <i class="bi bi-person-exclamation"></i>
                    <div class="fw-semibold mb-1">Shift belum tersedia</div>
                    <div class="small">Hubungi admin agar shift Anda diatur terlebih dahulu.</div>
                </div>
            `;
            return;
        }

        const windowHtml = context.window ? `
            <div class="data-summary-item mt-3">
                <div class="small text-muted">Window ${escapeHtml(context.next_action_label.toLowerCase())}</div>
                <div class="fw-semibold">${escapeHtml(context.window.start_time)} - ${escapeHtml(context.window.end_time)}</div>
                <div class="small text-muted mt-1">Jam target shift: ${escapeHtml(context.window.target_time)}</div>
            </div>
        ` : `
            <div class="data-summary-item mt-3">
                <div class="small text-muted">Window absensi</div>
                <div class="fw-semibold">Absensi hari ini sudah selesai</div>
            </div>
        `;

        document.getElementById('windowContent').innerHTML = `
            <div class="data-summary-item">
                <div class="small text-muted">Shift</div>
                <div class="fw-semibold">${escapeHtml(context.shift.name)}</div>
                <div class="small text-muted mt-1">${escapeHtml(context.shift.start_time)} - ${escapeHtml(context.shift.end_time)}</div>
            </div>
            ${windowHtml}
        `;
    }

    function renderLocationStatus() {
        const enabled = Boolean(attendanceLocationConfig.enabled);
        const label = attendanceLocationConfig.label || 'Lokasi kantor';

        document.getElementById('locationContent').innerHTML = enabled ? `
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Status</div>
                <div class="fw-semibold text-success">Aktif</div>
            </div>
            <div class="data-summary-item mb-3">
                <div class="small text-muted">Titik absensi</div>
                <div class="fw-semibold">${escapeHtml(label)}</div>
            </div>
            <div class="data-summary-item">
                <div class="small text-muted">Radius diizinkan</div>
                <div class="fw-semibold">${escapeHtml(attendanceLocationConfig.radius_meters)} meter</div>
            </div>
        ` : `
            <div class="empty-state py-3">
                <i class="bi bi-geo-alt"></i>
                <div class="fw-semibold mb-1">Belum aktif</div>
                <div class="small">Admin/server belum mengaktifkan pembatas lokasi absensi.</div>
            </div>
        `;
    }

    function stopQrCountdown() {
        if (qrCountdownTimer) {
            clearInterval(qrCountdownTimer);
            qrCountdownTimer = null;
        }
    }

    function startQrCountdown(expiresAt) {
        stopQrCountdown();

        const countdownTarget = document.getElementById('qrCountdown');
        if (!countdownTarget) {
            return;
        }

        const updateCountdown = () => {
            const diff = new Date(expiresAt).getTime() - Date.now();
            if (diff <= 0) {
                countdownTarget.textContent = 'QR sedang diperbarui otomatis...';
                return;
            }

            countdownTarget.textContent = `${Math.floor(diff / 1000)} detik tersisa`;
        };

        updateCountdown();
        qrCountdownTimer = setInterval(updateCountdown, 250);
    }

    function renderActiveQr(context) {
        const shell = document.getElementById('employeeQrShell');

        if (!context.shift) {
            stopQrCountdown();
            shell.innerHTML = `
                <div class="employee-qr-board">
                    <div class="empty-state py-0">
                        <i class="bi bi-person-exclamation"></i>
                        <div class="fw-semibold mb-1">Shift belum diatur</div>
                        <div class="small">QR absensi otomatis akan muncul setelah shift kerja Anda disetel oleh admin.</div>
                    </div>
                </div>
            `;
            return;
        }

        if (context.active_qr) {
            shell.innerHTML = `
                <div class="employee-qr-board">
                    <div>
                        <div class="small text-uppercase fw-bold text-muted" style="letter-spacing:.18em;">${escapeHtml(context.next_action_label)}</div>
                        <h3 class="fw-bold mt-2 mb-2">${escapeHtml(context.action_button_label)}</h3>
                        <div class="text-muted small">QR ini aktif untuk shift ${escapeHtml(context.shift.name)} dan akan diperbarui otomatis selama window masih terbuka.</div>
                    </div>
                    <div class="employee-qr-code">
                        <div id="employeeQrCodeMount"></div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <span class="soft-chip"><i class="bi bi-clock"></i>${escapeHtml(context.window.start_time)} - ${escapeHtml(context.window.end_time)}</span>
                        <span class="soft-chip"><i class="bi bi-arrow-repeat"></i><span id="qrCountdown">Memuat...</span></span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-primary" onclick="submitCurrentAttendance()">
                            <i class="bi bi-check2-circle me-1"></i>${escapeHtml(context.action_button_label)}
                        </button>
                    </div>
                </div>
            `;

            new QRCode(document.getElementById('employeeQrCodeMount'), {
                text: context.active_qr.code,
                width: 280,
                height: 280,
                colorDark: '#4a2f32',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            startQrCountdown(context.active_qr.expires_at);
            return;
        }

        stopQrCountdown();

        const body = context.is_complete
            ? 'Absensi hari ini sudah lengkap. Tidak ada QR aktif lain yang perlu ditampilkan.'
            : context.window
                ? `QR ${escapeHtml(context.next_action_label.toLowerCase())} akan muncul saat window ${escapeHtml(context.window.start_time)} - ${escapeHtml(context.window.end_time)} terbuka.`
                : 'Belum ada QR aktif yang bisa digunakan saat ini.';

        shell.innerHTML = `
            <div class="employee-qr-board">
                <div class="empty-state py-0">
                    <i class="bi bi-qr-code"></i>
                    <div class="fw-semibold mb-1">QR belum aktif</div>
                    <div class="small">${body}</div>
                </div>
            </div>
        `;
    }

    function renderAttendancePage(context) {
        attendanceContext = context;
        renderNotice(context);
        renderStatus(context);
        renderWindow(context);
        renderLocationStatus();
        renderActiveQr(context);
    }

    function loadTodayStatus(showError = false) {
        $.get('/attendance/today-status')
            .done(response => renderAttendancePage(response.context))
            .fail(() => {
                if (showError) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memuat status absensi',
                        text: 'Silakan muat ulang halaman dan coba lagi.'
                    });
                }
            });
    }

    function getCurrentLocationPayload() {
        if (!attendanceLocationConfig.enabled) {
            return Promise.resolve({});
        }

        if (!navigator.geolocation) {
            return Promise.reject(new Error('Browser/perangkat ini tidak mendukung akses lokasi.'));
        }

        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(
                position => resolve({
                    location_latitude: position.coords.latitude,
                    location_longitude: position.coords.longitude,
                    location_accuracy: position.coords.accuracy
                }),
                () => reject(new Error('Izin lokasi ditolak atau posisi perangkat belum bisa dibaca.')),
                {
                    enableHighAccuracy: true,
                    timeout: 12000,
                    maximumAge: 0
                }
            );
        });
    }

    async function processQRCode(code) {
        let locationPayload = {};
        try {
            locationPayload = await getCurrentLocationPayload();
        } catch (error) {
            Swal.fire({
                icon: 'warning',
                title: 'Lokasi diperlukan',
                text: error.message || 'Aktifkan izin lokasi lalu coba lagi.'
            });
            return;
        }

        $.post('/attendance/scan', {
            code,
            ...locationPayload,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        })
            .done(response => {
                const locationText = buildLocationResultHtml(response);
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    html: `<div class="small">Status: <strong>${getStatusText(response.status)}</strong><br>Waktu: <strong>${response.check_in_time || response.check_out_time}</strong>${locationText}</div>`
                }).then(() => loadTodayStatus(true));
            })
            .fail(xhr => {
                if (xhr.status === 419) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesi halaman sudah tidak sinkron',
                        text: 'Halaman absensi sudah terlalu lama terbuka atau sesi login berubah. Muat ulang halaman lalu coba lagi.',
                        confirmButtonText: 'Muat Ulang'
                    }).then(() => window.location.reload());
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Absensi gagal',
                    html: `<div class="small">${escapeHtml(xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses absensi.')}${buildLocationResultHtml(xhr.responseJSON)}</div>`
                });
            });
    }

    function submitCurrentAttendance() {
        const code = attendanceContext.active_qr?.code;
        if (!code) {
            Swal.fire({
                icon: 'info',
                title: 'QR belum aktif',
                text: 'Silakan tunggu sampai window absensi terbuka.'
            });
            return;
        }

        processQRCode(code);
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

    function manualScan(event) {
        event.preventDefault();
        const code = document.getElementById('manualQrCode').value.trim();
        if (code) {
            processQRCode(code);
            document.getElementById('manualQrCode').value = '';
        }
        return false;
    }

    setInterval(updateTime, 1000);

    document.addEventListener('DOMContentLoaded', () => {
        updateTime();
        renderAttendancePage(initialAttendanceContext);
        setInterval(() => loadTodayStatus(false), 5000);
    });

    window.addEventListener('beforeunload', () => {
        stopQrCountdown();
        stopScanner();
    });
</script>
@endpush
