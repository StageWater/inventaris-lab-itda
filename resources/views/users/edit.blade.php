@extends('layouts.app_simalab')

@section('title', 'Edit Pengguna | SIMALAB ITDA')
@section('header', 'Manajemen Pengguna')
@section('activeMenu', 'users')

@section('content')
    <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Pengguna
    </a>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-w-2xl">
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-xl font-bold text-blue-950">Edit Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi akun pengguna.</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Kosong untuk tidak ubah"
                        class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                    <p class="text-xs text-slate-500 mt-1">Simpan tetap untuk tidak ubah password.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Penempatan Ruangan</label>
                    <div class="relative">
                        <select name="ruangan_id" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                            <option value="" {{ $user->ruangan_id === null ? 'selected' : '' }}>Jadikan Super Admin Utama</option>
                            @foreach($ruangan as $r)
                                <option value="{{ $r->id }}" {{ $user->ruangan_id == $r->id ? 'selected' : '' }}>
                                    {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection