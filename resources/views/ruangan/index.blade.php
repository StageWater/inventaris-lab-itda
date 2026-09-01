@extends('layouts.app_simalab')

@section('title', 'Kelola Ruangan | SIMALAB ITDA')
@section('header', 'Manajemen Ruangan')
@section('activeMenu', 'ruangan')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-950">Daftar Ruangan</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola lokasi laboratorium untuk penempatan aset.</p>
        </div>
        <a href="{{ route('ruangan.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Ruangan
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Kode Ruangan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nama Ruangan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Jumlah Barang</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ruangan as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-blue-950">{{ $item->kode_ruangan }}</td>
                        <td class="px-6 py-4">{{ $item->nama_ruangan }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">
                                {{ $item->barangs_count }} barang
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('ruangan.edit', $item->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i> Edit
                            </a>
                            <form action="{{ route('ruangan.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus ruangan ini? Barang di dalamnya ikut terpengaruh.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center text-rose-600 hover:text-rose-800 font-medium transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="door-open" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Belum ada ruangan terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
