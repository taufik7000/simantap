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
        // Langkah 1: Ubah nama tabel 'layanans' menjadi 'kategori_layanan' TERLEBIH DAHULU
        Schema::rename('layanans', 'kategori_layanan');

        // Langkah 2: Sekarang nama 'layanan' sudah bebas, kita bisa ubah 'sub_layanans'
        Schema::rename('sub_layanans', 'layanan');

        // Langkah 3: Ubah nama kolom foreign key di tabel 'layanan' (yang baru)
        Schema::table('layanan', function (Blueprint $table) {
            $table->renameColumn('layanan_id', 'kategori_layanan_id');
        });

        // Langkah 4: Ubah nama kolom foreign key di tabel 'permohonans'
        Schema::table('permohonans', function (Blueprint $table) {
            $table->renameColumn('sub_layanan_id', 'layanan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Membalikkan langkah 4
        Schema::table('permohonans', function (Blueprint $table) {
            $table->renameColumn('layanan_id', 'sub_layanan_id');
        });

        // Membalikkan langkah 3
        Schema::table('layanan', function (Blueprint $table) {
            $table->renameColumn('kategori_layanan_id', 'layanan_id');
        });

        // Membalikkan langkah 2
        Schema::rename('layanan', 'sub_layanans');

        // Membalikkan langkah 1
        Schema::rename('kategori_layanan', 'layanans');
    }
};