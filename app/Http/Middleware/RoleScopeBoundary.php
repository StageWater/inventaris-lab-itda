<?php

namespace App\Http\Middleware;

use App\Models\Barang;
use App\Models\Gedung;
use App\Models\Ruangan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleScopeBoundary
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // ponytail: fail-closed bila tanpa user; pasang sesudah 'auth' agar redirect login tetap jalan.
        if (! $user) {
            abort(403);
        }

        if ($user->role === 'Super Admin') {
            return $next($request);
        }

        if ($user->role === 'Admin Gedung') {
            if (! $user->gedung_id) {
                abort(403, 'Akses ditolak: Anda tidak memiliki otoritas pada gedung ini');
            }
            $wanted = $this->requestedGedungId($request);
            if ($wanted !== null && (int) $wanted !== (int) $user->gedung_id) {
                abort(403, 'Akses ditolak: Anda tidak memiliki otoritas pada gedung ini');
            }

            return $next($request);
        }

        if ($user->role === 'Admin Ruangan') {
            if (! $user->ruangan_id) {
                abort(403, 'Akses ditolak: Anda tidak memiliki otoritas pada ruangan ini');
            }
            $wanted = $this->requestedRuanganId($request);
            if ($wanted !== null && (int) $wanted !== (int) $user->ruangan_id) {
                abort(403, 'Akses ditolak: Anda tidak memiliki otoritas pada ruangan ini');
            }

            return $next($request);
        }

        abort(403);
    }

    private function requestedRuanganId(Request $request): ?int
    {
        // $request->input() mencakup query string + body + JSON sekaligus.
        if ($request->input('ruangan_id') !== null) {
            return (int) $request->input('ruangan_id');
        }

        foreach (['ruangan', 'ruangan_id'] as $key) {
            $v = $request->route($key);
            if ($v instanceof Ruangan) {
                return (int) $v->getKey();
            }
            if ($v !== null) {
                return (int) $v;
            }
        }

        // Turunan: {barang} milik ruangan mana.
        // ponytail: 1 query ringan hanya bila param barang ada; peminjaman/surat
        // tak di-resolve di sini -- saring via ruanganIds() di controller.
        $barang = $request->route('barang') ?? $request->route('barang_id') ?? $request->input('barang_id');
        if ($barang instanceof Barang) {
            return $barang->ruangan_id !== null ? (int) $barang->ruangan_id : null;
        }
        if ($barang !== null) {
            $id = Barang::whereKey($barang)->value('ruangan_id');

            return $id !== null ? (int) $id : null;
        }

        return null;
    }

    private function requestedGedungId(Request $request): ?int
    {
        if ($request->input('gedung_id') !== null) {
            return (int) $request->input('gedung_id');
        }

        foreach (['gedung', 'gedung_id'] as $key) {
            $v = $request->route($key);
            if ($v instanceof Gedung) {
                return (int) $v->getKey();
            }
            if ($v !== null) {
                return (int) $v;
            }
        }

        // Turunan: ruangan/barang -> petakan ke gedung pemiliknya.
        // ponytail: ruangan legacy tanpa gedung (null) lolos di sini; backstop-nya
        // tetap ruanganIds() di controller sampai 31 ruangan lama diisi gedungnya.
        $ruanganId = $this->requestedRuanganId($request);
        if ($ruanganId !== null) {
            $gid = Ruangan::whereKey($ruanganId)->value('gedung_id');

            return $gid !== null ? (int) $gid : null;
        }

        return null;
    }
}
