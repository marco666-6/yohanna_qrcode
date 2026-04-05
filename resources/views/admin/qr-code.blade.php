@extends('layouts.app')

@section('title', 'QR Code Shift')
@section('page-kicker', 'QR Attendance Control')
@section('page-title', 'QR Code Shift')
@section('page-subtitle', 'Fokuskan tampilan pada QR yang sedang aktif, dengan status window absensi yang jelas per shift dan tipe scan.')

@section('content')
@php
    $shiftPayload = $shifts->map(function ($shift) {
        return [
            'id' => $shift->id,
            'name' => $shift->name,
            'start_time' => formatTime($shift->start_time),
            'end_time' => formatTime($shift->end_time),
            'check_in' => [
                'start' => getAttendanceWindow($shift, 'check_in')['start']->format('H:i'),
                'end' => getAttendanceWindow($shift, 'check_in')['end']->format('H:i'),
                'is_open' => getAttendanceWindow($shift, 'check_in')['is_open'],
            ],
            'check_out' => [
                'start' => getAttendanceWindow($shift, 'check_out')['start']->format('H:i'),
                'end' => getAttendanceWindow($shift, 'check_out')['end']->format('H:i'),
                'is_open' => getAttendanceWindow($shift, 'check_out')['is_open'],
            ],
        ];
    })->values();
@endphp

