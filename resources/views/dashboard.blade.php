@extends('layouts.app_simalab')

@section('title', 'Dashboard | SIMALAB ITDA')
@section('header', 'Ringkasan Sistem')
@section('activeMenu', 'dashboard')

@section('content')
<div class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-2xl p-6 sm:p-8 text-white shadow-lg border border-blue-700">
        <i data-lucide="plane-takeoff" class="absolute -right-8 -bottom-12 w-56 sm:w-64 h-56 sm:h-64 text-white opacity-10 transform -rotate-12"></i>
        <div class="relative z-10">
            <div class="inline-block px-3 py-1 bg-blue-950/40 rounded-full text-[11px] font-semibold tracking-wider uppercase backdrop-blur-sm border border-blue-400/30 mb-4">
                Pusat Kendali Inventaris
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold mb-2 leading-tight">Institut Teknologi Dirgantara Adisutjipto</h2>
            <p class="text-blue-100 max-w-xl text-sm leading-relaxed">
                Sistem Informasi Manajemen Aset dan Laboratorium (SIMALAB). Mengelola data ketersediaan barang dan riwayat peminjaman fasilitas kampus secara terpadu dan real-time.
            </p>
        </div>
    </div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6" data-reveal-stagger>
        <a href="{{ route('barang.index') }}" class="block rounded-xl border border-slate-200 bg-white shadow-sm hover:border-blue-300 hover:shadow-md transition-all group">
            <div class="p-6 flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600">Total Aset Lab</h3>
                <div class="p-2 bg-slate-100 rounded-lg"><i data-lucide="layers" class="h-4 w-4 text-slate-600"></i></div>
            </div>
            <div class="px-6 pt-0"><div class="text-3xl font-bold text-slate-900">{{ $total_barang ?? 0 }}</div></div>
            <div class="px-6 pb-5 pt-3">
                <span class="inline-flex items-center text-xs font-semibold text-blue-700 group-hover:text-blue-800 transition-colors">
                    Lihat semua aset <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </span>
            </div>
        </a>
        <a href="{{ route('barang.index', ['status' => 'Tersedia']) }}" class="block rounded-xl border border-slate-200 bg-white shadow-sm hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="p-6 flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600">Tersedia (Ready)</h3>
                <div class="p-2 bg-emerald-50 rounded-lg"><i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i></div>
            </div>
            <div class="px-6 pt-0"><div class="text-3xl font-bold text-emerald-600">{{ $barang_tersedia ?? 0 }}</div></div>
            <div class="px-6 pb-5 pt-3">
                <span class="inline-flex items-center text-xs font-semibold text-emerald-700 group-hover:text-emerald-800 transition-colors">
                    Lihat barang siap pakai <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </span>
            </div>
        </a>
        <a href="{{ route('barang.index', ['status' => 'Dipinjam']) }}" class="block rounded-xl border border-slate-200 bg-white shadow-sm hover:border-orange-300 hover:shadow-md transition-all group">
            <div class="p-6 flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600">Sedang Dipinjam</h3>
                <div class="p-2 bg-orange-50 rounded-lg"><i data-lucide="clock" class="h-4 w-4 text-orange-600"></i></div>
            </div>
            <div class="px-6 pt-0"><div class="text-3xl font-bold text-orange-600">{{ $barang_dipinjam ?? 0 }}</div></div>
            <div class="px-6 pb-5 pt-3">
                <span class="inline-flex items-center text-xs font-semibold text-orange-700 group-hover:text-orange-800 transition-colors">
                    Lihat barang dipinjam <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </span>
            </div>
        </a>
        <a href="{{ route('barang.index', ['rusak' => 1]) }}" class="block rounded-xl border border-slate-200 bg-white shadow-sm hover:border-rose-300 hover:shadow-md transition-all group">
            <div class="p-6 flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600">Perlu Perbaikan</h3>
                <div class="p-2 bg-rose-50 rounded-lg"><i data-lucide="wrench" class="h-4 w-4 text-rose-600"></i></div>
            </div>
            <div class="px-6 pt-0"><div class="text-3xl font-bold text-rose-600">{{ $barang_rusak ?? 0 }}</div></div>
            <div class="px-6 pb-5 pt-3">
                <span class="inline-flex items-center text-xs font-semibold text-rose-700 group-hover:text-rose-800 transition-colors">
                    Lihat barang rusak <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </span>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" data-reveal-stagger>
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-slate-800">Aktivitas Terbaru</h3>
                <a href="{{ route('log.index') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900 hover:underline shrink-0">Lihat semua</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($logAktivitas ?? [] as $log)
                <div class="flex items-start gap-3 px-6 py-3.5">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700 leading-snug">{{ $log->deskripsi }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $log->user->name ?? 'Sistem' }} · {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-slate-400 text-sm">
                    Belum ada aktivitas tercatat.
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800">Akses Cepat</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('barang.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-3 text-slate-400"></i> Tambah Barang
                </a>
                <a href="{{ route('peminjaman.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-3 text-slate-400"></i> Catat Peminjaman
                </a>
                <a href="{{ route('peminjaman.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="arrow-right-left" class="w-4 h-4 mr-3 text-slate-400"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('barang.cetak') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 mr-3 text-slate-400"></i> Cetak PDF Barang
                </a>
                @if(Auth::user()->ruangan_id === null)
                <a href="{{ route('users.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-3 text-slate-400"></i> Tambah Pengguna
                </a>
                <a href="{{ route('surat.bebas.lab') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    <i data-lucide="file-check" class="w-4 h-4 mr-3 text-slate-400"></i> Surat Bebas Lab
                </a>
                @endif
            </div>
        </div>
    </div>
@endsection