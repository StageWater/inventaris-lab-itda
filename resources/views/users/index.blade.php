@extends('layouts.app_simalab')

@section('title', 'Kelola Pengguna | SIMALAB ITDA')
@section('header', 'Manajemen Pengguna')
@section('activeMenu', 'users')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-950">Daftar Akun Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola akun Super Admin dan Admin Ruangan.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Tambah Pengguna
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">
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
                        <td class="px-6 py-4 text-right space-x-3">
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
    </div>
@endsection