<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-qr-code"></i></div>
                <div class="stat-value">{{ config('attendance.qr_code_expiry_seconds') }}</div>
                <div class="stat-label">Durasi QR aktif</div>
                <div class="stat-helper">Detik sebelum QR harus digenerate ulang.</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-box-arrow-in-right"></i></div>
                <div class="stat-value">{{ config('attendance.qr_code_before_minutes') }}</div>
                <div class="stat-label">Window sebelum shift</div>
                <div class="stat-helper">Menit sebelum jam target absensi.</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-box-arrow-right"></i></div>
                <div class="stat-value">{{ config('attendance.qr_code_after_minutes') }}</div>
                <div class="stat-label">Window sesudah shift</div>
                <div class="stat-helper">Menit setelah jam target absensi.</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-diagram-3"></i></div>
                <div class="stat-value">{{ $shifts->count() }}</div>
                <div class="stat-label">Shift aktif</div>
                <div class="stat-helper">Siap dipilih untuk check-in/check-out.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="fw-bold fs-5">QR aktif yang sedang difokuskan</div>
                        <div class="small text-muted">Tampilan besar agar lebih jelas saat ditunjukkan ke karyawan.</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <select class="form-select" id="shiftSelector" style="min-width:180px;">
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }} ({{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }})</option>
                            @endforeach
                        </select>
                        <select class="form-select" id="typeSelector" style="min-width:160px;">
                            <option value="check_in">Check-in</option>
                            <option value="check_out">Check-out</option>
                        </select>
                        <button type="button" class="btn btn-primary" onclick="generateSelectedQR()">Generate / Regenerate</button>
                        <button type="button" class="btn btn-outline-primary" onclick="autoGenerateQR()">Auto Generate</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="qr-focus-shell">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-7">
                                <div id="activeQrPreview" class="active-qr-preview">
                                    <div class="empty-state">
                                        <i class="bi bi-qr-code"></i>
                                        <div class="fw-semibold mb-1">Belum ada QR aktif</div>
                                        <div class="small">Pilih shift dan tipe lalu generate saat window terbuka.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="data-summary d-grid gap-3">
                                    <div class="data-summary-item">
                                        <div class="small text-muted">Shift</div>
                                        <div class="fw-semibold" id="activeShiftName">-</div>
                                    </div>
                                    <div class="data-summary-item">
                                        <div class="small text-muted">Tipe QR</div>
                                        <div class="fw-semibold" id="activeQrType">-</div>
                                    </div>
                                    <div class="data-summary-item">
                                        <div class="small text-muted">Status window</div>
                                        <div class="fw-semibold" id="activeWindowStatus">-</div>
                                    </div>
                                    <div class="data-summary-item">
                                        <div class="small text-muted">Countdown</div>
                                        <div class="fw-semibold" id="activeCountdown">Belum ada QR aktif</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="fw-bold fs-5">Ringkasan window shift</div>
                </div>
                <div class="card-body d-grid gap-3">
                    @foreach($shifts as $shift)
                        @php
                            $checkInWindow = getAttendanceWindow($shift, 'check_in');
                            $checkOutWindow = getAttendanceWindow($shift, 'check_out');
                        @endphp
                        <div class="data-summary-item">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $shift->name }}</div>
                                    <div class="small text-muted">{{ formatTime($shift->start_time) }} - {{ formatTime($shift->end_time) }}</div>
                                </div>
                                <span class="badge rounded-pill {{ $checkInWindow['is_open'] || $checkOutWindow['is_open'] ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                    {{ $checkInWindow['is_open'] || $checkOutWindow['is_open'] ? 'Ada window aktif' : 'Tertutup' }}
                                </span>
                            </div>
                            <div class="small text-muted mt-2">
                                Check-in: {{ $checkInWindow['start']->format('H:i') }} - {{ $checkInWindow['end']->format('H:i') }}<br>
                                Check-out: {{ $checkOutWindow['start']->format('H:i') }} - {{ $checkOutWindow['end']->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .qr-focus-shell {
        padding: 1rem;
        border-radius: 26px;
        background: linear-gradient(180deg, rgba(201,117,112,.07), rgba(255,255,255,.95));
        border: 1px solid rgba(129,101,104,.1);
    }
    .active-qr-preview {
        min-height: 460px;
        border-radius: 28px;
        border: 1px dashed rgba(129,101,104,.2);
        background: linear-gradient(135deg, #fff7f6, #fbf1ef);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        text-align: center;
    }
    .active-qr-preview canvas,
    .active-qr-preview img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    const shiftData = @json($shiftPayload);
    let currentTimer = null;
    let currentActive = null;
    let isGeneratingAutomatically = false;

    function getSelectedShift() {
        const shiftId = Number(document.getElementById('shiftSelector').value);
        return shiftData.find(shift => shift.id === shiftId);
    }

    function getSelectedType() {
        return document.getElementById('typeSelector').value;
    }

    function updateSelectionInfo() {
        const shift = getSelectedShift();
        const type = getSelectedType();
        if (!shift) return;
        const windowData = shift[type];
        document.getElementById('activeShiftName').textContent = `${shift.name} (${shift.start_time} - ${shift.end_time})`;
        document.getElementById('activeQrType').textContent = type === 'check_in' ? 'Check-in' : 'Check-out';
        document.getElementById('activeWindowStatus').textContent = windowData.is_open
            ? `Window terbuka (${windowData.start} - ${windowData.end})`
            : `Window tertutup (${windowData.start} - ${windowData.end})`;
        if (!currentActive || currentActive.shift_id !== shift.id || currentActive.type !== type) {
            document.getElementById('activeCountdown').textContent = 'Belum ada QR aktif untuk pilihan ini';
        }
    }

    function renderActiveQr(payload) {
        currentActive = payload;
        const preview = document.getElementById('activeQrPreview');
        preview.innerHTML = '<div id="qrCanvasMount"></div>';
        new QRCode(document.getElementById('qrCanvasMount'), {
            text: payload.code,
            width: 320,
            height: 320,
            colorDark: '#4a2f32',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        document.getElementById('activeShiftName').textContent = payload.shift_name;
        document.getElementById('activeQrType').textContent = payload.type === 'check_in' ? 'Check-in' : 'Check-out';
        document.getElementById('activeWindowStatus').textContent = `Window ${payload.window_start} - ${payload.window_end}`;
        startCountdown(payload.expires_at);
    }

    function startCountdown(expiresAt) {
        if (currentTimer) {
            clearInterval(currentTimer);
        }
        currentTimer = setInterval(() => {
            const diff = new Date(expiresAt).getTime() - Date.now();
            if (diff <= 0) {
                clearInterval(currentTimer);
                document.getElementById('activeCountdown').textContent = 'QR kadaluarsa, silakan generate ulang';
                return;
            }
            document.getElementById('activeCountdown').textContent = `${Math.floor(diff / 1000)} detik tersisa`;
        }, 200);
    }

    function generateSelectedQR() {
        const shift = getSelectedShift();
        const type = getSelectedType();
        $.post('/qr-code/generate', { shift_id: shift.id, type })
            .done(response => {
                renderActiveQr({
                    shift_id: shift.id,
                    shift_name: `${shift.name} (${shift.start_time} - ${shift.end_time})`,
                    type,
                    code: response.code,
                    expires_at: response.expires_at,
                    window_start: shift[type].start,
                    window_end: shift[type].end
                });
                Swal.fire({ icon: 'success', title: 'QR aktif', text: 'QR berhasil dibuat untuk window yang sedang terbuka.', timer: 1800, showConfirmButton: false });
            })
            .fail(xhr => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Window belum terbuka',
                    text: xhr.responseJSON?.message || 'QR hanya bisa digenerate saat window absensi aktif.'
                });
            });
    }

    function autoGenerateQR() {
        $.get('/qr-code/auto-generate')
            .done(response => {
                if (!response.generated_count) {
                    Swal.fire({ icon: 'info', title: 'Tidak ada window aktif', text: 'Saat ini tidak ada shift yang berada dalam window absensi.' });
                    return;
                }
                const selected = response.codes[0];
                const shift = shiftData.find(item => item.id === selected.shift_id);
                document.getElementById('shiftSelector').value = selected.shift_id;
                document.getElementById('typeSelector').value = selected.type;
                renderActiveQr({
                    shift_id: selected.shift_id,
                    shift_name: `${shift.name} (${shift.start_time} - ${shift.end_time})`,
                    type: selected.type,
                    code: selected.code,
                    expires_at: selected.expires_at,
                    window_start: shift[selected.type].start,
                    window_end: shift[selected.type].end
                });
                updateSelectionInfo();
                Swal.fire({ icon: 'success', title: 'Auto generate berhasil', text: `${response.generated_count} QR dibuat sesuai window aktif.`, timer: 1800, showConfirmButton: false });
            });
    }

    function loadFocusedActiveQr() {
        $.get('/qr-code/active')
            .done(response => {
                const shift = getSelectedShift();
                const type = getSelectedType();
                const active = response.qr_codes.find(qr => Number(qr.shift_id) === shift.id && qr.type === type);
                if (active) {
                    isGeneratingAutomatically = false;
                    renderActiveQr({
                        shift_id: active.shift_id,
                        shift_name: `${shift.name} (${shift.start_time} - ${shift.end_time})`,
                        type: active.type,
                        code: active.code,
                        expires_at: active.expires_at,
                        window_start: shift[active.type].start,
                        window_end: shift[active.type].end
                    });
                } else {
                    currentActive = null;
                    document.getElementById('activeQrPreview').innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-qr-code"></i>
                            <div class="fw-semibold mb-1">Belum ada QR aktif</div>
                            <div class="small">Generate QR untuk shift dan tipe yang dipilih saat window terbuka.</div>
                        </div>`;
                    updateSelectionInfo();

                    if (shift[type].is_open && !isGeneratingAutomatically) {
                        isGeneratingAutomatically = true;
                        $.get('/qr-code/auto-generate').done(autoResponse => {
                            if (autoResponse.generated_count) {
                                loadFocusedActiveQr();
                            } else {
                                isGeneratingAutomatically = false;
                            }
                        }).fail(() => {
                            isGeneratingAutomatically = false;
                        });
                    }
                }
            });
    }

    document.getElementById('shiftSelector').addEventListener('change', () => {
        updateSelectionInfo();
        loadFocusedActiveQr();
    });
    document.getElementById('typeSelector').addEventListener('change', () => {
        updateSelectionInfo();
        loadFocusedActiveQr();
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateSelectionInfo();
        loadFocusedActiveQr();
        setInterval(loadFocusedActiveQr, 5000);
    });
</script>
@endpush
