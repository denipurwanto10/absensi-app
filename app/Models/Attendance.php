<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'status',
        'leave_id',
        'source',
        'remote_category',
        'check_in_photo',
        'check_out_photo',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function remoteAttendances()
    {
        return $this->hasMany(RemoteAttendance::class);
    }

    public function getIsRemoteAttribute(): bool
    {
        return $this->source === 'remote';
    }

    public function getRemoteCategoryLabelAttribute(): ?string
    {
        return match ($this->remote_category) {
            'dinas' => 'Dinas Luar',
            'lapangan' => 'Kerja Lapangan',
            'wfh' => 'WFH',
            default => null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'telat' => 'Telat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            'alpha' => 'Alpha',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'telat' => 'warning',
            'izin', 'sakit', 'cuti' => 'info',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }
}
