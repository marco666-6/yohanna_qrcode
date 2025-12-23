<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran - PT Arung Laut Nusantara</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0d6efd;
        }
        
        .header h1 {
            font-size: 18pt;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            color: #666;
            font-weight: normal;
            margin-bottom: 10px;
        }
        
        .header .period {
            font-size: 11pt;
            color: #333;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .statistics {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .stats-header {
            background-color: #0d6efd;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 11pt;
        }
        
        .stats-body {
            display: table;
            width: 100%;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stats-cell {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }
        
        .stats-cell:last-child {
            border-right: none;
        }
        
        .stats-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stats-value {
            font-size: 20pt;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .stats-percentage {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table thead {
            background-color: #0d6efd;
            color: white;
        }
        
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        
        table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table td {
            padding: 6px 5px;
            font-size: 9pt;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            color: white;
        }
        
        .badge-success {
            background-color: #198754;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-info {
            background-color: #0dcaf0;
            color: #000;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
        
        .badge-secondary {
            background-color: #6c757d;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PT ARUNG LAUT NUSANTARA</h1>
        <h2>Laporan Kehadiran Karyawan</h2>
        <div class="period">
            Periode: {{ formatDate($startDate) }} - {{ formatDate($endDate) }}
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span>{{ formatDateTime(now()) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak Oleh:</span>
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Record:</span>
            <span>{{ $stats['total'] }} data kehadiran</span>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="statistics">
        <div class="stats-header">
            RINGKASAN STATISTIK KEHADIRAN
        </div>
        <div class="stats-body">
            <div class="stats-row">
                <div class="stats-cell">
                    <div class="stats-label">Total Kehadiran</div>
                    <div class="stats-value" style="color: #0d6efd;">{{ $stats['total'] }}</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-label">Tepat Waktu</div>
                    <div class="stats-value" style="color: #198754;">{{ $stats['on_time'] }}</div>
                    <div class="stats-percentage">
                        @if($stats['total'] > 0)
                            {{ number_format(($stats['on_time'] / $stats['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
                <div class="stats-cell">
                    <div class="stats-label">Terlambat</div>
                    <div class="stats-value" style="color: #ffc107;">{{ $stats['late'] }}</div>
                    <div class="stats-percentage">
                        @if($stats['total'] > 0)
                            {{ number_format(($stats['late'] / $stats['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
                <div class="stats-cell">
                    <div class="stats-label">Belum Check-out</div>
                    <div class="stats-value" style="color: #0dcaf0;">{{ $stats['incomplete'] }}</div>
                    <div class="stats-percentage">
                        @if($stats['total'] > 0)
                            {{ number_format(($stats['incomplete'] / $stats['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    @if($attendances->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="18%">Nama Karyawan</th>
                    <th width="10%">ID</th>
                    <th width="10%">Shift</th>
                    <th width="10%">Check-in</th>
                    <th width="10%">Check-out</th>
                    <th width="10%">Total Jam</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                        <br>
                        <small style="color: #666;">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                    </td>
                    <td>
                        <strong>{{ $attendance->user->name }}</strong>
                        @if($attendance->user->department)
                            <br><small style="color: #666;">{{ $attendance->user->department }}</small>
                        @endif
                    </td>
                    <td>{{ $attendance->user->employee_id }}</td>
                    <td>
                        @if($attendance->shift)
                            <span class="badge badge-secondary">{{ $attendance->shift->name }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($attendance->check_in)
                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($attendance->check_out)
                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($attendance->total_hours > 0)
                            {{ number_format($attendance->total_hours, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($attendance->status) {
                                'on_time' => 'badge-success',
                                'late' => 'badge-warning',
                                'incomplete' => 'badge-info',
                                'absent' => 'badge-danger',
                                default => 'badge-secondary',
                            };
                            $statusText = match($attendance->status) {
                                'on_time' => 'Tepat Waktu',
                                'late' => 'Terlambat',
                                'incomplete' => 'Belum Check-out',
                                'absent' => 'Tidak Hadir',
                                default => 'Tidak Diketahui',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        @if($attendance->notes)
                            <br><small style="color: #666; font-style: italic;">{{ Str::limit($attendance->notes, 30) }}</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary at the end -->
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
            <h3 style="font-size: 11pt; margin-bottom: 10px; color: #0d6efd;">Ringkasan Laporan</h3>
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <p><strong>Total Jam Kerja:</strong> {{ number_format($attendances->sum('total_hours'), 2) }} jam</p>
                    <p><strong>Rata-rata per Record:</strong> 
                        @if($stats['total'] > 0)
                            {{ number_format($attendances->sum('total_hours') / $stats['total'], 2) }} jam
                        @else
                            0 jam
                        @endif
                    </p>
                </div>
                <div>
                    <p><strong>Tingkat Kehadiran Tepat Waktu:</strong> 
                        @if($stats['total'] > 0)
                            {{ number_format(($stats['on_time'] / $stats['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                    <p><strong>Tingkat Keterlambatan:</strong> 
                        @if($stats['total'] > 0)
                            {{ number_format(($stats['late'] / $stats['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            Tidak ada data kehadiran untuk periode yang dipilih.
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="margin-top: 5px; font-weight: bold;">HRD Manager</div>
            <div class="signature-line">
                (___________________)
            </div>
        </div>
        <div class="signature-box">
            <div>Batam, {{ formatDate(now()) }}</div>
            <div style="margin-top: 5px; font-weight: bold;">Direktur</div>
            <div class="signature-line">
                (___________________)
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>PT Arung Laut Nusantara - Sistem Absensi QR Code</p>
        <p>Dokumen ini dibuat secara otomatis oleh sistem dan tidak memerlukan tanda tangan basah</p>
        <p style="margin-top: 5px;">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>