@extends('layouts.app_simalab')

@section('title', 'Dashboard | SIMALAB ITDA')
@section('header', 'Ringkasan Sistem')
@section('activeMenu', 'dashboard')

@section('content')
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-2xl p-5 sm:p-8 text-white shadow-lg border border-blue-700">
        <i data-lucide="plane-takeoff" class="absolute -right-8 -bottom-12 w-64 h-64 text-white opacity-10 transform -rotate-12"></i>
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <a href="{{ route('barang.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-6 hover:border-blue-300 hover:shadow-md transition-all">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600 group-hover:text-blue-700">Total Aset Lab</h3>
                <div class="p-2 bg-slate-100 rounded-lg group-hover:bg-blue-50 transition-colors"><i data-lucide="layers" class="h-4 w-4 text-slate-600 group-hover:text-blue-700"></i></div>
            </div>
            <div class="p-6 pt-0 flex items-center justify-between">
                <div class="text-3xl font-bold text-slate-900 group-hover:text-blue-700">{{ $total_barang ?? 0 }}</div>
                <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>
        <a href="{{ route('barang.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-6 hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600 group-hover:text-emerald-700">Tersedia (Ready)</h3>
                <div class="p-2 bg-emerald-50 rounded-lg group-hover:bg-emerald-100 transition-colors"><i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i></div>
            </div>
            <div class="p-6 pt-0 flex items-center justify-between">
                <div class="text-3xl font-bold text-emerald-600">{{ $barang_tersedia ?? 0 }}</div>
                <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>
        <a href="{{ route('peminjaman.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-6 hover:border-orange-300 hover:shadow-md transition-all">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-semibold text-slate-600 group-hover:text-orange-700">Sedang Dipinjam</h3>
                <div class="p-2 bg-orange-50 rounded-lg group-hover:bg-orange-100 transition-colors"><i data-lucide="clock" class="w-4 h-4 text-orange-600"></i></div>
            </div>
            <div class="p-6 pt-0 flex items-center justify-between">
                <div class="text-3xl font-bold text-orange-600">{{ $barang_dipinjam ?? 0 }}</div>
                <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>
    </div>
@endsection
