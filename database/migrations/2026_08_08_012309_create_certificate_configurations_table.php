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
        Schema::create('certificate_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('background_image')->nullable();
            $table->string('number_format')->default('/PAN/MASTAMARU/UMPO/2026');
            $table->integer('current_sequence')->default(0);
            
            // Koordinat X dan Y untuk Teks
            $table->integer('name_x')->default(100);
            $table->integer('name_y')->default(200);
            
            $table->integer('nim_x')->default(100);
            $table->integer('nim_y')->default(250);
            
            $table->integer('number_x')->default(100);
            $table->integer('number_y')->default(300);
            
            $table->integer('faculty_x')->nullable();
            $table->integer('faculty_y')->nullable();
            
            // Pengaturan Font
            $table->integer('font_size_name')->default(32);
            $table->integer('font_size_nim')->default(24);
            $table->integer('font_size_number')->default(24);
            $table->string('text_color')->default('#000000');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_configurations');
    }
};
