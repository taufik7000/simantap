<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_layanans', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['formulir_master_id']);
            // Hapus kolomnya
            $table->dropColumn('formulir_master_id');
        });
    }

    public function down(): void
    {
        Schema::table('sub_layanans', function (Blueprint $table) {
            // Kode untuk mengembalikan jika diperlukan (opsional)
            $table->foreignId('formulir_master_id')->nullable()->constrained('formulir_masters');
        });
    }
};