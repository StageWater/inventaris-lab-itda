@extends('layouts.app_simalab')

@section('title', 'Tambah Admin Ruangan | SIMALAB ITDA')
@section('header', 'Tambah Admin Ruangan')
@section('activeMenu', 'ruangan')

@section('breadcrumbs')
    <a href="{{ route('ruangan.index') }}" class="hover:text-blue-700 transition-colors font-medium">Kelola Ruangan</a>
    <i data-lucide="chevron-right" class="w-3 h-3 mx-1 inline-block shrink-0"></i>
    <span class="text-slate-700 font-medium truncate">Tambah Admin</span>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        @if($ruangan->isEmpty())
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm px-6 py-16 text-center">
                <i data-lucide="user-check" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                <p class="text-slate-500">Semua ruangan sudah punya admin.</p>
                <a href="{{ route('ruangan.index') }}" class="inline-block mt-4 text-sm font-semibold text-blue-700 hover:text-blue-900">Kembali ke daftar ruangan</a>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm flex items-start">
                <i data-lucide="info" class="w-5 h-5 mt-0.5 mr-2 shrink-0 text-blue-600"></i>
                <span>
                    <strong>{{ $ruangan->count() }} ruangan</strong> belum punya admin, jadi barangnya hanya bisa dikelola Super Admin.
                    Isi nama, email, dan password-nya, lalu simpan sekali jalan. Baris yang dikosongkan akan dilewati.
                </span>
            </div>

            <form method="POST" action="{{ route('ruangan.admin.store') }}">
                @csrf

                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="text" id="sandi-bersama" autocomplete="off"
                        placeholder="Password sama untuk semua baris"
                        class="w-full sm:max-w-xs px-3 py-2 text-sm border border-amber-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-700">
                    <button type="button" onclick="terapkanSandiBersama()"
                        class="inline-flex items-center justify-center shrink-0 px-4 h-10 text-sm font-semibold text-amber-900 bg-amber-200 hover:bg-amber-300 rounded-lg transition-colors">
                        <i data-lucide="copy-check" class="w-4 h-4 mr-2"></i> Terapkan ke Baris Terisi
                    </button>
                    <p class="text-xs text-amber-800 sm:ml-auto sm:text-right">
                        Password minimal 8 karakter.<br>
                        Lupa? bisa di-reset dari menu Kelola Pengguna.
                    </p>
                </div>

                <div class="mt-4 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Email</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Password</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($ruangan as $r)
                                <tr>
                                    <td class="px-6 py-3 align-top">
                                        <div class="font-semibold text-slate-900">{{ $r->nama_ruangan }}</div>
                                        <div class="text-xs text-slate-500">{{ $r->kode_ruangan }}</div>
                                    </td>
                                    <td class="px-6 py-3 align-top">
                                        <input type="text" data-baris="nama" name="admin[{{ $r->id }}][nama]" value="{{ old("admin.{$r->id}.nama") }}" maxlength="255"
                                            class="w-full min-w-40 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
                                        @error("admin.{$r->id}.nama")
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-3 align-top">
                                        <input type="email" data-baris="email" name="admin[{{ $r->id }}][email]" value="{{ old("admin.{$r->id}.email") }}" maxlength="255"
                                            class="w-full min-w-40 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
                                        @error("admin.{$r->id}.email")
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-3 align-top">
                                        <div class="flex items-center gap-2">
                                            <input type="password" data-baris="password" name="admin[{{ $r->id }}][password]" value="{{ old("admin.{$r->id}.password") }}"
                                                autocomplete="new-password" placeholder="min. 8 karakter"
                                                class="w-full min-w-40 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
                                            <button type="button" onclick="toggleSandi(this)"
                                                class="shrink-0 text-xs font-semibold text-slate-500 hover:text-blue-700 px-2 py-1 rounded-md hover:bg-slate-100 transition-colors">
                                                Lihat
                                            </button>
                                        </div>
                                        @error("admin.{$r->id}.password")
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-lg shadow-sm transition-colors flex-1">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Buat Akun yang Terisi
                    </button>
                    <a href="{{ route('ruangan.index') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors flex-1 sm:flex-none">
                        Kembali
                    </a>
                </div>
            </form>
        @endif
    </div>

    <script>
        // Password sengaja diketik Super Admin, bukan di-random: string acak
        // susah dibaca dan sering salah salin, sedangkan Super Admin berada
        // di ruangan yang sama dan bisa membacanya langsung.
        function toggleSandi(tombol) {
            const input = tombol.previousElementSibling;
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            tombol.textContent = tampil ? 'Sembunyi' : 'Lihat';
        }

        // Password yang sama untuk semua admin. Hanya baris yang nama + email-nya
        // sudah terisi, supaya baris yang sengaja dikosongkan tidak ikut terkirim.
        function terapkanSandiBersama() {
            const sandi = document.getElementById('sandi-bersama').value;
            if (!sandi) {
                alert('Isi passwordnya dulu.');
                return;
            }

            let jumlah = 0;
            document.querySelectorAll('tbody tr').forEach(tr => {
                const nama = tr.querySelector('[data-baris="nama"]').value.trim();
                const email = tr.querySelector('[data-baris="email"]').value.trim();
                if (!nama || !email) return;

                tr.querySelector('[data-baris="password"]').value = sandi;
                jumlah++;
            });

            if (jumlah === 0) {
                alert('Belum ada baris yang nama dan emailnya lengkap.');
            }
        }
    </script>
@endsection
