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
        Schema::table('tickets', function (Blueprint $table) {
            // Tambahkan kolom layanan_id setelah permohonan_id
            $table->foreignId('layanan_id')
                ->nullable()
                ->after('permohonan_id')
                ->constrained('layanan')
                ->onDelete('set null');
            
            // Tambahkan index untuk performance
            $table->index(['user_id', 'layanan_id']);
            $table->index(['status', 'layanan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['layanan_id']);
            $table->dropIndex(['user_id', 'layanan_id']);
            $table->dropIndex(['status', 'layanan_id']);
            $table->dropColumn('layanan_id');
        });
    }
};