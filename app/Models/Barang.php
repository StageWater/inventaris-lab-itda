<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori', 'ruangan_id', 'kondisi', 'status', 'qr_code', 'keterangan', 'foto'
    ];
}