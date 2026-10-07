@extends('layouts.app_simalab')

@section('title', 'Kelola Gedung | SIMALAB ITDA')
@section('header', 'Kelola Gedung')
@section('activeMenu', 'gedung')

@section('content')
<div class="mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-blue-950">Kelola Gedung</h2>
    <p class="text-sm text-slate-500 mt-1">Satu gedung = satu Admin Gedung. Isi ruangan lewat Kelola Ruangan.</p>
</div>

<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-2xl mb-6">
    <form action="{{ route('gedung.store') }}" method="POST" class="p-6 flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="text" name="nama_gedung" value="{{ old('nama_gedung') }}" required placeholder="Nama gedung (cth: Gedung A)"
            class="flex-1 px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700 placeholder-slate-400">
        <input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Lokasi (opsional)"
            class="flex-1 px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700 placeholder-slate-400">
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Tambah</button>
    </form>
</div>

<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-semibold tracking-wider">Nama Gedung</th>
                    <th class="px-6 py-4 font-semibold tracking-wider">Lokasi</th>
                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Ruangan</th>
                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Pengguna</th>
                    <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($gedung as $item)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $item->nama_gedung }}</td>
                    <td class="px-6 py-4">{{ $item->lokasi ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">{{ $item->ruangans_count }}</td>
                    <td class="px-6 py-4 text-center">{{ $item->users_count }}</td>
                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                        <a href="{{ route('gedung.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('gedung.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus gedung ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada gedung. Tambah satu lewat form di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
