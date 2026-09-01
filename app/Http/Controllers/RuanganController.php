<?php

namespace App\Http\Controllers;

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

    public function index()
    {
        $this->authorizeSuperAdmin();
        $ruangan = Ruangan::withCount('barangs')->get();
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
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
