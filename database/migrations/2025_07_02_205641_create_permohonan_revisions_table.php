<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Warga yang merevisi
            $table->integer('revision_number'); // Nomor revisi (1, 2, 3, dst)
            $table->json('berkas_revisi'); // Dokumen revisi yang dikirim
            $table->text('catatan_revisi')->nullable(); // Catatan dari warga
            $table->text('catatan_petugas')->nullable(); // Feedback dari petugas
            $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable(); // Kapan direview petugas
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Petugas yang review
            $table->timestamps();
            
            $table->index(['permohonan_id', 'revision_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_revisions');
    }
};
