<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller
{
    // Admin Ruangan hanya melihat transaksi barang di ruangannya sendiri
    private function query()
    {
        $query = Peminjaman::with('barang');
        if (Auth::user()->ruangan_id != null) {
            $query->whereHas('barang', function ($q) {
                $q->where('ruangan_id', Auth::user()->ruangan_id);
            });
        }
        return $query;
    }

    public function index()
    {
        $peminjaman = $this->query()->get();
        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        // Hanya tampilkan barang yang tersedia dan (jika admin ruangan) punya milik ruangannya
        $barang = Barang::where('status', 'Tersedia');
        if (Auth::user()->ruangan_id != null) {
            $barang->where('ruangan_id', Auth::user()->ruangan_id);
        }
        $barang = $barang->get();
        return view('peminjaman.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'nim' => 'nullable|string|max:30',
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_pinjam' => 'required|date'
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // RBAC: Admin Ruangan tidak boleh meminjamkan barang ruangan lain
        if (Auth::user()->ruangan_id != null && $barang->ruangan_id != Auth::user()->ruangan_id) {
            abort(403, 'Anda hanya dapat meminjamkan barang di ruangan Anda.');
        }

        // Anti-double: barang yang sudah "Dipinjam" tidak boleh dipinjam lagi
        if ($barang->status !== 'Tersedia') {
            return back()->with('error', 'Barang tersebut sedang dipinjam oleh pihak lain.');
        }

        Peminjaman::create([
            'nama_peminjam' => $request->nama_peminjam,
            'nim' => $request->nim,
            'barang_id' => $barang->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'status_pinjam' => 'Dipinjam'
        ]);

        $barang->update(['status' => 'Dipinjam']);
        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function kembalikan($id)
    {
        $peminjaman = $this->query()->findOrFail($id);

        if ($peminjaman->status_pinjam === 'Dipinjam') {
            $peminjaman->update([
                'status_pinjam' => 'Dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
            ]);
            Barang::where('id', $peminjaman->barang_id)->update(['status' => 'Tersedia']);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Barang berhasil dikembalikan.');
    }

    public function destroy($id)
    {
        $peminjaman = $this->query()->findOrFail($id);

        // Jika masih dipinjam, bebaskan barangnya dulu
        if ($peminjaman->status_pinjam === 'Dipinjam') {
            Barang::where('id', $peminjaman->barang_id)->update(['status' => 'Tersedia']);
        }

        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Riwayat peminjaman dihapus.');
    }

    public function suratBebasLab(Request $request)
    {
        $nama = trim($request->input('nama'));

        if ($nama) {
            $tanggungan = Peminjaman::where('nama_peminjam', 'like', "%$nama%")
                ->where('status_pinjam', 'Dipinjam')
                ->count();

            if ($tanggungan > 0) {
                return back()->with('error', "Gagal! Mahasiswa masih memiliki {$tanggungan} tanggungan barang yang belum dikembalikan.");
            }

            $pdf = Pdf::loadView('peminjaman.cetak_surat_pdf', ['namaPeminjam' => $nama]);
            return $pdf->download('Surat_Bebas_Lab_' . $nama . '.pdf');
        }

        return view('peminjaman.surat');
    }
}
