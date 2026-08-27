<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIMALAB ITDA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                <img src="{{ asset('logo-itda.png') }}" alt="Logo ITDA" class="w-full h-full object-contain" 
                     onerror="this.style.display='none'; document.getElementById('ikon-cadangan').style.display='block';">
                <i id="ikon-cadangan" data-lucide="plane" class="text-blue-800 w-6 h-6 hidden"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-lg tracking-tight text-white leading-tight">SIMALAB<span class="text-blue-400 font-normal">.</span></span>
                <span class="text-[9px] text-blue-200 uppercase tracking-widest mt-0.5">Dirgantara Adisutjipto</span>
            </div>
        </div>
        
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Menu Dashboard Aktif -->
            <a href="#" class="flex items-center px-3 py-2 text-sm font-semibold bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 text-blue-700"></i> Dashboard
            </a>
            <a href="{{ route('barang.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md transition-colors">
                <i data-lucide="box" class="w-4 h-4 mr-3 text-slate-400"></i> Data Barang
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
                    <p class="text-[11px] text-slate-500 font-medium">
                        {{ Auth::user()->ruangan_id == null ? 'Super Admin' : 'Admin Ruang ' . Auth::user()->ruangan_id }}
                    </p>
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
            <h1 class="text-lg font-semibold text-slate-800">Ringkasan Sistem</h1>
            <a href="{{ route('surat.bebas.lab') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-all bg-blue-700 text-white hover:bg-blue-800 shadow-sm hover:shadow-md h-9 px-4 py-2">
                <i data-lucide="file-check" class="w-4 h-4 mr-2"></i> Surat Bebas Lab
            </a>
        </header>

        <div class="p-8 max-w-7xl mx-auto space-y-6">
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-2xl p-8 text-white shadow-lg border border-blue-700">
                <i data-lucide="plane-takeoff" class="absolute -right-8 -bottom-12 w-64 h-64 text-white opacity-10 transform -rotate-12"></i>
                <div class="relative z-10">
                    <div class="inline-block px-3 py-1 bg-blue-950/40 rounded-full text-[11px] font-semibold tracking-wider uppercase backdrop-blur-sm border border-blue-400/30 mb-4">
                        Pusat Kendali Inventaris
                    </div>
                    <h2 class="text-3xl font-bold mb-2">Institut Teknologi Dirgantara Adisutjipto</h2>
                    <p class="text-blue-100 max-w-xl text-sm leading-relaxed">
                        Sistem Informasi Manajemen Aset dan Laboratorium (SIMALAB). Mengelola data ketersediaan barang dan riwayat peminjaman fasilitas kampus secara terpadu dan *real-time*.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:border-blue-300 transition-colors">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-semibold text-slate-600">Total Aset Lab</h3>
                        <div class="p-2 bg-slate-100 rounded-lg"><i data-lucide="layers" class="h-4 w-4 text-slate-600"></i></div>
                    </div>
                    <div class="p-6 pt-0"><div class="text-3xl font-bold text-slate-900">{{ $total_barang ?? 0 }}</div></div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:border-emerald-300 transition-colors">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-semibold text-slate-600">Tersedia (Ready)</h3>
                        <div class="p-2 bg-emerald-50 rounded-lg"><i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i></div>
                    </div>
                    <div class="p-6 pt-0"><div class="text-3xl font-bold text-emerald-600">{{ $barang_tersedia ?? 0 }}</div></div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:border-orange-300 transition-colors">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-semibold text-slate-600">Sedang Dipinjam</h3>
                        <div class="p-2 bg-orange-50 rounded-lg"><i data-lucide="clock" class="h-4 w-4 text-orange-600"></i></div>
                    </div>
                    <div class="p-6 pt-0"><div class="text-3xl font-bold text-orange-600">{{ $barang_dipinjam ?? 0 }}</div></div>
                </div>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>