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
            // Kita perlu doctrine/dbal untuk mengubah kolom
            // composer require doctrine/dbal
            $table->string('nik', 16)->nullable()->unique()->change();
            $table->string('nomor_kk', 16)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ini akan mengembalikannya menjadi not-nullable jika Anda rollback
            $table->string('nik', 16)->nullable(false)->unique()->change();
            $table->string('nomor_kk', 16)->nullable(false)->change();
        });
    }
};