@extends('layouts.app_simalab')

@section('title', 'Import Barang | SIMALAB ITDA')
@section('header', 'Manajemen Data Barang')
@section('activeMenu', 'barang')

@section('breadcrumbs')
<a href="{{ route('barang.index') }}" class="hover:text-blue-700 transition-colors font-medium">Manajemen Data Barang</a>
<i data-lucide="chevron-right" class="w-3 h-3 mx-1 inline-block"></i>
<span class="text-slate-700 font-medium">Import</span>
@endsection

@section('content')
    <a href="{{ route('barang.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Barang
    </a>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-3xl">
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-xl font-bold text-blue-950">Import Barang dari Excel</h2>
            <p class="text-sm text-slate-500 mt-1">
                Unggah file inventaris (.xls / .xlsx). Setiap sheet / ruangan di dalam file akan dibuat sebagai ruangan baru,
                lalu setiap baris datanya masuk ke tabel barang.
            </p>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('barang.import.proses') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">File Excel</label>
                    <input type="file" name="file" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                    <p class="text-xs text-slate-400 mt-1">Format yang didukung: No. Urut / No. Induk / No. Kode / Nama Barang, atau format DAFTAR INVENTARIS RUANG (NO / NAMA / JML / Keadaan B-RS-RB).</p>
                </div>

                <div class="pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end sm:space-x-3 space-y-reverse space-y-3 border-t border-slate-100">
                    <a href="{{ route('barang.index') }}" class="px-5 py-2.5 text-center text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-center text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center justify-center">
                        <i data-lucide="upload" class="w-4 h-4 mr-2"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection