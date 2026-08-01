<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Log setiap perubahan status pada pengajuan (izin, koreksi absensi, absen luar
        // kantor). Polymorphic supaya satu tabel bisa dipakai untuk ketiga jenis
        // pengajuan tersebut. Dipakai untuk menampilkan timeline "Diajukan → Diproses →
        // Disetujui/Ditolak" di halaman riwayat karyawan maupun detail admin.
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('historyable');
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
