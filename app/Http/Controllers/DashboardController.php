<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Gedung;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\PermohonanSurat;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth; // Tambahan wajib untuk mendeteksi siapa yang login

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // null = Super Admin (global), array = dibatasi, [] = tak ada akses
        $ids = $user->ruanganIds();
        $query = Barang::query();

        // Scope 3-tier: null = semua (Super Admin), array = dibatasi
        if (! is_null($ids)) {
            $query->whereIn('ruangan_id', $ids ?: [0]);
        }

        // Menghitung statistik berdasarkan query yang sudah dibatasi
        $total_barang = (clone $query)->count();
        $barang_tersedia = (clone $query)->where('status', 'Tersedia')->count();
        $barang_dipinjam = (clone $query)->where('status', 'Dipinjam')->count();
        $barang_rusak = (clone $query)->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();

        $pinjam = Peminjaman::where('status_pinjam', 'Dipinjam')
            ->whereHas('barang', fn ($q) => is_null($ids) ? $q : $q->whereIn('ruangan_id', $ids ?: [0]));
        $peminjaman_aktif = $pinjam->count();

        // ponytail: PermohonanSurat tanpa ruangan_id/gedung_id jadi hitungan
        // global untuk semua role; tambah kolom scope bila perlu dibatasi.
        $permohonan_pending = PermohonanSurat::where('status', 'Belum Dicetak')->count();

        $total_gedung = $user->isSuperAdmin() ? Gedung::count() : ($user->isAdminGedung() ? 1 : 0);
        $total_ruangan = is_null($ids) ? Ruangan::count() : count($ids);

        // ponytail: 1 query withCount, hanya Admin Gedung (Super Admin global,
        // Admin Ruangan cuma 1 baris jadi tak perlu tabel).
        $perRuangan = $user->isAdminGedung()
            ? Ruangan::withCount('barangs')->whereIn('id', $ids ?: [0])->orderBy('nama_ruangan')->get()
            : collect();

        $log = LogAktivitas::with('user');
        if (! is_null($ids)) {
            $log->where(function ($q) use ($user, $ids) {
                $q->whereHas('user', fn ($qq) => $qq->whereIn('ruangan_id', $ids ?: [0]));
                if ($user->isAdminGedung()) {
                    $q->orWhereHas('user', fn ($qq) => $qq->where('gedung_id', $user->gedung_id));
                }
            });
        }
        $logAktivitas = $log->latest()->take(5)->get();

        return view('dashboard', compact('total_barang', 'barang_tersedia', 'barang_dipinjam', 'barang_rusak', 'logAktivitas', 'peminjaman_aktif', 'permohonan_pending', 'total_gedung', 'total_ruangan', 'perRuangan'));
    }
}