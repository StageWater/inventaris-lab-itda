<?php

namespace App\Http\Controllers;

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

    public function index()
    {
        $this->authorizeSuperAdmin();
        $users = User::with('ruangan')->orderBy('name')->get();
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
            'ruangan_id' => $request->ruangan_id ?: null,
        ]);

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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'ruangan_id' => $request->ruangan_id ?: null,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
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
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
