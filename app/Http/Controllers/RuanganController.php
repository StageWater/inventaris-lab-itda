<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Opsional: Boleh ditambahkan use App\Models\Ruangan; di sini jika mau lebih rapi

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Mengambil semua data dari tabel ruangan menggunakan Model
        $ruangan = \App\Models\Ruangan::all();

        // 2. Mengirim data tersebut ke file tampilan (View) ruangan/index.blade.php
        return view('ruangan.index', compact('ruangan'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Membuka file tampilan form tambah data
        return view('ruangan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $ruangan = new \App\Models\Ruangan;
        
        // Menangkap Kode Ruangan dan Nama Ruangan
        $ruangan->kode_ruangan = $request->kode_ruangan;
        $ruangan->nama_ruangan = $request->nama_ruangan;
        
        // Simpan ke database
        $ruangan->save();

        return redirect()->route('ruangan.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Mencari data ruangan berdasarkan ID, lalu menampilkannya di form edit
        $ruangan = \App\Models\Ruangan::find($id);
        
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, string $id)
    {
        // Mencari data lama, menimpanya dengan data baru, lalu menyimpannya
        $ruangan = \App\Models\Ruangan::find($id);
        
        if ($ruangan) {
            $ruangan->kode_ruangan = $request->kode_ruangan;
            $ruangan->nama_ruangan = $request->nama_ruangan;
            $ruangan->save();
        }

        // Mengarahkan user kembali ke halaman tabel utama
        return redirect()->route('ruangan.index');
    }
    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
    {
        // 1. Cari data ruangan berdasarkan ID yang diklik
        $ruangan = \App\Models\Ruangan::find($id);
        
        // 2. Jika datanya ketemu, hapus dari database
        if ($ruangan) {
            $ruangan->delete();
        }

        // 3. Kembalikan halaman ke tabel awal
        return redirect()->route('ruangan.index');
    }
}