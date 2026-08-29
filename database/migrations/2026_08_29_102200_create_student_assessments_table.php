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
        if (!Schema::hasTable('student_assessments')) {
            Schema::create('student_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('attendances')->cascadeOnDelete();
                $table->unsignedSmallInteger('total_presence_points')->default(0);
                $table->decimal('attendance_score', 5, 2)->default(0.00); // 0 - 100
                $table->decimal('activity_score', 4, 2)->nullable(); // 1 - 10
                $table->decimal('final_score', 5, 2)->default(0.00); // 0 - 100
                $table->string('grade', 5)->default('D'); // A, B, C, D
                $table->string('status', 20)->default('proses'); // lulus, gagal, proses
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessments');
    }
};
