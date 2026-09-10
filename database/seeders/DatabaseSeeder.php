<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ---------------------------------------------------------------
        // CONTOH DATA (aman dijalankan berulang: firstOrCreate, tidak
        // menggandakan baris yang sudah ada)
        // ---------------------------------------------------------------

        // 1. RUANGAN
        $ruangans = [
            ['RPL-1', 'Lab Komputer 1', 'Laboratorium komputer untuk praktikum pemrograman.'],
            ['RPL-2', 'Lab Komputer 2', 'Laboratorium komputer untuk praktikum jaringan.'],
            ['RPL-3', 'Lab Dasar Elektro', 'Laboratorium dasar elektronika dan pengukuran.'],
            ['RPL-4', 'Lab Multimedia', 'Laboratorium produksi konten multimedia.'],
        ];
        $ruanganIds = [];
        foreach ($ruangans as [$kode, $nama, $ket]) {
            $ruanganIds[$kode] = Ruangan::firstOrCreate(
                ['kode_ruangan' => $kode],
                ['nama_ruangan' => $nama, 'keterangan' => $ket]
            )->id;
        }

        // 2. PENGGUNA
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@itda.ac.id'],
            ['name' => 'Super Admin Lab', 'password' => Hash::make('password123'), 'ruangan_id' => null]
        );
        $adminRpl1 = User::firstOrCreate(
            ['email' => 'admin.rpl1@itda.ac.id'],
            ['name' => 'Admin Lab Komputer 1', 'password' => Hash::make('password123'), 'ruangan_id' => $ruanganIds['RPL-1']]
        );
        $adminRpl2 = User::firstOrCreate(
            ['email' => 'admin.rpl2@itda.ac.id'],
            ['name' => 'Admin Lab Komputer 2', 'password' => Hash::make('password123'), 'ruangan_id' => $ruanganIds['RPL-2']]
        );

        // 3. BARANG
        $barangs = [
            // kode, nama, kategori, kondisi, status, kode_ruangan
            ['KMP-PC-001', 'PC Workstation Lenovo m720', 'Elektronik', 'Baik', 'Tersedia', 'RPL-1'],
            ['KMP-PC-002', 'PC Workstation Dell OptiPlex', 'Elektronik', 'Baik', 'Dipinjam', 'RPL-1'],
            ['KMP-SRV-001', 'Server ProLiant DL380', 'Elektronik', 'Baik', 'Tersedia', 'RPL-2'],
            ['KMP-SWC-001', 'Switch Cisco 24 Port', 'Jaringan', 'Baik', 'Tersedia', 'RPL-2'],
            ['KMP-OSC-001', 'Osiloskop Rigol DS1054Z', 'Elektronik', 'Baik', 'Tersedia', 'RPL-3'],
            ['KMP-MUL-001', 'Multimeter Digital Fluke', 'Alat Ukur', 'Rusak Ringan', 'Maintenance', 'RPL-3'],
            ['KMP-PRJ-001', 'Proyektor Epson EB-X06', 'Elektronik', 'Baik', 'Tersedia', 'RPL-4'],
            ['KMP-CAM-001', 'Kamera DSLR Canon 200D', 'Multimedia', 'Baik', 'Tersedia', 'RPL-4'],
        ];
        $barangByIdKode = [];
        foreach ($barangs as [$kodeB, $nama, $kategori, $kondisi, $status, $kodeR]) {
            $barangByIdKode[$kodeB] = Barang::firstOrCreate(
                ['kode_barang' => $kodeB],
                [
                    'nama_barang' => $nama,
                    'kategori' => $kategori,
                    'kondisi' => $kondisi,
                    'status' => $status,
                    'ruangan_id' => $ruanganIds[$kodeR],
                ]
            )->id;
        }

        // 4. PEMINJAMAN (data relatif terhadap tanggal hari ini)
        $peminjamans = [
            // nama, nim, kode_barang, tanggal_pinjam (offset hari), batas (offset hari), status
            ['Anisa Rahmawati', '620102001', 'KMP-PC-002', -12, -5, 'Dipinjam'],          // TERLAMBAT 5 hari
            ['Budi Santoso', '620102002', 'KMP-PC-001', -30, -23, 'Dikembalikan'],
            ['Citra Lestari', '620102003', 'KMP-OSC-001', -45, -38, 'Dikembalikan'],
            ['Dimas Prasetyo', '620102004', 'KMP-SRV-001', -2, 5, 'Dipinjam'],           // dipinjam & masih wajar
        ];
        foreach ($peminjamans as [$nama, $nim, $kodeB, $offsetPinjam, $offsetBatas, $status]) {
            $pinjam = now()->addDays($offsetPinjam)->toDateString();
            $batas = now()->addDays($offsetBatas)->toDateString();
            $sudahAda = Peminjaman::where('barang_id', $barangByIdKode[$kodeB])
                ->where('nama_peminjam', $nama)
                ->where('tanggal_pinjam', $pinjam)
                ->exists();
            if ($sudahAda) {
                continue;
            }
            Peminjaman::create([
                'barang_id' => $barangByIdKode[$kodeB],
                'nama_peminjam' => $nama,
                'nim' => $nim,
                'tanggal_pinjam' => $pinjam,
                'tanggal_batas' => $batas,
                'tanggal_kembali' => $status === 'Dikembalikan' ? now()->addDays($offsetBatas - 1)->toDateString() : null,
                'status_pinjam' => $status,
            ]);
        }

        // 5. LOG AWAL (biar dashboard Aktivitas Terbaru langsung hidup)
        LogAktivitas::firstOrCreate(
            ['deskripsi' => 'Contoh data sistem berhasil dimuat melalui seeder.'],
            ['user_id' => $superAdmin->id, 'aksi' => 'Sistem', 'created_at' => now(), 'updated_at' => now()]
        );
        LogAktivitas::firstOrCreate(
            ['deskripsi' => '8 barang contoh diunggah ke 4 ruangan laboratorium.'],
            ['user_id' => $superAdmin->id, 'aksi' => 'Sistem', 'created_at' => now()->subMinutes(2), 'updated_at' => now()->subMinutes(2)]
        );
    }
}