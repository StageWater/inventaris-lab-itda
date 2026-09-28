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

    public function test_admin_terakhir_tidak_bisa_dipindah_atau_dihapus(): void
    {
        $super = $this->makeUser(null);
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-3', 'nama_ruangan' => 'Lab 3']);
        $admin = $this->makeUser($ruangan);

        // Dipindah ke Super Admin -> Lab 3 jadi tanpa admin
        $this->actingAs($super)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'ruangan_id' => '',
        ])->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'ruangan_id' => $ruangan->id]);

        // Dihapus -> Lab 3 juga jadi tanpa admin
        $this->actingAs($super)->delete("/users/{$admin->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // Begitu ada admin kedua, admin pertama bebas dipindah
        $cadangan = $this->makeUser($ruangan);
        $this->actingAs($super)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'ruangan_id' => '',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'ruangan_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $cadangan->id, 'ruangan_id' => $ruangan->id]);
    }

    public function test_super_admin_terakhir_tidak_bisa_menurunkan_diri_sendiri(): void
    {
        $super = $this->makeUser(null);
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-4', 'nama_ruangan' => 'Lab 4']);

        $this->actingAs($super)->put("/users/{$super->id}", [
            'name' => $super->name,
            'email' => $super->email,
            'ruangan_id' => (string) $ruangan->id,
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $super->id, 'ruangan_id' => null]);

        // Begitu ada super admin kedua, yang pertama bebas diturunkan
        $cadangan = $this->makeUser(null);

        $this->actingAs($super)->put("/users/{$super->id}", [
            'name' => $super->name,
            'email' => $super->email,
            'ruangan_id' => (string) $ruangan->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $super->id, 'ruangan_id' => $ruangan->id]);
        $this->assertDatabaseHas('users', ['id' => $cadangan->id, 'ruangan_id' => null]);
    }
}