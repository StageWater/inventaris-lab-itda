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
            
            // Kita sederhanakan menjadi string/teks biasa dulu
            $table->string('kategori'); 
            
            // Relasi ke tabel ruangan yang sudah kita buat tadi
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');

            // Kita biarkan enum karena ini cukup aman dan bagus
            $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat'
            ])->default('Baik');

            $table->enum('status', [
                'Tersedia',
                'Maintenance',
                'Dipinjam'
            ])->default('Tersedia');

            $table->string('qr_code')->nullable(); // Kita siapkan untuk fitur QR nanti
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