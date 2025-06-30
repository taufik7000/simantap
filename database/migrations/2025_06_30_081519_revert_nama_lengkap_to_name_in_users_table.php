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
        Schema::table('users', function (Blueprint $table) {
            // Hanya jalankan jika kolom 'nama_lengkap' ada
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->renameColumn('nama_lengkap', 'name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ini akan mengembalikannya jika Anda perlu rollback
            $table->renameColumn('name', 'nama_lengkap');
        });
    }
};