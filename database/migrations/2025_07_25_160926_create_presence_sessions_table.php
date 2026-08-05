<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presence_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_name'); // Nama sesi presensi
            $table->string('slug')->unique(); // Slug untuk URL yang ramah
            $table->text('description')->nullable(); // Deskripsi sesi
            $table->datetime('start_time'); // Waktu mulai presensi
            $table->datetime('end_time'); // Waktu selesai presensi
            $table->boolean('is_active')->default(true); // Status aktif sesi
            $table->string('session_code')->unique(); // Kode unik sesi untuk submit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presence_sessions');
    }
};
