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
        Schema::create('api_data_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_configuration_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable()->index(); 
            $table->json('payload_data'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_data_records');
    }
};
