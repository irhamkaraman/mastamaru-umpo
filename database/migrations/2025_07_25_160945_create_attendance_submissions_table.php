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
            $table->foreignId('presence_session_id')->constrained('presence_sessions')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('attendances')->onDelete('cascade');
            $table->datetime('submitted_at');
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit'])->default('hadir');
            $table->text('notes')->nullable();
            $table->string('submission_method')->default('qr_code');
            $table->timestamps();
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
