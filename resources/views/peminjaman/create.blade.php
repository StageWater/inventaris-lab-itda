@extends('layouts.app_simalab')

@section('title', 'Catat Peminjaman | SIMALAB ITDA')
@section('header', 'Manajemen Transaksi')
@section('activeMenu', 'peminjaman')

@section('content')
    <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Riwayat Peminjaman
    </a>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-2xl">
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-xl font-bold text-blue-950">Catat Peminjaman Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Daftarkan proses peminjaman aset kepada mahasiswa.</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Peminjam</label>
                    <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}" required placeholder="Nama / NIM mahasiswa"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIM (Opsional)</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" placeholder="Contoh: 621801234"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Barang (Hanya yang Tersedia)</label>
                    @if($barang->isEmpty())
                        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg text-sm">
                            Tidak ada barang tersedia untuk dipinjam saat ini.
                        </div>
                    @else
                        <div class="relative">
                            <select name="barang_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                                <option value="" disabled selected>-- Pilih Barang --</option>
                                @foreach($barang as $item)
                                    <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->kode_barang }} - {{ $item->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', now()->toDateString()) }}" required
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <a href="{{ route('peminjaman.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
