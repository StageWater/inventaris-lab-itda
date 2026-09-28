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
    <div class="max-w-4xl mx-auto space-y-6">

        @if(session('admin_baru') && count(session('admin_baru')))
            <div class="bg-amber-50 border-2 border-amber-300 rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-amber-100 border-b border-amber-200 flex items-start gap-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mt-0.5 text-amber-700 shrink-0"></i>
                    <div>
                        <h3 class="font-bold text-amber-900">Simpan password ini sekarang</h3>
                        <p class="text-sm text-amber-800 mt-0.5">
                            Halaman ini hanya ditampilkan sekali. Setelah menutup halaman, password tidak bisa dilihat lagi --
                            kalau hilang, harus reset lewat menu pengguna.
                        </p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-amber-800 uppercase bg-amber-50 border-b border-amber-200">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Ruangan</th>
                                <th class="px-6 py-3 font-semibold">Nama</th>
                                <th class="px-6 py-3 font-semibold">Email</th>
                                <th class="px-6 py-3 font-semibold">Password</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-200 bg-white">
                            @foreach(session('admin_baru') as $a)
                            <tr>
                                <td class="px-6 py-3 text-slate-600">{{ $a['ruangan'] }}</td>
                                <td class="px-6 py-3 text-slate-800">{{ $a['nama'] }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $a['email'] }}</td>
                                <td class="px-6 py-3">
                                    <code class="px-2 py-1 bg-slate-900 text-slate-50 rounded text-xs font-semibold">{{ $a['sandi'] }}</code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

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
                    Isi nama dan email penanggung jawabnya, lalu simpan sekali jalan. Baris yang dikosongkan akan dilewati.
                </span>
            </div>

            <form method="POST" action="{{ route('ruangan.admin.store') }}">
                @csrf

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($ruangan as $r)
                                <tr>
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-slate-900">{{ $r->nama_ruangan }}</div>
                                        <div class="text-xs text-slate-500">{{ $r->kode_ruangan }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="text" name="admin[{{ $r->id }}][nama]" value="{{ old("admin.{$r->id}.nama") }}" maxlength="255"
                                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
                                        @error("admin.{$r->id}.nama")
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="email" name="admin[{{ $r->id }}][email]" value="{{ old("admin.{$r->id}.email") }}" maxlength="255"
                                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-700">
                                        @error("admin.{$r->id}.email")
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
@endsection
