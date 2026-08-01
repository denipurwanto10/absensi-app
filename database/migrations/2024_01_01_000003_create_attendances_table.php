<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['hadir', 'telat', 'alpha'])->default('hadir');
            $table->timestamps();

            $table->unique(['employee_id', 'date']); // 1 karyawan hanya 1 baris absensi per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
