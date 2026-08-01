<?php

namespace App\Models;

use App\Models\Concerns\HasStatusTimeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory, HasStatusTimeline;

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'date',
        'requested_check_in',
        'requested_check_out',
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
        'date' => 'date',
        'reviewed_at' => 'datetime',
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

    /**
     * Ringkasan singkat bagian mana yang diminta dikoreksi, untuk ditampilkan di tabel.
     */
    public function getFieldSummaryAttribute(): string
    {
        $parts = [];
        if ($this->requested_check_in) {
            $parts[] = 'Masuk → '.\Illuminate\Support\Carbon::parse($this->requested_check_in)->format('H:i');
        }
        if ($this->requested_check_out) {
            $parts[] = 'Pulang → '.\Illuminate\Support\Carbon::parse($this->requested_check_out)->format('H:i');
        }

        return implode(', ', $parts) ?: '-';
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
