<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;

class PeminjamanController extends Controller
{
    // 1. Tampilkan Daftar Transaksi
    public function index()
    {
        $peminjaman = Peminjaman::with('barang')->get();
        return view('peminjaman.index', compact('peminjaman'));
    }

    // 2. Tampilkan Form Pinjam Baru
    public function create()
    {
        // Hanya tampilkan barang yang statusnya 'Tersedia'
        $barang = Barang::where('status', 'Tersedia')->get();
        return view('peminjaman.create', compact('barang'));
    }

    // 3. Simpan Data Pinjaman Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'barang_id' => 'required',
            'tanggal_pinjam' => 'required|date'
        ]);

        Peminjaman::create([
            'nama_peminjam' => $request->nama_peminjam,
            'barang_id' => $request->barang_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'status_pinjam' => 'Dipinjam'
        ]);

        // Kunci status barang
        $barang = Barang::find($request->barang_id);
        if ($barang) {
            $barang->status = 'Dipinjam';
            $barang->save();
        }

        return redirect()->route('peminjaman.index');
    }

    // 4. Logika Tombol Kembalikan
    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::find($id);
        if ($peminjaman) {
           $peminjaman->status_pinjam = 'Dikembalikan';
            $peminjaman->save();

            $barang = Barang::find($peminjaman->barang_id);
            if ($barang) {
                $barang->status = 'Tersedia';
                $barang->save();
            }
        }
        return redirect()->route('peminjaman.index');
    }

    // 5. Logika Tombol Hapus (Untuk memberantas data duplikat/salah)
    public function destroy($id)
    {
        $peminjaman = Peminjaman::find($id);

        if ($peminjaman) {
            // Jika status masih 'Dipinjam', bebaskan barangnya dulu
            if ($peminjaman->status_pinjam == 'Dipinjam') {
                $barang = Barang::find($peminjaman->barang_id);
                if ($barang) {
                    $barang->status = 'Tersedia';
                    $barang->save();
                }
            }
            
            // Hapus data transaksi secara permanen
            $peminjaman->delete();
        }

        return redirect()->route('peminjaman.index');
    }

    // 6. Logika Surat Bebas Lab
    public function suratBebasLab(Request $request)
    {
        $namaPeminjam = $request->input('nama'); 

        if ($namaPeminjam) {
            // Cek apakah mahasiswa masih punya tanggungan pinjaman yang belum dikembalikan
            $tanggungan = \App\Models\Peminjaman::where('nama_peminjam', 'like', "%$namaPeminjam%")
                ->where('status_pinjam', 'Dipinjam')
                ->count();

            if ($tanggungan > 0) {
                return back()->with('error', 'Gagal! Mahasiswa masih memiliki ' . $tanggungan . ' tanggungan barang yang belum dikembalikan.');
            } else {
               if ($tanggungan > 0) {
                return back()->with('error', 'Gagal! Mahasiswa masih memiliki ' . $tanggungan . ' tanggungan barang yang belum dikembalikan.');
            } else {
                // MENGGUNAKAN FILE BLADE KHUSUS UNTUK DESAIN PDF
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('peminjaman.cetak_surat_pdf', compact('namaPeminjam'));
                return $pdf->download('Surat_Bebas_Lab_' . $namaPeminjam . '.pdf');
            }
            }
        }

        return view('peminjaman.surat');
    }
}