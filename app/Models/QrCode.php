<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'shift_id',
        'generated_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function checkInAttendances()
    {
        return $this->hasMany(Attendance::class, 'check_in_qr_id');
    }

    public function checkOutAttendances()
    {
        return $this->hasMany(Attendance::class, 'check_out_qr_id');
    }

    public function isExpired()
    {
        return now()->gt($this->expires_at);
    }

    public function isValid()
    {
        return $this->is_active && !$this->isExpired();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('expires_at', '>', now());
    }
}