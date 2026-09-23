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

        // RBAC: Admin Ruangan hanya melihat aktivitas pelaku di ruangannya sendiri;
        // aktivitas Super Admin (ruangan_id null) tidak ditampilkan ke Admin Ruangan.
        if (Auth::user()->ruangan_id != null) {
            $query->whereHas('user', fn ($q) => $q->where('ruangan_id', Auth::user()->ruangan_id));
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