<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang | SIMALAB ITDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col z-20">
        <div class="h-20 flex items-center px-5 border-b border-slate-200 bg-blue-950">
            <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center p-1 mr-3 shadow-md shrink-0">
                <img src="{{ asset('logo-itda.png') }}" alt="Logo ITDA" class="w-full h-full object-contain" onerror="this.style.display='none'; document.getElementById('ikon-cadangan').style.display='block';">
                <i id="ikon-cadangan" data-lucide="plane" class="text-blue-800 w-6 h-6 hidden"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-lg tracking-tight text-white leading-tight">SIMALAB<span class="text-blue-400 font-normal">.</span></span>
                <span class="text-[9px] text-blue-200 uppercase tracking-widest mt-0.5">Dirgantara Adisutjipto</span>
            </div>
        </div>
        
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md transition-colors">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 text-slate-400"></i> Dashboard
            </a>
            <!-- Menu Data Barang Aktif -->
            <a href="#" class="flex items-center px-3 py-2 text-sm font-semibold bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md">
                <i data-lucide="box" class="w-4 h-4 mr-3 text-blue-700"></i> Data Barang
            </a>
            <a href="{{ route('peminjaman.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md transition-colors">
                <i data-lucide="arrow-right-left" class="w-4 h-4 mr-3 text-slate-400"></i> Transaksi
            </a>
        </nav>

        <!-- Profil & Tombol Logout -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-500 font-medium">{{ Auth::user()->ruangan_id == null ? 'Super Admin' : 'Admin Ruang ' . Auth::user()->ruangan_id }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-md transition-colors border border-rose-100">
                    <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 overflow-y-auto">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <h1 class="text-lg font-semibold text-slate-800">Manajemen Data Barang</h1>
        </header>

        <div class="p-8 max-w-7xl mx-auto space-y-6">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-blue-950">Daftar Inventaris</h2>
                    <p class="text-sm text-slate-500 mt-1">Kelola seluruh data barang atau aset yang ada di laboratorium.</p>
                </div>
                <a href="{{ route('barang.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm h-10 px-5">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Barang
                </a>
            </div>

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative flex items-center mb-4" role="alert">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                <span class="block sm:inline text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold tracking-wider">Kode Barang</th>
                                <th class="px-6 py-4 font-semibold tracking-wider">Nama Barang</th>
                                <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                                <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                                <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($barang as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-blue-950">{{ $item->kode_barang }}</td>
                                <td class="px-6 py-4">{{ $item->nama_barang }}</td>
                                <td class="px-6 py-4 text-slate-500">Ruang {{ $item->ruangan_id }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status == 'Tersedia')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">Dipinjam</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('barang.edit', $item->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                        <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('barang.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-rose-600 hover:text-rose-800 font-medium transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                    <p>Belum ada data barang.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($barang, 'links'))
                <div class="p-4 border-t border-slate-200">{{ $barang->links() }}</div>
                @endif
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>