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
            // Kita akan menambahkan setiap kolom dengan memeriksa terlebih dahulu
            // Ini akan mencegah error jika kolom sudah ada atau jika urutan rusak

            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable();
            }
            if (!Schema::hasColumn('users', 'agama')) {
                $table->string('agama')->nullable();
            }
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable();
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('users', 'gol_darah')) {
                $table->string('gol_darah', 3)->nullable();
            }
            if (!Schema::hasColumn('users', 'rt_rw')) {
                $table->string('rt_rw', 7)->nullable();
            }
            if (!Schema::hasColumn('users', 'desa_kelurahan')) {
                $table->string('desa_kelurahan')->nullable();
            }
            if (!Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan')->nullable();
            }
            if (!Schema::hasColumn('users', 'kabupaten')) {
                $table->string('kabupaten')->nullable();
            }
            if (!Schema::hasColumn('users', 'status_keluarga')) {
                $table->string('status_keluarga')->nullable();
            }
            if (!Schema::hasColumn('users', 'status_perkawinan')) {
                $table->string('status_perkawinan')->nullable();
            }
            if (!Schema::hasColumn('users', 'pekerjaan')) {
                $table->string('pekerjaan')->nullable();
            }
            if (!Schema::hasColumn('users', 'pendidikan')) {
                $table->string('pendidikan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Metode down juga dibuat lebih aman
            $columnsToDrop = [
                'jenis_kelamin', 'agama', 'tempat_lahir', 'tanggal_lahir', 'gol_darah',
                'rt_rw', 'desa_kelurahan', 'kecamatan', 'kabupaten',
                'status_keluarga', 'status_perkawinan', 'pekerjaan', 'pendidikan'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};