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
            $table->string('foto_ktp')->nullable()->after('nomor_whatsapp');
            $table->string('foto_kk')->nullable()->after('foto_ktp');
            $table->string('foto_tanda_tangan')->nullable()->after('foto_kk');
            $table->string('foto_selfie_ktp')->nullable()->after('foto_tanda_tangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'foto_ktp',
                'foto_kk',
                'foto_tanda_tangan',
                'foto_selfie_ktp',
            ]);
        });
    }
};