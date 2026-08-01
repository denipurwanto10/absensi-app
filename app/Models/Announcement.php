<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Urutan tampil default: yang dipin dulu, lalu terbaru dulu.
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_pinned')->latest();
    }
}
