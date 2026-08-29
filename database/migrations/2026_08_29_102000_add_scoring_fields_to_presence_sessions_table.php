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
        Schema::table('presence_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('presence_sessions', 'session_type')) {
                $table->enum('session_type', ['datang', 'pulang'])->default('datang')->after('session_name');
            }
            if (!Schema::hasColumn('presence_sessions', 'day_number')) {
                $table->unsignedTinyInteger('day_number')->default(1)->after('session_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presence_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('presence_sessions', 'day_number')) {
                $table->dropColumn('day_number');
            }
            if (Schema::hasColumn('presence_sessions', 'session_type')) {
                $table->dropColumn('session_type');
            }
        });
    }
};
