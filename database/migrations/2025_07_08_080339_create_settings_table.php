<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('group')->primary();
            $table->string('name');
            $table->boolean('locked');
            $table->json('payload');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};