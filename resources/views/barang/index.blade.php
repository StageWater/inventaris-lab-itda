@extends('layouts.app_simalab')

@section('title', 'Data Barang | SIMALAB ITDA')
@section('header', 'Manajemen Data Barang')
@section('activeMenu', 'barang')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-950">Daftar Inventaris</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola seluruh data barang atau aset yang ada di laboratorium.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('barang.cetak') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium text-blue-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm h-10 px-4">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
            </a>
            <a href="{{ route('barang.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Barang
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('barang.index') }}" class="mb-4">
        <div class="relative max-w-sm">
            <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-slate-400"></i>
            <input type="text" name="katakunci" value="{{ request('katakunci') }}" placeholder="Cari kode / nama barang..."
                class="w-full pl-10 pr-20 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            <button type="submit" class="absolute right-1.5 top-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-md">Cari</button>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Kode Barang</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nama Barang</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Kondisi</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($barang as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-blue-950">{{ $item->kode_barang }}</td>
                        <td class="px-6 py-4">{{ $item->nama_barang }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->ruangan->nama_ruangan ?? 'Ruang ' . $item->ruangan_id }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                {{ $item->kondisi }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'Tersedia')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Tersedia</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">Dipinjam</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('barang.edit', $item->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i> Edit
                            </a>
                            <form action="{{ route('barang.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
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
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Belum ada data barang.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
