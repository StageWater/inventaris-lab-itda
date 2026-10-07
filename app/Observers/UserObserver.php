<?php

namespace App\Observers;

use App\Models\LogAktivitas;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        LogAktivitas::catat('Tambah Pengguna', "Pengguna {$user->name} ({$user->email}) ditambahkan.");
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['name', 'email', 'role', 'gedung_id', 'ruangan_id'])) {
            LogAktivitas::catat('Ubah Pengguna', "Data pengguna {$user->name} ({$user->email}) diperbarui.");
        }
    }

    public function deleted(User $user): void
    {
        LogAktivitas::catat('Hapus Pengguna', "Pengguna {$user->name} ({$user->email}) dihapus.");
    }
}
