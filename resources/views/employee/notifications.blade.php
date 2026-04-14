@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-kicker', 'Notification Center')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Lihat semua kabar penting terkait absensi, cuti, dan catatan reviewer dalam tampilan yang lebih rapi.')

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-bell"></i></div>
                <div class="stat-value">{{ $notifications->total() }}</div>
                <div class="stat-label">Total Notifikasi</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-envelope-open"></i></div>
                <div class="stat-value">{{ $notifications->where('is_read', true)->count() }}</div>
                <div class="stat-label">Sudah Dibaca</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-stars"></i></div>
                <div class="stat-value">{{ $notifications->where('is_read', false)->count() }}</div>
                <div class="stat-label">Masih Baru</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="fw-bold fs-5">Daftar notifikasi</div>
            <div class="small text-muted">Halaman ini otomatis menandai notifikasi Anda sebagai sudah dibaca.</div>
        </div>
        <div class="card-body p-0">
            @if($notifications->count())
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        @php
                            $iconClass = match($notification->type) {
                                'check_in' => 'bi-box-arrow-in-right text-success',
                                'check_out' => 'bi-box-arrow-right text-danger',
                                'leave_approved' => 'bi-check-circle text-success',
                                'leave_rejected' => 'bi-x-circle text-danger',
                                'leave_request' => 'bi-calendar-event text-warning',
                                'attendance_note' => 'bi-chat-left-text text-info',
                                default => 'bi-info-circle text-primary',
                            };
                        @endphp
                        <div class="notification-row {{ !$notification->is_read ? 'is-fresh' : '' }}">
                            <div class="notification-icon"><i class="bi {{ $iconClass }}"></i></div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $notification->title }}</div>
                                        <div class="text-muted">{{ $notification->message }}</div>
                                    </div>
                                    <div class="text-lg-end">
                                        <span class="badge {{ $notification->is_read ? 'badge-soft-info' : 'badge-soft-primary' }}">
                                            {{ $notification->is_read ? 'Dibaca' : 'Baru' }}
                                        </span>
                                        <div class="small text-muted mt-2">{{ formatDateTime($notification->created_at) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-bell-slash"></i>
                    <div class="fw-semibold mb-1">Belum ada notifikasi</div>
                    <div class="small">Notifikasi attendance, cuti, dan catatan akan muncul di sini.</div>
                </div>
            @endif
        </div>
        @if($notifications->hasPages())
            <div class="card-body border-top">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .notification-row {
        display: flex;
        gap: 1rem;
        padding: 1.2rem 1.3rem;
        border-bottom: 1px solid rgba(129, 101, 104, 0.08);
        transition: background .2s ease, transform .2s ease;
    }

    .notification-row:hover {
        background: rgba(201, 117, 112, 0.04);
    }

    .notification-row.is-fresh {
        background: rgba(201, 117, 112, 0.06);
        border-left: 4px solid var(--primary);
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: rgba(201, 117, 112, 0.09);
        font-size: 1.2rem;
        flex-shrink: 0;
    }
</style>
@endpush
