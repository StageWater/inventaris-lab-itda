@extends('layouts.app_simalab')

@section('title', 'Surat Bebas Lab | SIMALAB ITDA')
@section('header', 'Surat Bebas Lab')
@section('activeMenu', 'permohonan')

@section('breadcrumbs')
    <a href="{{ route('permohonan.index') }}" class="hover:text-blue-700 transition-colors font-medium">Surat Bebas Lab</a>
    <i data-lucide="chevron-right" class="w-3 h-3 mx-1 inline-block shrink-0"></i>
    <span class="text-slate-700 font-medium truncate">Edit</span>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center shrink-0">
                        <i data-lucide="file-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-blue-950">Data Permohonan</h2>
                        <p class="text-sm text-amber-600 font-medium"><i data-lucide="clock" class="w-3.5 h-3.5 mr-1 inline"></i>Dapat diubah selama belum dicetak.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('permohonan.update', $surat->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $surat->nim) }}" required maxlength="30"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    @error('nim')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $surat->nama) }}" required maxlength="255"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    @error('nama')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ old('jurusan', $surat->jurusan) }}" required maxlength="255"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 uppercase">
                    @error('jurusan')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Skripsi</label>
                    <textarea name="judul_skripsi" required rows="3" maxlength="255"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">{{ old('judul_skripsi', $surat->judul_skripsi) }}</textarea>
                    @error('judul_skripsi')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}" maxlength="50" placeholder="Contoh: 009/Lab. Terpadu/ITDA/2026"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    <p class="mt-1.5 text-xs text-slate-500">
                        <i data-lucide="info" class="w-3.5 h-3.5 mr-1 inline"></i>
                        Nomor surat terakhir yang terpakai adalah:
                        <strong class="text-blue-700">{{ $lastNomor ?? 'belum ada' }}</strong>
                    </p>
                    @error('nomor_surat')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-lg shadow-sm transition-colors flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('permohonan.index') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors flex-1 sm:flex-none">
                        Kembali
                    </a>
                </div>
            </form>

            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3 border-t border-slate-200 pt-4">
                @if(!$surat->nomor_surat)
                <p class="text-xs text-amber-600 font-medium"><i data-lucide="info" class="w-3.5 h-3.5 mr-1 inline"></i> Isi nomor surat terlebih dahulu agar tombol Cetak aktif.</p>
                @endif
                <div class="flex gap-3">
                    @if($surat->nomor_surat)
                    <form action="{{ route('permohonan.cetak', $surat->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('permohonan.destroy', $surat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin menghapus permohonan ini? NIM tersebut bisa mengajukan ulang.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection