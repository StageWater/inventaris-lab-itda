<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Filter ruangan hanya untuk Super Admin; untuk Admin Ruangan, filter ini
        // bertentangan dengan whereHas di query() sehingga selalu hasil kosong.
        if (Auth::user()->ruangan_id === null && $request->filled('ruangan_id')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        $ruangan = Auth::user()->ruangan_id === null ? Ruangan::orderBy('nama_ruangan')->get() : collect();

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
            // Wajib, bukan nullable: cek tanggungan surat bebas lab hanya cocok
            // lewat kolom nim. Peminjaman tanpa NIM tidak akan pernah ketahuan
            // dan mahasiswa yang sama bisa lolos mendapat surat bebas lab.
            'nim' => 'required|string|max:30',
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanggal_batas' => 'nullable|date|after_or_equal:tanggal_pinjam'
        ]);

        return DB::transaction(function () use ($request) {
            $barang = Barang::where('id', $request->barang_id)->lockForUpdate()->firstOrFail();

            if (Auth::user()->ruangan_id != null && $barang->ruangan_id != Auth::user()->ruangan_id) {
                abort(403, 'Anda hanya dapat meminjamkan barang di ruangan Anda.');
            }

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
        });
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
            LogAktivitas::catat('Pengembalian Barang', 'Barang ' . ($peminjaman->barang->kode_barang ?? '#' . $peminjaman->barang_id) . ' - ' . ($peminjaman->barang->nama_barang ?? '-') . ' dikembalikan oleh ' . $peminjaman->nama_peminjam . '.');
        }

        return redirect()->route('peminjaman.index')->with('success', 'Barang berhasil dikembalikan.');
    }

    public function destroy($id)
    {
        $peminjaman = $this->query()->findOrFail($id);

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
}