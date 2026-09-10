<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrityGuardTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['ruangan_id' => null, 'password' => bcrypt('password')]);
    }

    public function test_ruangan_dengan_barang_tidak_bisa_dihapus(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-01', 'nama_ruangan' => 'Lab A']);
        Barang::create([
            'kode_barang' => 'KMP-001',
            'nama_barang' => 'Monitor',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('ruangan.destroy', $ruangan->id), [], ['Referer' => route('ruangan.index')])
            ->assertRedirect(route('ruangan.index'));

        $this->assertDatabaseHas('ruangans', ['id' => $ruangan->id]);
        $this->assertDatabaseHas('barangs', ['kode_barang' => 'KMP-001']);
    }

    public function test_ruangan_kosong_bisa_dihapus(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-02', 'nama_ruangan' => 'Lab B']);

        $this->actingAs($admin)->delete(route('ruangan.destroy', $ruangan->id));

        $this->assertDatabaseMissing('ruangans', ['id' => $ruangan->id]);
    }

    public function test_super_admin_wajib_pilih_ruangan_saat_tambah_barang(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('barang.store'), [
                'kode_barang' => 'KMP-002',
                'nama_barang' => 'Proyektor',
            ])
            ->assertSessionHasErrors('ruangan_id');

        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'KMP-002']);
    }

    public function test_barang_dipinjam_tidak_bisa_ditandai_maintenance(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-03', 'nama_ruangan' => 'Lab C']);
        $barang = Barang::create([
            'kode_barang' => 'KMP-003',
            'nama_barang' => 'Monitor',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
            'status' => 'Dipinjam',
        ]);

        $this->actingAs($admin)
            ->put(route('barang.status', $barang->id), ['status' => 'Maintenance'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('barangs', ['id' => $barang->id, 'status' => 'Dipinjam']);
    }

    public function test_barang_tersedia_bisa_ditandai_maintenance_dan_tersimpan_lognya(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-04', 'nama_ruangan' => 'Lab D']);
        $barang = Barang::create([
            'kode_barang' => 'KMP-004',
            'nama_barang' => 'Proyektor',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)
            ->put(route('barang.status', $barang->id), ['status' => 'Maintenance'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('barangs', ['id' => $barang->id, 'status' => 'Maintenance']);
        $this->assertDatabaseHas('log_aktivitases', ['user_id' => $admin->id, 'aksi' => 'Ubah Status']);
    }

    public function test_peminjaman_default_batas_waktunya_satu_minggu(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-05', 'nama_ruangan' => 'Lab E']);
        $barang = Barang::create([
            'kode_barang' => 'KMP-005',
            'nama_barang' => 'Laptop',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)->post(route('peminjaman.store'), [
            'nama_peminjam' => 'Mahasiswa Satu',
            'nim' => '6200010',
            'barang_id' => $barang->id,
            'tanggal_pinjam' => '2026-09-01',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamans', [
            'nama_peminjam' => 'Mahasiswa Satu',
            'tanggal_batas' => '2026-09-08',
            'status_pinjam' => 'Dipinjam',
        ]);
    }

    public function test_peminjaman_terlambat_tampil_di_filter_terlambat(): void
    {
        $admin = $this->superAdmin();
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-06', 'nama_ruangan' => 'Lab F']);
        $barang = Barang::create([
            'kode_barang' => 'KMP-006',
            'nama_barang' => 'Printer',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
            'status' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Mahasiswa Telat',
            'nim' => '6200011',
            'tanggal_pinjam' => '2026-08-01',
            'tanggal_batas' => '2026-08-10',
            'status_pinjam' => 'Dipinjam',
        ]);
        Peminjaman::create([
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Mahasiswa Tepat',
            'nim' => '6200012',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_batas' => now()->addDays(7)->toDateString(),
            'status_pinjam' => 'Dipinjam',
        ]);

        $this->actingAs($admin)
            ->get(route('peminjaman.index', ['terlambat' => 1]))
            ->assertOk()
            ->assertSee('Mahasiswa Telat')
            ->assertDontSee('Mahasiswa Tepat');
    }

    public function test_registrasi_dan_login_tercatat_di_riwayat_aktivitas(): void
    {
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-07', 'nama_ruangan' => 'Lab G']);

        $this->post('/register', [
            'name' => 'Admin Baru',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->assertDatabaseHas('log_aktivitases', ['aksi' => 'Registrasi', 'deskripsi' => 'Akun Admin Baru (admin@example.com) terdaftar sebagai Admin Ruangan.']);

        $this->post('/logout');
        $this->assertDatabaseHas('log_aktivitases', ['aksi' => 'Logout']);
    }
}