<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RuanganController extends Controller
{
    // Hanya Super Admin (ruangan_id NULL) yang boleh mengelola ruangan
    private function authorizeSuperAdmin()
    {
        abort_if(Auth::user()->ruangan_id !== null, 403, 'Anda tidak memiliki akses untuk mengelola ruangan.');
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();
        $query = Ruangan::withCount('barangs');
        if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('kode_ruangan', 'like', "%$katakunci%")
                    ->orWhere('nama_ruangan', 'like', "%$katakunci%");
            });
        }
        $ruangan = $query->orderBy('nama_ruangan')->paginate(15)->withQueryString();
        return view('ruangan.index', compact('ruangan'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan',
            'nama_ruangan' => 'required',
        ]);

        Ruangan::create($request->only(['kode_ruangan', 'nama_ruangan', 'keterangan']));
        LogAktivitas::catat('Tambah Ruangan', "Ruangan {$request->kode_ruangan} - {$request->nama_ruangan} ditambahkan.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeSuperAdmin();
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();

        $ruangan = Ruangan::findOrFail($id);
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan,' . $id,
            'nama_ruangan' => 'required',
        ]);

        $ruangan->update($request->only(['kode_ruangan', 'nama_ruangan', 'keterangan']));
        LogAktivitas::catat('Ubah Ruangan', "Ruangan {$request->kode_ruangan} - {$request->nama_ruangan} diperbarui.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();
        $ruangan = Ruangan::withCount('barangs')->findOrFail($id);

        // Guard: mencegah penghapusan diam-diam semua barang via ON DELETE CASCADE
        if ($ruangan->barangs_count > 0) {
            return back()->with('error', "Gagal! Ruangan {$ruangan->nama_ruangan} masih memiliki {$ruangan->barangs_count} barang. Pindahkan atau hapus barangnya terlebih dahulu.");
        }

        $ruangan->delete();
        LogAktivitas::catat('Hapus Ruangan', "Ruangan {$ruangan->kode_ruangan} - {$ruangan->nama_ruangan} dihapus.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
