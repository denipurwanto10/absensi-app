<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            // 'api'    -> disinkronkan otomatis dari kalender hari libur nasional
            // 'manual' -> ditambahkan sendiri oleh admin (mis. cuti bersama internal)
            $table->enum('source', ['api', 'manual'])->default('manual');
            // Hari libur nasional resmi vs cuti bersama (dipakai untuk beda warna badge)
            $table->boolean('is_national')->default(true);
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
