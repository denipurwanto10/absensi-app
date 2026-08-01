<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@absensi.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $karyawanData = [
            ['name' => 'Budi Santoso', 'nip' => '2024001', 'position' => 'Staff IT'],
            ['name' => 'Siti Aminah', 'nip' => '2024002', 'position' => 'Staff HRD'],
            ['name' => 'Andi Wijaya', 'nip' => '2024003', 'position' => 'Staff Keuangan'],
        ];

        foreach ($karyawanData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => Str::slug($data['name']).'@absensi.test',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'position' => $data['position'],
                'qr_token' => 'EMP-'.Str::upper(Str::random(10)),
                'leave_quota' => 12, // jatah cuti tahunan default, bisa diubah admin per karyawan
            ]);
        }

        // Contoh data hari libur (opsional). Admin bisa menyinkronkan data resmi
        // lewat tombol "Sinkronkan" di halaman Kalender Libur.
        Holiday::updateOrCreate(['date' => now()->startOfYear()->addDays(0)->toDateString()], [
            'name' => 'Tahun Baru Masehi', 'source' => 'manual', 'is_national' => true,
        ]);
        Holiday::updateOrCreate(['date' => now()->startOfYear()->addMonths(7)->addDays(16)->toDateString()], [
            'name' => 'Hari Kemerdekaan RI', 'source' => 'manual', 'is_national' => true,
        ]);
    }
}
