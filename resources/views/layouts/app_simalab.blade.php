<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMALAB ITDA')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased flex h-screen overflow-hidden">

    @php
        $user = Auth::user();
        $isSuperAdmin = $user->ruangan_id === null;
        $activeMenu = $activeMenu ?? '';
    @endphp

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col z-20 shrink-0">
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
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'dashboard' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ $activeMenu === 'dashboard' ? 'text-blue-700' : 'text-slate-400' }}"></i> Dashboard
            </a>

            @if($isSuperAdmin)
            <a href="{{ route('ruangan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'ruangan' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="door-open" class="w-4 h-4 mr-3 {{ $activeMenu === 'ruangan' ? 'text-blue-700' : 'text-slate-400' }}"></i> Kelola Ruangan
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'users' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="users" class="w-4 h-4 mr-3 {{ $activeMenu === 'users' ? 'text-blue-700' : 'text-slate-400' }}"></i> Kelola Pengguna
            </a>
            @endif

            <a href="{{ route('barang.index') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'barang' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="box" class="w-4 h-4 mr-3 {{ $activeMenu === 'barang' ? 'text-blue-700' : 'text-slate-400' }}"></i> Data Barang
            </a>

            <a href="{{ route('peminjaman.index') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'peminjaman' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="arrow-right-left" class="w-4 h-4 mr-3 {{ $activeMenu === 'peminjaman' ? 'text-blue-700' : 'text-slate-400' }}"></i> Transaksi
            </a>

            <a href="{{ route('surat.bebas.lab') }}" class="flex items-center px-3 py-2 text-sm font-medium {{ $activeMenu === 'surat' ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md' }} transition-colors">
                <i data-lucide="file-check" class="w-4 h-4 mr-3 {{ $activeMenu === 'surat' ? 'text-blue-700' : 'text-slate-400' }}"></i> Surat Bebas Lab
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-sm shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="ml-3 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-slate-500 font-medium">{{ $isSuperAdmin ? 'Super Admin' : 'Admin Ruang ' . $user->ruangan_id }}</p>
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
            <h1 class="text-lg font-semibold text-slate-800">@yield('header', 'Dashboard')</h1>
            @yield('header-actions')
        </header>

        <div class="p-8 max-w-7xl mx-auto space-y-6">
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2 shrink-0"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2 shrink-0"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
