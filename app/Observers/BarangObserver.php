<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\LogAktivitas;

class BarangObserver
{
    public function created(Barang $barang): void
    {
        LogAktivitas::catat('Tambah Barang', "Barang {$barang->kode_barang} - {$barang->nama_barang} ditambahkan.");
    }

    public function updated(Barang $barang): void
    {
        // ponytail: update() qr_code/foto internal tak perlu diramaikan; status
        // dipisah agar badge log tetap akurat (Hijau/Kuning/Merah).
        if ($barang->wasChanged('status') && ! $barang->wasChanged(['kode_barang', 'nama_barang', 'kategori', 'kondisi', 'ruangan_id'])) {
            LogAktivitas::catat('Ubah Status', "Status barang {$barang->kode_barang} - {$barang->nama_barang} menjadi {$barang->status}.");
            return;
        }
        if ($barang->wasChanged(['kode_barang', 'nama_barang', 'kategori', 'kondisi', 'ruangan_id', 'status'])) {
            LogAktivitas::catat('Ubah Barang', "Data barang {$barang->kode_barang} - {$barang->nama_barang} diperbarui.");
        }
    }

    public function deleted(Barang $barang): void
    {
        LogAktivitas::catat('Hapus Barang', "Barang {$barang->kode_barang} - {$barang->nama_barang} dihapus.");
    }
}
