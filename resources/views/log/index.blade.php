@extends('layouts.app_simalab')

@section('title', 'Riwayat Aktivitas | SIMALAB ITDA')
@section('header', 'Riwayat Aktivitas')
@section('activeMenu', 'log')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-3 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-blue-950">Log Aktivitas Sistem</h2>
            <p class="text-sm text-slate-500 mt-1">Audit jejak perubahan data: siapa, apa, dan kapan.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('log.index') }}" class="mb-4">
        <div class="relative w-full md:max-w-sm">
            <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-slate-400"></i>
            <input type="text" name="kata" value="{{ request('kata') }}" placeholder="Cari di riwayat..."
                class="w-full pl-10 pr-20 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            <button type="submit" class="absolute right-1.5 top-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-md">Cari</button>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100" data-reveal-stagger>
            @forelse($logs as $log)
            <div class="px-5 sm:px-6 py-4 flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-slate-700 leading-snug">{{ $log->deskripsi }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">{{ $log->aksi }}</span>
                        <span class="ml-1">{{ $log->user->name ?? 'Sistem' }}</span>
                        <span class="mx-1">·</span>
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="px-6 py-14 text-center text-slate-400">
                <i data-lucide="history" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                <p>Tidak ada riwayat aktivitas ditemukan.</p>
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
@endsection