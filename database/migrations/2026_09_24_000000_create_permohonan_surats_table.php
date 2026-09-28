<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('jurusan');
            $table->string('judul_skripsi');
            $table->string('nomor_surat')->nullable()->unique();
            $table->enum('status', ['Belum Dicetak', 'Sudah Dicetak'])->default('Belum Dicetak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_surats');
    }
};