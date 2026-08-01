<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel target beserta kolom enum status-nya. Pakai raw SQL (bukan
     * Schema::table()->enum()) karena mengubah definisi ENUM yang sudah ada
     * butuh doctrine/dbal kalau lewat Blueprint, sementara ALTER TABLE ... MODIFY
     * langsung didukung MySQL/MariaDB tanpa dependency tambahan.
     */
    private array $tables = ['leaves', 'attendance_corrections', 'remote_attendances'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `status` ENUM('pending', 'processing', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            // Turunkan kembali pengajuan 'processing' ke 'pending' supaya tidak ada baris
            // dengan nilai yang sudah tidak dikenal enum lama sebelum enum dipersempit lagi.
            DB::table($table)->where('status', 'processing')->update(['status' => 'pending']);
            DB::statement("ALTER TABLE `{$table}` MODIFY `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
