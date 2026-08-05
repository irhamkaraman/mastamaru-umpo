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
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Contoh: Fetch Data Mahasiswa UMPO');
            $table->string('endpoint');
            $table->string('method')->default('GET')->comment('GET, POST, PUT, DELETE');
            $table->json('headers')->nullable()->comment('Menyimpan API Key, Auth Bearer, dll');
            $table->json('query_params')->nullable()->comment('Parameter URL (?page=1&limit=10)');
            $table->json('body_payload')->nullable()->comment('Body untuk request POST/PUT');
            $table->json('response_mapping')->nullable()->comment('Mapping field API ke Field Lokal');
            $table->json('sample_response')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
