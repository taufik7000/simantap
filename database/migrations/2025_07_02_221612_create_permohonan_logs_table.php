<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('catatan')->nullable(); // Untuk catatan dari petugas atau warga
            $table->foreignId('user_id')->nullable()->constrained(); // Siapa yang melakukan aksi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_logs');
    }
};
