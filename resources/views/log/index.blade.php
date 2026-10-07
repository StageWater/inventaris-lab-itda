@extends('layouts.app_simalab')

@section('title', 'Riwayat Aktivitas | SIMALAB ITDA')
@section('header', 'Riwayat Aktivitas')
@section('activeMenu', 'log')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-3 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-blue-950">Riwayat Aktivitas</h2>
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
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Waktu</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Aksi</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Pelaku</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    @php
                        $aksi = $log->aksi ?? '';
                        $badge = str_contains($aksi, 'Hapus') || str_contains($aksi, 'DELETE')
                            ? 'bg-rose-100 text-rose-700 border border-rose-200'
                            : (str_contains($aksi, 'Ubah') || str_contains($aksi, 'UPDATE') || str_contains($aksi, 'Status')
                                ? 'bg-amber-100 text-amber-700 border border-amber-200'
                                : (str_contains($aksi, 'Tambah') || str_contains($aksi, 'INSERT') || str_contains($aksi, 'Import') || str_contains($aksi, 'Catat')
                                    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                                    : 'bg-slate-100 text-slate-600 border border-slate-200'));
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ $aksi }}</span></td>
                        <td class="px-6 py-4 text-slate-700">{{ $log->deskripsi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->nama_user ?? $log->user->name ?? 'Sistem' }}{{ $log->role_user ? ' ('.$log->role_user.')' : '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="history" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Tidak ada riwayat aktivitas ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
@endsection