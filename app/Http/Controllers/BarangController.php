<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    private function query()
    {
        $query = Barang::query();
        // RBAC: Admin Ruangan hanya melihat barang ruangannya sendiri
        if (Auth::user()->ruangan_id != null) {
            $query->where('ruangan_id', Auth::user()->ruangan_id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->query();

        if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('nama_barang', 'like', "%$katakunci%")
                  ->orWhere('kode_barang', 'like', "%$katakunci%");
            });
        }

if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($request->rusak) {
            $query->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat']);
        }

        // Filter ruangan (hanya untuk Super Admin)
        if (Auth::user()->ruangan_id === null && $request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // Daftar ruangan untuk dropdown filter (hanya untuk Super Admin)
        $ruangan = Auth::user()->ruangan_id === null ? Ruangan::orderBy('nama_ruangan')->get() : collect();

        $barang = $query->orderBy('nama_barang')->paginate(15)->withQueryString();
        return view('barang.index', compact('barang', 'ruangan'));
    }

    public function create()
    {
        // Admin Ruangan tidak boleh memilih ruangan; hanya Super Admin yang bisa
        $ruangan = Auth::user()->ruangan_id === null ? Ruangan::all() : [];
        return view('barang.create', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $rules = [
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
        if (Auth::user()->ruangan_id === null) {
            $rules['ruangan_id'] = 'required|exists:ruangans,id';
        }

        $request->validate($rules, [
            'kode_barang.unique' => 'Gagal! Kode Barang sudah terpakai.',
            'ruangan_id.required' => 'Pilih lokasi ruangan terlebih dahulu.'
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'kategori', 'kondisi']);
        // RBAC: Admin Ruangan terpaksa memakai ruangannya sendiri
        $data['ruangan_id'] = Auth::user()->ruangan_id !== null
            ? Auth::user()->ruangan_id
            : $request->ruangan_id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }

        Barang::create($data);
        LogAktivitas::catat('Tambah Barang', "Barang {$data['kode_barang']} - {$data['nama_barang']} ditambahkan.");
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $barang = $this->query()->findOrFail($id);
        $riwayat = Peminjaman::where('barang_id', $barang->id)
            ->with('barang')
            ->orderByDesc('created_at')
            ->get();
        return view('barang.show', compact('barang', 'riwayat'));
    }

    public function edit(string $id)
    {
        $barang = $this->query()->findOrFail($id);
        $ruangan = Auth::user()->ruangan_id === null ? Ruangan::all() : [];
        return view('barang.edit', compact('barang', 'ruangan'));
    }

    public function update(Request $request, string $id)
    {
        $barang = $this->query()->findOrFail($id);

        $rules = ['kode_barang' => 'required|unique:barangs,kode_barang,' . $id];
        if (Auth::user()->ruangan_id === null) {
            $rules['ruangan_id'] = 'required|exists:ruangans,id';
        }
        $request->validate($rules, [
            'ruangan_id.required' => 'Pilih lokasi ruangan terlebih dahulu.'
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'kategori', 'kondisi']);
        // RBAC: Admin Ruangan tidak bisa pindahkan barang ke ruangan lain
        if (Auth::user()->ruangan_id === null) {
            $data['ruangan_id'] = $request->ruangan_id;
        }

        $barang->update($data);
        LogAktivitas::catat('Ubah Barang', "Data barang {$barang->kode_barang} - {$barang->nama_barang} diperbarui.");
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function ubahStatus(Request $request, string $id)
    {
        $barang = $this->query()->findOrFail($id);

        $request->validate(['status' => 'required|in:Tersedia,Maintenance']);
        if ($request->status === 'Maintenance' && $barang->status === 'Dipinjam') {
            return back()->with('error', 'Tidak bisa ditandai maintenance, barang sedang dipinjam.');
        }

        $barang->update(['status' => $request->status]);
        LogAktivitas::catat('Ubah Status', "Status barang {$barang->kode_barang} - {$barang->nama_barang} menjadi {$request->status}.");
        return back()->with('success', "Status barang {$barang->kode_barang} diubah menjadi {$request->status}.");
    }

    public function destroy(string $id)
    {
        $barang = $this->query()->findOrFail($id);
        LogAktivitas::catat('Hapus Barang', "Barang {$barang->kode_barang} - {$barang->nama_barang} dihapus.");
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function cetak_pdf()
    {
        $barang = $this->query()->orderBy('nama_barang')->get();
        $pdf = Pdf::loadView('barang.pdf', compact('barang'));
        return $pdf->download('Laporan_Stok_Barang_ITDA.pdf');
    }
}