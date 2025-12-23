<?php
// App/Exports/EmployeesExport.php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::with('shift')
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Karyawan',
            'Nama',
            'Email',
            'Telepon',
            'Departemen',
            'Posisi',
            'Shift',
            'Status',
            'Terdaftar Sejak',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->employee_id,
            $employee->name,
            $employee->email,
            $employee->phone ?? '-',
            $employee->department ?? '-',
            $employee->position ?? '-',
            $employee->shift->name ?? '-',
            $employee->is_active ? 'Aktif' : 'Tidak Aktif',
            formatDate($employee->created_at),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}