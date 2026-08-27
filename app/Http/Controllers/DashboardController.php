<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth; // Tambahan wajib untuk mendeteksi siapa yang login

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Barang::query();

        // LOGIKA KUNCI RUANGAN:
        // Jika akun yang login punya ruangan_id (Admin Ruangan), batasi datanya!
        // Jika tidak punya (Super Admin), biarkan query mengambil semua data.
        if ($user->ruangan_id != null) {
            $query->where('ruangan_id', $user->ruangan_id);
        }

        // Menghitung statistik berdasarkan query yang sudah dibatasi
        $total_barang = (clone $query)->count();
        $barang_tersedia = (clone $query)->where('status', 'Tersedia')->count();
        $barang_dipinjam = (clone $query)->where('status', 'Dipinjam')->count();
        $barang_rusak = (clone $query)->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();

        return view('dashboard', compact('total_barang', 'barang_tersedia', 'barang_dipinjam', 'barang_rusak'));
    }
}