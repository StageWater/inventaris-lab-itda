<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // RBAC: Hanya Super Admin (ruangan_id NULL) yang boleh mengelola pengguna
    private function authorizeSuperAdmin()
    {
        abort_if(Auth::user()->ruangan_id !== null, 403, 'Anda tidak memiliki akses untuk mengelola pengguna.');
    }

    // Invarian: setiap ruangan wajib punya minimal satu admin. Tanpa guard ini
    // Super Admin bisa memindahkan/menghapus admin terakhir dan ruangan itu
    // diam-diam tidak ada yang mengurus.
    // ponytail: akibatnya ruangannya juga tidak bisa dihapus selama adminnya masih
    // nempel (RuanganController::destroy menolak ruangan yang punya user). Satu
    // ruangan sungguhan mau dibongkar perlu beberapa langkah manual. Kalau itu
    // sering terjadi, tambah checkbox "hapus ruangan beserta adminnya" di form hapus.
    private function cekAdminTerakhir(?int $ruanganIdLama, int $kecualiUserId): ?string
    {
        if ($ruanganIdLama === null) {
            return null;
        }

        if (User::where('ruangan_id', $ruanganIdLama)->where('id', '!=', $kecualiUserId)->exists()) {
            return null;
        }

        $nama = Ruangan::find($ruanganIdLama)?->nama_ruangan ?? 'Ruangan tersebut';

        return "Gagal! {$nama} tidak punya admin lain. Tunjuk admin untuk ruangan itu terlebih dahulu.";
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();
        $query = User::with('ruangan');
        if ($katakunci = $request->katakunci) {
            $query->where(function ($q) use ($katakunci) {
                $q->where('name', 'like', "%$katakunci%")
                    ->orWhere('email', 'like', "%$katakunci%");
            });
        }
        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();
        $ruangan = Ruangan::all();
        return view('users.create', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // null => Super Admin, angka => Admin Ruangan
            // ponytail: satu user hanya boleh satu ruangan. Kalau nanti ada satu
            // orang yang memegang 2 ruangan, ganti ke pivot user_ruangan + scope
            // per ruangan; jangan tambah kolom kedua (mis. ruangan_id_2).
            'ruangan_id' => $request->ruangan_id ?: null,
        ]);

        LogAktivitas::catat('Tambah Pengguna', "Pengguna {$request->name} ({$request->email}) ditambahkan.");
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeSuperAdmin();
        $user = User::findOrFail($id);
        $ruangan = Ruangan::all();
        return view('users.edit', compact('user', 'ruangan'));
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);
        $ruanganBaru = $request->ruangan_id ?: null;

        if ($ruanganBaru !== $user->ruangan_id
            && $error = $this->cekAdminTerakhir($user->ruangan_id, $user->id)) {
            return back()->with('error', $error)->withInput();
        }

        // cekAdminTerakhir() cuma jaga tier Admin Ruangan. Tanpa baris ini
        // Super Admin terakhir bisa menurunkan dirinya sendiri, dan tidak ada
        // lagi yang bisa mengelola pengguna, ruangan, maupun surat bebas lab --
        // pemulihannya harus lewat edit database.
        if ($user->ruangan_id === null
            && $ruanganBaru !== null
            && !User::whereNull('ruangan_id')->where('id', '!=', $user->id)->exists()) {
            return back()->with('error', 'Gagal! Anda Super Admin terakhir. Tunjuk super admin lain sebelum menurunkan peran Anda.')->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'ruangan_id' => $ruanganBaru,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        LogAktivitas::catat('Ubah Pengguna', "Data pengguna {$user->name} ({$user->email}) diperbarui.");
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);
        // Cegah menghapus akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($error = $this->cekAdminTerakhir($user->ruangan_id, $user->id)) {
            return back()->with('error', $error);
        }

        $user->delete();
        LogAktivitas::catat('Hapus Pengguna', "Pengguna {$user->name} ({$user->email}) dihapus.");
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
