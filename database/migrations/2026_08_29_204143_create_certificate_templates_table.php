<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel sistem sertifikat lama (berbasis gambar / pixel coordinate)
        Schema::dropIfExists('certificate_configurations');

        // Buat tabel sistem sertifikat baru (berbasis Word template)
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('word_file');          // path ke file .docx di storage
            $table->string('applies_to')->default('lulus'); // 'lulus' | 'gagal' | 'semua'
            $table->string('number_format')->default('CERT/{seq}/MASTAMARU/2026');
            $table->unsignedInteger('current_sequence')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');

        // Restore tabel lama saat rollback
        Schema::create('certificate_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('background_image')->nullable();
            $table->string('number_format')->nullable();
            $table->unsignedInteger('current_sequence')->default(0);
            $table->integer('name_x')->nullable();
            $table->integer('name_y')->nullable();
            $table->integer('nim_x')->nullable();
            $table->integer('nim_y')->nullable();
            $table->integer('number_x')->nullable();
            $table->integer('number_y')->nullable();
            $table->integer('faculty_x')->nullable();
            $table->integer('faculty_y')->nullable();
            $table->integer('font_size_name')->default(40);
            $table->integer('font_size_nim')->default(30);
            $table->integer('font_size_number')->default(24);
            $table->integer('font_size_faculty')->default(30);
            $table->string('font_file_name')->nullable();
            $table->string('font_file_nim')->nullable();
            $table->string('font_file_number')->nullable();
            $table->string('font_file_faculty')->nullable();
            $table->string('text_color')->default('#000000');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }
};

