<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Titik lokasi karyawan pada SAAT mengirim pengajuan (bukan tanggal absensi
        // yang dikoreksi/diizinkan). Opsional — kalau karyawan menolak izin lokasi
        // browser, kolom ini tetap null dan admin akan melihat "lokasi tidak tersedia".
        Schema::table('leaves', function (Blueprint $table) {
            $table->decimal('submit_lat', 10, 7)->nullable()->after('attachment');
            $table->decimal('submit_lng', 10, 7)->nullable()->after('submit_lat');
        });

        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->decimal('submit_lat', 10, 7)->nullable()->after('attachment');
            $table->decimal('submit_lng', 10, 7)->nullable()->after('submit_lat');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['submit_lat', 'submit_lng']);
        });

        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->dropColumn(['submit_lat', 'submit_lng']);
        });
    }
};
