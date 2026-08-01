<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'position',
        'phone',
        'photo',
        'qr_token',
        'leave_quota',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    public function remoteAttendances()
    {
        return $this->hasMany(RemoteAttendance::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class)->latest();
    }

    /**
     * Ringkasan kelengkapan dokumen wajib (KTP & Kontrak Kerja) untuk badge
     * cepat di daftar karyawan. Sertifikat tidak dihitung wajib karena
     * jumlahnya bisa lebih dari satu / tidak semua posisi membutuhkannya.
     */
    public function getMissingRequiredDocumentsAttribute(): array
    {
        $uploadedTypes = $this->documents->pluck('type')->unique();
        $required = ['ktp' => 'KTP', 'kontrak_kerja' => 'Kontrak Kerja'];

        return collect($required)
            ->reject(fn ($label, $type) => $uploadedTypes->contains($type))
            ->values()
            ->all();
    }

    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', now()->toDateString())->first();
    }

    /**
     * Total hari cuti (type = 'cuti') yang sudah DISETUJUI pada tahun tertentu.
     * Cuti yang rentangnya melewati pergantian tahun dipotong (hanya hari yang
     * jatuh di tahun tersebut yang dihitung), supaya kuota per tahun tetap akurat.
     */
    public function cutiUsed(?int $year = null): int
    {
        $year = $year ?? now()->year;

        return $this->leaves()
            ->where('type', 'cuti')
            ->where('status', 'approved')
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->get()
            ->sum(function ($leave) use ($year) {
                $start = $leave->start_date->max(\Illuminate\Support\Carbon::create($year, 1, 1));
                $end = $leave->end_date->min(\Illuminate\Support\Carbon::create($year, 12, 31));

                return $start->diffInDays($end) + 1;
            });
    }

    /**
     * Sisa kuota cuti tahun berjalan (tidak pernah minus, minimal 0).
     */
    public function cutiRemaining(?int $year = null): int
    {
        return max(0, $this->leave_quota - $this->cutiUsed($year));
    }

    /**
     * URL foto profil. Kalau belum upload foto, pakai avatar placeholder
     * berbasis inisial nama (via ui-avatars.com).
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/'.$this->photo);
        }

        $name = urlencode($this->user->name ?? 'U');

        return "https://ui-avatars.com/api/?name={$name}&background=0d6efd&color=fff";
    }
}
