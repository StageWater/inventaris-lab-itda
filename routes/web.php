<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\ProfileController; // Tambahan untuk memanggil Profile

// ----------------------------------------------------
// SEMUA RUTE DI DALAM GRUP INI DIGEMBOK (WAJIB LOGIN)
// ----------------------------------------------------
Route::middleware(['auth'])->group(function () {
    
    // 1. Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // 2. Master Data
    Route::resource('ruangan', RuanganController::class);
    Route::resource('barang', App\Http\Controllers\BarangController::class);

    // 3. Transaksi
    Route::put('/peminjaman/{id}/kembalikan', [App\Http\Controllers\PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::resource('peminjaman', App\Http\Controllers\PeminjamanController::class);

    // 4. Rute Profile (Wajib ada agar menu Breeze tidak error)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 5. Cetak PDF
    Route::get('/cetak-barang', [App\Http\Controllers\BarangController::class, 'cetak_pdf'])->name('barang.cetak');
    // Rute Cek & Cetak Surat Bebas Lab
    Route::get('/surat-bebas-lab', [App\Http\Controllers\PeminjamanController::class, 'suratBebasLab'])->name('surat.bebas.lab');

});

// ----------------------------------------------------
// WAJIB ADA: Ini rute rahasia untuk mesin Login/Register
// ----------------------------------------------------
require __DIR__.'/auth.php';