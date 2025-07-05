<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->foreignId('knowledge_base_category_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropForeign(['knowledge_base_category_id']);
            $table->dropColumn('knowledge_base_category_id');
        });
    }
};
