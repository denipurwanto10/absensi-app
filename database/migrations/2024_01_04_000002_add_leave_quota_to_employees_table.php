<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Jumlah jatah cuti tahunan (hari), berlaku per tahun kalender berjalan.
            $table->unsignedSmallInteger('leave_quota')->default(12)->after('qr_token');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('leave_quota');
        });
    }
};
