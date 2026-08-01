<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'status',
        'note',
        'changed_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function historyable()
    {
        return $this->morphTo();
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Diajukan',
            'processing' => 'Diproses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bi-send',
            'processing' => 'bi-hourglass-split',
            'approved' => 'bi-check-circle-fill',
            'rejected' => 'bi-x-circle-fill',
            default => 'bi-dot',
        };
    }
}
