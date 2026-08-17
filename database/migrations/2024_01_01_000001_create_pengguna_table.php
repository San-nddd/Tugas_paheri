<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama', 100);
            $table->string('email', 150)->unique();
            $table->string('kata_sandi');
            $table->string('no_telepon', 20)->nullable();
            $table->enum('peran', ['admin', 'penyelenggara', 'kapten_tim'])
                ->default('kapten_tim');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
