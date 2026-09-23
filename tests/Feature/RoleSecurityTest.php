<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(?Ruangan $ruangan): User
    {
        return User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password123'),
            'ruangan_id' => $ruangan?->id,
        ]);
    }

    public function test_admin_ruangan_tidak_melihat_log_super_admin(): void
    {
        $super = $this->makeUser(null);
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-1', 'nama_ruangan' => 'Lab 1']);
        $admin = $this->makeUser($ruangan);

        $this->actingAs($super);
        LogAktivitas::catat('Tambah Barang', 'RAHASIA-KHUSUS-SUPER-ADMIN');

        $this->actingAs($super)->get('/log-aktivitas')->assertSee('RAHASIA-KHUSUS-SUPER-ADMIN');
        $this->actingAs($admin)->get('/log-aktivitas')->assertDontSee('RAHASIA-KHUSUS-SUPER-ADMIN');
    }

    public function test_ruangan_yang_memiliki_user_tidak_bisa_dihapus(): void
    {
        $super = $this->makeUser(null);
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-2', 'nama_ruangan' => 'Lab 2']);
        $this->makeUser($ruangan);

        $this->actingAs($super)->delete("/ruangan/{$ruangan->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('ruangans', ['id' => $ruangan->id]);
    }
}