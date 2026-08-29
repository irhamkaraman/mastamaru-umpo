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
        Schema::table('certificate_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('certificate_configurations', 'font_file_name')) {
                $table->string('font_file_name')->nullable()->after('font_size_name');
            }
            if (!Schema::hasColumn('certificate_configurations', 'font_file_nim')) {
                $table->string('font_file_nim')->nullable()->after('font_size_nim');
            }
            if (!Schema::hasColumn('certificate_configurations', 'font_file_number')) {
                $table->string('font_file_number')->nullable()->after('font_size_number');
            }
            if (!Schema::hasColumn('certificate_configurations', 'font_file_faculty')) {
                $table->string('font_file_faculty')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'font_file_name',
                'font_file_nim',
                'font_file_number',
                'font_file_faculty',
            ]);
        });
    }
};
