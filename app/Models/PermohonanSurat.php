<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanSurat extends Model
{
    protected $fillable = [
        'nim',
        'nama',
        'jurusan',
        'judul_skripsi',
        'nomor_surat',
        'status',
    ];
}