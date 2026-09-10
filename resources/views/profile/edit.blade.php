@extends('layouts.app_simalab')

@section('title', 'Profil Saya | SIMALAB ITDA')
@section('header', 'Profil Saya')
@section('activeMenu', 'profile')

@section('content')
    <div class="max-w-2xl space-y-6">

        {{-- Informasi Profil --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h2 class="text-xl font-bold text-blue-950">Informasi Profil</h2>
                <p class="text-sm text-slate-500 mt-1">Perbarui nama dan alamat email akun Anda.</p>
            </div>
            <div class="p-6">
                @if(session('status') === 'profile-updated')
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 shrink-0"></i> Profil berhasil diperbarui.
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all inline-flex items-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h2 class="text-xl font-bold text-blue-950">Ubah Password</h2>
                <p class="text-sm text-slate-500 mt-1">Pastikan akun Anda menggunakan password yang kuat.</p>
            </div>
            <div class="p-6">
                @if(session('status') === 'password-updated')
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 shrink-0"></i> Password berhasil diperbarui.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password Lama</label>
                        <input type="password" name="current_password" required autocomplete="current-password"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                        @error('password', 'updatePassword')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition-all inline-flex items-center">
                            <i data-lucide="key-round" class="w-4 h-4 mr-2"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
