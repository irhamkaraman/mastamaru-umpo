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
        Schema::table('api_data_records', function (Blueprint $table) {
            $table->boolean('is_imported')->default(false)->after('payload_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_data_records', function (Blueprint $table) {
            $table->dropColumn('is_imported');
        });
    }
};
