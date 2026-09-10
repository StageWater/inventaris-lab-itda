<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    // Menghentikan salah paham bahasa Inggris Laravel
    protected $table = 'peminjamans';

    protected $fillable = [
        'barang_id',
        'nama_peminjam',
        'nim',
        'berkas',
        'tanggal_pinjam',
        'tanggal_pengembalian',
        'tanggal_batas',
        'tanggal_kembali',
        'status_pinjam'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function getTerlambatAttribute(): bool
    {
        return $this->status_pinjam === 'Dipinjam'
            && $this->tanggal_batas
            && now()->startOfDay()->gt(Carbon::parse($this->tanggal_batas));
    }

    public function getHariTerlambatAttribute(): int
    {
        return max(0, (int) now()->startOfDay()->diffInDays(Carbon::parse($this->tanggal_batas), false));
    }
}