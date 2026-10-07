<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    protected $fillable = ['nama_gedung', 'lokasi'];

    public function ruangans(): HasMany
    {
        return $this->hasMany(Ruangan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
