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
            $table->string('font_file_name')->nullable()->after('font_size_name');
            $table->string('font_file_nim')->nullable()->after('font_size_nim');
            $table->string('font_file_number')->nullable()->after('font_size_number');
            $table->string('font_file_faculty')->nullable()->after('font_size_faculty');
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
