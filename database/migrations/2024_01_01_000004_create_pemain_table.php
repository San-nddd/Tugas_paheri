<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemain', function (Blueprint $table) {
            $table->id('id_pemain');
            $table->foreignId('id_tim')
                ->constrained('tim', 'id_tim')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('nama_game', 50);
            $table->string('id_mlbb', 30);
            $table->string('id_server', 20);
            $table->timestamps();

            // Satu ID MLBB tidak boleh dobel dalam satu tim yang sama.
            $table->unique(['id_tim', 'id_mlbb']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemain');
    }
};
