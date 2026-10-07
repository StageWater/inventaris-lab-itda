<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gedung_id',
        'ruangan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }

    public function isAdminGedung(): bool
    {
        return $this->role === 'Admin Gedung';
    }

    public function isAdminRuangan(): bool
    {
        return $this->role === 'Admin Ruangan';
    }

    // ponytail: null = semua ruangan (Super Admin). Array kosong = tak ada akses.
    // Guard gedung_id null: where('gedung_id', null) ikut cocok ke SEMUA ruangan
    // tanpa gedung (IS NULL) = eskalasi diam-diam jadi global. Tolak langsung.
    public function ruanganIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }
        if ($this->isAdminGedung()) {
            if (! $this->gedung_id) {
                return [];
            }

            return Ruangan::where('gedung_id', $this->gedung_id)->pluck('id')->all();
        }

        return $this->ruangan_id ? [$this->ruangan_id] : [];
    }
}
