<?php

namespace App\Models\Concerns;

use App\Models\StatusHistory;
use Illuminate\Support\Facades\Auth;

/**
 * Tempelkan trait ini pada model pengajuan (Leave, AttendanceCorrection,
 * RemoteAttendance) supaya setiap perubahan kolom `status` otomatis tercatat
 * sebagai satu baris di tabel status_histories — dipakai untuk menampilkan
 * timeline "Diajukan → Diproses → Disetujui/Ditolak" tanpa perlu menyentuh
 * kode di controller satu per satu.
 */
trait HasStatusTimeline
{
    public static function bootHasStatusTimeline(): void
    {
        static::created(function ($model) {
            $model->statusHistories()->create([
                'status' => $model->status,
                'note' => null,
                'changed_by' => Auth::id(),
            ]);
        });

        static::updated(function ($model) {
            if (! $model->wasChanged('status')) {
                return;
            }

            $model->statusHistories()->create([
                'status' => $model->status,
                'note' => $model->admin_note,
                'changed_by' => Auth::id() ?? $model->reviewed_by,
            ]);
        });
    }

    public function statusHistories()
    {
        return $this->morphMany(StatusHistory::class, 'historyable')->orderBy('created_at')->orderBy('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bi-hourglass-split',
            'processing' => 'bi-arrow-repeat',
            'approved' => 'bi-check-circle',
            'rejected' => 'bi-x-circle',
            default => 'bi-dot',
        };
    }
}
