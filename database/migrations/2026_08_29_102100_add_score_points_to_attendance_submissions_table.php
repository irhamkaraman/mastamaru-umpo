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
        Schema::table('attendance_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_submissions', 'score_points')) {
                $table->unsignedSmallInteger('score_points')->default(0)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_submissions', 'score_points')) {
                $table->dropColumn('score_points');
            }
        });
    }
};
