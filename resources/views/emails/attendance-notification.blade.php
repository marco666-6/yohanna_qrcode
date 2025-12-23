<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Absensi</title>
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
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
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
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .notification-box.success {
            border-left-color: #198754;
            background-color: #d1e7dd;
        }
        
        .notification-box.warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }
        
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        
        .info-table td:last-child {
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
        }
        
        .status-badge.on-time {
            background-color: #198754;
        }
        
        .status-badge.late {
            background-color: #ffc107;
            color: #000;
        }
        
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .message {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
            line-height: 1.8;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            color: #856404;
        }
        
        .divider {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin: 20px 0;
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
        
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        
        .button:hover {
            background-color: #0a58ca;
        }
        
        .reminder-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .reminder-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #004085;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            @if($type === 'check_in')
                <div class="icon">✓</div>
                <h1>Check-in Berhasil!</h1>
                <p>Notifikasi Absensi Masuk</p>
            @else
                <div class="icon">→</div>
                <h1>Check-out Berhasil!</h1>
                <p>Notifikasi Absensi Keluar</p>
            @endif
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Halo, {{ $userName }}!
            </div>

            @if($type === 'check_in')
                <!-- Check-in Message -->
                <div class="notification-box {{ $status === 'on_time' ? 'success' : 'warning' }}">
                    <p style="margin: 0; font-size: 16px; font-weight: bold;">
                        @if($status === 'on_time')
                            🎉 Anda telah melakukan check-in tepat waktu!
                        @else
                            ⚠️ Anda telah melakukan check-in terlambat.
                        @endif
                    </p>
                </div>

                <div class="message">
                    @if($status === 'on_time')
                        Terima kasih telah hadir tepat waktu. Semangat bekerja hari ini!
                    @else
                        Anda melakukan check-in melewati batas waktu yang ditentukan. Harap lebih memperhatikan waktu kehadiran Anda di masa mendatang.
                    @endif
                </div>

                <!-- Check-in Details -->
                <table class="info-table">
                    <tr>
                        <td>Tanggal:</td>
                        <td><strong>{{ $date }}</strong></td>
                    </tr>
                    <tr>
                        <td>Waktu Check-in:</td>
                        <td><strong class="highlight">{{ $time }}</strong></td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <span class="status-badge {{ $status === 'on_time' ? 'on-time' : 'late' }}">
                                @if($status === 'on_time')
                                    Tepat Waktu
                                @else
                                    Terlambat
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>

                <div class="reminder-box">
                    <p><strong>📝 Pengingat:</strong></p>
                    <p>• Jangan lupa untuk check-out saat pulang nanti</p>
                    <p>• Pastikan Anda bekerja sesuai dengan jam kerja yang ditentukan</p>
                    <p>• Semoga hari Anda produktif!</p>
                </div>

            @else
                <!-- Check-out Message -->
                <div class="notification-box success">
                    <p style="margin: 0; font-size: 16px; font-weight: bold;">
                        ✅ Anda telah menyelesaikan absensi hari ini!
                    </p>
                </div>

                <div class="message">
                    Terima kasih atas kerja keras Anda hari ini. Absensi Anda telah tercatat dengan lengkap.
                </div>

                <!-- Check-out Details -->
                <table class="info-table">
                    <tr>
                        <td>Tanggal:</td>
                        <td><strong>{{ $date }}</strong></td>
                    </tr>
                    <tr>
                        <td>Waktu Check-out:</td>
                        <td><strong class="highlight">{{ $time }}</strong></td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <span class="status-badge {{ $status === 'on_time' ? 'on-time' : 'late' }}">
                                @if($status === 'on_time')
                                    Tepat Waktu
                                @else
                                    Terlambat
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>

                <div class="reminder-box">
                    <p><strong>🏠 Pesan Penutup:</strong></p>
                    <p>• Selamat beristirahat!</p>
                    <p>• Jangan lupa check-in kembali besok</p>
                    <p>• Sampai jumpa besok!</p>
                </div>
            @endif

            <hr class="divider">

            <!-- CTA Button -->
            <div style="text-align: center;">
                <p>Anda dapat melihat riwayat kehadiran lengkap Anda di:</p>
                <a href="{{ config('app.url') }}/employee/attendance-history" class="button">
                    Lihat Riwayat Absensi
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