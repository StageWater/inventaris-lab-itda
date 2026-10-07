<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitases';

    protected $fillable = ['user_id', 'nama_user', 'role_user', 'aksi', 'deskripsi', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $deskripsi): void
    {
        $user = Auth::user();
        self::create([
            'user_id' => Auth::id(),
            'nama_user' => $user?->name,
            'role_user' => $user?->role,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            // ponytail: request() tak ada saat seeder/console; null lebih baik
            // daripada log gagal dan transaksi ikut rollback.
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
        ]);
    }
}