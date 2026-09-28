<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\PermohonanSurat;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermohonanSuratTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['ruangan_id' => null, 'password' => bcrypt('password')]);
    }

    private function barangDipinjam(string $kode): Barang
    {
        $ruangan = Ruangan::create([
            'kode_ruangan' => 'RPL-'.substr($kode, -2),
            'nama_ruangan' => 'Lab '.$kode,
        ]);

        return Barang::create([
            'kode_barang' => $kode,
            'nama_barang' => 'Osiloskop',
            'kategori' => 'Elektronik',
            'ruangan_id' => $ruangan->id,
            'status' => 'Dipinjam',
        ]);
    }

    public function test_mahasiswa_ditolak_jika_masih_memiliki_pinjaman_aktif(): void
    {
        $barang = $this->barangDipinjam('OSK-01');

        Peminjaman::create([
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Mahasiswa Tanggungan',
            'nim' => '6200099',
            'tanggal_pinjam' => now()->subDays(2)->toDateString(),
            'tanggal_batas' => now()->addDays(5)->toDateString(),
            'status_pinjam' => 'Dipinjam',
        ]);

        $this->post(route('permohonan.publik.store'), [
            'nim' => '6200099',
            'nama' => 'Mahasiswa Tanggungan',
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Uji Efektivitas Peminjaman Alat Lab',
        ])->assertSessionHasErrors('nim');

        $this->assertDatabaseMissing('permohonan_surats', ['nim' => '6200099']);
        $this->assertDatabaseMissing('log_aktivitases', ['aksi' => 'Pengajuan Surat Bebas Lab']);
    }

    public function test_permohonan_terkunci_dan_tidak_bisa_diedit_jika_status_sudah_dicetak(): void
    {
        $surat = PermohonanSurat::create([
            'nim' => '6200088',
            'nama' => 'Mahasiswa Sudah Dicetak',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Asli',
            'nomor_surat' => '700/UN/ITDA/001',
            'status' => 'Sudah Dicetak',
        ]);

        $this->actingAs($this->superAdmin())
            ->put(route('permohonan.update', $surat->id), [
                'nim' => '6200088',
                'nama' => 'Nama Diubah Ilegal',
                'jurusan' => 'TEKNIK INFORMATIKA',
                'judul_skripsi' => 'Judul Diubah Ilegal',
                'nomor_surat' => '700/UN/ITDA/002',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('permohonan_surats', [
            'id' => $surat->id,
            'nama' => 'Mahasiswa Sudah Dicetak',
            'judul_skripsi' => 'Judul Asli',
            'nomor_surat' => '700/UN/ITDA/001',
            'status' => 'Sudah Dicetak',
        ]);
    }

    public function test_admin_ruangan_tidak_dapat_mengakses_modul_surat_bebas_lab(): void
    {
        $ruangan = Ruangan::create(['kode_ruangan' => 'RPL-10', 'nama_ruangan' => 'Lab J']);
        $admin = User::factory()->create(['ruangan_id' => $ruangan->id, 'password' => bcrypt('password')]);

        $this->actingAs($admin)
            ->get('/admin/permohonan-surat')
            ->assertForbidden();

        $this->assertDatabaseCount('permohonan_surats', 0);
    }

    public function test_nomor_yang_diketik_admin_tampil_persis_di_kop_surat_tanpa_suffiks_ganda(): void
    {
        $surat = PermohonanSurat::create([
            'nim' => '6200077',
            'nama' => 'Mahasiswa Nomor Lengkap',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Skripsi',
            'nomor_surat' => '009/Lab. Terpadu/ITDA/2026',
        ]);

        // Binary PDF terkompresi dan font-nya hex, jadi slip template yang diuji.
        $html = view('peminjaman.cetak_surat_pdf', [
            'nama' => $surat->nama,
            'nim' => $surat->nim,
            'jurusan' => $surat->jurusan,
            'judul_skripsi' => $surat->judul_skripsi,
            'nomorSurat' => $surat->nomor_surat,
        ])->render();

        $this->assertStringContainsString('No: 009/Lab. Terpadu/ITDA/2026', $html);
        $this->assertSame(1, substr_count($html, 'Lab. Terpadu'));

        $this->actingAs($this->superAdmin())
            ->post(route('permohonan.cetak', $surat->id))
            ->assertOk();

        $this->assertDatabaseHas('permohonan_surats', [
            'id' => $surat->id,
            'status' => 'Sudah Dicetak',
        ]);
    }

    public function test_nim_berbeda_tapi_nama_sama_ditolak_karena_biasanya_salah_ketik(): void
    {
        // NIM 230103119 vs 2310103119: satu orang, selisih satu digit. Uniqueness
        // NIM tidak menangkapnya, makanya nama ikut dicocokkan.
        PermohonanSurat::create([
            'nim' => '230103119',
            'nama' => 'Syahrul Yovi Distyanto',
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Penerapan',
        ]);

        $this->post(route('permohonan.publik.store'), [
            'nim' => '2310103119',
            'nama' => '  syahrul yovi distyanto ', // beda kapital + spasi, tetap orang sama
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Penerapan',
        ])->assertSessionHasErrors('nim');

        $this->assertDatabaseMissing('permohonan_surats', ['nim' => '2310103119']);
    }

    public function test_nama_sama_dengan_nim_jauh_berbeda_tetap_boleh(): void
    {
        // Dua mahasiswa berbeda memang boleh sama nama; yang dicocokkan hanya
        // nama + NIM yang mirip digit. "Budi Santoso" di kampus ini lebih dari satu.
        $this->post(route('permohonan.publik.store'), [
            'nim' => '6200011111',
            'nama' => 'Budi Santoso',
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Judul Satu',
        ])->assertSessionHasNoErrors();

        $this->post(route('permohonan.publik.store'), [
            'nim' => '6400077777',
            'nama' => 'Budi Santoso',
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Judul Dua',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('permohonan_surats', 2);
    }

    public function test_mahasiswa_bisa_cek_status_dengan_nim_dan_nama(): void
    {
        $surat = PermohonanSurat::create([
            'nim' => '6200090',
            'nama' => 'Mahasiswa Pencari Surat',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Skripsi',
        ]);

        // kapitalisasi/spasi tidak boleh menggagalkan pencocokan
        $this->get(route('permohonan.publik.cek', ['nim' => '6200090', 'nama' => mb_strtolower($surat->nama)]))
            ->assertOk()
            ->assertSee('6200090')
            ->assertSee('menunggu nomor surat');

        $this->assertDatabaseHas('permohonan_surats', ['id' => $surat->id, 'status' => 'Belum Dicetak']);
    }

    public function test_cek_status_ditolak_jika_nama_tidak_cocok(): void
    {
        PermohonanSurat::create([
            'nim' => '6200091',
            'nama' => 'Mahasiswa Asli',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Skripsi',
        ]);

        $this->get(route('permohonan.publik.cek', ['nim' => '6200091', 'nama' => 'Nama Orang Lain']))
            ->assertSessionHasErrors('nim');
    }

    public function test_surat_yang_belum_dicetak_tidak_bisa_diunduh(): void
    {
        $surat = PermohonanSurat::create([
            'nim' => '6200092',
            'nama' => 'Mahasiswa Belum Dicetak',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Skripsi',
            'nomor_surat' => '010/Lab. Terpadu/ITDA/2026',
        ]);

        $this->get(route('permohonan.publik.unduh', ['id' => $surat->id, 'nim' => '6200092']))
            ->assertNotFound();
    }

    public function test_surat_orang_lain_tidak_bisa_diunduh_hanya_dengan_menebak_url(): void
    {
        $milikOrangLain = PermohonanSurat::create([
            'nim' => '6200093',
            'nama' => 'Mahasiswa Milik Orang',
            'jurusan' => 'TEKNIK INFORMATIKA',
            'judul_skripsi' => 'Judul Skripsi',
            'nomor_surat' => '011/Lab. Terpadu/ITDA/2026',
            'status' => 'Sudah Dicetak',
        ]);

        // id-nya berurutan dan bisa ditebak; NIM di query string wajib cocok
        $this->get(route('permohonan.publik.unduh', ['id' => $milikOrangLain->id, 'nim' => '9999999']))
            ->assertNotFound();

        $this->get(route('permohonan.publik.unduh', ['id' => $milikOrangLain->id, 'nim' => '6200093']))
            ->assertOk();
    }

    public function test_pengiriman_keempat_dari_ip_yang_sama_ditolak_dengan_429(): void
    {
        // Nama dibedakan: nama sama dengan NIM beda akan ditolak guard duplikat,
        // bukan oleh throttle, jadi test ini tidak akan mengukur rate limit.
        $kirim = fn (int $ke) => $this->post(route('permohonan.publik.store'), [
            'nim' => '620007'.$ke,
            'nama' => 'Mahasiswa Spam '.$ke,
            'jurusan' => 'TEKNIK INDUSTRI',
            'judul_skripsi' => 'Judul '.$ke,
        ]);

        $kirim(1)->assertSessionHasNoErrors();
        $kirim(2)->assertSessionHasNoErrors();
        $kirim(3)->assertSessionHasNoErrors();

        $kirim(4)->assertStatus(429)->assertSee('Terlalu Banyak Pengiriman');

        // 3 pengiriman pertama lolos, yang keempat tidak pernah sampai ke controller
        $this->assertDatabaseCount('permohonan_surats', 3);
        $this->assertDatabaseMissing('permohonan_surats', ['nim' => '6200074']);
    }
}
