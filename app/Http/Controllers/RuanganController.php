<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RuanganController extends Controller
{
    // Lihat: Super Admin (monitoring global) + Admin Gedung. Tulis:
    // hanya Admin Gedung (scope gedung_id). Admin Ruangan: ditolak semua.
    private function authorizeKelolaRuangan()
    {
        abort_if(Auth::user()->isAdminRuangan(), 403, 'Anda tidak memiliki akses untuk mengelola ruangan.');
    }

    private function authorizeOperasional()
    {
        abort_unless(Auth::user()->isAdminGedung(), 403, 'Super Admin hanya memantau. CRUD ruangan dikelola Admin Gedung.');
    }

    private function ruanganQuery()
    {
        $me = Auth::user();
        $query = Ruangan::with(['gedung'])->withCount(['barangs', 'users']);
        if ($me->isAdminGedung()) {
            $query->where('gedung_id', $me->gedung_id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $this->authorizeKelolaRuangan();
        $query = $this->ruanganQuery();
        if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('kode_ruangan', 'like', "%$katakunci%")
                    ->orWhere('nama_ruangan', 'like', "%$katakunci%");
            });
        }
        $ruangan = $query->orderBy('nama_ruangan')->paginate(15)->withQueryString();
        $tanpaAdmin = (clone $query)->has('users', '<', 1)->count();
        return view('ruangan.index', compact('ruangan', 'tanpaAdmin'));
    }

    public function create()
    {
        $this->authorizeOperasional();
        $gedung = \App\Models\Gedung::orderBy('nama_gedung')->get();
        return view('ruangan.create', compact('gedung'));
    }

    public function store(Request $request)
    {
        $this->authorizeOperasional();

        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan',
            'nama_ruangan' => 'required',
            'gedung_id' => 'nullable|exists:gedungs,id',
        ]);

        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'keterangan', 'gedung_id']);
        if (Auth::user()->isAdminGedung()) {
            $data['gedung_id'] = Auth::user()->gedung_id;
        }
        Ruangan::create($data);
        LogAktivitas::catat('Tambah Ruangan', "Ruangan {$request->kode_ruangan} - {$request->nama_ruangan} ditambahkan.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeOperasional();
        $ruangan = $this->ruanganQuery()->findOrFail($id);
        $gedung = \App\Models\Gedung::orderBy('nama_gedung')->get();
        return view('ruangan.edit', compact('ruangan', 'gedung'));
    }

    public function formTambahAdmin()
    {
        $this->authorizeKelolaRuangan();

        $q = Ruangan::has('users', '<', 1)->orderBy('nama_ruangan');
        if (Auth::user()->isAdminGedung()) {
            $q->where('gedung_id', Auth::user()->gedung_id);
        }
        $ruangan = $q->get(['id', 'kode_ruangan', 'nama_ruangan']);

        return view('ruangan.tambah_admin', compact('ruangan'));
    }

    public function storeTambahAdmin(Request $request)
    {
        $this->authorizeKelolaRuangan();

        // Baris yang tidak diisi sama sekali dibuang, jadi form boleh dikirim
        // setengah terisi: akun hanya dibuat untuk ruangan yang datanya diisi.
        $admin = array_filter(
            $request->input('admin', []),
            fn ($row) => filled($row['nama'] ?? null) || filled($row['email'] ?? null) || filled($row['password'] ?? null)
        );
        $request->merge(['admin' => $admin]);

        $terisi = $request->validate([
            'admin' => ['array'],
            'admin.*.nama' => ['required', 'string', 'max:255'],
            // distinct: menolak email yang sama di dua baris dalam satu request.
            // Aturan unique biasa hanya mengecek isi database, jadi duplikat
            // sebaris akan lolos validasi lalu menggagalkan constraint DB.
            'admin.*.email' => ['required', 'email', 'max:255', 'distinct', 'unique:users,email'],
            // Password diketik Super Admin, bukan di-random. String acak
            // (Str::password) susah dibaca dan sering salah salin, sedangkan
            // Super Admin berada di ruangan yang sama dan bisa membacanya.
            // Lupa? tinggal reset dari menu Kelola Pengguna.
            'admin.*.password' => ['required', 'string', 'min:8'],
        ], [
            'admin.*.nama.required' => 'Isi nama lengkap adminnya.',
            'admin.*.email.required' => 'Isi email adminnya.',
            'admin.*.email.email' => 'Format email tidak valid.',
            'admin.*.email.distinct' => 'Email yang sama tidak boleh dipakai di dua baris.',
            'admin.*.email.unique' => 'Email itu sudah dipakai akun lain.',
            'admin.*.password.required' => 'Isi password untuk admin ini.',
            'admin.*.password.min' => 'Password minimal 8 karakter.',
        ]);

        $baru = [];

        DB::transaction(function () use ($terisi, &$baru) {
            foreach ($terisi['admin'] as $ruanganId => $row) {
                $ruangan = Ruangan::find($ruanganId);
                if (! $ruangan || $ruangan->users()->exists()) {
                    continue;
                }
                if (Auth::user()->isAdminGedung() && $ruangan->gedung_id !== Auth::user()->gedung_id) {
                    continue;
                }

                User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'Admin Ruangan',
                    'gedung_id' => $ruangan->gedung_id,
                    'ruangan_id' => $ruangan->id,
                ]);

                $baru[] = $ruangan->nama_ruangan;
            }
        });

        foreach ($baru as $namaRuangan) {
            LogAktivitas::catat('Tambah Pengguna', "Admin untuk ruangan {$namaRuangan} ditambahkan.");
        }

        // Ruangan yang barusan diisi admin hilang dari form ini, jadi daftar
        // ini sekaligus jadi konfirmasi hasil. Tidak ada password yang perlu
        // disimpan di session.
        return redirect()->route('ruangan.admin.form')
            ->with('success', count($baru).' akun admin ruangan berhasil dibuat.');
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeOperasional();

        $ruangan = $this->ruanganQuery()->findOrFail($id);
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan,' . $id,
            'nama_ruangan' => 'required',
            'gedung_id' => 'nullable|exists:gedungs,id',
        ]);

        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'keterangan']);
        if (Auth::user()->isSuperAdmin()) {
            $data['gedung_id'] = $request->gedung_id ?: null;
        }
        $ruangan->update($data);
        LogAktivitas::catat('Ubah Ruangan', "Ruangan {$request->kode_ruangan} - {$request->nama_ruangan} diperbarui.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeOperasional();
        $ruangan = $this->ruanganQuery()->withCount(['barangs', 'users'])->findOrFail($id);

        // Guard: mencegah penghapusan diam-diam semua barang via ON DELETE CASCADE
        if ($ruangan->barangs_count > 0) {
            return back()->with('error', "Gagal! Ruangan {$ruangan->nama_ruangan} masih memiliki {$ruangan->barangs_count} barang. Pindahkan atau hapus barangnya terlebih dahulu.");
        }

        // Guard: jangan hapus ruangan yang masih punya admin -- tanpa ini FK nullOnDelete
        // diam-diam menaikkan admin ruangan jadi Super Admin.
        if ($ruangan->users_count > 0) {
            return back()->with('error', "Gagal! Ruangan {$ruangan->nama_ruangan} masih memiliki {$ruangan->users_count} pengguna. Pindahkan atau hapus penggunanya terlebih dahulu.");
        }

        $ruangan->delete();
        LogAktivitas::catat('Hapus Ruangan', "Ruangan {$ruangan->kode_ruangan} - {$ruangan->nama_ruangan} dihapus.");
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
