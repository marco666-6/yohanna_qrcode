<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShiftSeeder::class,
            UserSeeder::class,
        ]);

        $faker = Faker::create('id_ID');

        // ===============================
        // USERS
        // ===============================
        $users = DB::table('users')->get();
        $employees = $users->where('role', 'employee');
        $hrd = $users->where('role', 'hrd')->first();
        $admin = $users->where('role', 'admin')->first();

        // ===============================
        // QR CODES
        // ===============================
        $qrCodes = [];

        foreach (['check_in', 'check_out'] as $type) {
            for ($i = 1; $i <= 10; $i++) {
                $qrCodes[] = [
                    'code' => strtoupper($faker->uuid),
                    'type' => $type,
                    'shift_id' => rand(1, 3),
                    'generated_at' => now()->subMinutes(rand(1, 30)),
                    'expires_at' => now()->addMinutes(30),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('qr_codes')->insert($qrCodes);
        $qrIn = DB::table('qr_codes')->where('type', 'check_in')->pluck('id');
        $qrOut = DB::table('qr_codes')->where('type', 'check_out')->pluck('id');

        // ===============================
        // ATTENDANCES (LAST 7 DAYS)
        // ===============================
        $attendances = [];

        foreach ($employees as $employee) {
            for ($d = 0; $d < 7; $d++) {
                $date = Carbon::now()->subDays($d)->toDateString();
                $status = $faker->randomElement(['on_time', 'late', 'incomplete', 'absent']);

                $checkIn = null;
                $checkOut = null;
                $totalHours = 0;

                if ($status !== 'absent') {
                    $checkIn = Carbon::createFromTime(8, rand(0, 30), 0);
                }

                if (in_array($status, ['on_time', 'late'])) {
                    $checkOut = Carbon::createFromTime(16, rand(0, 30), 0);
                    $totalHours = $checkOut->diffInMinutes($checkIn) / 60;
                }

                $attendances[] = [
                    'user_id' => $employee->id,
                    'shift_id' => $employee->shift_id,
                    'date' => $date,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'check_in_qr_id' => $checkIn ? $qrIn->random() : null,
                    'check_out_qr_id' => $checkOut ? $qrOut->random() : null,
                    'status' => $status,
                    'total_hours' => $totalHours,
                    'notes' => $status === 'late' ? 'Terlambat karena macet' : null,
                    'created_by' => $status === 'absent' ? $admin->id : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('attendances')->insert($attendances);

        // ===============================
        // LEAVE REQUESTS
        // ===============================
        $leaveRequests = [];

        foreach ($employees->take(8) as $employee) {
            $start = Carbon::now()->addDays(rand(1, 5));
            $end = (clone $start)->addDays(rand(1, 3));

            $status = $faker->randomElement(['pending', 'approved', 'rejected']);

            $leaveRequests[] = [
                'user_id' => $employee->id,
                'leave_type' => $faker->randomElement(['sick', 'annual', 'unpaid']),
                'start_date' => $start,
                'end_date' => $end,
                'reason' => $faker->sentence,
                'status' => $status,
                'reviewed_by' => $status !== 'pending' ? $hrd->id : null,
                'reviewed_at' => $status !== 'pending' ? now() : null,
                'review_notes' => $status === 'rejected' ? 'Tidak sesuai kebijakan' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('leave_requests')->insert($leaveRequests);

        // ===============================
        // NOTIFICATIONS
        // ===============================
        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = [
                'user_id' => $user->id,
                'type' => 'attendance',
                'title' => 'Absensi Berhasil',
                'message' => 'Absensi Anda telah tercatat di sistem.',
                'is_read' => rand(0, 1),
                'read_at' => rand(0, 1) ? now() : null,
                'created_at' => now(),
            ];
        }

        DB::table('notifications')->insert($notifications);

        // ===============================
        // ACTIVITY LOGS
        // ===============================
        $activityLogs = [];

        foreach ($users as $user) {
            $activityLogs[] = [
                'user_id' => $user->id,
                'action' => 'LOGIN',
                'description' => 'User login ke sistem',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
                'created_at' => now(),
            ];
        }

        DB::table('activity_logs')->insert($activityLogs);
    }
}
