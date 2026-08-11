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
        Schema::create('barangs', function (Blueprint $table) {
    $table->id();
    $table->string('kode_barang')->unique();
    $table->string('nama_barang');

    $table->foreignId('ruangan_id')->constrained('ruangans');
    $table->foreignId('kategori_barang_id')->constrained('kategori_barangs');
    $table->foreignId('satuan_id')->constrained('satuans');

    $table->string('serial_number')->nullable()->unique();

    $table->enum('kondisi', [
        'Baik',
        'Rusak Ringan',
        'Rusak Berat'
    ]);

    $table->enum('status', [
        'Tersedia',
        'Maintenance',
        'Dipinjam'
    ])->default('Tersedia');

    $table->date('tanggal_pengadaan')->nullable();

    $table->string('qr_code')->nullable();

    $table->text('keterangan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
