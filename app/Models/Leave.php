<?php

namespace App\Models;

use App\Models\Concerns\HasStatusTimeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory, HasStatusTimeline;

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment',
        'submit_lat',
        'submit_lng',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            default => ucfirst($this->type),
        };
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? asset('storage/'.$this->attachment) : null;
    }

    /**
     * True kalau titik lokasi saat pengajuan berhasil ditangkap (karyawan
     * mengizinkan akses lokasi browser). Dipakai admin view untuk menampilkan
     * tombol "Lihat Lokasi" hanya jika datanya benar-benar ada.
     */
    public function getHasLocationAttribute(): bool
    {
        return $this->submit_lat !== null && $this->submit_lng !== null;
    }
}
