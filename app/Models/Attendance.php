<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'check_in',
        'check_out',
        'check_in_qr_id',
        'check_out_qr_id',
        'status',
        'total_hours',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function checkInQr()
    {
        return $this->belongsTo(QrCode::class, 'check_in_qr_id');
    }

    public function checkOutQr()
    {
        return $this->belongsTo(QrCode::class, 'check_out_qr_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculateTotalHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            return $checkOut->diffInMinutes($checkIn) / 60;
        }
        return 0;
    }

    public function updateStatus()
    {
        if (!$this->check_in) {
            $this->status = 'absent';
        } elseif (!$this->check_out) {
            $this->status = 'incomplete';
        } else {
            $checkInTime = Carbon::parse($this->check_in);
            $shiftStart = Carbon::parse($this->shift->start_time);
            $tolerance = $this->shift->late_tolerance;

            if ($checkInTime->lte($shiftStart->addMinutes($tolerance))) {
                $this->status = 'on_time';
            } else {
                $this->status = 'late';
            }
        }
        $this->save();
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}