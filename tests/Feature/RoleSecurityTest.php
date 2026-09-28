<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_admin_ruangan_tidak_bisa_membuat_akun_admin_ruangan(): void
    {
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-5', 'nama_ruangan' => 'Lab 5']);
        $admin = $this->makeUser($ruangan);

        $this->actingAs($admin)
            ->get('/ruangan/tambah-admin')
            ->assertForbidden();

        $this->actingAs($admin)
            ->post('/ruangan/tambah-admin', [
                'admin' => [
                    $ruangan->id => ['nama' => 'Penyusup', 'email' => 'penyusup@example.com'],
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'penyusup@example.com']);
    }

    public function test_beberapa_admin_ruangan_bisa_dibuat_sekali_jalan(): void
    {
        $a = Ruangan::create(['kode_ruangan' => 'RPL-6', 'nama_ruangan' => 'Lab 6']);
        $b = Ruangan::create(['kode_ruangan' => 'RPL-7', 'nama_ruangan' => 'Lab 7']);
        $c = Ruangan::create(['kode_ruangan' => 'RPL-8', 'nama_ruangan' => 'Lab 8']);
        $super = $this->makeUser(null);

        $this->actingAs($super)->post('/ruangan/tambah-admin', [
            'admin' => [
                $a->id => ['nama' => 'Admin Lab Enam', 'email' => 'enam@example.com', 'password' => 'EnamSekali'],
                $b->id => ['nama' => 'Admin Lab Tujuh', 'email' => 'tujuh@example.com', 'password' => 'TujuhSekali'],
                // dikosongkan -> harus dilewati, bukan ditolak
                $c->id => ['nama' => '', 'email' => '', 'password' => ''],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'enam@example.com', 'ruangan_id' => $a->id]);
        $this->assertDatabaseHas('users', ['email' => 'tujuh@example.com', 'ruangan_id' => $b->id]);
        $this->assertDatabaseMissing('users', ['ruangan_id' => $c->id]);

        // password yang diketik Super Admin harus bisa dipakai login
        $this->assertTrue(Hash::check('EnamSekali', User::where('email', 'enam@example.com')->first()->password));

        $this->assertDatabaseHas('log_aktivitases', [
            'aksi' => 'Tambah Pengguna',
            'deskripsi' => 'Admin untuk ruangan Lab 6 ditambahkan.',
        ]);
    }

    public function test_password_baris_yang_disi_set_bukan_dilewati(): void
    {
        $a = Ruangan::create(['kode_ruangan' => 'RPL-16', 'nama_ruangan' => 'Lab Enam Belas']);

        $this->actingAs($this->makeUser(null))
            ->post('/ruangan/tambah-admin', [
                'admin' => [$a->id => ['nama' => 'Admin', 'email' => 'baru@example.com', 'password' => '']],
            ])
            ->assertSessionHasErrors("admin.{$a->id}.password");

        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_password_pendek_ditolak(): void
    {
        $a = Ruangan::create(['kode_ruangan' => 'RPL-17', 'nama_ruangan' => 'Lab Tujuh Belas']);

        $this->actingAs($this->makeUser(null))
            ->post('/ruangan/tambah-admin', [
                'admin' => [$a->id => ['nama' => 'Admin', 'email' => 'pendek@example.com', 'password' => '1234567']],
            ])
            ->assertSessionHasErrors("admin.{$a->id}.password");

        $this->assertDatabaseMissing('users', ['email' => 'pendek@example.com']);
    }

    public function test_form_tambah_admin_hanya_menampilkan_ruangan_yang_belum_punya_admin(): void
    {
        $sudahAda = Ruangan::create(['kode_ruangan' => 'RPL-9', 'nama_ruangan' => 'Lab Sembilan']);
        $this->makeUser($sudahAda);
        $kosong = Ruangan::create(['kode_ruangan' => 'RPL-10', 'nama_ruangan' => 'Lab Sepuluh']);

        $this->actingAs($this->makeUser(null))
            ->get('/ruangan/tambah-admin')
            ->assertOk()
            ->assertSee('Lab Sepuluh')
            ->assertDontSee('Lab Sembilan');
    }

    public function test_email_yang_sudah_dipakai_ditolak_di_form_batch(): void
    {
        $sudahAda = Ruangan::create(['kode_ruangan' => 'RPL-11', 'nama_ruangan' => 'Lab Sebelas']);
        $pemilik = $this->makeUser($sudahAda);
        $baru = Ruangan::create(['kode_ruangan' => 'RPL-12', 'nama_ruangan' => 'Lab Dua Belas']);

        $this->actingAs($this->makeUser(null))
            ->post('/ruangan/tambah-admin', [
                'admin' => [$baru->id => ['nama' => 'Duplikat', 'email' => $pemilik->email, 'password' => 'DuplikatSandi']],
            ])
            ->assertSessionHasErrors("admin.{$baru->id}.email");

        $this->assertDatabaseCount('users', 2);
    }

    public function test_email_yang_sama_di_dua_baris_ditolak(): void
    {
        $a = Ruangan::create(['kode_ruangan' => 'RPL-13', 'nama_ruangan' => 'Lab Tiga Belas']);
        $b = Ruangan::create(['kode_ruangan' => 'RPL-14', 'nama_ruangan' => 'Lab Empat Belas']);

        $this->actingAs($this->makeUser(null))
            ->post('/ruangan/tambah-admin', [
                'admin' => [
                    $a->id => ['nama' => 'Sama', 'email' => 'sama@example.com', 'password' => 'SamaSekali'],
                    $b->id => ['nama' => 'Sama', 'email' => 'sama@example.com', 'password' => 'SamaSekali'],
                ],
            ])
            ->assertSessionHasErrors();

        // whole batch ditolak, tidak ada yang tersimpan setengah jalan
        $this->assertDatabaseMissing('users', ['email' => 'sama@example.com']);
    }

    public function test_ruangan_yang_sudah_punya_admin_tidak_diberi_admin_kedua_dari_form_batch(): void
    {
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-15', 'nama_ruangan' => 'Lab Lima Belas']);
        $lama = $this->makeUser($ruangan);

        $this->actingAs($this->makeUser(null))
            ->post('/ruangan/tambah-admin', [
                'admin' => [$ruangan->id => ['nama' => 'Admin Kedua', 'email' => 'kedua@example.com', 'password' => 'KeduaSekali']],
            ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('users', ['email' => 'kedua@example.com']);
        $this->assertDatabaseHas('users', ['id' => $lama->id, 'ruangan_id' => $ruangan->id]);
    }
}