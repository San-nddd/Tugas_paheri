<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->foreignId('id_turnamen')
                ->constrained('turnamen', 'id_turnamen')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_tim')
                ->constrained('tim', 'id_tim')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status_pendaftaran', ['menunggu', 'disetujui', 'ditolak'])
                ->default('menunggu');
            $table->string('keterangan_penolakan')->nullable();
            $table->timestamps();

            // Satu tim hanya boleh mendaftar sekali per turnamen -> mencegah eksploitasi kuota.
            $table->unique(['id_turnamen', 'id_tim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
