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
        // Ganti 'permohonan' menjadi 'permohonans'
        Schema::table('permohonans', function (Blueprint $table) {
            $table->timestamp('dokumen_diterbitkan_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ganti 'permohonan' menjadi 'permohonans'
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn('dokumen_diterbitkan_at');
        });
    }
};