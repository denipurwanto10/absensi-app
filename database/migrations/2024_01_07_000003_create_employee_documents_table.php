<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dokumen kepegawaian (KTP, kontrak kerja, sertifikat, dll). Bisa diunggah
        // oleh karyawan sendiri (dari halaman profil) maupun oleh admin (dari halaman
        // kelola karyawan) — kolom uploaded_by mencatat siapa yang mengunggah.
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['ktp', 'kontrak_kerja', 'sertifikat', 'lainnya'])->default('lainnya');
            $table->string('title'); // nama dokumen, mis. "KTP" atau "Sertifikat BNSP Welding"
            $table->string('file_path');
            $table->string('file_original_name')->nullable();
            $table->date('expires_at')->nullable(); // opsional: masa berlaku (KTP/kontrak/sertifikat)
            $table->text('note')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
