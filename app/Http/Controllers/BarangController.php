<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
    {
        $katakunci = $request->katakunci;
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Mulai antrean pencarian ke database
        $query = \App\Models\Barang::query();

        // LOGIKA KUNCI RUANGAN
        if ($user->ruangan_id != null) {
            $query->where('ruangan_id', $user->ruangan_id);
        }

        // LOGIKA PENCARIAN (Search Bar)
        if (strlen($katakunci)) {
            $query->where(function($q) use ($katakunci) {
                $q->where('nama_barang', 'like', "%$katakunci%")
                  ->orWhere('kode_barang', 'like', "%$katakunci%");
            });
        }
        
        $barang = $query->get();
        return view('barang.index', compact('barang'));
    }
    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        // Mengambil semua data ruangan untuk ditampilkan di pilihan dropdown
        $ruangan = \App\Models\Ruangan::all();
        
        // Membuka form tambah barang dan mengirimkan data ruangan tadi
        return view('barang.create', compact('ruangan'));
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
    {
        // Validasi ditambah aturan untuk file gambar (maks 2MB)
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'kode_barang.unique' => 'Gagal! Kode Barang sudah terpakai.'
        ]);

        $barang = new \App\Models\Barang;
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->kategori = $request->kategori;
        $barang->kondisi = $request->kondisi;
        $barang->ruangan_id = $request->ruangan_id;

        // LOGIKA UPLOAD FOTO
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto-barang', 'public');
            $barang->foto = $path;
        }

        $barang->save();
        return redirect()->route('barang.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = \App\Models\Barang::find($id);
        $ruangan = \App\Models\Ruangan::all(); // Tetap ambil data ruangan untuk dropdown
        
        return view('barang.edit', compact('barang', 'ruangan'));
    }

    public function update(Request $request, string $id)
    {
        $barang = \App\Models\Barang::find($id);
        
        if ($barang) {
            $barang->kode_barang = $request->kode_barang;
            $barang->nama_barang = $request->nama_barang;
            $barang->kategori = $request->kategori;
            $barang->kondisi = $request->kondisi;
            $barang->ruangan_id = $request->ruangan_id;
            $barang->save();
        }

        return redirect()->route('barang.index');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = \App\Models\Barang::find($id);
        if ($barang) {
            $barang->delete();
        }
        return redirect()->route('barang.index');
    }

    public function cetak_pdf()
    {
        // Ambil semua data barang
        $barang = \App\Models\Barang::all();
        
        // Panggil mesin PDF dan kirim datanya ke halaman cetak
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barang.pdf', compact('barang'));
        
        // Atur nama file saat di-download
        return $pdf->download('Laporan_Stok_Barang_ITDA.pdf');
    }
}
