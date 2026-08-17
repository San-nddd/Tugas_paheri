<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertandingan', function (Blueprint $table) {
            $table->id('id_pertandingan');
            $table->foreignId('id_turnamen')
                ->constrained('turnamen', 'id_turnamen')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('babak', 50); // contoh: "Perempat Final", "Semifinal", "Final"

            $table->foreignId('id_tim_1')->nullable()
                ->constrained('tim', 'id_tim')
                ->nullOnDelete();
            $table->foreignId('id_tim_2')->nullable()
                ->constrained('tim', 'id_tim')
                ->nullOnDelete();

            $table->unsignedTinyInteger('skor_1')->default(0);
            $table->unsignedTinyInteger('skor_2')->default(0);

            $table->foreignId('id_tim_pemenang')->nullable()
                ->constrained('tim', 'id_tim')
                ->nullOnDelete();

            $table->string('bukti_hasil')->nullable();
            $table->enum('status_pertandingan', ['menunggu', 'berlangsung', 'selesai'])
                ->default('menunggu');

            // Self-reference ke pertandingan berikutnya dalam bracket.
            $table->foreignId('next_match_id')->nullable()
                ->constrained('pertandingan', 'id_pertandingan')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertandingan');
    }
};
