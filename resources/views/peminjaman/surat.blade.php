@extends('layouts.app_simalab')

@section('title', 'Surat Bebas Lab | SIMALAB ITDA')
@section('header', 'Surat Bebas Lab')
@section('activeMenu', 'surat')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50 text-center">
                <div class="mx-auto w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <i data-lucide="file-check" class="w-7 h-7 text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-blue-950">Cetak Surat Bebas Lab</h2>
                <p class="text-sm text-slate-500 mt-1">Masukkan Nama atau NIM mahasiswa untuk mengecek tanggungan peminjaman.</p>
            </div>

            <div class="p-6">
                @if(session('error'))
                    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 shrink-0"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('surat.bebas.lab') }}" method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama / NIM Mahasiswa</label>
                        <input type="text" name="nama" value="{{ request('nama') }}" required placeholder="Ketik Nama / NIM Mahasiswa..."
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center justify-center">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Cek & Download PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
