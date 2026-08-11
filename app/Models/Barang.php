<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'ruangan_id',
        'kategori',
        'jumlah',
        'satuan',
        'kondisi',
        'tanggal_pengadaan',
        'keterangan',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }
}
