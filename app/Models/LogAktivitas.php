<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitases';

    protected $fillable = ['user_id', 'aksi', 'deskripsi'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $deskripsi): void
    {
        self::create([
            'user_id' => Auth::id(),
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
        ]);
    }
}