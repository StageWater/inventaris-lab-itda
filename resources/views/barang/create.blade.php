<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | SIMALAB ITDA</title>
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
            <!-- Menu Aktif -->
            <a href="{{ route('barang.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md">
                <i data-lucide="box" class="w-4 h-4 mr-3 text-blue-700"></i> Data Barang
            </a>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 overflow-y-auto">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center px-8">
            <h1 class="text-lg font-semibold text-slate-800">Manajemen Data Barang</h1>
        </header>

        <div class="p-8 max-w-3xl mx-auto">
            
            <!-- Tombol Kembali -->
            <a href="{{ route('barang.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-700 mb-6 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Barang
            </a>

            <!-- Form Card -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-xl font-bold text-blue-950">Tambah Barang Baru</h2>
                    <p class="text-sm text-slate-500 mt-1">Masukkan rincian data aset ke dalam sistem inventaris.</p>
                </div>
                
                <div class="p-6">
                    <!-- Form Action Mengarah ke Backend -->
                    <form action="{{ route('barang.store') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- Kolom Kode Barang -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Barang</label>
                            <input type="text" name="kode_barang" required placeholder="Contoh: KMP-001" 
                                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                        </div>

                        <!-- Kolom Nama Barang -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Barang</label>
                            <input type="text" name="nama_barang" required placeholder="Contoh: Monitor Samsung 24 Inch" 
                                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
                        </div>

                        <!-- Kolom Pilih Ruangan (Dropdown) -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Lokasi Ruangan</label>
                            <div class="relative">
                                <select name="ruangan_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                                    <option value="" disabled selected>-- Pilih Ruangan --</option>
                                    
                                    <!-- Nanti backend yang melooping data dari tabel ruangan -->
                                    <option value="1">Ruang 1 (Lab Komputer A)</option>
                                    <option value="2">Ruang 2 (Lab Komputer B)</option>
                                    
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100 mt-6">
                            <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                Reset
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm shadow-blue-200 transition-all flex items-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>