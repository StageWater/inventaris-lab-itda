@extends('layouts.app_simalab')

@section('title', 'Edit Ruangan | SIMALAB ITDA')
@section('header', 'Manajemen Ruangan')
@section('activeMenu', 'ruangan')

@section('content')
    <a href="{{ route('ruangan.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Ruangan
    </a>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-2xl">
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-xl font-bold text-blue-950">Edit Ruangan</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi lokasi laboratorium.</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Ruangan</label>
                    <input type="text" name="kode_ruangan" value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}" required
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}" required
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">{{ old('keterangan', $ruangan->keterangan) }}</textarea>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <a href="{{ route('ruangan.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
