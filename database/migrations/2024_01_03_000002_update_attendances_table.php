<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Perluas enum status supaya menampung izin/sakit/cuti dari fitur pengajuan izin.
        DB::statement("ALTER TABLE attendances MODIFY status ENUM('hadir', 'telat', 'izin', 'sakit', 'cuti', 'alpha') DEFAULT 'hadir'");

        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('check_in_lat', 10, 7)->nullable()->after('check_in');
            $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat');
            $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out');
            $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat');
            $table->foreignId('leave_id')->nullable()->after('status')->constrained('leaves')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leave_id');
            $table->dropColumn(['check_in_lat', 'check_in_lng', 'check_out_lat', 'check_out_lng']);
        });

        DB::statement("ALTER TABLE attendances MODIFY status ENUM('hadir', 'telat', 'alpha') DEFAULT 'hadir'");
    }
};
