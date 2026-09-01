<?php

namespace App\Http\Controllers;

use App\Models\Barang;
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

        $barang = $query->get();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        // Admin Ruangan tidak boleh memilih ruangan; hanya Super Admin yang bisa
        $ruangan = Auth::user()->ruangan_id === null ? Ruangan::all() : [];
        return view('barang.create', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'kode_barang.unique' => 'Gagal! Kode Barang sudah terpakai.'
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
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
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

        $request->validate(['kode_barang' => 'required|unique:barangs,kode_barang,' . $id]);

        $data = $request->only(['kode_barang', 'nama_barang', 'kategori', 'kondisi']);
        // RBAC: Admin Ruangan tidak bisa pindahkan barang ke ruangan lain
        if (Auth::user()->ruangan_id === null) {
            $data['ruangan_id'] = $request->ruangan_id;
        }

        $barang->update($data);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $barang = $this->query()->findOrFail($id);
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
