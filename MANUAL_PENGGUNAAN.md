# Manual Penggunaan — SIMALAB ITDA

Sistem Informasi Manajemen Aset Laboratorium (Inventaris & Peminjaman Barang)
Institut Teknologi Dirgantara Adisutjipto.

---

## Daftar Isi
1. [Tentang Aplikasi](#1-tentang-aplikasi)
2. [Peran dan Hak Akses](#2-peran-dan-hak-akses)
3. [Persiapan Awal (Teknis, Satu Kali)](#3-persiapan-awal-teknis-satu-kali)
4. [Status Barang dan Kondisi](#4-status-barang-dan-kondisi)
5. [Alur Penggunaan Tahap Awal](#5-alur-penggunaan-tahap-awal)
6. [Cara Kerja Sehari-hari](#6-cara-kerja-sehari-hari)
7. [Akun Contoh](#7-akun-contoh)
8. [FAQ Singkat](#8-faq-singkat)

---

## 1. Tentang Aplikasi

SIMALAB ITDA dipakai untuk mencatat **aset laboratorium** (barang), **ruangan** tempat
aset ditempatkan, serta **peminjaman** barang ke mahasiswa. Semua perubahan penting
tercatat otomatis di **Riwayat Aktivitas** (jejak audit).

Modul utama:

| Modul | Fungsi |
|---|---|
| Dashboard | Statistik ringkas; 4 kartu statistik dapat diklik untuk melihat barang terkait |
| Kelola Ruangan *(Super Admin)* | Daftar ruangan/lab sebagai lokasi aset |
| Data Barang | Katalog aset; edit, hapus, ubah status Maintenance, cari & filter |
| Transaksi | Catat peminjaman, pantau keterlambatan, kembalikan barang |
| Kelola Pengguna *(Super Admin)* | Kelola akun Admin Ruangan & Super Admin |
| Surat Bebas Lab *(Super Admin)* | Validasi mahasiswa bebas tanggungan barang |
| Riwayat Aktivitas | Log semua aksi pengguna (siapa, apa, kapan) |

---

## 2. Peran dan Hak Akses

| Kemampuan | Super Admin | Admin Ruangan |
|---|---|---|
| Lihat semua data seluruh lab | ✔ | ✘ (hanya ruangan sendiri) |
| Kelola Ruangan | ✔ | ✘ |
| Kelola Pengguna (akun) | ✔ | ✘ |
| Kelola Barang | ✔ semua ruangan | ✔ hanya di ruangan sendiri |
| Catat Peminjaman | ✔ semua | ✔ barang di ruangan sendiri |
| Surat Bebas Lab | ✔ | ✘ |

> Penentu peran: **Super Admin = tidak punya `deskripsi` ruangan**; Admin Ruangan = terikat
> satu ruangan. Tidak ada tombol "ubah peran"; peran ditentukan dari kolom "Penempatan"
> saat akun dibuat/diubah di Kelola Pengguna.

---

## 3. Persiapan Awal (Teknis, Satu Kali)

Dilakukan oleh pengelola sistem (bukan pengguna harian):

```bash
composer install
cp .env.example .env          # lalu isi koneksi database
php artisan key:generate
php artisan migrate           # buat tabel database
php artisan db:seed           # muat contoh data (aman diulang, tidak menggandakan)
php artisan storage:link      # penting! agar foto barang tampil
```

Lalu jalankan server (mis. `php artisan serve`) dan buka aplikasi.

---

## 4. Status Barang dan Kondisi

**Status** (menggambarkan keberadaan barang saat ini):

| Status | Arti |
|---|---|
| `Tersedia` | Siap dipinjam / tidak sedang dipakai |
| `Dipinjam` | Sedang dipinjam mahasiswa |
| `Maintenance` | Sedang diperbaiki; tidak boleh dipinjam |

**Kondisi** (menggambarkan kesehatan fisik barang):

| Kondisi | Arti |
|---|---|
| `Baik` | Normal |
| `Rusak Ringan` | Masih dipakai tapi ada kerusakan kecil |
| `Rusak Berat` | Perlu perbaikan/peremajaan |

> Barang berstatus `Dipinjam` **tidak dapat** ditandai Maintenance — harus dikembalikan dulu.

---

## 5. Alur Penggunaan Tahap Awal

Berikut urutan kerja yang disarankan pada awal pemakaian sistem.

### Tahap 0 — Login
1. Buka halaman aplikasi. Belum login akan diarahkan ke **Login**.
2. Akun pertama dibuat oleh pengelola (atau lewat Registrasi — pendaftar otomatis menjadi
   **Admin Ruangan** dan wajib memilih ruangan). Login dengan email & password.

### Tahap 1 — Buat Ruangan *(Super Admin)*
1. Menu **Kelola Ruangan → Tambah Ruangan**.
2. Isi `Kode Ruangan` (contoh: `RPL-1`) dan `Nama Ruangan` (contoh: `Lab Komputer 1`).
3. Simpan. **Ruangan yang masih berisi barang tidak bisa dihapus** (data aman).

### Tahap 2 — Buat Akun Admin Ruangan *(Super Admin)*
1. Menu **Kelola Pengguna → Tambah Pengguna**.
2. Isi nama, email, password, dan pilih **Penempatan** (ruangan) → status Admin Ruangan.
   Kosongkan penempatan → status Super Admin.
3. Simpan. Akun/aktivitas ini tercatat di Riwayat Aktivitas.

### Tahap 3 — Input Data Barang
1. Menu **Data Barang → Tambah Barang**.
2. Kode Barang (unik), Nama Barang, Kategori, Kondisi, Status, Ruangan, dan opsional foto.
3. Super Admin harus memilih ruangan; Admin Ruangan otomatis terikat ruangannya sendiri.
4. Simpan. Barang tampil di daftar; klik **Lihat** untuk detail & riwayat peminjaman.

### Tahap 4 — Catat Peminjaman
1. Menu **Transaksi → Catat Peminjaman**.
2. Pilih barang yang berstatus **Tersedia**, isi nama/NIM mahasiswa, tanggal pinjam, dan
   batas waktu kembali (default 1 minggu).
3. Simpan. Barang otomatis berubah menjadi **Dipinjam**.

---

## 6. Cara Kerja Sehari-hari

### Pencarian & Filter
Setiap daftar (Barang, Transaksi, Pengguna, Ruangan) punya kolom **Cari** dan *pill* filter.
Pencarian dan filter tetap dipertahankan saat pindah halaman (paginasi 15 data/halaman).

### Pantau Peminjaman & Keterlambatan
1. Menu **Transaksi**.
2. Jika melewati `Batas Waktu Pengembalian`, muncul badge merah **"Terlambat X hari"**.
3. Tekan pill **Terlambat** untuk melihat semua peminjaman yang lewat batas.
4. Saat barang dikembalikan: tekan **Kembalikan** — status barang kembali `Tersedia`,
   tanggal kembali diisi otomatis.

### Maintenance Barang
1. Buka **Data Barang**, klik **Lihat** pada barang.
2. Tekan tombol toggle status → status menjadi `Maintenance` (barang otomatis tidak
   tersedia untuk dipinjam). Tekan lagi untuk mengembalikan ke `Tersedia`.

### Surat Bebas Lab *(Super Admin)*
1. Menu **Surat Bebas Lab**.
2. Masukkan NIM mahasiswa. Sistem memeriksa apakah masih ada tanggungan barang.
3. Jika bersih, muncul lembar surat siap cetak (tampilkan file PDF).

### Riwayat Aktivitas
Menu **Riwayat Aktivitas** menampilkan semua aksi: login, logout, registrasi, tambah/
ubah/hapus barang, peminjaman, pengembalian, hingga perubahan status — lengkap dengan
pelaku dan waktu. Gunakan kolom **Cari** untuk menyaring.

### Cetak Laporan Barang
Menu **Data Barang → Cetak PDF** menghasilkan laporan stok barang (mengikuti ruangan
yang dapat dilihat akun tersebut) dalam bentuk PDF siap arsip.

---

## 7. Akun Contoh

Akun berikut dibuat otomatis oleh `php artisan db:seed`:

| Email | Password | Peran |
|---|---|---|
| `superadmin@itda.ac.id` | `password123` | Super Admin (semua ruangan) |
| `admin.rpl1@itda.ac.id` | `password123` | Admin Ruangan — Lab Komputer 1 |
| `admin.rpl2@itda.ac.id` | `password123` | Admin Ruangan — Lab Komputer 2 |

> Ganti password segera setelah digunakan di lingkungan nyata.

---

## 8. FAQ Singkat

- **Foto barang tidak tampil?** Jalankan `php artisan storage:link` sekali.
- **Data saya hilang?** Tidak. Contoh data dibuat dengan `firstOrCreate`, jadi
  `db:seed` diulang pun tidak menggandakan baris yang sudah ada.
- **Ruangan tidak bisa dihapus?** Ruangan yang masih berisi barang dilindungi. Pindahkan
  atau hapus barangnya terlebih dahulu.
- **Barang tidak bisa dipinjam?** Pastikan statusnya `Tersedia` (bukan `Dipinjam`/`Maintenance`).
- **Lupa password?** Login sebagai Super Admin → Kelola Pengguna → Edit → isi password baru
  (minimal 6 karakter).