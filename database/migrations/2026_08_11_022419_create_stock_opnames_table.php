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
        Schema::create('stock_opnames', function (Blueprint $table) {
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
    $table->enum('kondisi', [
        'Baik',
        'Rusak Ringan',
        'Rusak Berat',
        'Hilang'
    ]);
    $table->string('lokasi')->nullable();
    $table->text('catatan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
