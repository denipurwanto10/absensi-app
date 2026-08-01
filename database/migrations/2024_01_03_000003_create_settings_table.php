<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Perusahaan Saya');
            $table->boolean('geofence_enabled')->default(false);
            $table->decimal('office_lat', 10, 7)->nullable();
            $table->decimal('office_lng', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->default(150);
            $table->time('batas_telat')->default('08:00:00');
            $table->timestamps();
        });

        // Selalu ada tepat 1 baris pengaturan (row tunggal/singleton).
        DB::table('settings')->insert([
            'company_name' => 'Perusahaan Saya',
            'geofence_enabled' => false,
            'radius_meters' => 150,
            'batas_telat' => '08:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
