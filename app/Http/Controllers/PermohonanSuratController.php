<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\PermohonanSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanSuratController extends Controller
{
    private function authorizeSuperAdmin()
    {
        abort_if(Auth::user()->ruangan_id !== null, 403, 'Anda tidak memiliki akses untuk mengelola surat bebas lab.');
    }

    public function createPublic()
    {
        return view('permohonan-surat.create');
    }

    // Mahasiswa tidak punya akun, jadi tanpa halaman ini dia cuma bisa datang
    // ke lab untuk tahu pengajuannya sudah dicetak atau belum.
    // ponytail: NIM + nama adalah satu-satunya pembanding yang tersedia tanpa
    // menambah tabel akun/kode rahasia. NIM bisa ditebak, nama juga bukan rahasia
    // yang kuat -- jadi begitu form ini dibuka ke internet publik, ganti ke OTP
    // ke email atau kode undangan dari hasil pengajuan.
    public function cekStatus(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:30',
            'nama' => 'required|string|max:255',
        ]);

        $surat = PermohonanSurat::where('nim', $request->nim)->first();

        if (! $surat || mb_strtolower(trim($surat->nama)) !== mb_strtolower(trim($request->nama))) {
            return back()->withInput()->withErrors([
                'nim' => 'Tidak ditemukan pengajuan dengan NIM dan nama tersebut. Periksa kembali penulisan NIM dan nama Anda.',
            ]);
        }

        return view('permohonan-surat.cek', compact('surat'));
    }

    public function unduh(Request $request, string $id)
    {
        $request->validate(['nim' => 'required|string|max:30']);

        $surat = PermohonanSurat::findOrFail($id);
        // id-nya berurutan, jadi tanpa cek NIM surat orang lain bisa diunduh
        // hanya dengan menebak URL.
        abort_if($surat->nim !== $request->nim, 404);
        abort_if($surat->status !== 'Sudah Dicetak', 404);

        return $this->pdf($surat)->stream("surat_bebas_lab_{$surat->nim}.pdf");
    }

    public function storePublic(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:30|unique:permohonan_surats,nim',
            'nama' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'judul_skripsi' => 'required|string|max:255',
        ], [
            'nim.unique' => 'Pengajuan ditolak: NIM ini sudah pernah mengajukan surat bebas lab.',
        ]);

        // Tanggungan: masih ada barang berstatus Dipinjam atas NIM ini
        $masihTanggungan = Peminjaman::where('nim', $request->nim)
            ->where('status_pinjam', 'Dipinjam')
            ->exists();
        if ($masihTanggungan) {
            return back()->withInput()->withErrors([
                'nim' => 'Pengajuan ditolak: Anda masih memiliki tanggungan alat laboratorium yang belum dikembalikan.',
            ]);
        }

        // Aturan unique NIM tidak menangkap apa pun saat NIM-nya yang salah ketik:
        // orangnya sama, NIM-nya beda, jadi lolos. Cocokkan juga nama -- tapi hanya
        // kalau NIM-nya juga mirip (selisih <= 2 digit), karena dua mahasiswa beda
        // memang boleh sama nama. Selisih digit inilah cirinya salah ketik.
        $namaSama = PermohonanSurat::whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower(trim($request->nama))])
            ->get(['nim', 'nama'])
            ->first(fn ($p) => $p->nim !== $request->nim && levenshtein($p->nim, $request->nim) <= 2);
        if ($namaSama) {
            return back()->withInput()->withErrors([
                'nim' => "Pengajuan ditolak: nama ini sudah terdaftar dengan NIM {$namaSama->nim} yang mirip sekali dengan NIM Anda. Periksa kembali NIM Anda, atau hubungi laboratorium jika NIM ini memang yang benar.",
            ]);
        }

        PermohonanSurat::create($request->only(['nim', 'nama', 'jurusan', 'judul_skripsi']));
        LogAktivitas::catat('Pengajuan Surat Bebas Lab', "Mahasiswa {$request->nama} (NIM {$request->nim}) mengajukan surat bebas lab.");

        return redirect()->route('permohonan.publik')->with('status', 'Pengajuan berhasil dikirim. Silakan tunggu konfirmasi dari laboratorium.');
    }

    public function index()
    {
        $this->authorizeSuperAdmin();
        $permohonan = PermohonanSurat::orderByDesc('id')->paginate(15);
        return view('admin.permohonan.index', compact('permohonan'));
    }

    public function edit(string $id)
    {
        $this->authorizeSuperAdmin();
        $surat = PermohonanSurat::findOrFail($id);
        abort_if($surat->status === 'Sudah Dicetak', 403, 'Permohonan sudah dicetak dan dikunci.');
        $lastNomor = PermohonanSurat::whereNotNull('nomor_surat')->orderByDesc('id')->value('nomor_surat');
        return view('admin.permohonan.edit', compact('surat', 'lastNomor'));
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();
        $surat = PermohonanSurat::findOrFail($id);
        abort_if($surat->status === 'Sudah Dicetak', 403, 'Permohonan sudah dicetak dan dikunci.');

        $request->validate([
            'nim' => 'required|string|max:30|unique:permohonan_surats,nim,' . $id,
            'nama' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'judul_skripsi' => 'required|string|max:255',
            'nomor_surat' => 'nullable|string|max:50|unique:permohonan_surats,nomor_surat,' . $id,
        ]);

        $surat->update($request->only(['nim', 'nama', 'jurusan', 'judul_skripsi', 'nomor_surat']));
        LogAktivitas::catat('Ubah Surat Bebas Lab', "Data surat bebas lab NIM {$surat->nim} diperbarui.");
        return redirect()->route('permohonan.index')->with('success', 'Data permohonan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();
        $surat = PermohonanSurat::findOrFail($id);
        abort_if($surat->status === 'Sudah Dicetak', 403, 'Permohonan yang sudah dicetak tidak bisa dihapus.');
        LogAktivitas::catat('Hapus Surat Bebas Lab', "Permohonan surat NIM {$surat->nim} dihapus.");
        $surat->delete();
        return redirect()->route('permohonan.index')->with('success', 'Permohonan dihapus. NIM tersebut kini bebas mengajukan ulang.');
    }

    public function cetak(string $id)
    {
        $this->authorizeSuperAdmin();
        $surat = PermohonanSurat::findOrFail($id);

        if (!$surat->nomor_surat) {
            return back()->with('error', 'Nomor surat harus diisi terlebih dahulu sebelum mencetak.');
        }

        $surat->update(['status' => 'Sudah Dicetak']);
        LogAktivitas::catat('Mencetak Surat Bebas Lab', "Surat bebas lab NIM {$surat->nim} (nomor {$surat->nomor_surat}) dicetak.");

        // ponytail: update status + cetak tidak transaksional; bila surat gagal dibuat
        // datanya sudah terlanjur terkunci 'Sudah Dicetak'. Ganti counter/nomor-ulang
        // bila proyek butuh cetak ulang legit (mis. surat hilang).
        return $this->pdf($surat)->stream("surat_bebas_lab_{$surat->nim}.pdf");
    }

    private function pdf(PermohonanSurat $surat)
    {
        return Pdf::loadView('peminjaman.cetak_surat_pdf', [
            'nama' => $surat->nama,
            'nim' => $surat->nim,
            'jurusan' => $surat->jurusan,
            'judul_skripsi' => $surat->judul_skripsi,
            'nomorSurat' => $surat->nomor_surat,
        ]);
    }
}