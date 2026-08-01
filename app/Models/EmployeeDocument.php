<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'title',
        'file_path',
        'file_original_name',
        'expires_at',
        'note',
        'uploaded_by',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'ktp' => 'KTP',
            'kontrak_kerja' => 'Kontrak Kerja',
            'sertifikat' => 'Sertifikat',
            default => 'Lainnya',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'ktp' => 'bi-person-vcard',
            'kontrak_kerja' => 'bi-file-earmark-text',
            'sertifikat' => 'bi-patch-check',
            default => 'bi-file-earmark',
        };
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_original_name ?? $this->file_path, PATHINFO_EXTENSION));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * True kalau dokumen akan kedaluwarsa dalam 30 hari ke depan (tapi belum lewat).
     * Dipakai untuk memberi peringatan halus di tampilan admin/karyawan.
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expires_at !== null
            && ! $this->is_expired
            && now()->diffInDays($this->expires_at, false) <= 30;
    }
}
