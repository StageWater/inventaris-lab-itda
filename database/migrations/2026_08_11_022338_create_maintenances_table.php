<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
    $table->id();

    $table->foreignId('barang_id')
          ->constrained('barangs')
          ->cascadeOnUpdate()
          ->restrictOnDelete();

    $table->foreignId('user_id')
          ->constrained('users')
          ->cascadeOnUpdate()
          ->restrictOnDelete();

    $table->date('tanggal');
    $table->text('kerusakan');
    $table->text('tindakan')->nullable();
    $table->decimal('biaya', 12, 2)->default(0);
    $table->enum('status', ['Proses', 'Selesai'])->default('Proses');
    $table->text('keterangan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
