<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->latest();

        // Scope 3-tier: Admin Ruangan se-ruangan, Admin Gedung se-gedung
        if (! is_null($ids = Auth::user()->ruanganIds())) {
            $me = Auth::user();
            $query->where(function ($q) use ($me, $ids) {
                $q->whereHas('user', fn ($qq) => $qq->whereIn('ruangan_id', $ids ?: [0]));
                if ($me->isAdminGedung()) {
                    $q->orWhereHas('user', fn ($qq) => $qq->where('gedung_id', $me->gedung_id));
                }
            });
        }

        if ($kata = $request->kata) {
            $query->where(function ($q) use ($kata) {
                $q->where('aksi', 'like', "%$kata%")
                  ->orWhere('deskripsi', 'like', "%$kata%");
            });
        }

        $logs = $query->paginate(15);
        return view('log.index', compact('logs'));
    }
}