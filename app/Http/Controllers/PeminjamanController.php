<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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

    public function index(Request $request)
    {
        $query = $this->query();

if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('nama_peminjam', 'like', "%$katakunci%")
                    ->orWhere('nim', 'like', "%$katakunci%")
                    ->orWhereHas('barang', function ($b) use ($katakunci) {
                        $b->where('nama_barang', 'like', "%$katakunci%")
                            ->orWhere('kode_barang', 'like', "%$katakunci%");
                    });
            });
        }

        if ($status = $request->status) {
            $query->where('status_pinjam', $status);
        }

        if ($request->terlambat) {
            $query->where('status_pinjam', 'Dipinjam')
                ->whereNotNull('tanggal_batas')
                ->whereDate('tanggal_batas', '<', now()->toDateString());
        }

        if ($request->filled('ruangan_id')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        $ruangan = Ruangan::orderBy('nama_ruangan')->get();

        $peminjaman = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('peminjaman.index', compact('peminjaman', 'ruangan'));
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
            'tanggal_pinjam' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanggal_batas' => 'nullable|date|after_or_equal:tanggal_pinjam'
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

        $data = [
            'nama_peminjam' => $request->nama_peminjam,
            'nim' => $request->nim,
            'barang_id' => $barang->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'tanggal_batas' => $request->tanggal_batas ?? Carbon::parse($request->tanggal_pinjam)->addDays(7)->toDateString(),
            'status_pinjam' => 'Dipinjam'
        ];

        if ($request->hasFile('berkas')) {
            $data['berkas'] = $request->file('berkas')->store('berkas-peminjaman', 'public');
        }

        Peminjaman::create($data);

        $barang->update(['status' => 'Dipinjam']);
        LogAktivitas::catat('Catat Peminjaman', $request->nama_peminjam . ' (' . ($request->nim ?? '-') . ') meminjam ' . $barang->kode_barang . ' - ' . $barang->nama_barang . '.');
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
            LogAktivitas::catat('Pengembalian Barang', 'Barang ' . ($peminjaman->barang->nama_barang ?? '#') . $peminjaman->barang_id . ' dikembalikan oleh ' . $peminjaman->nama_peminjam . '.');
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

if ($peminjaman->berkas && \Storage::disk('public')->exists($peminjaman->berkas)) {
            \Storage::disk('public')->delete($peminjaman->berkas);
        }

        LogAktivitas::catat('Hapus Riwayat Peminjaman', 'Riwayat peminjaman ' . $peminjaman->nama_peminjam . ' (' . ($peminjaman->nim ?? '-') . ') dihapus.');
        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Riwayat peminjaman dihapus.');
    }

    public function suratBebasLab(Request $request)
    {
        // RBAC: hanya Super Admin (ruangan_id null) yang boleh akses
        abort_if(Auth::user()->ruangan_id !== null, 403, 'Anda tidak memiliki akses.');

        $nim = trim($request->input('nim'));

        if ($nim) {
            $nama = Peminjaman::where('nim', $nim)->orderByDesc('id')->value('nama_peminjam');

            if (!$nama) {
                return back()->with('error', "Gagal! Tidak ditemukan riwayat peminjaman dengan NIM {$nim}.");
            }

            $tanggungan = Peminjaman::where('nim', $nim)
                ->where('status_pinjam', 'Dipinjam')
                ->count();

            if ($tanggungan > 0) {
                return back()->with('error', "Gagal! Mahasiswa NIM {$nim} masih memiliki {$tanggungan} tanggungan barang yang belum dikembalikan.");
            }

            return view('peminjaman.cetak_surat_pdf', compact('nama', 'nim'));
        }

        return view('peminjaman.surat');
    }
}
