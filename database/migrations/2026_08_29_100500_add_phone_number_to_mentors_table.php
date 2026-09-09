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
        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('student_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            if (Schema::hasColumn('mentors', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });
    }
};
