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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            
            // Mengaitkan transaksi ini dengan barang yang dipinjam
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            
            // Data peminjam dan waktu
            $table->string('nama_peminjam');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable(); // Boleh kosong karena barangnya belum tentu langsung dikembalikan
            
            // Status transaksi
            $table->enum('status_pinjam', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
