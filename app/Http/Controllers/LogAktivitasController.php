<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->latest();

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