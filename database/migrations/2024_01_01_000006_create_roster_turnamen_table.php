<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_turnamen', function (Blueprint $table) {
            $table->id('id_roster');
            $table->foreignId('id_pendaftaran')
                ->constrained('pendaftaran', 'id_pendaftaran')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_pemain')
                ->constrained('pemain', 'id_pemain')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('peran_game', 30);
            $table->timestamps();

            // Satu pemain hanya boleh masuk sekali dalam satu roster pendaftaran.
            $table->unique(['id_pendaftaran', 'id_pemain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_turnamen');
    }
};
