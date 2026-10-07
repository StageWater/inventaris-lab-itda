<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $fillable = ['gedung_id', 'kode_ruangan', 'nama_ruangan', 'keterangan'];

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

public function barangs(): HasMany
{
    return $this->hasMany(Barang::class);
}

public function users(): HasMany
{
    return $this->hasMany(User::class);
}
}
