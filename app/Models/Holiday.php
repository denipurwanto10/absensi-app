<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'name',
        'source',
        'is_national',
    ];

    protected $casts = [
        'date' => 'date',
        'is_national' => 'boolean',
    ];

    /**
     * Cek apakah sebuah tanggal adalah "tanggal merah": hari Minggu (libur
     * mingguan tetap/otomatis) ATAU tercatat sebagai hari libur/cuti bersama
     * di tabel holidays (baik hasil sinkronisasi API maupun input manual admin).
     */
    public static function isRedDate($date): bool
    {
        $date = Carbon::parse($date);

        if ($date->isSunday()) {
            return true;
        }

        return static::query()->whereDate('date', $date->toDateString())->exists();
    }

    /**
     * Ambil nama hari libur untuk tanggal tertentu (null kalau bukan hari libur
     * yang tercatat — meski tetap hari Minggu).
     */
    public static function nameFor($date): ?string
    {
        $date = Carbon::parse($date);

        return static::query()->whereDate('date', $date->toDateString())->value('name');
    }

    /**
     * Ambil seluruh hari libur dalam satu bulan, diindeks per tanggal (format Y-m-d)
     * supaya gampang dicocokkan saat merender grid kalender.
     */
    public static function forMonth(int $year, int $month)
    {
        return static::query()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($h) => $h->date->toDateString());
    }
}
