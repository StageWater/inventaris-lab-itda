<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    private function authorizeOperasional()
    {
        abort_if(Auth::user()->isSuperAdmin(), 403, 'Super Admin hanya memantau. Operasional barang dikelola Admin Gedung/Ruangan.');
    }

    private function query()
    {
        $query = Barang::query();
        if (! is_null($ids = Auth::user()->ruanganIds())) {
            $query->whereIn('ruangan_id', $ids ?: [0]);
        }
        return $query;
    }

    private function ruanganOptions()
    {
        if (is_null($ids = Auth::user()->ruanganIds())) {
            return Ruangan::orderBy('nama_ruangan')->get();
        }
        return Ruangan::whereIn('id', $ids ?: [0])->orderBy('nama_ruangan')->get();
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

        // Filter ruangan: abaikan pilihan di luar scope (ketikan manual query string).
        $me = Auth::user();
        $scope = $me->ruanganIds();
        if (! $me->isAdminRuangan() && $request->filled('ruangan_id')
            && ($scope === null || in_array($request->ruangan_id, $scope))) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // Daftar ruangan untuk dropdown filter
        $ruanganOptions = $this->ruanganOptions();

        // Barang terbaru tampil paling atas
        $barang = $query->with('ruangan')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('barang.index', compact('barang', 'ruanganOptions'));
    }

    public function create()
    {
        $this->authorizeOperasional();
        return view('barang.create', ['ruanganOptions' => $this->ruanganOptions()]);
    }

    public function store(Request $request)
    {
        $this->authorizeOperasional();
        $me = Auth::user();
        $rules = [
            'kode_barang' => 'required|unique:barangs,kode_barang',
            // kondisi/kategori kolomnya NOT NULL (kondisi = enum di DB); tanpa
            // validasi, POST buatan tangan dengan nilai ngawur menggagalkan insert.
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
        if (! $me->isAdminRuangan()) {
            $rules['ruangan_id'] = ['required', 'exists:ruangans,id'];
            if (! is_null($scope = $me->ruanganIds())) {
                // Tolak ruangan_id di luar wilayah dengan 422, bukan 403 --
                // cegah manipulasi inspect element / Postman.
                $rules['ruangan_id'][] = Rule::in($scope);
            }
        }

        $request->validate($rules, [
            'kode_barang.unique' => 'Gagal! Kode Barang sudah terpakai.',
            'ruangan_id.required' => 'Pilih lokasi ruangan terlebih dahulu.',
            'ruangan_id.in' => 'Ruangan di luar wilayah akses Anda.'
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'kategori', 'kondisi']);
        if ($me->isAdminRuangan()) {
            $data['ruangan_id'] = $me->ruangan_id;
        } else {
            $data['ruangan_id'] = $request->ruangan_id;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }

        $barang = Barang::create($data);
        $barang->generateQrCode();
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
        $this->authorizeOperasional();
        $barang = $this->query()->findOrFail($id);
        return view('barang.edit', ['barang' => $barang, 'ruanganOptions' => $this->ruanganOptions()]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeOperasional();
        $barang = $this->query()->findOrFail($id);

        $me = Auth::user();
        $rules = [
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ];
        if (! $me->isAdminRuangan()) {
            $rules['ruangan_id'] = ['required', 'exists:ruangans,id'];
            if (! is_null($scope = $me->ruanganIds())) {
                $rules['ruangan_id'][] = Rule::in($scope);
            }
        }
        $request->validate($rules, [
            'ruangan_id.required' => 'Pilih lokasi ruangan terlebih dahulu.',
            'ruangan_id.in' => 'Ruangan di luar wilayah akses Anda.'
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'kategori', 'kondisi']);
        if (! $me->isAdminRuangan()) {
            $data['ruangan_id'] = $request->ruangan_id;
        }

        $barang->update($data);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function ubahStatus(Request $request, string $id)
    {
        $this->authorizeOperasional();
        $barang = $this->query()->findOrFail($id);

        $request->validate(['status' => 'required|in:Tersedia,Maintenance']);
        if ($request->status === 'Maintenance' && $barang->status === 'Dipinjam') {
            return back()->with('error', 'Tidak bisa ditandai maintenance, barang sedang dipinjam.');
        }

        $barang->update(['status' => $request->status]);
        return back()->with('success', "Status barang {$barang->kode_barang} diubah menjadi {$request->status}.");
    }

    public function destroy(string $id)
    {
        $this->authorizeOperasional();
        $barang = $this->query()->findOrFail($id);

        // FK peminjaman memakai ON DELETE CASCADE, jadi menghapus barang yang sedang
        // dipinjam akan ikut menghapus riwayat pinjamannya. Efek yang lebih buruk:
        // mahasiswa itu otomatis lolos cek tanggungan di PermohonanSuratController
        // dan bisa mendapat surat bebas lab padahal masih memegang alat.
        if ($barang->status === 'Dipinjam') {
            return back()->with('error', "Gagal! {$barang->kode_barang} sedang dipinjam. Kembalikan barang ini terlebih dahulu sebelum dihapus.");
        }

        $berkas = array_filter([$barang->foto, $barang->qr_code]);

        $barang->delete();

        foreach ($berkas as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function import()
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isAdminGedung(), 403, 'Hanya Super Admin / Admin Gedung yang bisa mengimport data.');
        return view('barang.import');
    }

    public function importData(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isAdminGedung(), 403, 'Hanya Super Admin / Admin Gedung yang bisa mengimport data.');

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xls atau .xlsx.',
        ]);

        try {
            $result = (new \App\Services\ImportBarangService)->import($request->file('file')->getRealPath());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengimport file: ' . $e->getMessage());
        }

        LogAktivitas::catat('Import Barang', "Import selesai: {$result['ruangan']} ruangan & {$result['barang']} barang.");
        return redirect()->route('barang.index')->with('success', "Import berhasil: {$result['ruangan']} ruangan baru & {$result['barang']} barang.");
    }

    public function cetak_pdf()
    {
        $barang = $this->query()->orderBy('nama_barang')->get();
        $pdf = Pdf::loadView('barang.pdf', compact('barang'));
        return $pdf->download('Laporan_Stok_Barang_ITDA.pdf');
    }
}