<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengajuan Cuti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        
        .email-header.approved {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        }
        
        .email-header.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .email-body {
            padding: 30px 20px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        
        .notification-box {
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .notification-box.success {
            background-color: #d1e7dd;
            border-left: 4px solid #198754;
        }
        
        .notification-box.danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        .notification-box p {
            margin: 0;
            font-size: 16px;
        }
        
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #f8f9fa;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-table tr:last-child td {
            border-bottom: none;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        
        .info-table td:last-child {
            color: #333;
        }
        
        .message {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
            line-height: 1.8;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #198754;
            color: #ffffff;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: #ffffff;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-info {
            background-color: #0dcaf0;
            color: #000;
        }
        
        .review-notes-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .review-notes-box h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        
        .review-notes-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        
        .divider {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin: 20px 0;
        }
        
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .button:hover {
            background-color: #0a58ca;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .footer a {
            color: #0d6efd;
            text-decoration: none;
        }
        
        .info-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #004085;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header {{ $status }}">
            @if($status === 'approved')
                <div class="icon">✓</div>
                <h1>Pengajuan Cuti Disetujui</h1>
                <p>Cuti Anda telah disetujui oleh HRD</p>
            @else
                <div class="icon">✗</div>
                <h1>Pengajuan Cuti Ditolak</h1>
                <p>Cuti Anda tidak dapat disetujui</p>
            @endif
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Halo, {{ $leaveRequest->user->name }}!
            </div>

            @if($status === 'approved')
                <!-- Approved Message -->
                <div class="notification-box success">
                    <p><strong>🎉 Selamat!</strong> Pengajuan cuti Anda telah disetujui.</p>
                </div>

                <div class="message">
                    Pengajuan cuti Anda telah ditinjau dan disetujui oleh HRD. Anda dapat mengambil cuti sesuai dengan periode yang diajukan.
                </div>
            @else
                <!-- Rejected Message -->
                <div class="notification-box danger">
                    <p><strong>❌ Mohon Maaf</strong> Pengajuan cuti Anda tidak dapat disetujui.</p>
                </div>

                <div class="message">
                    Setelah meninjau pengajuan Anda, HRD memutuskan untuk tidak menyetujui cuti yang Anda ajukan. Silakan baca alasan penolakan di bawah ini.
                </div>
            @endif

            <!-- Leave Request Details -->
            <table class="info-table">
                <tr>
                    <td>Jenis Cuti:</td>
                    <td>
                        @php
                            $leaveTypeText = match($leaveRequest->leave_type) {
                                'sick' => 'Cuti Sakit',
                                'annual' => 'Cuti Tahunan',
                                'unpaid' => 'Cuti Tanpa Bayaran',
                                'other' => 'Lainnya',
                                default => 'Tidak Diketahui',
                            };
                            $badgeClass = match($leaveRequest->leave_type) {
                                'sick' => 'badge-warning',
                                'annual' => 'badge-info',
                                'unpaid' => 'badge-danger',
                                'other' => 'badge-info',
                                default => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $leaveTypeText }}</span>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Mulai:</td>
                    <td><strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Selesai:</td>
                    <td><strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td>Durasi:</td>
                    <td><strong>{{ $leaveRequest->total_days }} hari</strong></td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td>
                        <span class="badge {{ $status === 'approved' ? 'badge-success' : 'badge-danger' }}">
                            {{ $status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Ditinjau Oleh:</td>
                    <td>{{ $leaveRequest->reviewer->name ?? 'HRD' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Review:</td>
                    <td>{{ $leaveRequest->reviewed_at ? \Carbon\Carbon::parse($leaveRequest->reviewed_at)->format('d F Y, H:i') : '-' }}</td>
                </tr>
            </table>

            <!-- Review Notes -->
            @if($leaveRequest->review_notes)
                <div class="review-notes-box">
                    <h4>{{ $status === 'approved' ? '📝 Catatan dari HRD:' : '❗ Alasan Penolakan:' }}</h4>
                    <p>{{ $leaveRequest->review_notes }}</p>
                </div>
            @endif

            <hr class="divider">

            @if($status === 'approved')
                <!-- Approved Info -->
                <div class="info-box">
                    <p><strong>ℹ️ Informasi Penting:</strong></p>
                    <p>• Pastikan Anda telah menyelesaikan pekerjaan atau mendelegasikan tugas Anda</p>
                    <p>• Hubungi atasan langsung Anda jika ada hal yang perlu dikoordinasikan</p>
                    <p>• Jangan lupa untuk kembali bekerja sesuai jadwal setelah cuti berakhir</p>
                    <p>• Selamat menikmati masa cuti Anda!</p>
                </div>
            @else
                <!-- Rejected Info -->
                <div class="info-box">
                    <p><strong>ℹ️ Langkah Selanjutnya:</strong></p>
                    <p>• Anda dapat mengajukan cuti kembali dengan periode yang berbeda</p>
                    <p>• Hubungi HRD jika Anda memerlukan penjelasan lebih lanjut</p>
                    <p>• Pastikan alasan cuti Anda jelas dan sesuai dengan kebijakan perusahaan</p>
                </div>
            @endif

            <!-- CTA Button -->
            <div style="text-align: center; margin-top: 30px;">
                <p>Lihat detail lengkap pengajuan cuti Anda:</p>
                <a href="{{ config('app.url') }}/employee/leave-requests/{{ $leaveRequest->id }}" class="button">
                    Lihat Detail Pengajuan
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>PT Arung Laut Nusantara</strong></p>
            <p>Sistem Absensi QR Code</p>
            <p style="margin-top: 15px;">
                Email ini dikirim secara otomatis oleh sistem.<br>
                Mohon tidak membalas email ini.
            </p>
            <p style="margin-top: 10px;">
                Jika Anda memiliki pertanyaan, silakan hubungi HRD di 
                <a href="mailto:hrd@arunglautnusantara.com">hrd@arunglautnusantara.com</a>
            </p>
            <p style="margin-top: 15px; color: #999; font-size: 11px;">
                © {{ date('Y') }} PT Arung Laut Nusantara. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>