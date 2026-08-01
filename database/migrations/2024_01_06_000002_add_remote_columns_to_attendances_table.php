<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Menandai asal absensi: 'qr' (scan QR seperti biasa) atau 'remote'
            // (hasil approve pengajuan absen luar kantor). Dipakai untuk badge
            // pembeda di laporan, tanpa mengubah makna kolom `status` yang sudah ada.
            $table->enum('source', ['qr', 'remote'])->default('qr')->after('status');
            $table->enum('remote_category', ['dinas', 'lapangan', 'wfh'])->nullable()->after('source');
            $table->string('check_in_photo')->nullable()->after('check_in_lng');
            $table->string('check_out_photo')->nullable()->after('check_out_lng');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['source', 'remote_category', 'check_in_photo', 'check_out_photo']);
        });
    }
};
