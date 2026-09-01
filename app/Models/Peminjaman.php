<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    // Menghentikan salah paham bahasa Inggris Laravel
    protected $table = 'peminjamans';

    protected $fillable = [
        'barang_id',
        'nama_peminjam',
        'nim',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status_pinjam'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}