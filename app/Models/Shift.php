<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_tolerance',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isOvernight()
    {
        return Carbon::parse($this->end_time)->lte(Carbon::parse($this->start_time));
    }

    public function durationHours()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($end->lte($start)) {
            $end->addDay();
        }

        return round($end->diffInMinutes($start) / 60, 2);
    }
}
