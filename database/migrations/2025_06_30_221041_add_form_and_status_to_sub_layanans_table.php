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
    Schema::table('sub_layanans', function (Blueprint $table) {
        $table->foreignId('formulir_master_id')->nullable()->constrained()->nullOnDelete();
        $table->boolean('is_active')->default(true);
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_layanans', function (Blueprint $table) {
            //
        });
    }
};
