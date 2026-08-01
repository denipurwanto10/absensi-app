<?php

namespace App\Models;

use App\Models\Concerns\HasStatusTimeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemoteAttendance extends Model
{
    use HasFactory, HasStatusTimeline;

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'date',
        'punch_type',
        'category',
        'time',
        'lat',
        'lng',
        'photo',
        'note',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }

    public function getPunchTypeLabelAttribute(): string
    {
        return $this->punch_type === 'pulang' ? 'Absen Pulang' : 'Absen Masuk';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'dinas' => 'Dinas Luar',
            'lapangan' => 'Kerja Lapangan',
            'wfh' => 'WFH',
            default => ucfirst($this->category),
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'dinas' => 'bi-briefcase',
            'lapangan' => 'bi-signpost-split',
            'wfh' => 'bi-house-door',
            default => 'bi-geo-alt',
        };
    }

}
