<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengajuan absen di luar kantor (dinas/lapangan/WFH). Berbeda dari absen QR
        // biasa: tidak butuh QR token, tapi wajib foto + titik GPS, dan baru "sah"
        // (masuk ke tabel attendances) setelah disetujui admin.
        Schema::create('remote_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->enum('punch_type', ['masuk', 'pulang'])->default('masuk');
            $table->enum('category', ['dinas', 'lapangan', 'wfh'])->default('lapangan');
            $table->time('time'); // jam saat karyawan submit pengajuan
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('photo'); // bukti foto (selfie/lokasi) wajib
            $table->text('note')->nullable(); // keperluan/keterangan singkat
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remote_attendances');
    }
};
