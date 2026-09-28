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
    // Hanya Super Admin (ruangan_id NULL) yang boleh mengelola ruangan
    private function authorizeSuperAdmin()
    {
        abort_if(Auth::user()->ruangan_id !== null, 403, 'Anda tidak memiliki akses untuk mengelola ruangan.');
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();
        $query = Ruangan::withCount(['barangs', 'users']);
        if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('kode_ruangan', 'like', "%$katakunci%")
                    ->orWhere('nama_ruangan', 'like', "%$katakunci%");
            });
        }
        $ruangan = $query->orderBy('nama_ruangan')->paginate(15)->withQueryString();
        $tanpaAdmin = Ruangan::has('users', '<', 1)->count();
        return view('ruangan.index', compact('ruangan', 'tanpaAdmin'));
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

    public function formTambahAdmin()
    {
        $this->authorizeSuperAdmin();

        $ruangan = Ruangan::has('users', '<', 1)
            ->orderBy('nama_ruangan')
            ->get(['id', 'kode_ruangan', 'nama_ruangan']);

        return view('ruangan.tambah_admin', compact('ruangan'));
    }

    public function storeTambahAdmin(Request $request)
    {
        $this->authorizeSuperAdmin();

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

                // Form ini hanya menampilkan ruangan tanpa admin, tapi tetap
                // dijaga agar tidak pernah membuat admin kedua dari form ini.
                if (! $ruangan || $ruangan->users()->exists()) {
                    continue;
                }

                User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
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
        $ruangan = Ruangan::withCount(['barangs', 'users'])->findOrFail($id);

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
