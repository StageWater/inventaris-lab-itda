@extends('layouts.app_simalab')

@section('title', 'Transaksi Peminjaman | SIMALAB ITDA')
@section('header', 'Manajemen Transaksi')
@section('activeMenu', 'peminjaman')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-950">Riwayat Peminjaman</h2>
            <p class="text-sm text-slate-500 mt-1">Catat dan pantau sirkulasi peminjaman aset laboratorium.</p>
        </div>
        <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5 shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Catat Peminjaman
        </a>
    </div>

    @if(Auth::user()->ruangan_id === null)
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
            <div class="sm:w-72">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Filter Ruangan</label>
                <select name="ruangan_id" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($ruangan as $ruang)
                        <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-row gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-slate-700 text-white hover:bg-slate-800 h-10 px-5 flex-1 sm:flex-none">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i> Terapkan
                </button>
                @if(request('ruangan_id'))
                <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 h-10 px-5 flex-1 sm:flex-none">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i> Reset
                </a>
                @endif
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nama Mahasiswa</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">NIM</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Barang Dipinjam</th>
                        @if(Auth::user()->ruangan_id === null)
                        <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                        @endif
                        <th class="px-6 py-4 font-semibold tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Tgl. Pengembalian</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Berkas</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peminjaman as $pinjam)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $pinjam->nama_peminjam }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $pinjam->nim ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-blue-700">{{ $pinjam->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                        @if(Auth::user()->ruangan_id === null)
                        <td class="px-6 py-4 text-slate-500">{{ optional($pinjam->barang)->ruangan->nama_ruangan ?? '-' }}</td>
                        @endif
                        <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $pinjam->tanggal_pengembalian ? \Carbon\Carbon::parse($pinjam->tanggal_pengembalian)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">
                            @if($pinjam->berkas)
                                <a href="{{ asset('storage/' . $pinjam->berkas) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> Lihat Berkas
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($pinjam->status_pinjam == 'Dipinjam')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i> Dipinjam
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i> Dikembalikan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <!-- Tombol Kembalikan hanya muncul saat masih dipinjam -->
                            @if($pinjam->status_pinjam == 'Dipinjam')
                            <form action="{{ route('peminjaman.kembalikan', $pinjam->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center text-emerald-600 hover:text-emerald-800 font-medium transition-colors">
                                    <i data-lucide="undo-2" class="w-4 h-4 mr-1"></i> Kembalikan
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('peminjaman.destroy', $pinjam->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin menghapus riwayat ini?');">
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
                        <td colspan="{{ Auth::user()->ruangan_id === null ? 9 : 8 }}" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="clipboard-list" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Belum ada riwayat transaksi peminjaman.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
