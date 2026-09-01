@extends('layouts.app_simalab')

@section('title', 'Tambah Barang | SIMALAB ITDA')
@section('header', 'Manajemen Data Barang')
@section('activeMenu', 'barang')

@section('content')
    <a href="{{ route('barang.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Barang
    </a>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-3xl">
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-xl font-bold text-blue-950">Tambah Barang Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Masukkan rincian data aset ke dalam sistem inventaris.</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" required placeholder="Contoh: KMP-001"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Monitor Samsung 24 Inch"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: Elektronik"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kondisi</label>
                        <select name="kondisi" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Lokasi Ruangan</label>
                        @if(Auth::user()->ruangan_id === null)
                            <div class="relative">
                                <select name="ruangan_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                                    <option value="" disabled selected>-- Pilih Ruangan --</option>
                                    @foreach($ruangan as $r)
                                        <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                            </div>
                        @else
                            <input type="text" value="Ruang {{ Auth::user()->ruangan_id }}" disabled
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 bg-slate-50 rounded-lg text-slate-500 cursor-not-allowed">
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Foto (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
