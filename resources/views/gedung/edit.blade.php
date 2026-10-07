@extends('layouts.app_simalab')

@section('title', 'Edit Gedung | SIMALAB ITDA')
@section('header', 'Kelola Gedung')
@section('activeMenu', 'gedung')

@section('content')
<a href="{{ route('gedung.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6">Kembali ke Kelola Gedung</a>
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-2xl">
    <form action="{{ route('gedung.update', $gedung->id) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Gedung</label>
            <input type="text" name="nama_gedung" value="{{ old('nama_gedung', $gedung->nama_gedung) }}" required
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Lokasi (Opsional)</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $gedung->lokasi) }}"
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
        </div>
        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('gedung.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Update Data</button>
        </div>
    </form>
</div>
@endsection
