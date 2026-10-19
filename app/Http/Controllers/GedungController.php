<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GedungController extends Controller
{
    private function authorizeSuperAdmin()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Hanya Super Admin yang bisa mengelola unit.');
    }

    public function index()
    {
        $this->authorizeSuperAdmin();
        $gedung = Gedung::withCount(['ruangans', 'users'])->orderBy('nama_gedung')->get();
        return view('gedung.index', compact('gedung'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();
        $request->validate(['nama_gedung' => 'required|string|max:255', 'lokasi' => 'nullable|string|max:255']);
        Gedung::create($request->only(['nama_gedung', 'lokasi']));
        LogAktivitas::catat('Tambah Unit', "Unit {$request->nama_gedung} ditambahkan.");
        return back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeSuperAdmin();
        $gedung = Gedung::findOrFail($id);
        return view('gedung.edit', compact('gedung'));
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();
        $gedung = Gedung::findOrFail($id);
        $request->validate(['nama_gedung' => 'required|string|max:255', 'lokasi' => 'nullable|string|max:255']);
        $gedung->update($request->only(['nama_gedung', 'lokasi']));
        LogAktivitas::catat('Ubah Unit', "Unit {$gedung->nama_gedung} diperbarui.");
        return redirect()->route('gedung.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();
        $gedung = Gedung::withCount(['ruangans', 'users'])->findOrFail($id);
        if ($gedung->ruangans_count > 0 || $gedung->users_count > 0) {
            return back()->with('error', "Gagal! {$gedung->nama_gedung} masih dipakai {$gedung->ruangans_count} lab / {$gedung->users_count} pengguna. Kosongkan dulu.");
        }
        $gedung->delete();
        LogAktivitas::catat('Hapus Unit', "Unit {$gedung->nama_gedung} dihapus.");
        return back()->with('success', 'Unit berhasil dihapus.');
    }
}
