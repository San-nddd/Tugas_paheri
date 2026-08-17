<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnamen', function (Blueprint $table) {
            $table->id('id_turnamen');
            $table->foreignId('id_penyelenggara')
                ->constrained('pengguna', 'id_pengguna')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nama_turnamen', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('kuota_maksimal');
            $table->decimal('biaya', 12, 2)->default(0);
            $table->string('kode_akses', 20)->unique();
            $table->enum('status_turnamen', ['draf', 'buka', 'berlangsung', 'selesai'])
                ->default('draf');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnamen');
    }
};
