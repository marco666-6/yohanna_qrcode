<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;

class EmployeeAttendanceService
{
    public function __construct(
        protected QrCodeService $qrCodeService
    ) {
    }

    public function buildContext(User $user): array
    {
        $user->loadMissing('shift');

        $shift = $user->shift;
        $attendance = $this->resolveCurrentAttendance($user);
        $nextAction = $this->resolveNextAction($attendance);
        $window = $shift && $nextAction ? getAttendanceWindow($shift, $nextAction) : null;
        $activeQr = $shift && $nextAction && $window['is_open']
            ? $this->qrCodeService->ensureActiveCode($shift, $nextAction)
            : null;

        return [
            'shift' => $shift,
            'attendance' => $attendance,
            'next_action' => $nextAction,
            'next_action_label' => $this->getActionLabel($nextAction),
            'action_button_label' => $nextAction === 'check_out' ? 'Check-out sekarang' : 'Check-in sekarang',
            'window' => $window,
            'active_qr' => $activeQr,
            'notice' => $this->buildNotice($shift, $attendance, $nextAction, $window),
            'is_complete' => (bool) ($attendance?->check_in && $attendance?->check_out),
            'can_take_attendance' => (bool) $activeQr && !$attendance?->check_out,
        ];
    }

    public function serializeContext(array $context): array
    {
        $shift = $context['shift'];
        $attendance = $context['attendance'];
        $window = $context['window'];
        $activeQr = $context['active_qr'];

        return [
            'shift' => $shift ? [
                'id' => $shift->id,
                'name' => $shift->name,
                'start_time' => formatTime($shift->start_time),
                'end_time' => formatTime($shift->end_time),
                'late_tolerance' => $shift->late_tolerance,
            ] : null,
            'attendance' => $attendance ? [
                'id' => $attendance->id,
                'date' => $attendance->date?->format('Y-m-d'),
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'status' => $attendance->status,
                'status_text' => getStatusText($attendance->status),
                'total_hours' => $attendance->total_hours,
                'shift' => $attendance->shift ? [
                    'name' => $attendance->shift->name,
                ] : ($shift ? ['name' => $shift->name] : null),
            ] : null,
            'next_action' => $context['next_action'],
            'next_action_label' => $context['next_action_label'],
            'action_button_label' => $context['action_button_label'],
            'window' => $window ? [
                'is_open' => $window['is_open'],
                'target_time' => $window['target']->format('H:i'),
                'start_time' => $window['start']->format('H:i'),
                'end_time' => $window['end']->format('H:i'),
                'start_at' => $window['start']->toIso8601String(),
                'end_at' => $window['end']->toIso8601String(),
            ] : null,
            'active_qr' => $activeQr ? [
                'code' => $activeQr->code,
                'type' => $activeQr->type,
                'expires_at' => $activeQr->expires_at->toIso8601String(),
            ] : null,
            'notice' => $context['notice'],
            'is_complete' => $context['is_complete'],
            'can_take_attendance' => $context['can_take_attendance'],
        ];
    }

    protected function resolveCurrentAttendance(User $user): ?Attendance
    {
        $todayAttendance = Attendance::query()
            ->where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->with('shift')
            ->first();

        if ($todayAttendance) {
            return $todayAttendance;
        }

        return Attendance::openForCheckout($user->id, $user->shift_id)
            ->with('shift')
            ->first();
    }

    protected function resolveNextAction(?Attendance $attendance): ?string
    {
        if (!$attendance || !$attendance->check_in) {
            return 'check_in';
        }

        if (!$attendance->check_out) {
            return 'check_out';
        }

        return null;
    }

    protected function buildNotice($shift, ?Attendance $attendance, ?string $nextAction, ?array $window): array
    {
        if (!$shift) {
            return [
                'variant' => 'warning',
                'title' => 'Shift Anda belum diatur',
                'message' => 'QR absensi otomatis akan muncul setelah admin menetapkan shift kerja Anda.',
            ];
        }

        if ($attendance?->check_in && $attendance?->check_out) {
            return [
                'variant' => 'success',
                'title' => 'Absensi hari ini sudah lengkap',
                'message' => 'Anda sudah check-in pukul ' . formatTime($attendance->check_in) . ' dan check-out pukul ' . formatTime($attendance->check_out) . '.',
            ];
        }

        if (!$nextAction || !$window) {
            return [
                'variant' => 'secondary',
                'title' => 'Status absensi sedang disiapkan',
                'message' => 'Silakan muat ulang halaman beberapa saat lagi.',
            ];
        }

        $label = $this->getActionLabel($nextAction);

        if ($window['is_open']) {
            return [
                'variant' => $nextAction === 'check_in' ? 'warning' : 'info',
                'title' => 'Saatnya ' . strtolower($label),
                'message' => 'Window ' . strtolower($label) . ' untuk shift ' . $shift->name . ' sedang terbuka sampai pukul ' . $window['end']->format('H:i') . '.',
            ];
        }

        if (now()->lt($window['start'])) {
            return [
                'variant' => 'info',
                'title' => 'Jam ' . strtolower($label) . ' belum dibuka',
                'message' => ucfirst($label) . ' untuk shift ' . $shift->name . ' dimulai pukul ' . $window['start']->format('H:i') . ' dan mengikuti jam shift ' . $window['target']->format('H:i') . '.',
            ];
        }

        return [
            'variant' => 'danger',
            'title' => ucfirst($label) . ' belum terpenuhi',
            'message' => 'Jam ' . strtolower($label) . ' untuk shift ' . $shift->name . ' sudah lewat pada pukul ' . $window['end']->format('H:i') . '. Segera selesaikan absensi atau hubungi admin/HRD bila perlu.',
        ];
    }

    protected function getActionLabel(?string $action): string
    {
        return match ($action) {
            'check_out' => 'Check-out',
            default => 'Check-in',
        };
    }
}
