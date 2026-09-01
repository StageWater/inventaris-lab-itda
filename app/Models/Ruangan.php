<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $fillable = ['kode_ruangan', 'nama_ruangan', 'keterangan'];

public function barangs(): HasMany
{
    return $this->hasMany(Barang::class);
}
}
