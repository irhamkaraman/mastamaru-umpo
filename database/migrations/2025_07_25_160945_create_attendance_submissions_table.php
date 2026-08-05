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
        Schema::create('attendance_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_session_id')->constrained('presence_sessions')->onDelete('cascade'); // Sesi presensi
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade'); // Kelompok
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade'); // Pendamping
            $table->foreignId('student_id')->constrained('attendances')->onDelete('cascade'); // Peserta (dari tabel attendances)
            $table->datetime('submitted_at'); // Waktu submit presensi
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit'])->default('hadir'); // Status kehadiran
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->string('submission_method')->default('qr_code'); // Metode submit (qr_code, manual)
            $table->timestamps();
            
            // Unique constraint: satu peserta hanya bisa submit sekali per sesi
            $table->unique(['presence_session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_submissions');
    }
};
