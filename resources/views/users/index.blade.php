@extends('layouts.app_simalab')

@section('title', 'Kelola Pengguna | SIMALAB ITDA')
@section('header', 'Manajemen Pengguna')
@section('activeMenu', 'users')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-blue-950">Daftar Akun Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola akun Super Admin dan Admin Ruangan.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5 flex-1 sm:flex-none">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Tambah Pengguna
        </a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
        <div class="relative w-full md:max-w-sm">
            <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-slate-400"></i>
            <input type="text" name="katakunci" value="{{ request('katakunci') }}" placeholder="Cari nama / email pengguna..."
                class="w-full pl-10 pr-20 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            <button type="submit" class="absolute right-1.5 top-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-md">Cari</button>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nama</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Email</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Peran</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Penempatan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" data-reveal-stagger>
                    @forelse($users as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900 min-w-[10rem]">
                            {{ $item->name }}
                            @if($item->id === Auth::id())
                                <span class="ml-1 text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5">Anda</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $item->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->ruangan_id === null ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $item->ruangan_id === null ? 'Super Admin' : 'Admin Ruangan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->ruangan->nama_ruangan ?? 'Global' }}</td>
                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('users.edit', $item->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i> Edit
                            </a>
                            @if($item->id !== Auth::id())
                            <form action="{{ route('users.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center text-rose-600 hover:text-rose-800 font-medium transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Belum ada pengguna terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 md:hidden" data-reveal-stagger>
            @forelse($users as $item)
            <div class="p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-900 truncate">
                            {{ $item->name }}
                            @if($item->id === Auth::id())
                                <span class="ml-1 text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5">Anda</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $item->email }}</div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 border-t border-slate-100 pt-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->ruangan_id === null ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                        {{ $item->ruangan_id === null ? 'Super Admin' : 'Admin Ruangan' }}
                    </span>
                    <span>
                        <i data-lucide="door-open" class="w-3.5 h-3.5 inline-block text-slate-400 mr-1"></i>
                        {{ $item->ruangan->nama_ruangan ?? 'Global' }}
                    </span>
                </div>
                <div class="flex items-center gap-5 pt-1">
                    <a href="{{ route('users.edit', $item->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                        <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i> Edit
                    </a>
                    @if($item->id !== Auth::id())
                    <form action="{{ route('users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center text-rose-600 hover:text-rose-800 font-medium text-sm transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-slate-400">
                <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                <p>Belum ada pengguna terdaftar.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
@endsection