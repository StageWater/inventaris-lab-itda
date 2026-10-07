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
    // Super Admin: semua. Admin Gedung: gedungnya saja. Admin Ruangan: ditolak.
    private function authorizeKelolaUser()
    {
        abort_if(Auth::user()->isAdminRuangan(), 403, 'Anda tidak memiliki akses untuk mengelola pengguna.');
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
        $this->authorizeKelolaUser();
        $me = Auth::user();
        $query = User::with(['ruangan', 'gedung']);
        if ($me->isAdminGedung()) {
            $ids = $me->ruanganIds();
            $query->where(function ($q) use ($me, $ids) {
                $q->where('gedung_id', $me->gedung_id)
                    ->orWhereIn('ruangan_id', $ids ?: [0]);
            });
        }
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
        $this->authorizeKelolaUser();
        $me = Auth::user();
        $ruangan = $me->isSuperAdmin() ? Ruangan::all()
            : Ruangan::where('gedung_id', $me->gedung_id)->get();
        $gedung = $me->isSuperAdmin() ? \App\Models\Gedung::all() : \App\Models\Gedung::where('id', $me->gedung_id)->get();
        return view('users.create', compact('ruangan', 'gedung'));
    }

    public function store(Request $request)
    {
        $this->authorizeKelolaUser();
        $me = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'gedung_id' => 'nullable|exists:gedungs,id',
            'role' => 'nullable|in:Super Admin,Admin Gedung,Admin Ruangan',
        ]);

        $role = $request->role ?: ($request->ruangan_id ? 'Admin Ruangan' : 'Super Admin');
        if ($me->isAdminGedung()) {
            $role = 'Admin Ruangan';
            abort_unless(in_array($request->ruangan_id, $me->ruanganIds()), 403);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            // ponytail: satu user hanya boleh satu ruangan. Kalau nanti ada satu
            // orang yang memegang 2 ruangan, ganti ke pivot user_ruangan + scope
            // per ruangan; jangan tambah kolom kedua (mis. ruangan_id_2).
            'gedung_id' => $me->isAdminGedung() ? $me->gedung_id : ($request->gedung_id ?: null),
            'ruangan_id' => $request->ruangan_id ?: null,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeKelolaUser();
        $me = Auth::user();
        $user = User::findOrFail($id);
        abort_if($me->isAdminGedung() && ! in_array($user->ruangan_id, $me->ruanganIds()) && $user->gedung_id !== $me->gedung_id, 403);
        $ruangan = $me->isSuperAdmin() ? Ruangan::all()
            : Ruangan::where('gedung_id', $me->gedung_id)->get();
        $gedung = $me->isSuperAdmin() ? \App\Models\Gedung::all() : \App\Models\Gedung::where('id', $me->gedung_id)->get();
        return view('users.edit', compact('user', 'ruangan', 'gedung'));
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeKelolaUser();

        $user = User::findOrFail($id);
        $me = Auth::user();
        abort_if($me->isAdminGedung() && ! in_array($user->ruangan_id, $me->ruanganIds()) && $user->gedung_id !== $me->gedung_id, 403);
        $ruanganBaru = $request->ruangan_id ?: null;
        if ($me->isAdminGedung()) {
            abort_if($ruanganBaru && ! in_array($ruanganBaru, $me->ruanganIds()), 403);
        }

        if ($ruanganBaru !== $user->ruangan_id
            && $error = $this->cekAdminTerakhir($user->ruangan_id, $user->id)) {
            return back()->with('error', $error)->withInput();
        }

        // cekAdminTerakhir() cuma jaga tier Admin Ruangan. Tanpa baris ini
        // Super Admin terakhir bisa menurunkan dirinya sendiri, dan tidak ada
        // lagi yang bisa mengelola pengguna, ruangan, maupun surat bebas lab --
        // pemulihannya harus lewat edit database.
        // Guard Super Admin terakhir: hitung by role, bukan by ruangan null --
        // Admin Gedung juga ruangan_id null dan ikut kehitung kalau pakai whereNull.
        if ($user->isSuperAdmin()
            && ($ruanganBaru !== null || ($request->filled('role') && $request->role !== 'Super Admin'))
            && !User::where('role', 'Super Admin')->where('id', '!=', $user->id)->exists()) {
            return back()->with('error', 'Gagal! Anda Super Admin terakhir. Tunjuk super admin lain sebelum menurunkan peran Anda.')->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'gedung_id' => 'nullable|exists:gedungs,id',
            'role' => 'nullable|in:Super Admin,Admin Gedung,Admin Ruangan',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'ruangan_id' => $ruanganBaru,
            'gedung_id' => $me->isAdminGedung() ? $me->gedung_id : ($request->gedung_id ?: null),
        ];
        if ($me->isSuperAdmin() && $request->filled('role')) {
            $data['role'] = $request->role;
        } elseif ($ruanganBaru) {
            $data['role'] = 'Admin Ruangan';
        }
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeKelolaUser();

        $user = User::findOrFail($id);
        abort_if(Auth::user()->isAdminGedung() && ! in_array($user->ruangan_id, Auth::user()->ruanganIds()) && $user->gedung_id !== Auth::user()->gedung_id, 403);
        // Cegah menghapus akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }
        // ponytail: tanpa ini Super Admin terakhir bisa dihapus dan menu
        // Kelola Gedung/Pengguna tak bisa dibuka siapa pun lagi.
        if ($user->isSuperAdmin() && !User::where('role', 'Super Admin')->where('id', '!=', $user->id)->exists()) {
            return back()->with('error', 'Gagal! Ini Super Admin terakhir. Tunjuk super admin lain sebelum menghapusnya.');
        }

        if ($error = $this->cekAdminTerakhir($user->ruangan_id, $user->id)) {
            return back()->with('error', $error);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
