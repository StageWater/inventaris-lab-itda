<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\ProfileController; // Tambahan untuk memanggil Profile

// ----------------------------------------------------
// RUTE PUBLIK: Pengajuan Surat Bebas Lab oleh Mahasiswa (tanpa login)
// throttle:3,1 = maksimal 3 pengiriman per menit per IP. Tanpa ini form publik
// terbuka untuk spam dan tiap spam meninggalkan baris log anonymously (user_id NULL).
// ponytail: throttle per IP, bukan per NIM. CAPTCHA atau verifikasi email baru perlu
// kalau form ini dibuka ke internet publik, bukan hanya jaringan kampus.
// throttle:10,1 untuk halaman cek status -- lebih longgar karena hanya dibaca, dan
// mahasiswa sah sering salah ketik NIM lalu mengulang.
// ----------------------------------------------------
Route::get('/permohonan-bebas-lab', [App\Http\Controllers\PermohonanSuratController::class, 'createPublic'])->name('permohonan.publik');
Route::post('/permohonan-bebas-lab', [App\Http\Controllers\PermohonanSuratController::class, 'storePublic'])
    ->middleware('throttle:3,1')
    ->name('permohonan.publik.store');
Route::get('/permohonan-bebas-lab/cek', [App\Http\Controllers\PermohonanSuratController::class, 'cekStatus'])
    ->middleware('throttle:10,1')
    ->name('permohonan.publik.cek');
Route::get('/permohonan-bebas-lab/{id}/unduh', [App\Http\Controllers\PermohonanSuratController::class, 'unduh'])
    ->middleware('throttle:10,1')
    ->name('permohonan.publik.unduh');

// ----------------------------------------------------
// SEMUA RUTE DI DALAM GRUP INI DIGEMBOK (WAJIB LOGIN)
// ----------------------------------------------------
Route::middleware(['auth', 'scope'])->group(function () {
    
    // 1. Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // 2. Master Data
    Route::get('/barang/import', [App\Http\Controllers\BarangController::class, 'import'])->name('barang.import');
    Route::post('/barang/import', [App\Http\Controllers\BarangController::class, 'importData'])->name('barang.import.proses');
    // Pengisian admin ruangan sekaligus. Dari 31 ruangan, hanya 3 yang punya
    // admin; sisanya harus diisi Super Admin. Satu form per lab berarti bolak-balik
    // 28 kali. Ditaruh sebelum resource 'ruangan' supaya '/ruangan/tambah-admin'
    // tidak tertangkap sebagai /ruangan/{ruangan} (param, bukan halaman form).
    Route::get('/ruangan/tambah-admin', [RuanganController::class, 'formTambahAdmin'])->name('ruangan.admin.form');
    Route::post('/ruangan/tambah-admin', [RuanganController::class, 'storeTambahAdmin'])->name('ruangan.admin.store');
    Route::resource('ruangan', RuanganController::class);
    Route::resource('gedung', App\Http\Controllers\GedungController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::resource('users', App\Http\Controllers\UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // 3. Peminjaman
    Route::put('/peminjaman/{id}/kembalikan', [App\Http\Controllers\PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::resource('peminjaman', App\Http\Controllers\PeminjamanController::class)->only(['index', 'create', 'store', 'destroy']);

    // 4. Rute Profile (Wajib ada agar menu Breeze tidak error)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 5. Cetak PDF
    Route::get('/cetak-barang', [App\Http\Controllers\BarangController::class, 'cetak_pdf'])->name('barang.cetak');

    // 6. Info Status Barang (Maintenance / Tersedia)
    Route::put('/barang/{id}/status', [App\Http\Controllers\BarangController::class, 'ubahStatus'])->name('barang.status');

    // 7. Riwayat Aktivitas
    Route::get('/log-aktivitas', [App\Http\Controllers\LogAktivitasController::class, 'index'])->name('log.index');

    // 8. Permohonan Surat Bebas Lab (khusus Super Admin, proteksi di controller)
    Route::resource('admin/permohonan-surat', App\Http\Controllers\PermohonanSuratController::class)
        ->only(['index', 'edit', 'update', 'destroy'])
        ->names([
            'index' => 'permohonan.index',
            'edit' => 'permohonan.edit',
            'update' => 'permohonan.update',
            'destroy' => 'permohonan.destroy',
        ]);
    Route::post('/admin/permohonan-surat/{id}/cetak', [App\Http\Controllers\PermohonanSuratController::class, 'cetak'])->name('permohonan.cetak');

});

// ----------------------------------------------------
// WAJIB ADA: Ini rute rahasia untuk mesin Login/Register
// ----------------------------------------------------
require __DIR__.'/auth.php';