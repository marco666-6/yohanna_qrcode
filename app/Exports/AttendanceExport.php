<?php
// App/Exports/AttendanceExport.php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Attendance::with(['user', 'shift'])
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'ID Karyawan',
            'Nama',
            'Shift',
            'Check-in',
            'Check-out',
            'Total Jam',
            'Status',
            'Catatan',
        ];
    }

    public function map($attendance): array
    {
        return [
            formatDate($attendance->date),
            $attendance->user->employee_id,
            $attendance->user->name,
            $attendance->shift->name ?? '-',
            formatTime($attendance->check_in),
            formatTime($attendance->check_out),
            $attendance->total_hours ? number_format($attendance->total_hours, 2) . ' jam' : '-',
            getStatusText($attendance->status),
            $attendance->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}