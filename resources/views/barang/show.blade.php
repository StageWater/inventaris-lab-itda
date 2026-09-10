@extends('layouts.app_simalab')

@section('title', 'Detail Barang | SIMALAB ITDA')
@section('header', 'Manajemen Data Barang')
@section('activeMenu', 'barang')

@section('breadcrumbs')
<a href="{{ route('barang.index') }}" class="hover:text-blue-700 transition-colors font-medium">Manajemen Data Barang</a>
<i data-lucide="chevron-right" class="w-3 h-3 mx-1 inline-block"></i>
<span class="text-slate-700 font-medium truncate">{{ $barang->kode_barang }}</span>
@endsection

@section('content')
    <a href="{{ route('barang.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Barang
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" data-reveal-stagger>
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-start gap-5 border-b border-slate-200 bg-slate-50/50">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                    @if($barang->foto)
                        <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-full items-center justify-center text-slate-300">
                            <i data-lucide="box" class="w-10 h-10"></i>
                        </div>
                    @else
                        <i data-lucide="box" class="w-10 h-10 text-slate-300"></i>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-bold text-blue-950">{{ $barang->nama_barang }}</h2>
                        @if($barang->status == 'Tersedia')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Tersedia</span>
                        @elseif($barang->status == 'Maintenance')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">Maintenance</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">Dipinjam</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Kode Barang: <strong class="text-slate-700">{{ $barang->kode_barang }}</strong></p>
                    @if($barang->qr_code)
                        <p class="text-xs text-slate-400 mt-1">QR: {{ $barang->qr_code }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('barang.edit', $barang->id) }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold text-blue-700 bg-white border border-blue-300 hover:bg-blue-50 shadow-sm h-9 px-4 transition-colors">
                            <i data-lucide="edit-3" class="w-4 h-4 mr-2"></i> Edit
                        </a>
                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-md text-sm font-semibold text-rose-700 bg-white border border-rose-300 hover:bg-rose-50 shadow-sm h-9 px-4 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                            </button>
                        </form>
                        @if($barang->status === 'Tersedia' || $barang->status === 'Maintenance')
                        <form action="{{ route('barang.status', $barang->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $barang->status === 'Tersedia' ? 'Maintenance' : 'Tersedia' }}">
                            <button type="submit" class="inline-flex items-center rounded-md text-sm font-semibold text-amber-700 bg-amber-100 border border-amber-300 hover:bg-amber-200/70 shadow-sm h-9 px-4 transition-colors">
                                <i data-lucide="wrench" class="w-4 h-4 mr-2"></i>
                                {{ $barang->status === 'Tersedia' ? 'Tandai Maintenance' : 'Selesai Maintenance' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <dl class="divide-y divide-slate-100">
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1">
                    <dt class="text-sm font-medium text-slate-400">Ruangan</dt>
                    <dd class="text-sm font-medium text-slate-700">{{ $barang->ruangan->nama_ruangan ?? 'Ruang ' . $barang->ruangan_id }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1">
                    <dt class="text-sm font-medium text-slate-400">Kategori</dt>
                    <dd class="text-sm font-medium text-slate-700">{{ $barang->kategori ?? '-' }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1">
                    <dt class="text-sm font-medium text-slate-400">Kondisi</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $barang->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                            {{ $barang->kondisi }}
                        </span>
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1">
                    <dt class="text-sm font-medium text-slate-400">Status</dt>
                    <dd class="text-sm font-medium text-slate-700">{{ $barang->status }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1">
                    <dt class="text-sm font-medium text-slate-400">Keterangan</dt>
                    <dd class="text-sm text-slate-600">{{ $barang->keterangan ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800">Riwayat Peminjaman</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($riwayat as $pinjam)
                <div class="px-5 py-3.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $pinjam->nama_peminjam }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $pinjam->nim ?? '-' }}</p>
                        </div>
                        @if($pinjam->status_pinjam == 'Dipinjam')
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-700 border border-orange-200">Dipinjam</span>
                        @else
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Kembali</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">
                        <i data-lucide="calendar" class="w-3 h-3 inline-block mr-1"></i>
                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }}
                        @if($pinjam->tanggal_kembali)
                            → {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d M Y') }}
                        @endif
                    </p>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-sm text-slate-400">
                    <i data-lucide="clipboard-list" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                    Belum ada riwayat peminjaman.
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection