<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'name' => 'Administrator',
                'email' => 'admin@arunglaut.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'employee_id' => 'ADM001',
                'phone' => '081234567890',
                'department' => 'IT',
                'position' => 'System Administrator',
                'shift_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // HRD
            [
                'name' => 'HR Manager',
                'email' => 'hrd@arunglaut.com',
                'password' => Hash::make('hrd123'),
                'role' => 'hrd',
                'employee_id' => 'HRD001',
                'phone' => '081234567891',
                'department' => 'Human Resources',
                'position' => 'HR Manager',
                'shift_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Employees
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@arunglaut.com',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
                'employee_id' => 'EMP001',
                'phone' => '081234567892',
                'department' => 'Operations',
                'position' => 'Staff',
                'shift_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@arunglaut.com',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
                'employee_id' => 'EMP002',
                'phone' => '081234567893',
                'department' => 'Operations',
                'position' => 'Staff',
                'shift_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@arunglaut.com',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
                'employee_id' => 'EMP003',
                'phone' => '081234567894',
                'department' => 'Operations',
                'position' => 'Staff',
                'shift_id' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}