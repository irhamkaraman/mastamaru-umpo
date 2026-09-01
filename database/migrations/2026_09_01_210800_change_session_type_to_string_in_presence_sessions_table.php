<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('presence_sessions')) {
            // Ubah tipe kolom session_type menjadi VARCHAR(50) agar mendukung semua jenis sesi (datang, pulang, materi, dll)
            DB::statement("ALTER TABLE `presence_sessions` MODIFY COLUMN `session_type` VARCHAR(50) NOT NULL DEFAULT 'datang'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('presence_sessions')) {
            DB::statement("ALTER TABLE `presence_sessions` MODIFY COLUMN `session_type` ENUM('datang', 'pulang') NOT NULL DEFAULT 'datang'");
        }
    }
};
