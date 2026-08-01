<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'geofence_enabled',
        'office_lat',
        'office_lng',
        'radius_meters',
        'batas_telat',
    ];

    protected $casts = [
        'geofence_enabled' => 'boolean',
    ];

    /**
     * Pengaturan aplikasi disimpan sebagai satu baris tunggal (singleton).
     * Dipanggil lewat Setting::current() dari mana saja di aplikasi.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'company_name' => 'Perusahaan Saya',
            'geofence_enabled' => false,
            'radius_meters' => 150,
            'batas_telat' => '08:00:00',
        ]);
    }

    /**
     * Hitung jarak antara dua koordinat (meter) memakai formula Haversine.
     */
    public static function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter

        $latRad1 = deg2rad($lat1);
        $latRad2 = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2 + cos($latRad1) * cos($latRad2) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
